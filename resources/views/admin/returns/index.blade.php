@extends('layouts.admin')

@section('title', 'Daftar Retur Barang')

@section('breadcrumb')
    <li class="breadcrumb-item active">Retur Barang</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Retur Barang</h3>
        <div class="card-tools">
            <a href="{{ route('admin.returns.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Retur
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <select name="status" class="form-control">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
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
                        <th>Kode Transaksi</th>
                        <th>Supplier</th>
                        <th>Nama Barang</th>
                        <th>Qty Diretur</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    <tr>
                        <td>{{ $loop->iteration + ($returns->currentPage() - 1) * $returns->perPage() }}</td>
                        <td>
                            <a href="{{ route('admin.transactions.show', $return->transaction) }}">
                                {{ $return->transaction->transaction_code }}
                            </a>
                        </td>
                        <td>{{ $return->transaction->supplier->name }}</td>
                        <td>{{ $return->transactionDetail->item->name }}</td>
                        <td>{{ number_format($return->quantity_returned, 2) }} {{ $return->transactionDetail->item->unit }}</td>
                        <td>{{ $return->reason }}</td>
                        <td>
                            @if($return->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($return->status == 'approved')
                                <span class="badge badge-success">Approved</span>
                            @else
                                <span class="badge badge-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $return->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.returns.show', $return) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($return->status == 'pending')
                            <form action="{{ route('admin.returns.destroy', $return) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus retur ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data retur</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer clearfix">
        {{ $returns->appends(request()->query())->links() }}
    </div>
</div>
@endsection
