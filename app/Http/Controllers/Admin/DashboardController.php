<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_suppliers' => Supplier::count(),
            'total_items' => Item::count(),
            'total_transactions' => Transaction::count(),
            'total_purchases' => Transaction::where('type', 'purchase')->count(),
            'total_sales' => Transaction::where('type', 'sale')->count(),
            'recent_transactions' => Transaction::with(['supplier', 'creator'])
                ->latest()
                ->take(10)
                ->get()
        ];

        return view('admin.dashboard', $data);
    }
}
