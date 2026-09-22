@extends('frontend.layouts.app')

@push('styles')
<style>
    .settings-nav .nav-link {
        color: #555e6d;
        font-weight: 600;
        padding: 0.85rem 1.25rem;
        border-radius: 10px;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.2s ease;
    }

    .settings-nav .nav-link:hover {
        background-color: #f4f6fa;
        color: #7638ff;
    }

    .settings-nav .nav-link.active {
        background-color: #7638ff;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(118, 56, 255, 0.25);
    }

    .card-settings {
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }

    .form-section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #2b3040;
        border-bottom: 2px solid #f0f2f5;
        padding-bottom: 0.75rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .media-preview-card {
        border: 2px dashed #dbe2ea;
        border-radius: 12px;
        padding: 16px;
        background: #f8fafc;
        text-align: center;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: all 0.2s ease;
    }

    .media-preview-card.has-image {
        border-style: solid;
        border-color: #7638ff;
        background: #ffffff;
    }

    .media-preview-card img {
        max-height: 80px;
        max-width: 100%;
        object-fit: contain;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .media-preview-card.dark-bg {
        background: #1e293b;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="content-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="card-title fw-bold text-dark mb-1">System Settings</h4>
                <p class="text-muted small mb-0">Configure application branding, logos, favicon, general company info, and localization</p>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fe fe-check-circle fs-5"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Left Sidebar Navigation Tabs -->
            <div class="col-lg-3 col-md-4 col-12">
                <div class="card card-settings bg-white p-3">
                    <div class="nav flex-column settings-nav" id="settings-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="tab-brand-link" data-bs-toggle="pill" data-bs-target="#tab-brand" type="button" role="tab">
                            <i class="fe fe-image"></i>
                            <span>Brand & Media</span>
                        </button>
                        <button class="nav-link" id="tab-general-link" data-bs-toggle="pill" data-bs-target="#tab-general" type="button" role="tab">
                            <i class="fe fe-sliders"></i>
                            <span>General Information</span>
                        </button>
                        <button class="nav-link" id="tab-localization-link" data-bs-toggle="pill" data-bs-target="#tab-localization" type="button" role="tab">
                            <i class="fe fe-globe"></i>
                            <span>Localization & Currency</span>
                        </button>
                    </div>
                </div>

                <!-- Save Button Card -->
                <div class="card card-settings bg-white p-3 mt-3">
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 shadow-sm d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="fe fe-check"></i>
                        <span>Save All Settings</span>
                    </button>
                </div>
            </div>

            <!-- Right Content Panels -->
            <div class="col-lg-9 col-md-8 col-12">
                <div class="tab-content" id="settings-tabContent">

                    <!-- TAB 1: Brand & Media Assets -->
                    <div class="tab-pane fade show active" id="tab-brand" role="tabpanel">
                        <div class="card card-settings bg-white">
                            <div class="card-body p-4">
                                <div class="form-section-title">
                                    <i class="fe fe-image text-primary"></i>
                                    <span>Brand Logos & Favicon</span>
                                </div>

                                <div class="row g-4">
                                    <!-- Main Navbar Logo (Light Theme) -->
                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">
                                            Main Navbar Logo (Default / Light)
                                        </label>
                                        <div class="media-preview-card mb-2 {{ !empty($settings['site_logo']) ? 'has-image' : '' }}" id="preview_box_site_logo">
                                            @if(!empty($settings['site_logo']) && file_exists(public_path($settings['site_logo'])))
                                                <img src="{{ asset($settings['site_logo']) }}" alt="Main Logo">
                                            @else
                                                <i class="fe fe-image fs-1 text-muted mb-2"></i>
                                                <span class="text-muted small">No custom logo uploaded</span>
                                            @endif
                                        </div>
                                        <input type="file" name="site_logo" class="form-control" accept="image/*" onchange="previewMedia(this, 'preview_box_site_logo')">
                                        <small class="text-muted fs-8 d-block mt-1">Displayed in top navigation bar. Transparent PNG/SVG recommended (Max 2MB).</small>
                                    </div>

                                    <!-- Main Navbar Dark Mode Logo -->
                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">
                                            Main Navbar White / Dark Mode Logo
                                        </label>
                                        <div class="media-preview-card dark-bg mb-2 {{ !empty($settings['site_logo_white']) ? 'has-image' : '' }}" id="preview_box_site_logo_white">
                                            @if(!empty($settings['site_logo_white']) && file_exists(public_path($settings['site_logo_white'])))
                                                <img src="{{ asset($settings['site_logo_white']) }}" alt="White Logo">
                                            @else
                                                <i class="fe fe-image fs-1 text-muted mb-2"></i>
                                                <span class="text-muted small">White logo version</span>
                                            @endif
                                        </div>
                                        <input type="file" name="site_logo_white" class="form-control" accept="image/*" onchange="previewMedia(this, 'preview_box_site_logo_white')">
                                        <small class="text-muted fs-8 d-block mt-1">Displayed in dark theme sidebar/header. Transparent white PNG/SVG (Max 2MB).</small>
                                    </div>

                                    <!-- Login Page Logo -->
                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">
                                            Login & Authentication Screen Logo
                                        </label>
                                        <div class="media-preview-card mb-2 {{ !empty($settings['login_logo']) ? 'has-image' : '' }}" id="preview_box_login_logo">
                                            @if(!empty($settings['login_logo']) && file_exists(public_path($settings['login_logo'])))
                                                <img src="{{ asset($settings['login_logo']) }}" alt="Login Logo">
                                            @else
                                                <i class="fe fe-lock fs-1 text-muted mb-2"></i>
                                                <span class="text-muted small">Uses Main Logo by default</span>
                                            @endif
                                        </div>
                                        <input type="file" name="login_logo" class="form-control" accept="image/*" onchange="previewMedia(this, 'preview_box_login_logo')">
                                        <small class="text-muted fs-8 d-block mt-1">Displayed centered above the login form at <code>/login</code> (Max 2MB).</small>
                                    </div>

                                    <!-- Browser Favicon -->
                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">
                                            Browser Tab Favicon
                                        </label>
                                        <div class="media-preview-card mb-2 {{ !empty($settings['favicon']) ? 'has-image' : '' }}" id="preview_box_favicon">
                                            @if(!empty($settings['favicon']) && file_exists(public_path($settings['favicon'])))
                                                <img src="{{ asset($settings['favicon']) }}" style="max-height: 48px; max-width: 48px;" alt="Favicon">
                                            @else
                                                <i class="fe fe-globe fs-1 text-muted mb-2"></i>
                                                <span class="text-muted small">No favicon uploaded</span>
                                            @endif
                                        </div>
                                        <input type="file" name="favicon" class="form-control" accept="image/x-icon,image/png,image/jpeg,image/svg+xml" onchange="previewMedia(this, 'preview_box_favicon')">
                                        <small class="text-muted fs-8 d-block mt-1">Square icon (32x32 or 64x64 px). <code>.ico</code>, <code>.png</code>, or <code>.jpg</code> (Max 1MB).</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: General Information -->
                    <div class="tab-pane fade" id="tab-general" role="tabpanel">
                        <div class="card card-settings bg-white">
                            <div class="card-body p-4">
                                <div class="form-section-title">
                                    <i class="fe fe-sliders text-primary"></i>
                                    <span>General Application Details</span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">Application / Site Name <span class="text-danger">*</span></label>
                                        <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings['site_name'] ?? 'Inoodex Inventory') }}" required>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">Application Tagline</label>
                                        <input type="text" name="site_tagline" class="form-control" value="{{ old('site_tagline', $settings['site_tagline'] ?? 'Enterprise ERP & Inventory Management') }}">
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">Official Support Email</label>
                                        <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $settings['contact_email'] ?? 'support@inoodex.com') }}">
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">Official Contact Phone</label>
                                        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $settings['contact_phone'] ?? '+880 1700-000000') }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-secondary">Office / Physical Address</label>
                                        <textarea name="address" class="form-control" rows="2">{{ old('address', $settings['address'] ?? 'Dhaka, Bangladesh') }}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-secondary">Footer Copyright Text</label>
                                        <input type="text" name="footer_text" class="form-control" value="{{ old('footer_text', $settings['footer_text'] ?? '© 2026 Inoodex Inventory. All rights reserved.') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Localization & Currency -->
                    <div class="tab-pane fade" id="tab-localization" role="tabpanel">
                        <div class="card card-settings bg-white">
                            <div class="card-body p-4">
                                <div class="form-section-title">
                                    <i class="fe fe-globe text-primary"></i>
                                    <span>Localization & Regional Formats</span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">Currency Symbol</label>
                                        <input type="text" name="currency_symbol" class="form-control font-monospace" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '৳') }}" placeholder="e.g. ৳, $, €">
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">Currency Code</label>
                                        <input type="text" name="currency_code" class="form-control font-monospace" value="{{ old('currency_code', $settings['currency_code'] ?? 'BDT') }}" placeholder="e.g. BDT, USD, EUR">
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">Standard Date Format</label>
                                        <select name="date_format" class="form-select">
                                            @php $currentDateFormat = old('date_format', $settings['date_format'] ?? 'd M, Y'); @endphp
                                            <option value="d M, Y" {{ $currentDateFormat == 'd M, Y' ? 'selected' : '' }}>d M, Y (e.g. 22 Sep, 2026)</option>
                                            <option value="d/m/Y" {{ $currentDateFormat == 'd/m/Y' ? 'selected' : '' }}>d/m/Y (e.g. 22/09/2026)</option>
                                            <option value="Y-m-d" {{ $currentDateFormat == 'Y-m-d' ? 'selected' : '' }}>Y-m-d (e.g. 2026-09-22)</option>
                                            <option value="m/d/Y" {{ $currentDateFormat == 'm/d/Y' ? 'selected' : '' }}>m/d/Y (e.g. 09/22/2026)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <label class="form-label fw-semibold small text-secondary">Default Timezone</label>
                                        <input type="text" name="timezone" class="form-control font-monospace" value="{{ old('timezone', $settings['timezone'] ?? 'Asia/Dhaka') }}" placeholder="e.g. Asia/Dhaka, UTC">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewMedia(input, boxId) {
        const box = document.getElementById(boxId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                box.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                box.classList.add('has-image');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
