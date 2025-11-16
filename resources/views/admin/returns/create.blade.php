@extends('layouts.admin')

@section('title', 'Tambah Retur Barang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.returns.index') }}">Retur Barang</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Retur Barang</h3>
    </div>
    <form action="{{ route('admin.returns.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Pilih Transaksi <span class="text-danger">*</span></label>
                <select name="transaction_id" id="transactionSelect" class="form-control @error('transaction_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Transaksi --</option>
                    @foreach($transactions as $trans)
                        <option value="{{ $trans->id }}" {{ old('transaction_id', request('transaction_id')) == $trans->id ? 'selected' : '' }}>
                            {{ $trans->transaction_code }} - {{ $trans->supplier->name }} - {{ $trans->transaction_date->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
                @error('transaction_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <small class="text-muted">Pilih transaksi terlebih dahulu untuk melihat detail barang</small>
            </div>

            @if($transaction)
            <div class="alert alert-info">
                <strong>Transaksi:</strong> {{ $transaction->transaction_code }}<br>
                <strong>Supplier:</strong> {{ $transaction->supplier->name }}<br>
                <strong>Tanggal:</strong> {{ $transaction->transaction_date->format('d F Y') }}
            </div>

            @if($details->count() > 0)
            <div class="form-group">
                <label>Pilih Barang <span class="text-danger">*</span></label>
                <select name="transaction_detail_id" id="detailSelect" class="form-control @error('transaction_detail_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($details as $detail)
                        <option value="{{ $detail->id }}"
                                data-max="{{ $detail->getEffectiveQuantity() }}"
                                data-unit="{{ $detail->item->unit }}"
                                {{ old('transaction_detail_id') == $detail->id ? 'selected' : '' }}>
                            {{ $detail->item->name }} - Tersedia: {{ number_format($detail->getEffectiveQuantity(), 2) }} {{ $detail->item->unit }}
                        </option>
                    @endforeach
                </select>
                @error('transaction_detail_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Jumlah Retur <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="quantity_returned" id="qtyInput" class="form-control @error('quantity_returned') is-invalid @enderror"
                           value="{{ old('quantity_returned') }}" min="0.01" step="0.01" required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="unitDisplay">-</span>
                    </div>
                </div>
                @error('quantity_returned')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
                <small class="text-muted" id="maxQtyInfo">Maksimal: -</small>
            </div>

            <div class="form-group">
                <label>Alasan Retur <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" required>{{ old('reason') }}</textarea>
                @error('reason')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <small class="text-muted">Contoh: Barang rusak, tidak sesuai spesifikasi, dll</small>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Tidak ada barang yang bisa diretur dalam transaksi ini (semua sudah diretur penuh)
            </div>
            @endif
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Silakan pilih transaksi terlebih dahulu atau
                <button type="button" class="btn btn-sm btn-primary" onclick="$('#transactionSelect').trigger('change')">
                    Refresh
                </button>
            </div>
            @endif
        </div>

        <div class="card-footer">
            @if($transaction && $details->count() > 0)
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Retur
            </button>
            @endif
            <a href="{{ route('admin.returns.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#transactionSelect').change(function() {
        const transactionId = $(this).val();
        if (transactionId) {
            window.location.href = '{{ route("admin.returns.create") }}?transaction_id=' + transactionId;
        }
    });

    $('#detailSelect').change(function() {
        const selectedOption = $(this).find('option:selected');
        const maxQty = selectedOption.data('max');
        const unit = selectedOption.data('unit');

        if (maxQty) {
            $('#qtyInput').attr('max', maxQty);
            $('#unitDisplay').text(unit);
            $('#maxQtyInfo').text('Maksimal: ' + maxQty + ' ' + unit);
        }
    });

    // Trigger on load if detail is selected
    if ($('#detailSelect').val()) {
        $('#detailSelect').trigger('change');
    }
});
</script>
@endpush
