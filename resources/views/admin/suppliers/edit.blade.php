@extends('layouts.admin')

@section('title', 'Edit Supplier')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.suppliers.index') }}">Data Supplier</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Supplier</h3>
    </div>
    <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label>Nama Supplier <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $supplier->name) }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                          rows="3">{{ old('address', $supplier->address) }}</textarea>
                @error('address')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Nomor HP/WhatsApp</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $supplier->phone) }}" placeholder="Contoh: 081234567890">
                @error('phone')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <small class="text-muted">Format: 08xxx atau 62xxx (untuk WhatsApp)</small>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection
