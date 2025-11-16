@extends('layouts.admin')

@section('title', 'Laporan Per Supplier')

@section('breadcrumb')
    <li class="breadcrumb-item">Laporan</li>
    <li class="breadcrumb-item active">Per Supplier</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Transaksi Per Supplier</h3>
        <div class="card-tools">
            <form method="GET" action="{{ route('admin.reports.by-supplier-pdf') }}" class="d-inline">
                <input type="hidden" name="supplier_id" value="{{ request('supplier_id') }}">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <input type="hidden" name="from_date" value="{{ request('from_date') }}">
                <input type="hidden" name="to_date" value="{{ request('to_date') }}">
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">-- Semua Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Tipe</label>
                    <select name="type" class="form-control">
                        <option value="">-- Semua Tipe --</option>
                        <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Pembelian</option>
                        <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Penjualan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Dari Tanggal</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>

        @if(request()->hasAny(['supplier_id', 'type', 'from_date', 'to_date']))
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total_transactions'] }}</h3>
                        <p>Total Transaksi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</h3>
                        <p>Total Keseluruhan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>Rp {{ number_format($stats['total_purchase'], 0, ',', '.') }}</h3>
                        <p>Total Pembelian</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>Rp {{ number_format($stats['total_sale'], 0, ',', '.') }}</h3>
                        <p>Total Penjualan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Deposit</th>
                        <th>Total Akhir</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <a href="{{ route('admin.transactions.show', $transaction) }}">
                                {{ $transaction->transaction_code }}
                            </a>
                        </td>
                        <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $transaction->type == 'purchase' ? 'primary' : 'success' }}">
                                {{ $transaction->getTypeLabel() }}
                            </span>
                        </td>
                        <td>{{ $transaction->supplier->name }}</td>
                        <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($transaction->deposit, 0, ',', '.') }}</td>
                        <td class="text-right"><strong>Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($transaction->is_verified)
                                <span class="badge badge-success">Lunas</span>
                            @else
                                <span class="badge badge-warning">Belum Lunas</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($transactions->count() > 0)
                <tfoot class="bg-light">
                    <tr>
                        <th colspan="5" class="text-right">TOTAL:</th>
                        <th class="text-right">Rp {{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}</th>
                        <th class="text-right">Rp {{ number_format($transactions->sum('deposit'), 0, ',', '.') }}</th>
                        <th class="text-right">Rp {{ number_format($transactions->sum('final_amount'), 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Silakan pilih filter untuk menampilkan laporan
        </div>
        @endif
    </div>
</div>
@endsection
