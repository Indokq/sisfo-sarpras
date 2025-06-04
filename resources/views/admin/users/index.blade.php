@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">
                <i class="fas fa-users text-primary me-2"></i>
                User Management
            </h1>
            <p class="text-muted mb-0">Kelola pengguna dan hak akses sistem</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" onclick="refreshData()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah User
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle text-danger me-2 fs-5"></i>
                <div>
                    <strong>Error!</strong> {{ session('error') }}
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
                                <i class="fas fa-users text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Total Users</div>
                            <div class="fs-5 fw-bold">{{ $users->count() }}</div>
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
                                <i class="fas fa-user-shield text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Admin Users</div>
                            <div class="fs-5 fw-bold">{{ $users->filter(function($user) { return $user->roles->first() && $user->roles->first()->name === 'admin'; })->count() }}</div>
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
                                <i class="fas fa-user text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">Regular Users</div>
                            <div class="fs-5 fw-bold">{{ $users->filter(function($user) { return $user->roles->first() && $user->roles->first()->name === 'user'; })->count() }}</div>
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
                                <i class="fas fa-user-times text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">No Role</div>
                            <div class="fs-5 fw-bold">{{ $users->filter(function($user) { return !$user->roles->first(); })->count() }}</div>
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
                        Daftar Pengguna
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari pengguna..." id="searchInput">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="usersTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">#</th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-user me-1"></i>Nama
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-envelope me-1"></i>Email
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-user-tag me-1"></i>Role
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted">
                                <i class="fas fa-calendar me-1"></i>Bergabung
                            </th>
                            <th class="border-0 px-4 py-3 fw-semibold text-muted text-center">
                                <i class="fas fa-cogs me-1"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
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
                                            <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                            <small class="text-muted">ID: {{ $user->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-muted me-2"></i>
                                        <span>{{ $user->email }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($user->roles->first())
                                        @if($user->roles->first()->name === 'admin')
                                            <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">
                                                <i class="fas fa-user-shield me-1"></i>Admin
                                            </span>
                                        @elseif($user->roles->first()->name === 'user')
                                            <span class="badge bg-primary bg-opacity-15 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">
                                                <i class="fas fa-user me-1"></i>User
                                            </span>
                                        @else
                                            <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill">
                                                <i class="fas fa-user-tag me-1"></i>{{ ucfirst($user->roles->first()->name) }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                            <i class="fas fa-user-times me-1"></i>No Role
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-muted small">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $user->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $user->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="btn btn-warning btn-sm rounded-start"
                                           data-bs-toggle="tooltip" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm rounded-end"
                                                        onclick="return confirm('Yakin hapus user ini? Data ini akan hilang permanen!')"
                                                        data-bs-toggle="tooltip" title="Hapus User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="btn btn-secondary btn-sm rounded-end disabled"
                                                  data-bs-toggle="tooltip" title="Tidak bisa hapus diri sendiri">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="mb-3">
                                            <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">Tidak ada pengguna</h5>
                                        <p class="text-muted mb-3">Belum ada pengguna yang terdaftar dalam sistem.</p>
                                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i> Tambah User Pertama
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

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="fas fa-filter me-2"></i>Filter Users
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label for="roleFilter" class="form-label">Role</label>
                        <select class="form-select" id="roleFilter">
                            <option value="">Semua Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                            <option value="no-role">No Role</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="emailFilter" class="form-label">Domain Email</label>
                        <input type="text" class="form-control" id="emailFilter" placeholder="Contoh: gmail.com">
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
        const tableRows = document.querySelectorAll('#usersTable tbody tr');

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
        const roleFilter = document.getElementById('roleFilter').value;
        const emailFilter = document.getElementById('emailFilter').value.toLowerCase();
        const tableRows = document.querySelectorAll('#usersTable tbody tr');

        tableRows.forEach(row => {
            let showRow = true;

            // Role filter
            if (roleFilter) {
                const roleCell = row.cells[3];
                const roleText = roleCell.textContent.toLowerCase();

                if (roleFilter === 'admin' && !roleText.includes('admin')) {
                    showRow = false;
                } else if (roleFilter === 'user' && !roleText.includes('user') || roleText.includes('admin')) {
                    showRow = false;
                } else if (roleFilter === 'no-role' && !roleText.includes('no role')) {
                    showRow = false;
                }
            }

            // Email filter
            if (emailFilter) {
                const emailCell = row.cells[2];
                if (!emailCell || !emailCell.textContent.toLowerCase().includes(emailFilter)) {
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
        const tableRows = document.querySelectorAll('#usersTable tbody tr');
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