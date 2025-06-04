@extends('layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">
                <i class="fas fa-chart-bar text-primary me-2"></i>
                Laporan Peminjaman
            </h1>
            <p class="text-muted mb-0">Laporan lengkap data peminjaman barang</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="exportToExcel()">
                <i class="fas fa-file-excel me-1"></i> Excel
            </button>
            <button class="btn btn-outline-danger btn-sm" onclick="exportToPDF()">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </button>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-hand-holding text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Peminjaman</div>
                            <div class="fs-5 fw-bold">{{ $peminjamans->count() }}</div>
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
                            <div class="text-muted small">Approved</div>
                            <div class="fs-5 fw-bold">{{ $peminjamans->where('status', 'approved')->count() }}</div>
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
                                <i class="fas fa-undo text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Returned</div>
                            <div class="fs-5 fw-bold">{{ $peminjamans->where('status', 'returned')->count() }}</div>
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
                                <i class="fas fa-times text-danger fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Rejected</div>
                            <div class="fs-5 fw-bold">{{ $peminjamans->where('status', 'rejected')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Header for Print -->
    <div class="print-header d-none">
        <div class="text-center mb-4">
            <h2 class="fw-bold">LAPORAN PEMINJAMAN BARANG</h2>
            <h4>SISTEM INFORMASI SARANA PRASARANA</h4>
            <p class="mb-0">Tanggal Cetak: {{ now()->format('d F Y') }}</p>
            <hr class="my-3">
        </div>
    </div>

    <!-- Main Report Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 no-print">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-table text-primary me-2"></i>
                        Data Laporan Peminjaman
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari data..." id="searchInput">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="laporanTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">#</th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-user me-1"></i>Nama Peminjam
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-box me-1"></i>Barang
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-sort-numeric-up me-1"></i>Jumlah
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-calendar-plus me-1"></i>Tanggal Pinjam
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-calendar-minus me-1"></i>Tanggal Kembali
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-comment me-1"></i>Alasan
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-info-circle me-1"></i>Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($peminjamans as $index => $peminjaman)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <span class="badge bg-light text-dark rounded-pill">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $peminjaman->nama_peminjam }}</div>
                                            <small class="text-muted">{{ $peminjaman->user->email ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-box text-info"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $peminjaman->barang->nama_barang ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $peminjaman->barang->kategori->nama_kategori ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                        <i class="fas fa-sort-numeric-up me-1"></i>{{ $peminjaman->jumlah }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-muted small">
                                        <i class="fas fa-calendar-plus me-1"></i>
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-muted small">
                                        <i class="fas fa-calendar-minus me-1"></i>
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d M Y') }}
                                    </div>
                                    <div class="text-muted small">
                                        @php
                                            $days = \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->diffInDays(\Carbon\Carbon::parse($peminjaman->tanggal_kembali));
                                        @endphp
                                        {{ $days }} hari
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $peminjaman->alasan_meminjam }}">
                                        {{ $peminjaman->alasan_meminjam }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($peminjaman->status == 'pending')
                                        <span class="badge bg-warning bg-opacity-15 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @elseif($peminjaman->status == 'approved')
                                        <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="fas fa-check me-1"></i>Approved
                                        </span>
                                    @elseif($peminjaman->status == 'rejected')
                                        <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="fas fa-times me-1"></i>Rejected
                                        </span>
                                    @elseif($peminjaman->status == 'returned')
                                        <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill">
                                            <i class="fas fa-undo me-1"></i>Returned
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="mb-3">
                                            <i class="fas fa-chart-bar text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">Tidak ada data peminjaman</h5>
                                        <p class="text-muted mb-0">Belum ada data peminjaman untuk ditampilkan dalam laporan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Print Footer -->
    <div class="print-footer d-none mt-4">
        <div class="row">
            <div class="col-6">
                <p class="mb-0"><strong>Total Data:</strong> {{ $peminjamans->count() }} peminjaman</p>
            </div>
            <div class="col-6 text-end">
                <p class="mb-0">Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
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
                    <i class="fas fa-filter me-2"></i>Filter Laporan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="returned">Returned</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggalFilter" class="form-label">Periode Tanggal</label>
                        <div class="row">
                            <div class="col">
                                <input type="date" class="form-control" id="tanggalMulai" placeholder="Tanggal Mulai">
                            </div>
                            <div class="col">
                                <input type="date" class="form-control" id="tanggalAkhir" placeholder="Tanggal Akhir">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="peminjamFilter" class="form-label">Nama Peminjam</label>
                        <input type="text" class="form-control" id="peminjamFilter" placeholder="Cari nama peminjam...">
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

    .card {
        transition: box-shadow 0.15s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    @media print {
        .no-print,
        .topbar,
        .sidebar,
        .footer {
            display: none !important;
        }

        .print-header,
        .print-footer {
            display: block !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-body {
            padding: 0 !important;
        }

        body {
            padding: 0;
            margin: 0;
        }

        .container-fluid {
            width: 100%;
            padding: 0;
        }

        table {
            width: 100%;
            font-size: 12px;
        }

        .badge {
            background-color: #f8f9fa !important;
            color: #000 !important;
            border: 1px solid #dee2e6 !important;
        }
    }
</style>

<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#laporanTable tbody tr');

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Filter functions
    function applyFilter() {
        const statusFilter = document.getElementById('statusFilter').value;
        const tanggalMulai = document.getElementById('tanggalMulai').value;
        const tanggalAkhir = document.getElementById('tanggalAkhir').value;
        const peminjamFilter = document.getElementById('peminjamFilter').value.toLowerCase();
        const tableRows = document.querySelectorAll('#laporanTable tbody tr');

        tableRows.forEach(row => {
            let showRow = true;

            // Status filter
            if (statusFilter) {
                const statusCell = row.cells[7];
                if (!statusCell || !statusCell.textContent.toLowerCase().includes(statusFilter)) {
                    showRow = false;
                }
            }

            // Peminjam filter
            if (peminjamFilter) {
                const peminjamCell = row.cells[1];
                if (!peminjamCell || !peminjamCell.textContent.toLowerCase().includes(peminjamFilter)) {
                    showRow = false;
                }
            }

            // Date range filter (simplified)
            if (tanggalMulai || tanggalAkhir) {
                const tanggalCell = row.cells[4];
                // You might want to implement proper date comparison here
            }

            row.style.display = showRow ? '' : 'none';
        });

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
        modal.hide();
    }

    function resetFilter() {
        document.getElementById('filterForm').reset();
        const tableRows = document.querySelectorAll('#laporanTable tbody tr');
        tableRows.forEach(row => {
            row.style.display = '';
        });

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
        modal.hide();
    }

    // Export functions (placeholder - you'll need to implement these)
    function exportToExcel() {
        alert('Fitur export Excel akan segera tersedia!');
    }

    function exportToPDF() {
        alert('Fitur export PDF akan segera tersedia!');
    }
</script>
@endsection