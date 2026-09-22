@extends('frontend.layouts.app')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
    }

    .table-custom tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-custom tbody tr:hover {
        background-color: #fcfbff !important;
    }

    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.12) !important;
        color: #198754 !important;
        font-weight: 600;
    }

    .badge-soft-danger {
        background-color: rgba(220, 53, 69, 0.12) !important;
        color: #dc3545 !important;
        font-weight: 600;
    }

    .badge-soft-primary {
        background-color: rgba(118, 56, 255, 0.12) !important;
        color: #7638ff !important;
        font-weight: 600;
    }

    .search-box-custom input {
        border-radius: 8px;
    }

    .btn-action-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dbe2ea !important;
        border-radius: 8px !important;
        background-color: #ffffff !important;
        color: #555e6d !important;
        padding: 0;
        transition: all 0.2s ease;
    }

    .btn-action-icon:hover {
        background-color: #7638ff !important;
        color: #ffffff !important;
        border-color: #7638ff !important;
    }

    .table-custom th,
    .table-custom td {
        white-space: nowrap;
    }</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Company Details</h4>
                <p class="text-muted small mb-0">Manage legal entities, authorized signatories, contact information, and billing defaults</p>
            </div>
            <div>
                <a href="{{ route('company-details.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-plus-circle fs-6"></i>
                    <span>Add Company Details</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Summary Stats Bar -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-primary-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-briefcase fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Total Entities</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($companies->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-success-light text-success rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-check-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Active Companies</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($companies->where('is_active', true)->count()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 col-12">
            <div class="card stat-card bg-white shadow-sm rounded-3 h-100 mb-0">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg bg-info-light text-info rounded-circle me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="fe fe-star fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-normal mb-1">Default Billing Company</h6>
                        <h5 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 180px;">
                            {{ $companies->firstWhere('is_default', true)?->name ?? 'None' }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Summary Stats Bar -->

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <!-- Search & Filter Controls -->
        <div class="card-header bg-white py-3 border-bottom border-light">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="search-box-custom">
                        <input type="text" id="companySearchInput" class="form-control border-light-subtle" placeholder="Search company name, signatory, phone, email...">
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-7 text-md-end text-muted small">
                    Showing <span id="visibleCompanyCount" class="fw-bold text-dark">{{ $companies->count() }}</span> of {{ $companies->count() }} companies
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0" style="overflow: visible;">
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle mb-0" id="companyTable">
                    <thead class="bg-light text-secondary fs-7 text-uppercase">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Company Name</th>
                            <th>Signatory & Designation</th>
                            <th>Letterhead / Background</th>
                            <th>Contact Info</th>
                            <th>Status</th>
                            <th>Default</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($companies as $company)
                        @php
                        $searchString = strtolower(($company->name ?? '') . ' ' . ($company->signatory_name ?? '') . ' ' . ($company->signatory_designation ?? '') . ' ' . ($company->phone ?? '') . ' ' . ($company->email ?? ''));
                        @endphp
                        <tr class="company-row" data-search="{{ $searchString }}">
                            <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-bold text-dark d-block" title="{{ $company->name }}">
                                    {{ Str::limit($company->name, 35) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <span class="fw-semibold text-dark d-block">{{ $company->signatory_name }}</span>
                                    <small class="text-muted fs-7">{{ $company->signatory_designation }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($company->pad_image && file_exists(public_path($company->pad_image)))
                                        <a href="{{ asset($company->pad_image) }}" target="_blank" title="View Custom Invoice Pad">
                                            <img src="{{ asset($company->pad_image) }}" style="width: 32px; height: 42px; object-fit: cover; border-radius: 4px; border: 1px solid #dbe2ea;" alt="Pad">
                                        </a>
                                    @else
                                        <a href="{{ asset('assets/invoice/final_pad.png') }}" target="_blank" title="Default System Letterhead">
                                            <img src="{{ asset('assets/invoice/final_pad.png') }}" style="width: 32px; height: 42px; object-fit: cover; border-radius: 4px; border: 1px dashed #dbe2ea; opacity: 0.7;" alt="Default Pad">
                                        </a>
                                    @endif
                                    <div class="d-flex flex-column">
                                        <span class="badge {{ ($company->show_invoice_bg ?? true) ? 'bg-success-light text-success' : 'bg-light text-muted' }} py-0 px-2 rounded-1 fs-8 mb-1" style="font-size: 0.7rem;">
                                            Invoice BG: {{ ($company->show_invoice_bg ?? true) ? 'ON' : 'OFF' }}
                                        </span>
                                        <span class="badge {{ ($company->show_report_bg ?? true) ? 'bg-info-light text-info' : 'bg-light text-muted' }} py-0 px-2 rounded-1 fs-8" style="font-size: 0.7rem;">
                                            Report BG: {{ ($company->show_report_bg ?? true) ? 'ON' : 'OFF' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="d-block text-dark small"><i class="fe fe-phone me-1 text-muted"></i>{{ $company->phone ?? 'N/A' }}</span>
                                    <small class="text-muted"><i class="fe fe-mail me-1 text-muted"></i>{{ $company->email ?? 'N/A' }}</small>
                                </div>
                            </td>
                            <td>
                                @if ($company->is_active)
                                <span class="badge badge-soft-success px-3 py-2 rounded-pill fs-7">
                                    <i class="fe fe-check-circle me-1"></i> Active
                                </span>
                                @else
                                <span class="badge badge-soft-danger px-3 py-2 rounded-pill fs-7">
                                    <i class="fe fe-x-circle me-1"></i> Inactive
                                </span>
                                @endif
                            </td>
                            <td>
                                @if ($company->is_default)
                                <span class="badge badge-soft-primary px-3 py-2 rounded-pill fs-7">
                                    <i class="fe fe-star me-1"></i> Default
                                </span>
                                @else
                                <form action="{{ route('company-details.set-default', $company->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fs-7">
                                        Set Default
                                    </button>
                                </form>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <a href="javascript:void(0)" class="btn-action-icon shadow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                        @if(!$company->is_default)
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="javascript:void(0)"
                                                onclick="document.getElementById('setDefaultForm{{ $company->id }}').submit();">
                                                <i class="fe fe-star text-warning"></i>
                                                <span>Set as Default</span>
                                            </a>
                                            <form id="setDefaultForm{{ $company->id }}" action="{{ route('company-details.set-default', $company->id) }}" method="POST" class="d-none">
                                                @csrf
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider opacity-50"></li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('company-details.edit', $company->id) }}">
                                                <i class="fe fe-edit text-primary"></i>
                                                <span>Edit Company</span>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider opacity-50"></li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" href="javascript:void(0)"
                                                onclick="if (confirm('Are you sure you want to delete these company details?')) { document.getElementById('deleteCompany{{ $company->id }}').submit(); }">
                                                <i class="fe fe-trash-2 text-danger"></i>
                                                <span>Delete Company</span>
                                            </a>
                                            <form id="deleteCompany{{ $company->id }}" action="{{ route('company-details.destroy', $company->id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyStateRow">
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="avatar avatar-xl bg-primary-light text-primary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                        <i class="fe fe-briefcase fs-1"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">No Company Details Found</h5>
                                    <p class="text-muted small mb-3">Create company details to manage legal entities and signatories for invoices & bills</p>
                                    <a href="{{ route('company-details.create') }}" class="btn btn-primary btn-sm px-3 rounded-2">
                                        Add Company Details
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('companySearchInput');
        const rows = document.querySelectorAll('.company-row');
        const visibleCountSpan = document.getElementById('visibleCompanyCount');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                rows.forEach(row => {
                    const rowSearchText = row.dataset.search || '';
                    if (query === '' || rowSearchText.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (visibleCountSpan) {
                    visibleCountSpan.textContent = visibleCount;
                }
            });
        }
    });
</script>
@endpush
@endsection
