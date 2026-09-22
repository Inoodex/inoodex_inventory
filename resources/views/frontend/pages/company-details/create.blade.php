@extends('frontend.layouts.app')

@push('styles')
<style>
    .form-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2b3040;
        border-bottom: 2px solid #f0f2f5;
        padding-bottom: 0.6rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .preview-box {
        border: 2px dashed #dbe2ea;
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        background: #f8fafc;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .preview-box img {
        max-height: 100px;
        max-width: 100%;
        object-fit: contain;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .preview-box.has-image {
        border-style: solid;
        border-color: #7638ff;
        background: #ffffff;
    }
    .card-custom {
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }
    .toggle-card {
        background-color: #f8fafc;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1rem;
        transition: border-color 0.2s;
    }
    .toggle-card:hover {
        border-color: #7638ff;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">Create Company Details</h4>
                <p class="text-muted small mb-0">Add a new legal company profile, authorized signatory, letterhead pad, and document settings</p>
            </div>
            <div>
                <a href="{{ route('company-details.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fe fe-arrow-left"></i>
                    <span>Back to Company Details</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fe fe-alert-triangle fs-5"></i>
                <strong class="fs-6">Please resolve the following errors:</strong>
            </div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('company-details.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Basic Details & Signatory -->
            <div class="col-lg-7 col-12">
                <div class="card card-custom bg-white mb-4">
                    <div class="card-body p-4">
                        <div class="form-section-title">
                            <i class="fe fe-briefcase text-primary"></i>
                            <span>Company Basic Information</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-secondary">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Inoodex Solutions Ltd" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold small text-secondary">Phone Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="e.g. +880 1700-000000">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold small text-secondary">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="e.g. info@inoodex.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small text-secondary">Website URL</label>
                                <input type="text" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website') }}" placeholder="e.g. https://inoodex.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small text-secondary">Office Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Full registered company office address...">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-custom bg-white mb-4">
                    <div class="card-body p-4">
                        <div class="form-section-title">
                            <i class="fe fe-user-check text-primary"></i>
                            <span>Authorized Signatory</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold small text-secondary">Signatory Name <span class="text-danger">*</span></label>
                                <input type="text" name="signatory_name" class="form-control @error('signatory_name') is-invalid @enderror" value="{{ old('signatory_name') }}" placeholder="e.g. John Doe" required>
                                @error('signatory_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold small text-secondary">Signatory Designation <span class="text-danger">*</span></label>
                                <input type="text" name="signatory_designation" class="form-control @error('signatory_designation') is-invalid @enderror" value="{{ old('signatory_designation') }}" placeholder="e.g. Managing Director" required>
                                @error('signatory_designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold small text-secondary">Signature Image</label>
                                <input type="file" name="signature_image" id="input_signature" class="form-control mb-2" accept="image/*" onchange="previewImage(this, 'preview_signature')">
                                <div id="preview_signature" class="preview-box">
                                    <i class="fe fe-edit-3 fs-3 text-muted mb-1"></i>
                                    <span class="text-muted small">Signature Preview</span>
                                </div>
                                <small class="text-muted fs-8">Transparent PNG recommended (Max 2MB)</small>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold small text-secondary">Company Seal / Stamp</label>
                                <input type="file" name="seal_image" id="input_seal" class="form-control mb-2" accept="image/*" onchange="previewImage(this, 'preview_seal')">
                                <div id="preview_seal" class="preview-box">
                                    <i class="fe fe-shield fs-3 text-muted mb-1"></i>
                                    <span class="text-muted small">Seal Preview</span>
                                </div>
                                <small class="text-muted fs-8">Transparent PNG recommended (Max 2MB)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Letterhead Pads & Toggles -->
            <div class="col-lg-5 col-12">
                <div class="card card-custom bg-white mb-4">
                    <div class="card-body p-4">
                        <div class="form-section-title">
                            <i class="fe fe-image text-primary"></i>
                            <span>Letterhead & Background Pads</span>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-secondary">Invoice / Commercial Letterhead Pad</label>
                            <input type="file" name="pad_image" id="input_pad" class="form-control mb-2" accept="image/*" onchange="previewImage(this, 'preview_pad')">
                            <div id="preview_pad" class="preview-box">
                                <i class="fe fe-file-text fs-3 text-muted mb-1"></i>
                                <span class="text-muted small">Invoice Pad Preview</span>
                            </div>
                            <small class="text-muted fs-8 d-block mt-1">Used for Invoices, Bills, Challans, and Quotations. A4 portrait ratio (2480x3508 px) recommended.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Report Background Pad</label>
                            <input type="file" name="report_bg_image" id="input_report_bg" class="form-control mb-2" accept="image/*" onchange="previewImage(this, 'preview_report_bg')">
                            <div id="preview_report_bg" class="preview-box">
                                <i class="fe fe-bar-chart-2 fs-3 text-muted mb-1"></i>
                                <span class="text-muted small">Report Pad Preview</span>
                            </div>
                            <small class="text-muted fs-8 d-block mt-1">Optional separate background used for Financial Statements and Operational Reports.</small>
                        </div>
                    </div>
                </div>

                <div class="card card-custom bg-white mb-4">
                    <div class="card-body p-4">
                        <div class="form-section-title">
                            <i class="fe fe-sliders text-primary"></i>
                            <span>Document & Status Settings</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="toggle-card">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="show_invoice_bg" id="show_invoice_bg" value="1" {{ old('show_invoice_bg', '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark ms-1" for="show_invoice_bg">
                                            Show Background on Invoices / Bills / Challans
                                        </label>
                                    </div>
                                    <small class="text-muted d-block ms-5 mt-1">Turn off if printing directly on physical pre-printed letterhead stationary.</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="toggle-card">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="show_report_bg" id="show_report_bg" value="1" {{ old('show_report_bg', '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark ms-1" for="show_report_bg">
                                            Show Background on Reports
                                        </label>
                                    </div>
                                    <small class="text-muted d-block ms-5 mt-1">Turn off for clean plain-paper financial report printing.</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="toggle-card">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark ms-1" for="is_default">
                                            Set as Default Billing Company
                                        </label>
                                    </div>
                                    <small class="text-muted d-block ms-5 mt-1">Automatically used across all newly generated invoices, bills, and system headers.</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="toggle-card">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark ms-1" for="is_active">
                                            Active Status
                                        </label>
                                    </div>
                                    <small class="text-muted d-block ms-5 mt-1">Only active companies appear in billing and selection dropdowns.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Area -->
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('company-details.index') }}" class="btn btn-light px-4 py-2 rounded-3 text-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="fe fe-check"></i>
                        <span>Save Company Details</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input, previewId) {
        const previewBox = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewBox.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                previewBox.classList.add('has-image');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
