@extends('layouts.admin')

@section('title', 'Data Barang')

@section('breadcrumb')
    <li class="breadcrumb-item active">Data Barang</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Barang</h3>
        <div class="card-tools">
            <div class="btn-group mr-2">
                <a href="{{ route('admin.items.export-pdf') }}" class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('admin.items.export-excel') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
            <a href="{{ route('admin.items.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Barang
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Harga</th>
                    <th>Terakhir Update</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr id="row-item-{{ $item->id }}">
                    <td>{{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}</td>
                    <td><strong>{{ $item->name }}</strong></td>
                    <td><span class="badge badge-info">{{ strtoupper($item->unit) }}</span></td>
                    <td class="price-cell-{{ $item->id }}">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="date-cell-{{ $item->id }}">{{ $item->price_updated_at ? $item->price_updated_at->format('d/m/Y') : '-' }}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="openUpdatePriceModal({{ $item->id }}, '{{ $item->name }}', {{ $item->price }})">
                            <i class="fas fa-dollar-sign"></i> Update Harga
                        </button>
                        <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data barang</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $items->links() }}
    </div>
</div>

<!-- Modal Update Harga -->
<div class="modal fade" id="updatePriceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">Update Harga Barang</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="updatePriceForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="item_id">

                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" class="form-control" id="item_name" readonly>
                    </div>

                    <div class="form-group">
                        <label>Harga Saat Ini</label>
                        <input type="text" class="form-control" id="current_price" readonly>
                    </div>

                    <div class="form-group">
                        <label>Harga Baru <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="new_price" name="price"
                               placeholder="Masukkan harga baru" required min="0" step="0.01">
                        <small class="text-muted">Masukkan harga baru untuk barang ini</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info" id="btnUpdatePrice">
                        <i class="fas fa-save"></i> Update Harga
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openUpdatePriceModal(itemId, itemName, currentPrice) {
    $('#item_id').val(itemId);
    $('#item_name').val(itemName);
    $('#current_price').val('Rp ' + new Intl.NumberFormat('id-ID').format(currentPrice));
    $('#new_price').val('');
    $('#updatePriceModal').modal('show');
}

$(document).ready(function() {
    $('#updatePriceForm').on('submit', function(e) {
        e.preventDefault();

        const itemId = $('#item_id').val();
        const newPrice = $('#new_price').val();
        const submitBtn = $('#btnUpdatePrice');

        // Validasi
        if (!newPrice || newPrice <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Harga harus lebih dari 0!'
            });
            return;
        }

        // Disable button dan ubah text
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

        // AJAX Request
        $.ajax({
            url: '{{ url("admin/items") }}/' + itemId + '/update-price',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                price: newPrice
            },
            success: function(response) {
                console.log('Success response:', response);

                if(response.success) {
                    // Tutup modal
                    $('#updatePriceModal').modal('hide');

                    // Update tampilan harga tanpa reload
                    $('.price-cell-' + itemId).html(response.data.formatted_price);

                    // Update tanggal
                    const today = new Date();
                    const dateStr = String(today.getDate()).padStart(2, '0') + '/' +
                                  String(today.getMonth() + 1).padStart(2, '0') + '/' +
                                  today.getFullYear();
                    $('.date-cell-' + itemId).html(dateStr);

                    // Highlight row yang diupdate
                    $('#row-item-' + itemId).addClass('table-success');
                    setTimeout(function() {
                        $('#row-item-' + itemId).removeClass('table-success');
                    }, 3000);

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', xhr.responseText);

                let errorMsg = 'Terjadi kesalahan saat update harga';

                if(xhr.status === 422) {
                    // Validation error
                    const errors = xhr.responseJSON.errors;
                    errorMsg = Object.values(errors).flat().join(', ');
                } else if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errorMsg
                });
            },
            complete: function() {
                // Enable button kembali
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Harga');
            }
        });
    });

    // Reset form saat modal ditutup
    $('#updatePriceModal').on('hidden.bs.modal', function () {
        $('#updatePriceForm')[0].reset();
        $('#btnUpdatePrice').prop('disabled', false).html('<i class="fas fa-save"></i> Update Harga');
    });
});
</script>

<style>
.table-success {
    background-color: #d4edda !important;
    transition: background-color 0.5s ease;
}
</style>
@endpush
