<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnItem;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = ReturnItem::with(['transaction.supplier', 'transactionDetail.item', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('transaction_id')) {
            $query->where('transaction_id', $request->transaction_id);
        }

        $returns = $query->latest()->paginate(20);

        return view('admin.returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        $transaction = null;
        $details = collect();

        if ($transactionId) {
            $transaction = Transaction::with(['details.item'])->findOrFail($transactionId);
            $details = $transaction->details->filter(function($detail) {
                return $detail->getEffectiveQuantity() > 0;
            });
        }

        $transactions = Transaction::with('supplier')->latest()->take(50)->get();

        return view('admin.returns.create', compact('transactions', 'transaction', 'details'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'transaction_detail_id' => 'required|exists:transaction_details,id',
            'quantity_returned' => 'required|numeric|min:0.01',
            'reason' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $detail = TransactionDetail::findOrFail($request->transaction_detail_id);

            // Validasi jumlah retur
            $maxReturn = $detail->getEffectiveQuantity();
            if ($request->quantity_returned > $maxReturn) {
                return back()->with('error', 'Jumlah retur melebihi jumlah yang tersedia')->withInput();
            }

            // Buat retur
            $return = ReturnItem::create([
                'transaction_id' => $request->transaction_id,
                'transaction_detail_id' => $request->transaction_detail_id,
                'quantity_returned' => $request->quantity_returned,
                'reason' => $request->reason,
                'status' => 'approved',
                'created_by' => auth()->id()
            ]);

            // Update quantity_returned di detail
            $detail->increment('quantity_returned', $request->quantity_returned);

            // Update total transaksi
            $transaction = $detail->transaction;
            $newTotal = $transaction->details->sum(function($d) {
                return $d->getEffectiveSubtotal();
            });

            $transaction->update([
                'final_amount' => $newTotal - $transaction->deposit
            ]);

            DB::commit();

            return redirect()->route('admin.returns.index')
                ->with('success', 'Retur barang berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(ReturnItem $return)
    {
        $return->load(['transaction.supplier', 'transactionDetail.item', 'creator']);
        return view('admin.returns.show', compact('return'));
    }

    public function updateStatus(Request $request, ReturnItem $return)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $return->update(['status' => $request->status]);

        if ($request->status === 'approved') {
            // Update detail transaksi
            $detail = $return->transactionDetail;
            $detail->increment('quantity_returned', $return->quantity_returned);

            // Update total transaksi
            $transaction = $return->transaction;
            $newTotal = $transaction->details->sum(function($d) {
                return $d->getEffectiveSubtotal();
            });

            $transaction->update([
                'final_amount' => $newTotal - $transaction->deposit
            ]);
        }

        return back()->with('success', 'Status retur berhasil diperbarui');
    }

    public function destroy(ReturnItem $return)
    {
        DB::beginTransaction();
        try {
            if ($return->status === 'approved') {
                // Kembalikan quantity_returned
                $detail = $return->transactionDetail;
                $detail->decrement('quantity_returned', $return->quantity_returned);

                // Update total transaksi
                $transaction = $return->transaction;
                $newTotal = $transaction->details->sum(function($d) {
                    return $d->getEffectiveSubtotal();
                });

                $transaction->update([
                    'final_amount' => $newTotal - $transaction->deposit
                ]);
            }

            $return->delete();
            DB::commit();

            return redirect()->route('admin.returns.index')
                ->with('success', 'Retur berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus retur');
        }
    }
}
