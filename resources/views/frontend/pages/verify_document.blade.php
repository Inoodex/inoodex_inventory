<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $docData['verified'] ? 'Document Verified - ' . $docData['reference'] : 'Document Verification Failed' }} | {{ getSetting('site_name', 'Inoodex Inventory') }}</title>
    <link rel="icon" href="{{ asset(getSetting('favicon', 'assets/img/favicon.png')) }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css') }}">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg-page: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 24px 12px;
        }

        .verify-container {
            max-width: 680px;
            margin: 0 auto;
            width: 100%;
        }

        .verify-card {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .verify-header {
            padding: 32px 28px 24px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .badge-verified {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: rgba(16, 185, 129, 0.12);
            color: var(--success);
            padding: 8px 18px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 16px;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .badge-failed {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: rgba(239, 68, 68, 0.12);
            color: var(--danger);
            padding: 8px 18px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 16px;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        .status-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pill-paid {
            background-color: rgba(16, 185, 129, 0.15);
            color: #059669;
        }

        .status-pill-partial {
            background-color: rgba(245, 158, 11, 0.15);
            color: #d97706;
        }

        .status-pill-pending {
            background-color: rgba(239, 68, 68, 0.15);
            color: #dc2626;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            padding: 24px 28px;
            background: #fbfcfe;
            border-bottom: 1px solid var(--border-color);
        }

        @media (max-width: 576px) {
            .info-grid {
                grid-template-columns: 1fr;
                padding: 18px;
            }
            .verify-header {
                padding: 24px 18px 18px;
            }
            .content-section {
                padding: 18px !important;
            }
        }

        .info-item .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-item .value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
        }

        .content-section {
            padding: 24px 28px;
        }

        .section-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .items-list-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .items-list-table th {
            text-align: left;
            padding: 10px 12px;
            background: #f1f5f9;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            border-radius: 6px;
        }

        .items-list-table td {
            padding: 12px 12px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
        }

        .financial-summary {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid var(--border-color);
            margin-top: 16px;
        }

        .financial-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
            color: var(--text-muted);
        }

        .financial-row.total-row {
            border-top: 1px dashed var(--border-color);
            margin-top: 8px;
            padding-top: 10px;
            font-size: 15px;
            font-weight: 800;
            color: var(--text-main);
        }

        .seal-signature-bar {
            text-align: center;
            padding: 20px 28px;
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            font-size: 11px;
            color: var(--text-muted);
        }

        .verification-hash {
            font-family: monospace;
            font-size: 11px;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
            color: #475569;
            display: inline-block;
            margin-top: 6px;
            word-break: break-all;
        }
    </style>
</head>
<body>

<div class="verify-container">
    <!-- Brand Title -->
    <div class="text-center mb-4">
        @php
            $logo = getSetting('site_logo', 'assets/img/logo.png');
        @endphp
        @if(file_exists(public_path($logo)))
            <img src="{{ asset($logo) }}" alt="Logo" style="max-height: 44px; margin-bottom: 8px;">
        @else
            <h3 class="fw-bold text-dark mb-0">{{ getSetting('site_name', 'Inoodex Inventory') }}</h3>
        @endif
        <div class="small text-muted">Official Document Verification Portal</div>
    </div>

    <!-- Main Card -->
    <div class="verify-card">
        @if($docData['verified'])
            <!-- Verified Header -->
            <div class="verify-header">
                <div class="badge-verified">
                    <i class="fas fa-check-circle fs-5"></i>
                    <span>Authentic &amp; Verified Document</span>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $docData['type'] }}</h4>
                <div class="text-primary fw-bold fs-5">{{ $docData['reference'] }}</div>
                <p class="text-muted small mb-0 mt-1">Issued by {{ $docData['issuer'] }}</p>
            </div>

            <!-- Meta Grid -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Issue Date</div>
                    <div class="value">{{ $docData['date'] ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Status</div>
                    <div class="value">
                        @php
                            $st = strtolower($docData['status'] ?? '');
                            $pillClass = str_contains($st, 'paid') || str_contains($st, 'completed') || str_contains($st, 'delivered') ? 'status-pill-paid' : (str_contains($st, 'partial') ? 'status-pill-partial' : 'status-pill-pending');
                        @endphp
                        <span class="status-pill {{ $pillClass }}">{{ $docData['status'] }}</span>
                    </div>
                </div>
                @if(!empty($docData['customer']))
                    <div class="info-item">
                        <div class="label">Recipient / Customer</div>
                        <div class="value">{{ $docData['customer']['name'] ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Contact Info</div>
                        <div class="value">{{ $docData['customer']['phone'] ?? 'N/A' }}</div>
                    </div>
                @endif
            </div>

            <!-- Items Breakdown -->
            @if(!empty($docData['items']))
                <div class="content-section">
                    <div class="section-title">
                        <i class="fas fa-list-check text-primary"></i>
                        <span>Document Items / Scope</span>
                    </div>

                    <div class="table-responsive">
                        <table class="items-list-table">
                            <thead>
                                <tr>
                                    <th>Item Details</th>
                                    <th class="text-center" style="width: 80px;">Qty</th>
                                    @if(isset($docData['items'][0]['unit_price']) && $docData['items'][0]['unit_price'] !== null)
                                        <th class="text-end" style="width: 120px;">Price</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($docData['items'] as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $item['name'] }}</div>
                                            @if(!empty($item['model']))
                                                <div class="small text-muted">Model: {{ $item['model'] }}</div>
                                            @endif
                                            @if(!empty($item['serial']))
                                                <div class="small text-primary">Serial: {{ $item['serial'] }}</div>
                                            @endif
                                        </td>
                                        <td class="text-center fw-semibold">
                                            {{ $item['quantity'] }} {{ $item['unit'] ?? '' }}
                                        </td>
                                        @if(isset($item['unit_price']) && $item['unit_price'] !== null)
                                            <td class="text-end fw-semibold">
                                                {{ number_format($item['total_price'] ?? ($item['unit_price'] * $item['quantity']), 2) }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Financial Summary if exists -->
                    @if(!empty($docData['financials']))
                        <div class="financial-summary">
                            @if(isset($docData['financials']['subtotal']))
                                <div class="financial-row">
                                    <span>Subtotal:</span>
                                    <span>{{ number_format($docData['financials']['subtotal'], 2) }}</span>
                                </div>
                            @endif
                            @if(!empty($docData['financials']['tax']))
                                <div class="financial-row">
                                    <span>Tax / VAT:</span>
                                    <span>{{ number_format($docData['financials']['tax'], 2) }}</span>
                                </div>
                            @endif
                            @if(!empty($docData['financials']['discount']))
                                <div class="financial-row text-success">
                                    <span>Discount:</span>
                                    <span>-{{ number_format($docData['financials']['discount'], 2) }}</span>
                                </div>
                            @endif
                            @if(isset($docData['financials']['payable']) || isset($docData['financials']['total']))
                                <div class="financial-row total-row">
                                    <span>Total Payable:</span>
                                    <span class="text-primary">{{ number_format($docData['financials']['payable'] ?? $docData['financials']['total'], 2) }}</span>
                                </div>
                            @endif
                            @if(isset($docData['financials']['paid']))
                                <div class="financial-row pt-2">
                                    <span>Paid Amount:</span>
                                    <span class="text-success fw-bold">{{ number_format($docData['financials']['paid'], 2) }}</span>
                                </div>
                            @endif
                            @if(isset($docData['financials']['due']) && $docData['financials']['due'] > 0)
                                <div class="financial-row">
                                    <span>Due Balance:</span>
                                    <span class="text-danger fw-bold">{{ number_format($docData['financials']['due'], 2) }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            <!-- Seal & Digital Verification Footer -->
            <div class="seal-signature-bar">
                <i class="fas fa-shield-halved text-success me-1"></i>
                Digitally verified from the database registry of <strong>{{ $docData['issuer'] }}</strong>.
                <div class="mt-2">
                    <span class="verification-hash">SHA256: {{ hash('sha256', $type . ':' . $code . ':' . config('app.key')) }}</span>
                </div>
            </div>

        @else
            <!-- Verification Failed Screen -->
            <div class="verify-header py-5">
                <div class="badge-failed">
                    <i class="fas fa-triangle-exclamation fs-5"></i>
                    <span>Document Not Found</span>
                </div>
                <h4 class="fw-bold text-dark mt-2">Verification Failed</h4>
                <p class="text-muted mt-2 px-4">
                    We could not find any official document matching reference <strong>"{{ $code }}"</strong> under category <strong>"{{ ucfirst($type) }}"</strong> in our system registry.
                </p>
                <div class="mt-4">
                    <a href="{{ url('/') }}" class="btn btn-primary px-4 rounded-pill">Return to Homepage</a>
                </div>
            </div>
        @endif
    </div>

    <div class="text-center mt-4 text-muted small">
        &copy; {{ date('Y') }} {{ getSetting('site_name', 'Inoodex') }}. All rights reserved.
    </div>
</div>

</body>
</html>
