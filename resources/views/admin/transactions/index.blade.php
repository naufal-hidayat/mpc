@extends('layouts.admin')

@section('title', 'Daftar Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item active">Transaksi</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Transaksi</h3>
        <div class="card-tools">
            <a href="{{ route('admin.transactions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <select name="type" class="form-control">
                        <option value="">-- Semua Tipe --</option>
                        <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Pembelian</option>
                        <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Penjualan</option>
                    </select>
                </div>
                <div class="col-md-3">
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
                    <input type="date" name="from_date" class="form-control" placeholder="Dari Tanggal"
                           value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="to_date" class="form-control" placeholder="Sampai Tanggal"
                           value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info btn-block">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Deposit</th>
                        <th>Total Akhir</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                        <td><strong>{{ $transaction->transaction_code }}</strong></td>
                        <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $transaction->type == 'purchase' ? 'primary' : 'success' }}">
                                {{ $transaction->getTypeLabel() }}
                            </span>
                        </td>
                        <td>{{ $transaction->supplier->name }}</td>
                        <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($transaction->deposit, 0, ',', '.') }}</td>
                        <td><strong>Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($transaction->is_verified)
                                <span class="badge badge-success">Lunas</span>
                            @else
                                <span class="badge badge-warning">Belum Lunas</span>
                            @endif
                            @if($transaction->hasReturns())
                                <br><span class="badge badge-danger mt-1">Ada Retur</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.transactions.show', $transaction) }}"
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.transactions.edit', $transaction) }}"
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.transactions.pdf', $transaction) }}"
                                   class="btn btn-sm btn-success" title="Download PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer clearfix">
        {{ $transactions->appends(request()->query())->links() }}
    </div>
</div>
@endsection
