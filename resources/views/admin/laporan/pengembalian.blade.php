@extends('layouts.app')

@section('title', 'Laporan Pengembalian')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">
                <i class="fas fa-chart-line text-primary me-2"></i>
                Laporan Pengembalian
            </h1>
            <p class="text-muted mb-0">Laporan lengkap data pengembalian barang</p>
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
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-money-bill text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Denda</div>
                            <div class="fs-5 fw-bold">Rp {{ number_format($pengembalians->sum('denda'), 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Header for Print -->
    <div class="print-header d-none">
        <div class="text-center mb-4">
            <h2 class="fw-bold">LAPORAN PENGEMBALIAN BARANG</h2>
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
                        Data Laporan Pengembalian
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
                                <i class="fas fa-sort-numeric-up me-1"></i>Jumlah Dikembalikan
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-calendar me-1"></i>Tanggal Pengembalian
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-money-bill me-1"></i>Denda
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-comment me-1"></i>Keterangan
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-info-circle me-1"></i>Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengembalians as $index => $pengembalian)
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
                                            <div class="fw-semibold text-dark">{{ $pengembalian->peminjaman->nama_peminjam ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $pengembalian->peminjaman->user->email ?? 'N/A' }}</small>
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
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                        <i class="fas fa-sort-numeric-up me-1"></i>{{ $pengembalian->jumlah_dikembalikan }}
                                    </span>
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
                                <td class="px-4 py-3">
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $pengembalian->keterangan ?? '-' }}">
                                        {{ $pengembalian->keterangan ?? '-' }}
                                    </div>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="mb-3">
                                            <i class="fas fa-chart-line text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">Tidak ada data pengembalian</h5>
                                        <p class="text-muted mb-0">Belum ada data pengembalian untuk ditampilkan dalam laporan.</p>
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
                <p class="mb-0"><strong>Total Data:</strong> {{ $pengembalians->count() }} pengembalian</p>
                <p class="mb-0"><strong>Total Denda:</strong> Rp {{ number_format($pengembalians->sum('denda'), 0, ',', '.') }}</p>
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
                    <i class="fas fa-filter me-2"></i>Filter Laporan Pengembalian
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
        const dendaFilter = document.getElementById('dendaFilter').value;
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

            // Denda filter
            if (dendaFilter) {
                const dendaCell = row.cells[5];
                const dendaText = dendaCell.textContent.toLowerCase();

                if (dendaFilter === 'ada' && dendaText.includes('tidak ada')) {
                    showRow = false;
                } else if (dendaFilter === 'tidak' && !dendaText.includes('tidak ada')) {
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