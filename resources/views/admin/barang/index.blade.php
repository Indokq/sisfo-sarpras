@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">
                <i class="fas fa-box text-primary me-2"></i>
                Data Barang
            </h1>
            <p class="text-muted mb-0">Kelola inventaris barang dalam sistem</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" onclick="refreshData()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            <a href="{{ route('admin.barang.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Barang
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle text-success me-2 fs-5"></i>
                <div>
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-box text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Barang</div>
                            <div class="fs-5 fw-bold">{{ $barangs->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-images text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Dengan Foto</div>
                            <div class="fs-5 fw-bold">{{ $barangs->filter(function($barang) { return $barang->foto; })->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-tags text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Kategori Unik</div>
                            <div class="fs-5 fw-bold">{{ $barangs->pluck('kategori.nama_kategori')->unique()->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-chart-line text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Dengan Stok</div>
                            <div class="fs-5 fw-bold">{{ $barangs->filter(function($barang) { return $barang->stok && $barang->stok->jumlah > 0; })->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Data Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-list text-primary me-2"></i>
                        Daftar Barang
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari barang..." id="searchInput">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="barangTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">#</th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-image me-1"></i>Foto
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-box me-1"></i>Nama Barang
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-tag me-1"></i>Kategori
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-chart-line me-1"></i>Stok
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-calendar me-1"></i>Dibuat
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted text-center">
                                <i class="fas fa-cogs me-1"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangs as $barang)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <span class="badge bg-light text-dark rounded-pill">{{ $loop->iteration }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($barang->foto)
                                        <div class="position-relative">
                                            <img src="{{ asset('storage/' . $barang->foto) }}"
                                                 alt="foto barang"
                                                 class="rounded-3 shadow-sm"
                                                 style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                                 data-bs-toggle="modal"
                                                 data-bs-target="#imageModal"
                                                 onclick="showImage('{{ asset('storage/' . $barang->foto) }}', '{{ $barang->nama_barang }}')">
                                            <div class="position-absolute top-0 end-0 translate-middle">
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="fas fa-check" style="font-size: 8px;"></i>
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light rounded-3"
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-box text-info"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $barang->nama_barang }}</div>
                                            <small class="text-muted">{{ Str::limit($barang->deskripsi, 50) ?? 'Tidak ada deskripsi' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($barang->kategori)
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                            <i class="fas fa-tag me-1"></i>{{ $barang->kategori->nama_kategori }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                            <i class="fas fa-question me-1"></i>Tidak ada kategori
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($barang->stok)
                                        @if($barang->stok->jumlah > 10)
                                            <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                                <i class="fas fa-check me-1"></i>{{ $barang->stok->jumlah }}
                                            </span>
                                        @elseif($barang->stok->jumlah > 0)
                                            <span class="badge bg-warning bg-opacity-15 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill">
                                                <i class="fas fa-exclamation me-1"></i>{{ $barang->stok->jumlah }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">
                                                <i class="fas fa-times me-1"></i>Kosong
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                            <i class="fas fa-minus me-1"></i>Belum ada stok
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-muted small">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $barang->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $barang->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.barang.edit', $barang->id) }}"
                                           class="btn btn-warning btn-sm rounded-start"
                                           data-bs-toggle="tooltip" title="Edit Barang">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-end"
                                                    onclick="return confirm('Yakin hapus barang ini? Data stok dan peminjaman terkait akan terpengaruh!')"
                                                    data-bs-toggle="tooltip" title="Hapus Barang">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="mb-3">
                                            <i class="fas fa-box text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">Tidak ada barang</h5>
                                        <p class="text-muted mb-3">Belum ada barang yang terdaftar dalam sistem.</p>
                                        <a href="{{ route('admin.barang.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i> Tambah Barang Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="imageModalLabel">Preview Foto Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" alt="Preview" class="img-fluid rounded-bottom" style="max-height: 500px;">
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="fas fa-filter me-2"></i>Filter Barang
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label for="kategoriFilter" class="form-label">Kategori</label>
                        <select class="form-select" id="kategoriFilter">
                            <option value="">Semua Kategori</option>
                            @foreach($barangs->pluck('kategori.nama_kategori')->unique()->filter() as $kategori)
                                <option value="{{ $kategori }}">{{ $kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="stokFilter" class="form-label">Status Stok</label>
                        <select class="form-select" id="stokFilter">
                            <option value="">Semua Status</option>
                            <option value="ada">Ada Stok</option>
                            <option value="kosong">Stok Kosong</option>
                            <option value="sedikit">Stok Sedikit (≤10)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fotoFilter" class="form-label">Status Foto</label>
                        <select class="form-select" id="fotoFilter">
                            <option value="">Semua</option>
                            <option value="ada">Ada Foto</option>
                            <option value="tidak">Tidak Ada Foto</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="applyFilter()">Terapkan Filter</button>
                <button type="button" class="btn btn-outline-primary" onclick="resetFilter()">Reset</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
        transition: background-color 0.15s ease-in-out;
    }

    .btn-group .btn {
        border-radius: 0.375rem !important;
    }

    .btn-group .btn:not(:last-child) {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }

    .btn-group .btn:not(:first-child) {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }

    .card {
        transition: box-shadow 0.15s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .alert {
        animation: slideInDown 0.3s ease-out;
    }

    @keyframes slideInDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .position-relative img:hover {
        transform: scale(1.05);
        transition: transform 0.2s ease-in-out;
    }
</style>

<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Show image in modal
    function showImage(src, title) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModalLabel').textContent = 'Preview: ' + title;
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#barangTable tbody tr');

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Refresh data function
    function refreshData() {
        location.reload();
    }

    // Filter functions
    function applyFilter() {
        const kategoriFilter = document.getElementById('kategoriFilter').value;
        const stokFilter = document.getElementById('stokFilter').value;
        const fotoFilter = document.getElementById('fotoFilter').value;
        const tableRows = document.querySelectorAll('#barangTable tbody tr');

        tableRows.forEach(row => {
            let showRow = true;

            // Kategori filter
            if (kategoriFilter) {
                const kategoriCell = row.cells[3];
                if (!kategoriCell || !kategoriCell.textContent.toLowerCase().includes(kategoriFilter.toLowerCase())) {
                    showRow = false;
                }
            }

            // Stok filter
            if (stokFilter) {
                const stokCell = row.cells[4];
                const stokText = stokCell.textContent.toLowerCase();

                if (stokFilter === 'ada' && (stokText.includes('kosong') || stokText.includes('belum'))) {
                    showRow = false;
                } else if (stokFilter === 'kosong' && !stokText.includes('kosong')) {
                    showRow = false;
                } else if (stokFilter === 'sedikit' && !stokText.includes('exclamation')) {
                    showRow = false;
                }
            }

            // Foto filter
            if (fotoFilter) {
                const fotoCell = row.cells[1];
                const hasImage = fotoCell.querySelector('img') !== null;

                if (fotoFilter === 'ada' && !hasImage) {
                    showRow = false;
                } else if (fotoFilter === 'tidak' && hasImage) {
                    showRow = false;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
        modal.hide();
    }

    function resetFilter() {
        document.getElementById('filterForm').reset();
        const tableRows = document.querySelectorAll('#barangTable tbody tr');
        tableRows.forEach(row => {
            row.style.display = '';
        });

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
        modal.hide();
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endsection
