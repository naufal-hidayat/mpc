@extends('layouts.admin')

@section('title', 'Edit Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.transactions.index') }}">Transaksi</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Transaksi - {{ $transaction->transaction_code }}</h3>
    </div>
    <form action="{{ route('admin.transactions.update', $transaction) }}" method="POST" id="transactionForm">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Tipe transaksi tidak dapat diubah. Untuk mengubah tipe, silakan buat transaksi baru.
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tipe Transaksi</label>
                        <input type="text" class="form-control" value="{{ $transaction->getTypeLabel() }}" readonly>
                        <input type="hidden" name="type" value="{{ $transaction->type }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror"
                               value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required>
                        @error('transaction_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id', $transaction->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }} - {{ $supplier->phone }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>
            <h5>Detail Barang</h5>

            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="bg-light">
                        <tr>
                            <th width="30%">Barang</th>
                            <th width="15%">Qty</th>
                            <th width="10%">Satuan</th>
                            <th width="15%">Harga</th>
                            <th width="15%">Subtotal</th>
                            <th width="10%">Retur?</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @foreach($transaction->details as $index => $detail)
                        <tr class="item-row" data-row="{{ $index }}">
                            <td>
                                <select name="items[{{ $index }}][item_id]" class="form-control item-select" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}"
                                            data-unit="{{ $item->unit }}"
                                            data-price="{{ $item->price }}"
                                            {{ $detail->item_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][quantity]"
                                       class="form-control qty-input"
                                       value="{{ $detail->quantity }}" min="0" step="0.01" required>
                            </td>
                            <td>
                                <input type="text" class="form-control unit-display" readonly value="{{ $detail->item->unit }}">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][price]"
                                       class="form-control price-input"
                                       value="{{ $detail->price }}" min="0" step="0.01" required>
                            </td>
                            <td>
                                <input type="text" class="form-control subtotal-display" readonly
                                       value="Rp {{ number_format($detail->subtotal, 0, ',', '.') }}">
                            </td>
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input retur-checkbox"
                                           id="retur{{ $index }}" {{ $detail->isFullyReturned() ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="retur{{ $index }}"></label>
                                </div>
                                <input type="hidden" name="items[{{ $index }}][original_quantity]"
                                       class="original-qty" value="{{ $detail->quantity }}">
                                <textarea name="items[{{ $index }}][return_reason]"
                                          class="form-control mt-2 return-reason"
                                          placeholder="Alasan retur" rows="2"
                                          style="{{ $detail->isFullyReturned() ? '' : 'display:none;' }}">{{ $detail->isFullyReturned() ? ($transaction->returns->where('transaction_detail_id', $detail->id)->first()->reason ?? '') : '' }}</textarea>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-item" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7">
                                <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                                    <i class="fas fa-plus"></i> Tambah Barang
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $transaction->notes) }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Total</label>
                                <input type="text" class="form-control" id="displayTotal" readonly value="Rp 0">
                            </div>
                            <div class="form-group">
                                <label>Deposit / Uang Muka</label>
                                <input type="number" name="deposit" class="form-control" id="depositInput"
                                       value="{{ old('deposit', $transaction->deposit) }}" min="0" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>Total Akhir</label>
                                <input type="text" class="form-control font-weight-bold" id="displayFinalTotal" readonly value="Rp 0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Transaksi
            </button>
            <a href="{{ route('admin.transactions.show', $transaction) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const itemsData = @json($items);
let itemRowCount = {{ $transaction->details->count() }};

$(document).ready(function() {
    calculateTotal();

    $('#addItemBtn').click(function() {
        addItemRow();
    });

    $(document).on('input', '.qty-input, .price-input, #depositInput', calculateTotal);
    $(document).on('change', '.item-select', updateItemPrice);
    $(document).on('change', '.retur-checkbox', toggleReturFields);
    $(document).on('click', '.remove-item', removeItem);
});

function addItemRow() {
    itemRowCount++;

    let optionsHtml = '<option value="">-- Pilih Barang --</option>';
    itemsData.forEach(item => {
        optionsHtml += `<option value="${item.id}" data-unit="${item.unit}" data-price="${item.price}">${item.name}</option>`;
    });

    const row = `
        <tr class="item-row" data-row="${itemRowCount}">
            <td>
                <select name="items[${itemRowCount}][item_id]" class="form-control item-select" required>
                    ${optionsHtml}
                </select>
            </td>
            <td>
                <input type="number" name="items[${itemRowCount}][quantity]" class="form-control qty-input"
                       value="0" min="0" step="0.01" required>
            </td>
            <td>
                <input type="text" class="form-control unit-display" readonly value="-">
            </td>
            <td>
                <input type="number" name="items[${itemRowCount}][price]" class="form-control price-input"
                       value="0" min="0" step="0.01" required>
            </td>
            <td>
                <input type="text" class="form-control subtotal-display" readonly value="Rp 0">
            </td>
            <td>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input retur-checkbox" id="retur${itemRowCount}">
                    <label class="custom-control-label" for="retur${itemRowCount}"></label>
                </div>
                <input type="hidden" name="items[${itemRowCount}][original_quantity]" class="original-qty" value="0">
                <textarea name="items[${itemRowCount}][return_reason]" class="form-control mt-2 return-reason"
                          placeholder="Alasan retur" rows="2" style="display:none;"></textarea>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-item" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    $('#itemsBody').append(row);
}

function updateItemPrice() {
    const row = $(this).closest('.item-row');
    const selectedOption = $(this).find('option:selected');
    const unit = selectedOption.data('unit');
    const price = selectedOption.data('price');

    row.find('.unit-display').val(unit || '-');
    row.find('.price-input').val(price || 0);

    calculateTotal();
}

function toggleReturFields() {
    const row = $(this).closest('.item-row');
    const isReturned = $(this).is(':checked');
    const qtyInput = row.find('.qty-input');
    const originalQtyInput = row.find('.original-qty');
    const returnReason = row.find('.return-reason');

    if (isReturned) {
        originalQtyInput.val(qtyInput.val());
        qtyInput.val(0);
        returnReason.show().prop('required', true);
    } else {
        qtyInput.val(originalQtyInput.val());
        originalQtyInput.val(0);
        returnReason.hide().prop('required', false).val('');
    }

    calculateTotal();
}

function removeItem() {
    if ($('.item-row').length > 1) {
        $(this).closest('.item-row').remove();
        calculateTotal();
    } else {
        alert('Minimal harus ada 1 barang!');
    }
}

function calculateTotal() {
    let total = 0;

    $('.item-row').each(function() {
        const qty = parseFloat($(this).find('.qty-input').val()) || 0;
        const price = parseFloat($(this).find('.price-input').val()) || 0;
        const subtotal = qty * price;

        $(this).find('.subtotal-display').val('Rp ' + formatNumber(subtotal));
        total += subtotal;
    });

    const deposit = parseFloat($('#depositInput').val()) || 0;
    const finalTotal = total - deposit;

    $('#displayTotal').val('Rp ' + formatNumber(total));
    $('#displayFinalTotal').val('Rp ' + formatNumber(finalTotal));
}

function formatNumber(num) {
    return num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

$('#transactionForm').submit(function(e) {
    let hasItems = false;

    $('.item-row').each(function() {
        const itemId = $(this).find('.item-select').val();
        if (itemId) {
            hasItems = true;
        }
    });

    if (!hasItems) {
        e.preventDefault();
        alert('Harap tambahkan minimal 1 barang!');
        return false;
    }
});
</script>
@endpush
