<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemsExport;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::latest()->paginate(20);
        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        return view('admin.items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|in:kg,pcs',
            'price' => 'required|numeric|min:0'
        ]);

        $item = Item::create([
            'name' => $request->name,
            'unit' => $request->unit,
            'price' => $request->price,
            'price_updated_at' => now()
        ]);

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Item $item)
    {
        return view('admin.items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|in:kg,pcs',
            'price' => 'required|numeric|min:0'
        ]);

        $item->update([
            'name' => $request->name,
            'unit' => $request->unit,
            'price' => $request->price
        ]);

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil diperbarui');
    }

    // Update harga via modal
    public function updatePrice(Request $request, Item $item)
    {
        try {
            $validated = $request->validate([
                'price' => 'required|numeric|min:0'
            ]);

            $oldPrice = $item->price;

            // Update harga
            $item->price = $validated['price'];
            $item->save();

            // Refresh data
            $item->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Harga berhasil diperbarui',
                'data' => [
                    'old_price' => $oldPrice,
                    'new_price' => $item->price,
                    'formatted_price' => 'Rp ' . number_format($item->price, 0, ',', '.'),
                    'updated_at' => now()->format('d/m/Y')
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Item $item)
    {
        try {
            $item->delete();
            return redirect()->route('admin.items.index')
                ->with('success', 'Barang berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.items.index')
                ->with('error', 'Gagal menghapus barang. Barang mungkin sudah digunakan dalam transaksi.');
        }
    }

    // Export ke PDF
    public function exportPdf()
    {
        $items = Item::latest()->get();
        $pdf = Pdf::loadView('admin.items.pdf', compact('items'));

        return $pdf->download('daftar-harga-barang-' . date('Y-m-d') . '.pdf');
    }

    // Export ke Excel
    public function exportExcel()
    {
        return Excel::download(new ItemsExport, 'daftar-harga-barang-' . date('Y-m-d') . '.xlsx');
    }
}
