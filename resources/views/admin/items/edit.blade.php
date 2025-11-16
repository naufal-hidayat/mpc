@extends('layouts.admin')

@section('title', 'Edit Barang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.items.index') }}">Data Barang</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Barang</h3>
    </div>
    <form action="{{ route('admin.items.update', $item) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label>Nama Barang <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $item->name) }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Satuan <span class="text-danger">*</span></label>
                <select name="unit" class="form-control @error('unit') is-invalid @enderror" required>
                    <option value="kg" {{ old('unit', $item->unit) == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                    <option value="pcs" {{ old('unit', $item->unit) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                </select>
                @error('unit')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Harga <span class="text-danger">*</span></label>
                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                       value="{{ old('price', $item->price) }}" min="0" step="0.01" required>
                @error('price')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                @if($item->price_updated_at)
                <small class="text-muted">Terakhir diupdate: {{ $item->price_updated_at->format('d/m/Y') }}</small>
                @endif
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection
