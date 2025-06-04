@extends('layouts.app')

@section('title', 'Data Pengembalian')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">
                <i class="fas fa-undo text-primary me-2"></i>
                Data Pengembalian
            </h1>
            <p class="text-muted mb-0">Kelola dan pantau pengembalian barang</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" onclick="refreshData()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
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
                                <i class="fas fa-undo text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Pengembalian</div>
                            <div class="fs-5 fw-bold">{{ $pengembalians->count() }}</div>
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
                                <i class="fas fa-clock text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Pending</div>
                            <div class="fs-5 fw-bold">{{ $pengembalians->where('status_pengembalian', 'pending')->count() }}</div>
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
                                <i class="fas fa-check text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Completed</div>
                            <div class="fs-5 fw-bold">{{ $pengembalians->where('status_pengembalian', 'completed')->count() }}</div>
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
                            <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Damaged</div>
                            <div class="fs-5 fw-bold">{{ $pengembalians->where('status_pengembalian', 'damaged')->count() }}</div>
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
                        Daftar Pengembalian
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari pengembalian..." id="searchInput">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="pengembalianTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">#</th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-user me-1"></i>Peminjam
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-box me-1"></i>Barang
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-calendar me-1"></i>Tanggal Kembali
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-sort-numeric-up me-1"></i>Jumlah
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-info-circle me-1"></i>Status
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-money-bill me-1"></i>Denda
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted text-center">
                                <i class="fas fa-cogs me-1"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengembalians as $pengembalian)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <span class="badge bg-light text-dark rounded-pill">{{ $loop->iteration }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $pengembalian->peminjaman->nama_peminjam ?? 'N/A' }}</div>
                                            <small class="text-muted">ID: {{ $pengembalian->peminjaman_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-box text-info"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $pengembalian->peminjaman->barang->nama_barang ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $pengembalian->peminjaman->barang->kategori->nama_kategori ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-muted small">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($pengembalian->tanggal_pengembalian)->format('d M Y') }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ \Carbon\Carbon::parse($pengembalian->tanggal_pengembalian)->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                        <i class="fas fa-sort-numeric-up me-1"></i>{{ $pengembalian->jumlah_dikembalikan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($pengembalian->status_pengembalian == 'pending')
                                        <span class="badge bg-warning bg-opacity-15 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @elseif($pengembalian->status_pengembalian == 'completed')
                                        <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="fas fa-check me-1"></i>Completed
                                        </span>
                                    @elseif($pengembalian->status_pengembalian == 'damaged')
                                        <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Damaged
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($pengembalian->denda > 0)
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">
                                            <i class="fas fa-money-bill me-1"></i>Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                            <i class="fas fa-check me-1"></i>Tidak ada denda
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($pengembalian->status_pengembalian == 'pending')
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('pengembalian.approve', $pengembalian->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm rounded-start"
                                                        onclick="return confirm('Apakah Anda yakin ingin menyetujui pengembalian ini?')"
                                                        data-bs-toggle="tooltip" title="Setujui Pengembalian">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('pengembalian.reject', $pengembalian->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm rounded-end"
                                                        onclick="return confirm('Apakah Anda yakin ingin menolak pengembalian ini?')"
                                                        data-bs-toggle="tooltip" title="Tolak Pengembalian">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($pengembalian->status_pengembalian == 'damaged')
                                        <a href="{{ route('admin.pengembalian.mark_damaged', $pengembalian->id) }}"
                                           class="btn btn-warning btn-sm"
                                           data-bs-toggle="tooltip" title="Lihat Detail Kerusakan">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    @else
                                        <span class="text-muted small">
                                            <i class="fas fa-info-circle me-1"></i>
                                            {{ ucfirst($pengembalian->status_pengembalian) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="mb-3">
                                            <i class="fas fa-undo text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">Tidak ada data pengembalian</h5>
                                        <p class="text-muted mb-0">Belum ada pengembalian yang terdaftar dalam sistem.</p>
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

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="fas fa-filter me-2"></i>Filter Pengembalian
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label for="statusFilter" class="form-label">Status Pengembalian</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dendaFilter" class="form-label">Status Denda</label>
                        <select class="form-select" id="dendaFilter">
                            <option value="">Semua</option>
                            <option value="ada">Ada Denda</option>
                            <option value="tidak">Tidak Ada Denda</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggalFilter" class="form-label">Tanggal Pengembalian</label>
                        <div class="row">
                            <div class="col">
                                <input type="date" class="form-control" id="tanggalMulai">
                            </div>
                            <div class="col">
                                <input type="date" class="form-control" id="tanggalAkhir">
                            </div>
                        </div>
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
</style>

<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#pengembalianTable tbody tr');

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
        const statusFilter = document.getElementById('statusFilter').value;
        const dendaFilter = document.getElementById('dendaFilter').value;
        const tanggalMulai = document.getElementById('tanggalMulai').value;
        const tanggalAkhir = document.getElementById('tanggalAkhir').value;
        const tableRows = document.querySelectorAll('#pengembalianTable tbody tr');

        tableRows.forEach(row => {
            let showRow = true;

            // Status filter
            if (statusFilter) {
                const statusCell = row.cells[5];
                if (!statusCell || !statusCell.textContent.toLowerCase().includes(statusFilter)) {
                    showRow = false;
                }
            }

            // Denda filter
            if (dendaFilter) {
                const dendaCell = row.cells[6];
                const dendaText = dendaCell.textContent.toLowerCase();

                if (dendaFilter === 'ada' && dendaText.includes('tidak ada')) {
                    showRow = false;
                } else if (dendaFilter === 'tidak' && !dendaText.includes('tidak ada')) {
                    showRow = false;
                }
            }

            // Tanggal filter
            if (tanggalMulai || tanggalAkhir) {
                const tanggalCell = row.cells[3];
                const tanggalText = tanggalCell.textContent;
                // Simple date comparison - you might want to improve this
                if (tanggalMulai && tanggalText < tanggalMulai) {
                    showRow = false;
                }
                if (tanggalAkhir && tanggalText > tanggalAkhir) {
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
        const tableRows = document.querySelectorAll('#pengembalianTable tbody tr');
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
