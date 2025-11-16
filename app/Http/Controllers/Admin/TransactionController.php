<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\ReturnItem;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['supplier', 'creator']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        $transactions = $query->latest()->paginate(20);
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.transactions.index', compact('transactions', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        return view('admin.transactions.create', compact('suppliers', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:purchase,sale',
            'supplier_id' => 'required|exists:suppliers,id',
            'transaction_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Hitung total
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }

            $deposit = $request->deposit ?? 0;
            $finalAmount = $totalAmount - $deposit;

            // Buat transaksi
            $transaction = Transaction::create([
                'transaction_code' => Transaction::generateCode($request->type),
                'type' => $request->type,
                'supplier_id' => $request->supplier_id,
                'transaction_date' => $request->transaction_date,
                'total_amount' => $totalAmount,
                'deposit' => $deposit,
                'final_amount' => $finalAmount,
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ]);

            // Simpan detail transaksi & retur
            foreach ($request->items as $itemData) {
                $quantity = $itemData['quantity'];
                $price = $itemData['price'];
                $subtotal = $quantity * $price;

                $detail = TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'quantity_returned' => 0
                ]);

                // Jika qty = 0, berarti barang diretur
                if ($quantity == 0) {
                    ReturnItem::create([
                        'transaction_id' => $transaction->id,
                        'transaction_detail_id' => $detail->id,
                        'quantity_returned' => $itemData['original_quantity'] ?? 0,
                        'reason' => $itemData['return_reason'] ?? 'Barang rusak/tidak sesuai',
                        'status' => 'approved',
                        'created_by' => auth()->id()
                    ]);

                    // Update quantity_returned di detail
                    $detail->update(['quantity_returned' => $itemData['original_quantity'] ?? 0]);
                }
            }

            DB::commit();

            // Kirim nota ke WhatsApp jika ada nomor HP
            if ($request->has('send_to_whatsapp') && $request->send_to_whatsapp) {
                $this->sendNotaToWhatsApp($transaction);
            }

            return redirect()->route('admin.transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['supplier', 'details.item', 'returns.transactionDetail.item', 'creator']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $transaction->load(['details.item']);
        $suppliers = Supplier::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        return view('admin.transactions.edit', compact('transaction', 'suppliers', 'items'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'transaction_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Hitung total
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }

            $deposit = $request->deposit ?? 0;
            $finalAmount = $totalAmount - $deposit;

            // Update transaksi
            $transaction->update([
                'supplier_id' => $request->supplier_id,
                'transaction_date' => $request->transaction_date,
                'total_amount' => $totalAmount,
                'deposit' => $deposit,
                'final_amount' => $finalAmount,
                'notes' => $request->notes
            ]);

            // Hapus detail lama
            $transaction->details()->delete();
            $transaction->returns()->delete();

            // Simpan detail baru
            foreach ($request->items as $itemData) {
                $quantity = $itemData['quantity'];
                $price = $itemData['price'];
                $subtotal = $quantity * $price;

                $detail = TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'quantity_returned' => 0
                ]);

                if ($quantity == 0) {
                    ReturnItem::create([
                        'transaction_id' => $transaction->id,
                        'transaction_detail_id' => $detail->id,
                        'quantity_returned' => $itemData['original_quantity'] ?? 0,
                        'reason' => $itemData['return_reason'] ?? 'Barang rusak/tidak sesuai',
                        'status' => 'approved',
                        'created_by' => auth()->id()
                    ]);

                    $detail->update(['quantity_returned' => $itemData['original_quantity'] ?? 0]);
                }
            }

            DB::commit();

            return redirect()->route('admin.transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->delete();
            return redirect()->route('admin.transactions.index')
                ->with('success', 'Transaksi berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus transaksi');
        }
    }

    public function print(Transaction $transaction)
    {
        $transaction->load(['supplier', 'details.item', 'returns.transactionDetail.item', 'creator']);
        return view('admin.transactions.print', compact('transaction'));
    }

    public function downloadPdf(Transaction $transaction)
    {
        $transaction->load(['supplier', 'details.item', 'returns.transactionDetail.item', 'creator']);

        $pdf = Pdf::loadView('admin.transactions.print', compact('transaction'))
            ->setPaper('a4', 'portrait');

        // Format: Nota_MPC-NamaSupplier-TipeTrans-Tanggal.pdf
        $supplierName = str_replace(' ', '_', $transaction->supplier->name); // Ganti spasi dengan underscore
        $type = $transaction->type == 'purchase' ? 'Pembelian' : 'Penjualan';
        $date = $transaction->transaction_date->format('d-m-Y');

        $filename = "Nota_MPC-{$supplierName}-{$type}-{$date}.pdf";

        return $pdf->download($filename);
    }

    public function sendToWhatsApp(Transaction $transaction)
    {
        try {
            $result = $this->sendNotaToWhatsApp($transaction);

            if ($result['success']) {
                return back()->with('success', 'Nota berhasil dikirim ke WhatsApp');
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim nota: ' . $e->getMessage());
        }
    }

    private function sendNotaToWhatsApp(Transaction $transaction)
    {
        $supplier = $transaction->supplier;

        if (!$supplier->whatsapp_number) {
            return ['success' => false, 'message' => 'Nomor WhatsApp supplier tidak tersedia'];
        }

        // Generate PDF
        $transaction->load(['supplier', 'details.item', 'returns.transactionDetail.item', 'creator']);
        $pdf = Pdf::loadView('admin.transactions.print', compact('transaction'))
            ->setPaper('a4', 'portrait');

        // Format nama file: Nota_MPC-NamaSupplier-TipeTrans-Tanggal.pdf
        $supplierName = str_replace(' ', '_', $supplier->name);
        $type = $transaction->type == 'purchase' ? 'Pembelian' : 'Penjualan';
        $date = $transaction->transaction_date->format('d-m-Y');
        $filename = "Nota_MPC-{$supplierName}-{$type}-{$date}.pdf";

        $pdfPath = storage_path('app/public/' . $filename);
        $pdf->save($pdfPath);

        // Kirim via WhatsApp API (Anda perlu menggunakan service seperti Fonnte, Wablas, dll)
        $phone = $supplier->whatsapp_number;
        $message = "Halo *{$supplier->name}*,\n\n";
        $message .= "Berikut nota transaksi {$transaction->getTypeLabel()}:\n";
        $message .= "No: *{$transaction->transaction_code}*\n";
        $message .= "Tanggal: " . $transaction->transaction_date->format('d F Y') . "\n";
        $message .= "Total: Rp " . number_format($transaction->final_amount, 0, ',', '.') . "\n\n";
        $message .= "Terima kasih atas kerjasamanya.\n\n";
        $message .= "*PT Mitra Panel Cherbond*";

        // Contoh integrasi dengan Fonnte (sesuaikan dengan API yang Anda gunakan)
        // Anda perlu mendaftar dan mendapatkan API key dari provider WhatsApp Gateway

        // Untuk saat ini, kita hanya return URL WhatsApp Web
        $waUrl = "https://wa.me/{$phone}?text=" . urlencode($message);

        // Hapus file temporary
        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }

        return [
            'success' => true,
            'message' => 'Silakan klik link untuk mengirim via WhatsApp',
            'url' => $waUrl
        ];
    }

    public function verify(Transaction $transaction)
    {
        $transaction->update([
            'is_verified' => true,
            'paid_at' => now(),
            'payment_method' => 'cash' // atau sesuai input
        ]);

        return back()->with('success', 'Transaksi berhasil diverifikasi');
    }
}
