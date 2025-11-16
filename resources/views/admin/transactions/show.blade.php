@extends('layouts.admin')

@section('title', 'Detail Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.transactions.index') }}">Transaksi</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informasi Transaksi</h3>
                <div class="card-tools">
                    <span class="badge badge-{{ $transaction->type == 'purchase' ? 'primary' : 'success' }} badge-lg">
                        {{ $transaction->getTypeLabel() }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Kode Transaksi</th>
                        <td>: <strong>{{ $transaction->transaction_code }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>: {{ $transaction->transaction_date->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>{{ $transaction->getPartnerLabel() }}</th>
                        <td>: {{ $transaction->supplier->name }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>: {{ $transaction->supplier->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>No. HP</th>
                        <td>
                            : @if($transaction->supplier->phone)
                                <a href="https://wa.me/{{ $transaction->supplier->whatsapp_number }}" target="_blank">
                                    <i class="fab fa-whatsapp text-success"></i> {{ $transaction->supplier->phone }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Dibuat Oleh</th>
                        <td>: {{ $transaction->creator->name ?? 'Admin' }}</td>
                    </tr>
                    <tr>
                        <th>Status Pembayaran</th>
                        <td>
                            : @if($transaction->is_verified)
                                <span class="badge badge-success">Lunas</span>
                                <small class="text-muted">({{ $transaction->paid_at->format('d/m/Y H:i') }})</small>
                            @else
                                <span class="badge badge-warning">Belum Lunas</span>
                            @endif
                        </td>
                    </tr>
                    @if($transaction->notes)
                    <tr>
                        <th>Catatan</th>
                        <td>: {{ $transaction->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Barang</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%">Nama Barang</th>
                            <th width="15%">Qty</th>
                            <th width="10%">Satuan</th>
                            <th width="20%">Harga</th>
                            <th width="20%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalQty = 0; $totalAmount = 0; @endphp
                        @foreach($transaction->details as $detail)
                            @php
                                $effectiveQty = $detail->getEffectiveQuantity();
                                $effectiveSubtotal = $detail->getEffectiveSubtotal();
                                $isReturned = $detail->isFullyReturned();

                                if (!$isReturned) {
                                    $totalQty += $effectiveQty;
                                    $totalAmount += $effectiveSubtotal;
                                }
                            @endphp
                            <tr class="{{ $isReturned ? 'bg-danger text-white' : '' }}">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    {{ $detail->item->name }}
                                    @if($isReturned)
                                        <span class="badge badge-dark">RETUR PENUH</span>
                                    @elseif($detail->quantity_returned > 0)
                                        <span class="badge badge-warning">RETUR SEBAGIAN</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($isReturned)
                                        <s>{{ number_format($detail->quantity, 2) }}</s> → 0
                                    @else
                                        {{ number_format($effectiveQty, 2) }}
                                        @if($detail->quantity_returned > 0)
                                            <br><small>(Awal: {{ number_format($detail->quantity, 2) }})</small>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">{{ $detail->item->unit }}</td>
                                <td class="text-right">{{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td class="text-right">
                                    @if($isReturned)
                                        <s>{{ number_format($detail->subtotal, 0, ',', '.') }}</s> → 0
                                    @else
                                        {{ number_format($effectiveSubtotal, 0, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="2" class="text-right">TOTAL:</th>
                            <th class="text-right">{{ number_format($totalQty, 2) }}</th>
                            <th colspan="2"></th>
                            <th class="text-right">{{ number_format($totalAmount, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($transaction->hasReturns())
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Daftar Barang Retur</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Qty Diretur</th>
                            <th>Alasan</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->returns()->where('status', 'approved')->get() as $return)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $return->transactionDetail->item->name }}</td>
                            <td>{{ number_format($return->quantity_returned, 2) }} {{ $return->transactionDetail->item->unit }}</td>
                            <td>{{ $return->reason }}</td>
                            <td>{{ $return->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Ringkasan</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Total Awal:</th>
                        <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    @if($transaction->hasReturns())
                    <tr class="text-danger">
                        <th>Retur:</th>
                        <td class="text-right">- Rp {{ number_format($transaction->total_amount - $totalAmount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Deposit:</th>
                        <td class="text-right">- Rp {{ number_format($transaction->deposit, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-top">
                        <th>Total Akhir:</th>
                        <th class="text-right text-success">Rp {{ number_format($totalAmount - $transaction->deposit, 0, ',', '.') }}</th>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aksi</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.transactions.print', $transaction) }}" target="_blank"
                   class="btn btn-info btn-block mb-2">
                    <i class="fas fa-print"></i> Print Nota
                </a>
                <a href="{{ route('admin.transactions.pdf', $transaction) }}"
                   class="btn btn-success btn-block mb-2">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
                {{-- <form action="{{ route('admin.transactions.send-whatsapp', $transaction) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-block mb-2">
                        <i class="fab fa-whatsapp"></i> Kirim ke WhatsApp
                    </button>
                </form> --}}
                @if(!$transaction->is_verified)
                <form action="{{ route('admin.transactions.verify', $transaction) }}" method="POST"
                      onsubmit="return confirm('Tandai transaksi ini sebagai lunas?')">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-check-circle"></i> Tandai Lunas
                    </button>
                </form>
                @endif
                <a href="{{ route('admin.transactions.edit', $transaction) }}"
                   class="btn btn-warning btn-block mb-2">
                    <i class="fas fa-edit"></i> Edit Transaksi
                </a>
                <hr>
                <form action="{{ route('admin.transactions.destroy', $transaction) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-trash"></i> Hapus Transaksi
                    </button>
                </form>
            </div>
        </div>

        <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary btn-block">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection
