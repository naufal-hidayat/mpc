@extends('layouts.admin')

@section('title', 'Data Supplier')

@section('breadcrumb')
    <li class="breadcrumb-item active">Data Supplier</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Supplier</h3>
        <div class="card-tools">
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Supplier
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No. HP/WA</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td>{{ $loop->iteration + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}</td>
                    <td><strong>{{ $supplier->name }}</strong></td>
                    <td>{{ $supplier->address ?? '-' }}</td>
                    <td>
                        @if($supplier->phone)
                            <a href="https://wa.me/{{ $supplier->whatsapp_number }}" target="_blank" class="text-success">
                                <i class="fab fa-whatsapp"></i> {{ $supplier->phone }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
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
                    <td colspan="5" class="text-center">Belum ada data supplier</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
