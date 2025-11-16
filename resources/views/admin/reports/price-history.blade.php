@extends('layouts.admin')

@section('title', 'Histori Harga Barang')

@section('breadcrumb')
    <li class="breadcrumb-item">Laporan</li>
    <li class="breadcrumb-item active">Histori Harga</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Histori Perubahan Harga Barang</h3>
        <div class="card-tools">
            <form method="GET" action="{{ route('admin.reports.price-history-pdf') }}" class="d-inline">
                <input type="hidden" name="item_id" value="{{ request('item_id') }}">
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
                <div class="col-md-4">
                    <label>Barang</label>
                    <select name="item_id" class="form-control">
                        <option value="">-- Semua Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Dari Tanggal</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th>Harga Lama</th>
                        <th>Harga Baru</th>
                        <th>Perubahan</th>
                        <th>Diubah Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                    @php
                        $diff = $history->new_price - $history->old_price;
                        $percent = $history->old_price > 0 ? ($diff / $history->old_price * 100) : 0;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration + ($histories->currentPage() - 1) * $histories->perPage() }}</td>
                        <td>{{ $history->changed_at->format('d/m/Y') }}</td>
                        <td><strong>{{ $history->item->name }}</strong></td>
                        <td class="text-right">Rp {{ number_format($history->old_price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($history->new_price, 0, ',', '.') }}</td>
                        <td class="text-right">
                            @if($diff > 0)
                                <span class="badge badge-success">
                                    <i class="fas fa-arrow-up"></i> Rp {{ number_format($diff, 0, ',', '.') }}
                                    ({{ number_format($percent, 1) }}%)
                                </span>
                            @elseif($diff < 0)
                                <span class="badge badge-danger">
                                    <i class="fas fa-arrow-down"></i> Rp {{ number_format(abs($diff), 0, ',', '.') }}
                                    ({{ number_format($percent, 1) }}%)
                                </span>
                            @else
                                <span class="badge badge-secondary">Tidak Berubah</span>
                            @endif
                        </td>
                        <td>{{ $history->changedBy->name ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada histori perubahan harga</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer clearfix">
        {{ $histories->appends(request()->query())->links() }}
    </div>
</div>
@endsection
