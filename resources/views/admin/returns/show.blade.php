@extends('layouts.admin')

@section('title', 'Detail Retur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.returns.index') }}">Retur Barang</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Retur</h3>
                <div class="card-tools">
                    @if($return->status == 'pending')
                        <span class="badge badge-warning badge-lg">Pending</span>
                    @elseif($return->status == 'approved')
                        <span class="badge badge-success badge-lg">Approved</span>
                    @else
                        <span class="badge badge-danger badge-lg">Rejected</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Transaksi</th>
                        <td>:
                            <a href="{{ route('admin.transactions.show', $return->transaction) }}">
                                {{ $return->transaction->transaction_code }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Transaksi</th>
                        <td>: {{ $return->transaction->transaction_date->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>: {{ $return->transaction->supplier->name }}</td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td>: <strong>{{ $return->transactionDetail->item->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Qty Diretur</th>
                        <td>: <strong class="text-danger">{{ number_format($return->quantity_returned, 2) }} {{ $return->transactionDetail->item->unit }}</strong></td>
                    </tr>
                    <tr>
                        <th>Alasan Retur</th>
                        <td>: {{ $return->reason }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat Oleh</th>
                        <td>: {{ $return->creator->name ?? 'Admin' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Retur</th>
                        <td>: {{ $return->created_at->format('d F Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aksi</h3>
            </div>
            <div class="card-body">
                @if($return->status == 'pending')
                <form action="{{ route('admin.returns.update-status', $return) }}" method="POST" class="mb-2">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Approve retur ini?')">
                        <i class="fas fa-check-circle"></i> Approve
                    </button>
                </form>
                <form action="{{ route('admin.returns.update-status', $return) }}" method="POST" class="mb-2">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="btn btn-warning btn-block" onclick="return confirm('Reject retur ini?')">
                        <i class="fas fa-times-circle"></i> Reject
                    </button>
                </form>
                <hr>
                <form action="{{ route('admin.returns.destroy', $return) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Yakin ingin menghapus retur ini?')">
                        <i class="fas fa-trash"></i> Hapus Retur
                    </button>
                </form>
                @else
                <div class="alert alert-info">
                    Status retur sudah {{ $return->status }}. Tidak dapat diubah.
                </div>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.returns.index') }}" class="btn btn-secondary btn-block">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection
