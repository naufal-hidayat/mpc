<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\PriceHistory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function bySupplier(Request $request)
    {
        $query = Transaction::with(['supplier', 'details.item']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        $transactions = $query->latest('transaction_date')->get();
        $suppliers = Supplier::orderBy('name')->get();

        // Statistik
        $stats = [
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->sum('final_amount'),
            'total_purchase' => $transactions->where('type', 'purchase')->sum('final_amount'),
            'total_sale' => $transactions->where('type', 'sale')->sum('final_amount'),
        ];

        return view('admin.reports.by-supplier', compact('transactions', 'suppliers', 'stats'));
    }

    public function bySupplierPdf(Request $request)
    {
        $query = Transaction::with(['supplier', 'details.item']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date', '<=', $request->to_date);
        }

        $transactions = $query->latest('transaction_date')->get();

        $stats = [
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->sum('final_amount'),
            'total_purchase' => $transactions->where('type', 'purchase')->sum('final_amount'),
            'total_sale' => $transactions->where('type', 'sale')->sum('final_amount'),
        ];

        $pdf = Pdf::loadView('admin.reports.by-supplier-pdf', compact('transactions', 'stats'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Supplier_' . now()->format('YmdHis') . '.pdf');
    }

    public function priceHistory(Request $request)
    {
        $query = PriceHistory::with(['item', 'changedBy']);

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('changed_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('changed_at', '<=', $request->to_date);
        }

        $histories = $query->latest('changed_at')->paginate(30);
        $items = Item::orderBy('name')->get();

        return view('admin.reports.price-history', compact('histories', 'items'));
    }

    public function priceHistoryPdf(Request $request)
    {
        $query = PriceHistory::with(['item', 'changedBy']);

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('changed_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('changed_at', '<=', $request->to_date);
        }

        $histories = $query->latest('changed_at')->get();

        $pdf = Pdf::loadView('admin.reports.price-history-pdf', compact('histories'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Histori_Harga_' . now()->format('YmdHis') . '.pdf');
    }
}
