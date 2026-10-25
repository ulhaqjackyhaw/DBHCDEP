@extends('layouts.app')

{{-- Mengisi judul tab browser --}}
@section('title', 'Dashboard Utama')

{{-- Mengisi judul di header --}}
@section('header-title', 'Dashboard Kepegawaian Regional 1 PT Angkasa Pura Indonesia')

{{-- Script & Style di head --}}
@push('head-scripts')
    {{-- Modern Font & Icons --}}
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    {{-- Script Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- Opsional: plugin datalabels untuk menampilkan nilai & persen di chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    {{-- CSS Modern --}}
    <style>
        /* ===== GLOBAL & TYPOGRAPHY ===== */
        :root {
            --primary-color: #4f46e5;
            /* Indigo */
            --body-bg: #f8fafc;
            /* Slate 50 */
            --card-bg: #ffffff;
            --text-color-dark: #1e293b;
            /* Slate 800 */
            --text-color-light: #64748b;
            /* Slate 500 */
            --border-color: #e2e8f0;
            /* Slate 200 */
        }

        * {
            box-sizing: border-box;
        }

        html {
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--body-bg);
            font-family: 'Inter', sans-serif;
            color: var(--text-color-dark);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        .container,
        .container-fluid,
        .row,
        [class*="col-"] {
            max-width: 100%;
        }

        /* Smooth transitions untuk semua elemen */
        * {
            transition: all 0.2s ease-out;
        }

        /* Override transition untuk hover effects */
        .kpi,
        .card,
        button,
        a {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fw-semibold {
            font-weight: 600 !important;
        }

        /* ===== REFINED CARDS ===== */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            background-color: var(--card-bg);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
        }

        .card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-color-dark);
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* ===== MODERN KPI WIDGETS ===== */
        .kpi {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 0.85rem;
            padding: 1rem 0.85rem;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
        }

        .kpi:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .kpi .icon {
            flex-shrink: 0;
            display: grid;
            place-items: center;
            width: clamp(36px, 5vw, 48px);
            height: clamp(36px, 5vw, 48px);
            border-radius: 50%;
            font-size: clamp(0.9rem, 1.5vw, 1.25rem);
            transition: all 0.3s ease;
        }

        .kpi .value {
            font-size: clamp(1rem, 2.2vw, 1.75rem);
            /* Responsive font size */
            font-weight: 700;
            line-height: 1.2;
            color: var(--text-color-dark);
            word-break: break-word;
            transition: font-size 0.3s ease;
        }

        .kpi .label {
            font-size: clamp(0.65rem, 1.3vw, 0.85rem);
            /* Responsive font size */
            color: var(--text-color-light);
            line-height: 1.3;
            word-break: break-word;
            transition: font-size 0.3s ease;
        }

        .kpi .content {
            min-width: 0;
            flex: 1;
            overflow: hidden;
        }

        /* Fluid Typography untuk Header */
        .navbar-brand {
            font-size: clamp(0.85rem, 2.5vw, 1.1rem) !important;
            white-space: normal;
            line-height: 1.3;
        }

        h1 {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
        }

        h2 {
            font-size: clamp(1.25rem, 3.5vw, 2rem);
        }

        h3 {
            font-size: clamp(1.1rem, 3vw, 1.75rem);
        }

        h4,
        h5,
        h6 {
            font-size: clamp(0.9rem, 2.5vw, 1.25rem);
        }

        /* Specific header title di dashboard */
        [class*="header-title"] {
            font-size: clamp(0.8rem, 2.5vw, 1.1rem) !important;
        }

        .kpi .icon-total {
            background-color: #e0e7ff;
            color: #4338ca;
        }

        .kpi .icon-female {
            background-color: #fce7f3;
            color: #db2777;
        }

        .kpi .icon-male {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .kpi .icon-age {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .kpi .icon-mk {
            background-color: #ffedd5;
            color: #f97316;
        }

        /* ===== IMPROVED TABLES ===== */
        .table {
            border-color: var(--border-color);
        }

        .table thead th {
            background: #f1f5f9;
            /* Slate 100 */
            border-bottom: 2px solid var(--border-color);
            color: var(--text-color-light);
            font-weight: 600;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-fixed {
            table-layout: fixed;
        }

        .table-fixed td,
        .table-fixed th {
            word-wrap: break-word;
            vertical-align: middle;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .table-fixed tbody td {
            border-left: 1px solid var(--border-color);
        }

        .table-fixed tbody td:first-child {
            border-left: 0;
        }

        .h-rows {
            max-height: 420px;
            overflow: auto;
        }

        .cell-name {
            font-weight: 600;
            line-height: 1.2;
            color: var(--text-color-dark);
        }

        .cell-role {
            font-size: .85rem;
            color: var(--text-color-light);
        }

        /* ===== RESPONSIVE CHART CONTAINERS ===== */
        canvas {
            max-width: 100%;
            height: auto !important;
            transition: all 0.3s ease;
        }

        /* Chart parent container */
        [class*="chart-container"],
        [style*="position: relative"] canvas {
            display: block;
        }

        /* Responsive table wrapper */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .table {
            width: 100%;
            table-layout: auto;
        }

        .table td,
        .table th {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            max-width: 200px;
        }

        @media (max-width: 576px) {

            .table td,
            .table th {
                max-width: 150px;
                font-size: 0.75rem;
                padding: 0.5rem 0.25rem;
            }
        }

        /* ===== CSS BARU & PENYESUAIAN UNTUK JABATAN LOWONG ===== */
        .total-lowongan-card {
            /* Kartu total keseluruhan */
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: .75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .total-lowongan-card .value {
            font-size: 2.25rem;
            font-weight: 800;
        }

        .lowongan-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .lowongan-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        .lowongan-group {
            border: 1px solid var(--border-color);
            border-radius: .75rem;
            overflow: hidden;
            background-color: #f8fafc;
        }

        .lowongan-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background-color: #f1f5f9;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
        }

        .lowongan-header i {
            color: var(--primary-color);
        }

        .lowongan-body {
            padding: .5rem;
        }

        .lowongan-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0.5rem;
            border-radius: .5rem;
        }

        .lowongan-item:hover {
            background-color: #eef2ff;
        }

        .lowongan-item.clickable-item:hover {
            background-color: #e0e7ff;
            transform: translateX(2px);
            transition: all 0.2s ease;
        }

        .lowongan-item.clickable-item:active {
            transform: translateX(0px);
        }

        .lowongan-level {
            font-size: .9rem;
            color: var(--text-color-dark);
        }

        .lowongan-badge {
            background-color: var(--primary-color);
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            min-width: 28px;
            text-align: center;
        }

        .lowongan-footer {
            /* Baris total per lokasi */
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-top: 1px solid var(--border-color);
            background-color: #f1f5f9;
            font-weight: 600;
            font-size: .9rem;
        }

        /* ===== END OF CSS BARU ===== */

        /* ===== BUTTON GROUP FIX ===== */
        .btn-group {
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        .btn-group .btn {
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-group-sm>.btn,
        .btn-sm {
            font-size: 0.75rem;
            padding: 0.4rem 0.6rem;
        }

        /* Ensure buttons don't overflow */
        .d-flex.flex-wrap {
            max-width: 100%;
        }

        /* ===== INTERACTIVE CHART HINTS ===== */
        .chart-hint {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.5rem;
            color: #1e40af;
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.75rem;
        }

        .chart-hint i {
            color: #3b82f6;
        }

        /* Animate hint on hover */
        .card:hover .chart-hint {
            background-color: #dbeafe;
            border-color: #93c5fd;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        /* Interactive cursor untuk chart */
        .interactive-chart {
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .interactive-chart:hover {
            transform: scale(1.02);
        }

        /* ===== RESPONSIVE ADJUSTMENTS ===== */
        /* Mobile phones (portrait) */
        @media (max-width: 576px) {
            .kpi {
                padding: 0.65rem 0.5rem !important;
                gap: 0.45rem !important;
                flex-direction: row !important;
            }

            .kpi .icon {
                width: 32px !important;
                height: 32px !important;
                font-size: 0.85rem !important;
            }

            .kpi .value {
                font-size: 1.1rem !important;
            }

            .kpi .label {
                font-size: 0.62rem !important;
                line-height: 1.2 !important;
            }

            .card-title {
                font-size: 0.85rem !important;
                line-height: 1.3 !important;
            }

            h6.card-title {
                margin-bottom: 0.5rem !important;
            }

            .card-body {
                padding: 1rem !important;
            }

            .total-lowongan-card .value {
                font-size: 1.5rem !important;
            }

            .lowongan-container {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }

            /* Button groups responsive */
            .btn-sm {
                font-size: 0.7rem !important;
                padding: 0.35rem 0.5rem !important;
                white-space: nowrap;
            }

            .btn-group {
                flex-wrap: wrap !important;
                gap: 0.25rem !important;
            }

            /* Hide long text in buttons */
            .btn .me-1 {
                margin-right: 0.25rem !important;
            }

            /* Button text tidak wrap */
            .btn span {
                display: inline-block;
            }

            /* Table responsive */
            .table {
                font-size: 0.75rem !important;
            }

            .table thead th {
                font-size: 0.65rem !important;
                padding: 0.5rem 0.25rem !important;
            }

            .table tbody td {
                padding: 0.5rem 0.25rem !important;
            }

            .badge {
                font-size: 0.65rem !important;
                padding: 0.2rem 0.4rem !important;
            }

            /* Chart containers - limit height di mobile */
            canvas {
                max-height: 220px !important;
            }

            [style*="height: clamp"] {
                height: 200px !important;
            }

            /* Specific chart heights */
            #growthChart,
            #statusChart,
            #genderChart {
                max-height: 200px !important;
            }

            #usiaChart,
            #mkChart,
            #fungsiJabatanChart,
            #instansiChart {
                max-height: 220px !important;
            }

            /* Row gaps - reduce spacing */
            .g-3 {
                --bs-gutter-x: 0.5rem !important;
                --bs-gutter-y: 0.75rem !important;
            }

            .g-4 {
                --bs-gutter-x: 0.75rem !important;
                --bs-gutter-y: 1rem !important;
            }

            .gap-1 {
                gap: 0.25rem !important;
            }

            .gap-2 {
                gap: 0.35rem !important;
            }

            .mb-4 {
                margin-bottom: 1rem !important;
            }

            .mb-3 {
                margin-bottom: 0.75rem !important;
            }

            /* Make sure flex wraps */
            .d-flex:not(.flex-nowrap) {
                flex-wrap: wrap !important;
            }

            /* Reduce heading size */
            .card-title {
                line-height: 1.3 !important;
            }
        }

        /* iPad (768-1024px) - DEDICATED BREAKPOINT */
        @media (min-width: 768px) and (max-width: 1024px) {
            .card-body {
                padding: 1rem !important;
            }

            .kpi {
                padding: 0.85rem 0.7rem !important;
                gap: 0.65rem !important;
                flex-direction: row !important;
            }

            .kpi .icon {
                width: 38px !important;
                height: 38px !important;
                font-size: 0.95rem !important;
            }

            .kpi .value {
                font-size: 1.25rem !important;
            }

            .kpi .label {
                font-size: 0.68rem !important;
                line-height: 1.2 !important;
            }

            /* Chart heights optimal untuk iPad */
            [style*="height: clamp"] {
                height: 240px !important;
            }

            canvas {
                max-height: 240px !important;
            }

            /* Button groups */
            .btn-sm {
                font-size: 0.7rem !important;
                padding: 0.35rem 0.55rem !important;
            }

            .btn-group {
                flex-wrap: wrap !important;
                gap: 0.35rem !important;
            }

            /* Grid gaps */
            .g-2 {
                --bs-gutter-x: 0.5rem !important;
                --bs-gutter-y: 0.5rem !important;
            }

            .g-3 {
                --bs-gutter-x: 0.75rem !important;
                --bs-gutter-y: 0.75rem !important;
            }

            /* Table font sizes */
            .table {
                font-size: 0.8rem !important;
            }

            .table thead th {
                font-size: 0.7rem !important;
                padding: 0.5rem 0.5rem !important;
            }

            .table tbody td {
                padding: 0.6rem 0.5rem !important;
            }

            /* Lowongan container */
            .lowongan-container {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)) !important;
                gap: 1rem !important;
            }

            .lowongan-header {
                padding: 0.6rem 0.85rem !important;
                font-size: 0.85rem !important;
            }

            .lowongan-item {
                padding: 0.5rem 0.4rem !important;
            }

            .lowongan-level {
                font-size: 0.8rem !important;
            }

            /* Card title */
            .card-title {
                font-size: 0.9rem !important;
            }

            /* Margin/padding adjustments */
            .mb-3 {
                margin-bottom: 0.75rem !important;
            }

            .mb-4 {
                margin-bottom: 1rem !important;
            }
        }

        /* MacBook (1280-1440px) - DEDICATED BREAKPOINT */
        @media (min-width: 1280px) and (max-width: 1440px) {
            .card-body {
                padding: 1.25rem !important;
            }

            .kpi {
                padding: 0.9rem 0.75rem !important;
                gap: 0.75rem !important;
                flex-direction: row !important;
            }

            .kpi .icon {
                width: 40px !important;
                height: 40px !important;
                font-size: 1rem !important;
            }

            .kpi .value {
                font-size: 1.45rem !important;
            }

            .kpi .label {
                font-size: 0.72rem !important;
                line-height: 1.2 !important;
            }

            /* Chart heights untuk MacBook */
            [style*="height: clamp"] {
                height: 280px !important;
            }

            canvas {
                max-height: 280px !important;
            }

            /* Button groups */
            .btn-sm {
                font-size: 0.75rem !important;
                padding: 0.4rem 0.65rem !important;
            }

            /* Grid gaps */
            .g-2 {
                --bs-gutter-x: 0.75rem !important;
                --bs-gutter-y: 0.75rem !important;
            }

            .g-3 {
                --bs-gutter-x: 1rem !important;
                --bs-gutter-y: 1rem !important;
            }

            /* Lowongan container */
            .lowongan-container {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
            }

            /* Card title */
            .card-title {
                font-size: 0.95rem !important;
            }
        }

        /* Desktop besar (≥1441px) - FHD & above */
        @media (min-width: 1441px) {
            .kpi {
                padding: 1.1rem 0.9rem !important;
                gap: 1rem !important;
                flex-direction: row !important;
            }

            .kpi .icon {
                width: 48px !important;
                height: 48px !important;
                font-size: 1.25rem !important;
            }

            .kpi .value {
                font-size: 1.75rem !important;
            }

            .kpi .label {
                font-size: 0.85rem !important;
            }
        }

        /* Tablets (portrait) */
        @media (min-width: 577px) and (max-width: 767px) {
            .kpi {
                padding: 1rem !important;
                gap: 0.75rem !important;
                flex-direction: row !important;
            }

            .kpi .icon {
                width: 42px !important;
                height: 42px !important;
                font-size: 1.05rem !important;
            }

            .kpi .value {
                font-size: 1.5rem !important;
            }

            .kpi .label {
                font-size: 0.75rem !important;
            }

            .card-body {
                padding: 1.25rem !important;
            }

            .lowongan-container {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
            }

            .btn-sm {
                font-size: 0.75rem !important;
                padding: 0.4rem 0.6rem !important;
            }

            canvas {
                max-height: 250px !important;
            }
        }

        /* Tablets (landscape) */
        @media (min-width: 769px) and (max-width: 992px) {
            .kpi .value {
                font-size: 1.85rem !important;
            }

            .kpi .label {
                font-size: 0.825rem !important;
            }
        }

        /* Small laptops */
        @media (min-width: 993px) and (max-width: 1200px) {
            .kpi .value {
                font-size: 1.9rem !important;
            }
        }

        /* ===== TOUCH DEVICE OPTIMIZATION ===== */
        @media (hover: none) and (pointer: coarse) {

            /* Untuk touchscreen devices */
            .btn,
            .kpi,
            .card,
            .lowongan-item {
                -webkit-tap-highlight-color: rgba(79, 70, 229, 0.1);
                touch-action: manipulation;
            }

            .kpi:active,
            .btn:active {
                transform: scale(0.98);
                transition: transform 0.1s ease;
            }

            /* Increase touch target size */
            .btn {
                min-height: 44px;
                min-width: 44px;
            }

            /* Smooth scroll on touch */
            .table-responsive,
            .modal-body {
                -webkit-overflow-scrolling: touch;
                scroll-behavior: smooth;
            }

            /* Better tap feedback */
            a,
            button {
                -webkit-tap-highlight-color: rgba(79, 70, 229, 0.15);
            }
        }

        /* ===== PRINT STYLES ===== */
        @media print {

            .btn,
            .alert,
            .modal,
            .btn-group {
                display: none !important;
            }

            .card {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            canvas {
                max-height: 300px !important;
            }
        }
    </style>
@endpush

{{-- Konten utama --}}
@section('content')
    <div class="bg-white p-3 p-md-4 p-lg-5 rounded-xl shadow-sm mb-4 mb-md-5" style="max-width: 100%; overflow-x: hidden;">

        {{-- ROW 0: KPI Ringkas (Modernized with Icons) --}}
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <div class="col-6 col-md-6 col-lg-4 col-xl-2">
                <div class="kpi h-100">
                    <div class="icon icon-total"><i class="fa-solid fa-users"></i></div>
                    <div class="content">
                        <div class="value">{{ isset($total) ? number_format($total) : '-' }}</div>
                        <div class="label">Total Karyawan</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6 col-lg-4 col-xl-2">
                <div class="kpi h-100">
                    <div class="icon icon-female"><i class="fa-solid fa-venus"></i></div>
                    <div class="content">
                        <div class="value">{{ isset($femalePct) ? number_format($femalePct, 1) . '%' : '-' }}</div>
                        <div class="label">% Perempuan</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6 col-lg-4 col-xl-2">
                <div class="kpi h-100">
                    <div class="icon icon-male"><i class="fa-solid fa-mars"></i></div>
                    <div class="content">
                        <div class="value">
                            {{ isset($femalePct) && is_numeric($femalePct) ? number_format(100 - $femalePct, 1) . '%' : '-' }}
                        </div>
                        <div class="label">% Laki-laki</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6 col-lg-6 col-xl-3">
                <div class="kpi h-100">
                    <div class="icon icon-age"><i class="fa-solid fa-cake-candles"></i></div>
                    <div class="content">
                        <div class="value">{{ $avgAge ?? '-' }}</div>
                        <div class="label">Rata-rata Usia</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-6 col-lg-6 col-xl-3">
                <div class="kpi h-100">
                    <div class="icon icon-mk"><i class="fa-solid fa-business-time"></i></div>
                    <div class="content">
                        <div class="value">{{ $avgMK ?? '-' }}</div>
                        <div class="label">Rata-rata Masa Kerja (Tahun)</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 0.5: Grafik Pertumbuhan Karyawan (BARU) --}}
        @if (isset($versionGrowth) && $versionGrowth['hasData'])
            <div class="row g-2 g-md-3 mb-3 mb-md-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title mb-1">Tren Pertumbuhan Karyawan</h6>
                                    <small class="text-muted">
                                        <i class="fa-solid fa-chart-line me-1"></i>
                                        Berdasarkan snapshot history database
                                    </small>
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Filter periode">
                                        <input type="radio" class="btn-check" name="periodFilter" id="filterAll"
                                            value="all" autocomplete="off" checked>
                                        <label class="btn btn-outline-primary" for="filterAll">
                                            <i class="fa-solid fa-infinity me-1 d-none d-sm-inline"></i><span
                                                class="d-none d-sm-inline">Semua</span><span
                                                class="d-inline d-sm-none">All</span>
                                        </label>

                                        <input type="radio" class="btn-check" name="periodFilter" id="filterYear"
                                            value="year" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="filterYear">
                                            <i class="fa-solid fa-calendar-alt me-1 d-none d-sm-inline"></i>Tahun
                                        </label>

                                        <input type="radio" class="btn-check" name="periodFilter" id="filterMonth"
                                            value="month" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="filterMonth">
                                            <i class="fa-solid fa-calendar-days me-1 d-none d-sm-inline"></i>Bulan
                                        </label>
                                    </div>

                                    <a href="{{ route('versions.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-archive"></i>
                                        <span class="d-none d-md-inline ms-1">History</span>
                                    </a>
                                </div>
                            </div>
                            <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3" role="alert"
                                style="font-size: clamp(0.7rem, 2vw, 0.85rem);">
                                <i class="fa-solid fa-lightbulb me-2 d-none d-md-inline"></i>
                                <strong>Info:</strong> Grafik dari <strong>history snapshot version</strong>. <span
                                    class="d-none d-lg-inline">Semakin sering menyimpan snapshot, semakin detail tren yang
                                    terlihat.</span>
                                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"
                                    aria-label="Close" style="font-size: 0.7rem;"></button>
                            </div>
                            <div style="position: relative; height: clamp(200px, 30vh, 300px); max-height: 300px;">
                                <canvas id="growthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ROW 1 (BARU): Status & Gender --}}
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start mb-3 gap-2">
                            <h6 class="card-title mb-0">Status Kepegawaian</h6>
                            <div class="text-start text-sm-end">
                                <small class="text-muted d-block" style="font-size: clamp(0.65rem, 1.8vw, 0.8rem);">
                                    <i class="fa-solid fa-mouse-pointer me-1 d-none d-md-inline"></i>
                                    <span class="d-none d-lg-inline">Klik untuk analisis detail</span>
                                    <span class="d-inline d-lg-none">Klik untuk detail</span>
                                </small>
                            </div>
                        </div>
                        <div class="alert alert-info alert-dismissible fade show py-2 px-3 mb-3 d-none d-md-block"
                            role="alert" style="font-size: clamp(0.7rem, 2vw, 0.85rem);">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <strong>Tips:</strong> Klik segmen chart KP / ALIH DAYA untuk analisis detail.
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"
                                aria-label="Close" style="font-size: 0.7rem;"></button>
                        </div>
                        <div style="position: relative; height: clamp(180px, 28vh, 240px); max-height: 240px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start mb-3 gap-2">
                            <h6 class="card-title mb-0">Distribusi Gender</h6>
                            <small class="text-muted" style="font-size: clamp(0.65rem, 1.8vw, 0.8rem);">
                                <i class="fa-solid fa-chart-bar me-1 d-none d-md-inline"></i>
                                <span class="d-none d-lg-inline">Perbandingan organik vs outsourcing</span>
                                <span class="d-inline d-lg-none">Organik vs OS</span>
                            </small>
                        </div>
                        <div style="position: relative; height: clamp(180px, 28vh, 240px); max-height: 240px;">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 1.5 (BARU): Pendidikan --}}
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-3 gap-2">
                            <h6 class="card-title mb-0">
                                <span class="d-none d-lg-inline">Distribusi Pendidikan yang Diakui Perusahaan</span>
                                <span class="d-none d-md-inline d-lg-none">Distribusi Pendidikan</span>
                                <span class="d-inline d-md-none">Pendidikan</span>
                            </h6>
                            <small class="text-muted" style="font-size: clamp(0.65rem, 1.8vw, 0.8rem);">
                                <i class="fa-solid fa-graduation-cap me-1"></i>
                                Komposisi tingkat pendidikan karyawan
                            </small>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;" class="text-center">#</th>
                                        <th style="width: 30%;">Tingkat Pendidikan</th>
                                        <th style="width: 15%;" class="text-center">Organik</th>
                                        <th style="width: 15%;" class="text-center">Outsourcing</th>
                                        <th style="width: 15%;" class="text-center">Total</th>
                                        <th style="width: 20%;">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($pendGroupLabels) && count($pendGroupLabels) > 0)
                                        @php
                                            $totalAll = array_sum($pendGroupOrganic) + array_sum($pendGroupOS);
                                        @endphp
                                        @foreach ($pendGroupLabels as $index => $label)
                                            @php
                                                $organic = $pendGroupOrganic[$index] ?? 0;
                                                $os = $pendGroupOS[$index] ?? 0;
                                                $total = $organic + $os;
                                                $percentage = $totalAll > 0 ? ($total / $totalAll) * 100 : 0;
                                            @endphp
                                            <tr>
                                                <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                                                <td>
                                                    <i class="fa-solid fa-graduation-cap me-2 text-primary"></i>
                                                    <strong>{{ $label }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary"
                                                        style="min-width: 60px;">{{ number_format($organic) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning"
                                                        style="min-width: 60px;">{{ number_format($os) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <strong class="text-dark">{{ number_format($total) }}</strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress flex-grow-1" style="height: 20px;">
                                                            <div class="progress-bar bg-success" role="progressbar"
                                                                style="width: {{ $percentage }}%;"
                                                                aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                                aria-valuemax="100">
                                                                <small
                                                                    class="fw-semibold">{{ number_format($percentage, 1) }}%</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-secondary fw-bold">
                                            <td colspan="2" class="text-end">TOTAL KESELURUHAN</td>
                                            <td class="text-center">{{ number_format(array_sum($pendGroupOrganic)) }}</td>
                                            <td class="text-center">{{ number_format(array_sum($pendGroupOS)) }}</td>
                                            <td class="text-center">{{ number_format($totalAll) }}</td>
                                            <td>
                                                <span class="badge bg-dark"
                                                    style="font-size: 0.9rem; padding: 0.5rem 1rem;">100%</span>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center text-muted p-4">
                                                <i class="fa-solid fa-inbox fa-2x mb-2"></i>
                                                <p class="mb-0">Data pendidikan tidak tersedia</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 3: Usia & Masa Kerja --}}
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">Sebaran Usia (tahun)</h6>
                        <div style="position: relative; height: clamp(200px, 32vh, 260px); max-height: 260px;">
                            <canvas id="usiaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">Sebaran Masa Kerja (tahun)</h6>
                        <div style="position: relative; height: clamp(200px, 32vh, 260px); max-height: 260px;">
                            <canvas id="mkChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 3.25: Fungsi Jabatan & Instansi --}}
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">Distribusi Fungsi Jabatan</h6>
                        <small class="text-muted d-block mb-3">
                            <i class="fa-solid fa-briefcase me-1 d-none d-md-inline"></i>
                            <span class="d-none d-lg-inline">Distribusi karyawan berdasarkan fungsi jabatan</span>
                            <span class="d-inline d-lg-none">Per fungsi jabatan</span>
                        </small>
                        <div style="position: relative; height: clamp(200px, 32vh, 260px); max-height: 260px;">
                            <canvas id="fungsiJabatanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">Distribusi Instansi</h6>
                        <small class="text-muted d-block mb-3">
                            <i class="fa-solid fa-building me-1 d-none d-md-inline"></i>
                            <span class="d-none d-lg-inline">Jumlah karyawan per instansi</span>
                            <span class="d-inline d-lg-none">Per instansi</span>
                        </small>
                        <div style="position: relative; height: clamp(200px, 32vh, 260px); max-height: 260px;">
                            <canvas id="instansiChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 3.5: TAMPILAN BARU JABATAN LOWONG --}}
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
                            <h6 class="card-title mb-0">
                                <span class="d-none d-lg-inline">Rekapitulasi Jabatan Lowong Karyawan Organik PT Angkasa
                                    Pura Indonesia Regional 1</span>
                                <span class="d-none d-md-inline d-lg-none">Jabatan Lowong Karyawan Organik</span>
                                <span class="d-inline d-md-none">Jabatan Lowong</span>
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('jabatan-lowong.export') }}"
                                    class="btn btn-success btn-sm d-flex align-items-center gap-1"
                                    title="Export hanya jabatan yang lowong (sisa > 0)">
                                    <i class="fa-solid fa-download"></i>
                                    <span class="d-none d-lg-inline">Unduh Jabatan Lowong</span>
                                    <span class="d-none d-md-inline d-lg-none">Jabatan Lowong</span>
                                    <span class="d-inline d-md-none">Lowong</span>
                                </a>
                                <a href="{{ route('formasi.export.semua.lengkap') }}"
                                    class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                                    title="Export semua jabatan lengkap dengan kolom: Formasi, Terisi, Lowong">
                                    <i class="fa-solid fa-file-excel"></i>
                                    <span class="d-none d-xl-inline">Unduh Semua Formatif</span>
                                    <span class="d-none d-md-inline d-xl-none">Semua</span>
                                    <span class="d-inline d-md-none">All</span>
                                </a>
                            </div>
                        </div>
                        @if (isset($jabatanLowongGrouped) && $jabatanLowongGrouped->isNotEmpty())

                            <!-- KARTU TOTAL KESELURUHAN (BARU) -->
                            <div class="total-lowongan-card">
                                <span class="fw-semibold">Total Jabatan Lowong</span>
                                <div class="value">{{ $totalJabatanLowong }}</div>
                            </div>

                            <div class="lowongan-container">
                                {{-- Loop untuk setiap LOKASI --}}
                                @foreach ($jabatanLowongGrouped as $lokasi => $levels)
                                    <div class="lowongan-group">
                                        <div class="lowongan-header">
                                            <i class="fa-solid fa-map-marker-alt"></i>
                                            <span>{{ $lokasi }}</span>
                                        </div>
                                        <div class="lowongan-body">
                                            {{-- Loop untuk setiap LEVEL di dalam lokasi --}}
                                            @foreach ($levels as $lowong)
                                                <div class="lowongan-item clickable-item"
                                                    data-lokasi="{{ $lokasi }}" data-level="{{ $lowong->level }}"
                                                    data-total="{{ $lowong->total }}"
                                                    onclick="showDetailLowong('{{ $lokasi }}', '{{ $lowong->level }}', {{ $lowong->total }})"
                                                    style="cursor: pointer;"
                                                    title="Klik untuk melihat detail posisi kosong">
                                                    <span class="lowongan-level">{{ $lowong->level }}</span>
                                                    <span class="lowongan-badge">{{ $lowong->total }} <i
                                                            class="fa-solid fa-external-link-alt ms-1"
                                                            style="font-size: 0.7rem;"></i></span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- FOOTER TOTAL PER LOKASI (BARU) -->
                                        <div class="lowongan-footer">
                                            <span>Total</span>
                                            <span>{{ $levels->sum('total') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-4">
                                <i class="fa-solid fa-check-circle fa-2x text-success mb-2"></i>
                                <p class="text-muted mb-0">Tidak ada data jabatan yang lowong saat ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        {{-- ROW 4: Top Unit & Tabel Unit --}}
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">Top 10 Unit Berdasarkan Jumlah Karyawan</h6>
                        <div style="position: relative; height: clamp(250px, 40vh, 380px); max-height: 380px;">
                            <canvas id="unitTopChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="card-title mb-0">Daftar Unit</h6>
                            <input id="searchUnit" class="form-control form-control-sm" style="max-width: 240px"
                                placeholder="Cari nama unit...">
                        </div>
                        <div class="table-responsive flex-grow-1"
                            style="max-height: clamp(250px, 40vh, 380px); overflow:auto;">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Unit</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="unitTableBody">
                                    @forelse(($unitTable ?? []) as $row)
                                        <tr>
                                            <td>{{ $row->unit_kerja ?? '-' }}</td>
                                            <td class="text-end">
                                                {{ isset($row->total) ? number_format($row->total) : '0' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-muted p-4 text-center">Data unit belum
                                                tersedia.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Modal Detail Jabatan Lowong --}}
    <div class="modal fade" id="detailLowongModal" tabindex="-1" aria-labelledby="detailLowongModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-sm-down modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailLowongModalLabel">
                        <i class="fa-solid fa-list-ul me-2"></i>
                        Detail Jabatan Lowong
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong><i class="fa-solid fa-map-marker-alt me-1"></i> Lokasi:</strong>
                            <span id="modalLokasi">-</span>
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fa-solid fa-layer-group me-1"></i> Level:</strong>
                            <span id="modalLevel">-</span>
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fa-solid fa-users me-1"></i> Total Lowong:</strong>
                            <span class="badge bg-danger" id="modalTotal">0</span>
                        </div>
                    </div>

                    <div class="loading-container" id="loadingDetail" style="display: none;">
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat detail posisi kosong...</p>
                        </div>
                    </div>

                    <div id="detailContent">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fa-solid fa-briefcase me-2"></i>
                            Daftar Posisi Yang Kosong
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 15%;">Kode Jabatan</th>
                                        <th style="width: 25%;">Nama Jabatan</th>
                                        <th style="width: 17%;">Unit</th>
                                        <th style="width: 7%;" class="text-center">Formasi</th>
                                        <th style="width: 7%;" class="text-center">Terisi</th>
                                        <th style="width: 9%;" class="text-center">Lowong</th>
                                        <th style="width: 20%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="detailTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted p-3">
                                            Klik item di dashboard untuk melihat detail
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-times me-1"></i> Tutup
                    </button>
                    {{-- @can('admin')
                        <button type="button" class="btn btn-primary" id="btnExportDetail">
                            <i class="fa-solid fa-download me-1"></i> Export Excel
                        </button>
                    @endcan --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal 1: Penempatan Definitif --}}
    <div class="modal fade" id="modalAssignDefinitif" tabindex="-1" aria-labelledby="modalAssignDefinitifLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalAssignDefinitifLabel">
                        <i class="fa-solid fa-user-check me-2"></i>Penetapan Jabatan Definitif
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="reopenDetailModal()"></button>
                </div>
                <form id="formAssignDefinitif" onsubmit="submitAssignDefinitif(event)">
                    <div class="modal-body p-4">
                        <!-- Informasi Formasi Tujuan -->
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body py-3">
                                <h6 class="fw-bold text-success mb-2">
                                    <i class="fa-solid fa-briefcase me-1"></i> Posisi Jabatan Tujuan (Yang Lowong)
                                </h6>
                                <div class="row g-2 small">
                                    <div class="col-md-6">
                                        <span class="text-muted">Kode Jabatan:</span>
                                        <code class="d-block fs-6" id="definitifTargetKode">-</code>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Nama Jabatan:</span>
                                        <strong class="d-block fs-6" id="definitifTargetJabatan">-</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Unit Kerja:</span>
                                        <div class="fw-semibold" id="definitifTargetUnit">-</div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="text-muted">Lokasi:</span>
                                        <div class="fw-semibold" id="definitifTargetLokasi">-</div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="text-muted">Level:</span>
                                        <div class="fw-semibold" id="definitifTargetLevel">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="definitifKodeJabatan" name="kode_jabatan">
                        <input type="hidden" id="definitifKaryawanId" name="karyawan_id">

                        <!-- Pencarian Karyawan -->
                        <div class="mb-3 position-relative">
                            <label class="form-label fw-bold">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Pilih Karyawan Definitif
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                                <input type="text" class="form-control" id="definitifSearchInput" placeholder="Ketik NIK atau Nama Karyawan..." autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="definitifBtnClearSearch" style="display:none;" onclick="clearKaryawanSelection('definitif')">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                            <!-- Dropdown Hasil Pencarian -->
                            <div id="definitifSearchResults" class="list-group position-absolute w-100 shadow mt-1" style="z-index: 1055; max-height: 220px; overflow-y: auto; display: none;"></div>
                        </div>

                        <!-- Karyawan Terpilih Card -->
                        <div id="definitifSelectedCard" class="card border-success mb-3" style="display: none; background-color: #f0fdf4;">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-success mb-1">Karyawan Terpilih</span>
                                        <h6 class="mb-1 fw-bold text-dark" id="definitifSelectedNama">-</h6>
                                        <div class="small text-muted mb-2">NIK: <span id="definitifSelectedNik" class="fw-semibold">-</span></div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearKaryawanSelection('definitif')">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Ganti
                                    </button>
                                </div>
                                <div class="border-top pt-2">
                                    <div class="small text-muted">Jabatan Saat Ini:</div>
                                    <div class="fw-semibold text-danger" id="definitifSelectedOldJabatan">-</div>
                                    <div class="small text-muted mt-1">Unit / Lokasi Saat Ini: <span class="fw-semibold" id="definitifSelectedOldUnit">-</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Peringatan Mutasi -->
                        <div id="definitifWarningAlert" class="alert alert-warning d-flex align-items-center mb-0" style="display: none !important;">
                            <i class="fa-solid fa-triangle-exclamation fs-4 me-3 text-warning"></i>
                            <div>
                                <strong>Perhatian:</strong> Mengisi jabatan lowong ini akan memindahkan karyawan dari posisi lamanya. <strong>Jabatan sebelumnya akan otomatis menjadi lowong.</strong>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="reopenDetailModal()">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="submit" class="btn btn-success" id="btnSubmitDefinitif" disabled>
                            <i class="fa-solid fa-check me-1"></i> Simpan Penempatan Definitif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal 2: Penugasan PGS --}}
    <div class="modal fade" id="modalAssignPgs" tabindex="-1" aria-labelledby="modalAssignPgsLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalAssignPgsLabel">
                        <i class="fa-solid fa-user-clock me-2"></i>Penugasan PGS (Pelaksana Tugas Sementara)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="reopenDetailModal()"></button>
                </div>
                <form id="formAssignPgs" onsubmit="submitAssignPgs(event)">
                    <div class="modal-body p-4">
                        <!-- Info Jabatan PGS Target -->
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body py-3">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="fa-solid fa-briefcase me-1"></i> Posisi PGS Yang Ditugaskan
                                </h6>
                                <div class="row g-2 small">
                                    <div class="col-md-6">
                                        <span class="text-muted">Kode Jabatan:</span>
                                        <code class="d-block fs-6" id="pgsTargetKode">-</code>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Jabatan PGS:</span>
                                        <strong class="d-block fs-6" id="pgsTargetJabatan">-</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Unit Kerja:</span>
                                        <div class="fw-semibold" id="pgsTargetUnit">-</div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Lokasi Kerja:</span>
                                        <div class="fw-semibold" id="pgsTargetLokasi">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="pgsKodeJabatan" name="kode_jabatan">
                        <input type="hidden" id="pgsKaryawanId" name="karyawan_id">

                        <!-- Pencarian Karyawan -->
                        <div class="mb-3 position-relative">
                            <label class="form-label fw-bold">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Pilih Karyawan Yang Ditugaskan
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                                <input type="text" class="form-control" id="pgsSearchInput" placeholder="Ketik NIK atau Nama Karyawan..." autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="pgsBtnClearSearch" style="display:none;" onclick="clearKaryawanSelection('pgs')">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                            <div id="pgsSearchResults" class="list-group position-absolute w-100 shadow mt-1" style="z-index: 1055; max-height: 220px; overflow-y: auto; display: none;"></div>
                        </div>

                        <!-- Karyawan Terpilih Card -->
                        <div id="pgsSelectedCard" class="card border-warning mb-3" style="display: none; background-color: #fffbeb;">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-warning text-dark mb-1">Karyawan Ditugaskan</span>
                                        <h6 class="mb-1 fw-bold text-dark" id="pgsSelectedNama">-</h6>
                                        <div class="small text-muted mb-2">NIK: <span id="pgsSelectedNik" class="fw-semibold">-</span></div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearKaryawanSelection('pgs')">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Ganti
                                    </button>
                                </div>
                                <div class="border-top pt-2">
                                    <div class="small text-muted">Jabatan Definitif (Tetap):</div>
                                    <div class="fw-semibold text-primary" id="pgsSelectedJabatanDefinitif">-</div>
                                    <div class="small text-muted mt-1">Unit / Lokasi Definitif: <span class="fw-semibold" id="pgsSelectedUnitDefinitif">-</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 small mb-3">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            Jabatan definitif karyawan tetap ada (rangkap jabatan). Data otomatis tercatat ke <strong>Master Data PGS</strong> dan kuota jabatan lowong ini akan berkurang.
                        </div>

                        <!-- Input Tanggal PGS -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Mulai PGS <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="pgsTanggalMulai" name="tanggal_pgs" required>
                                <div class="form-text">Tanggal mulai menjabat sebagai PGS</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Target Selesai PGS <span class="text-muted">(Opsional)</span></label>
                                <input type="date" class="form-control" id="pgsTanggalSelesai" name="tanggal_selesai_pgs">
                                <div class="form-text">Estimasi tanggal berakhirnya penugasan PGS</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="reopenDetailModal()">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="submit" class="btn btn-warning" id="btnSubmitPgs" disabled>
                            <i class="fa-solid fa-check me-1"></i> Simpan Penugasan PGS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal 3: Penugasan (Sprint) --}}
    <div class="modal fade" id="modalAssignPenugasan" tabindex="-1" aria-labelledby="modalAssignPenugasanLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-info text-dark">
                    <h5 class="modal-title" id="modalAssignPenugasanLabel">
                        <i class="fa-solid fa-file-signature me-2"></i>Surat Perintah / Penugasan (Sprint)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="reopenDetailModal()"></button>
                </div>
                <form id="formAssignPenugasan" onsubmit="submitAssignPenugasan(event)">
                    <div class="modal-body p-4">
                        <!-- Target Unit Penugasan -->
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body py-3">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="fa-solid fa-location-dot me-1"></i> Lokasi & Unit Penugasan
                                </h6>
                                <div class="row g-2 small">
                                    <div class="col-md-6">
                                        <span class="text-muted">Unit Penugasan:</span>
                                        <strong class="d-block fs-6" id="penugasanTargetUnit">-</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Lokasi Penugasan:</span>
                                        <strong class="d-block fs-6" id="penugasanTargetLokasi">-</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Posisi Formasi:</span>
                                        <div class="fw-semibold" id="penugasanTargetJabatan">-</div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Kode Jabatan:</span>
                                        <code class="d-block" id="penugasanTargetKode">-</code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="penugasanKodeJabatan" name="kode_jabatan">
                        <input type="hidden" id="penugasanKaryawanId" name="karyawan_id">

                        <!-- Pencarian Karyawan -->
                        <div class="mb-3 position-relative">
                            <label class="form-label fw-bold">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Pilih Karyawan
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                                <input type="text" class="form-control" id="penugasanSearchInput" placeholder="Ketik NIK atau Nama Karyawan..." autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="penugasanBtnClearSearch" style="display:none;" onclick="clearKaryawanSelection('penugasan')">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                            <div id="penugasanSearchResults" class="list-group position-absolute w-100 shadow mt-1" style="z-index: 1055; max-height: 220px; overflow-y: auto; display: none;"></div>
                        </div>

                        <!-- Karyawan Terpilih Card -->
                        <div id="penugasanSelectedCard" class="card border-info mb-3" style="display: none; background-color: #f0f9ff;">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-info text-dark mb-1">Karyawan Terpilih</span>
                                        <h6 class="mb-1 fw-bold text-dark" id="penugasanSelectedNama">-</h6>
                                        <div class="small text-muted mb-2">NIK: <span id="penugasanSelectedNik" class="fw-semibold">-</span></div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearKaryawanSelection('penugasan')">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Ganti
                                    </button>
                                </div>
                                <div class="border-top pt-2">
                                    <div class="small text-muted">Jabatan & Unit Definitif:</div>
                                    <div class="fw-semibold text-dark" id="penugasanSelectedJabatanDefinitif">-</div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Input Detail Penugasan -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nomor Sprint / Surat Tugas</label>
                                <input type="text" class="form-control" id="penugasanNomorSprint" name="nomor_sprint" placeholder="Contoh: SPR.001/KP.04/2026">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">PIC</label>
                                <input type="text" class="form-control" id="penugasanPic" name="pic" placeholder="Nama PIC Penugasan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="penugasanTanggalMulai" name="tanggal_mulai" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Selesai <span class="text-muted">(Opsional)</span></label>
                                <input type="date" class="form-control" id="penugasanTanggalSelesai" name="tanggal_selesai">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea class="form-control" id="penugasanKeterangan" name="keterangan" rows="2" placeholder="Catatan atau keterangan penugasan..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="reopenDetailModal()">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                        </button>
                        <button type="submit" class="btn btn-info text-dark" id="btnSubmitPenugasan" disabled>
                            <i class="fa-solid fa-check me-1"></i> Simpan Penugasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- Script halaman --}}
@push('body-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.ChartDataLabels) {
                Chart.register(ChartDataLabels);
            }
            const palette = ['#4f46e5', '#db2777', '#f97316', '#16a34a', '#8b5cf6', '#0891b2', '#f43f5e', '#78350f',
                '#0d9488'
            ];
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#64748b';
            Chart.defaults.plugins.legend.position = 'bottom';
            Chart.defaults.plugins.legend.labels.usePointStyle = true;
            Chart.defaults.plugins.legend.labels.padding = 20;
            Chart.defaults.plugins.tooltip.backgroundColor = '#1e293b';
            Chart.defaults.plugins.tooltip.titleFont = {
                weight: 'bold',
                size: 14
            };
            Chart.defaults.plugins.tooltip.bodyFont = {
                size: 12
            };
            Chart.defaults.plugins.tooltip.padding = 10;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            Chart.defaults.plugins.tooltip.displayColors = false;

            const statusLabels = @json($labels ?? []);
            const statusData = @json($data ?? []);
            const statusCtx = document.getElementById('statusChart')?.getContext('2d');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            label: 'Jumlah Karyawan',
                            data: statusData,
                            backgroundColor: palette,
                            borderWidth: 0,
                            hoverBorderColor: '#fff',
                            hoverBorderWidth: 2,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'right'
                            },
                            datalabels: {
                                anchor: 'center',
                                align: 'center',
                                color: '#fff',
                                formatter: (value, ctx) => {
                                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a +
                                        b, 0);
                                    const pct = total ? (value / total * 100).toFixed(1) : 0;
                                    if (pct < 5) return '';
                                    return `${value}\n(${pct}%)`;
                                },
                                font: {
                                    weight: '700',
                                    size: 11
                                }
                            }
                        },
                        onClick: (evt, elements) => {
                            if (elements.length > 0) {
                                const chart = elements[0].element.$context.chart;
                                const index = elements[0].index;
                                const label = chart.data.labels[index].toLowerCase().trim();
                                // Handle berbagai variasi label outsourcing
                                if (label.includes('outsourcing') || label.includes('alih daya') ||
                                    label.includes('alihdaya')) {
                                    window.location.href = '{{ route('analitik.outsourcing') }}';
                                } else {
                                    window.location.href = '{{ route('analitik.organic') }}';
                                }
                            }
                        },
                        onHover: (event, chartElement) => {
                            const canvas = event.native.target;
                            canvas.style.cursor = chartElement[0] ? 'pointer' : 'default';
                        }
                    }
                });
            }

            const genderGroupLabels = @json($genderGroupLabels ?? []);
            const genderGroupOS = @json($genderGroupOS ?? []);
            const genderGroupOrganic = @json($genderGroupOrganic ?? []);
            const hasGenderGrouped = Array.isArray(genderGroupLabels) && genderGroupLabels.length > 0;
            const genderLabels = @json($genderLabels ?? []);
            const genderData = @json($genderData ?? []);
            const pendLabels = @json($pendLabels ?? []);
            const pendData = @json($pendData ?? []);
            const usiaLabels = @json($usiaLabels ?? []);
            const usiaData = @json($usiaData ?? []);
            const mkLabels = @json($mkLabels ?? []);
            const mkData = @json($mkData ?? []);
            const unitTopLabels = @json($unitTopLabels ?? []);
            const unitTopData = @json($unitTopData ?? []);

            function makeChart(elId, type, labels, data, opts = {}) {
                const el = document.getElementById(elId);
                if (!el || !labels || !labels.length) return;
                const barDatalabels = window.ChartDataLabels ? {
                    anchor: 'end',
                    align: 'end',
                    color: '#334155',
                    formatter: (v) => v > 0 ? v : '',
                    font: {
                        weight: '600',
                        size: 10
                    }
                } : false;
                return new Chart(el.getContext('2d'), {
                    type: type,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: opts.label || 'Jumlah',
                            data: data,
                            backgroundColor: opts.horizontal ? palette[0] : palette,
                            borderColor: opts.horizontal ? palette[0] : palette,
                            borderWidth: type === 'line' ? 2 : 0,
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: opts.horizontal ? 'y' : 'x',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true
                            },
                            datalabels: barDatalabels
                        },
                        scales: (type === 'bar') ? {
                            x: {
                                grid: {
                                    display: opts.horizontal,
                                    drawBorder: false
                                },
                                ticks: {
                                    autoSkip: true,
                                    maxRotation: 0
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: !opts.horizontal,
                                    color: '#e2e8f0',
                                    drawBorder: false
                                },
                                ticks: {
                                    precision: 0
                                }
                            }
                        } : {}
                    }
                });
            }

            const genderCanvas = document.getElementById('genderChart');
            if (genderCanvas && hasGenderGrouped) {
                new Chart(genderCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: genderGroupLabels,
                        datasets: [{
                                label: 'Organik',
                                data: genderGroupOrganic,
                                backgroundColor: palette[0],
                                borderRadius: 4
                            },
                            {
                                label: 'Outsourcing',
                                data: genderGroupOS,
                                backgroundColor: palette[1],
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                enabled: true
                            },
                            // ---- MODIFIKASI UNTUK MENAMPILKAN TOTAL DI DALAM BAR ----
                            datalabels: {
                                display: true, // 1. Aktifkan label
                                color: 'white', // 2. Atur warna font menjadi putih
                                anchor: 'center', // 3. Posisikan di tengah bar
                                align: 'center', // 4. Ratakan teks di tengah
                                font: {
                                    weight: 'bold', // 5. Buat font tebal agar mudah dibaca
                                    size: 12
                                },
                                // 6. Fungsi untuk menampilkan angka hanya jika nilainya lebih dari 0
                                formatter: (value) => {
                                    return value > 0 ? value : '';
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: '#e2e8f0',
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } else if (genderCanvas) {
                const pieGenderCtx = genderCanvas.getContext('2d');
                new Chart(pieGenderCtx, {
                    type: 'pie',
                    data: {
                        labels: genderLabels,
                        datasets: [{
                            data: genderData,
                            backgroundColor: [palette[0], palette[1]],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            }




            makeChart('usiaChart', 'bar', usiaLabels, usiaData);
            makeChart('mkChart', 'bar', mkLabels, mkData);
            makeChart('unitTopChart', 'bar', unitTopLabels, unitTopData, {
                horizontal: true
            });

            // ====== GRAFIK PERTUMBUHAN KARYAWAN (LINE CHART) ======
            @if (isset($versionGrowth) && $versionGrowth['hasData'])
                // Data original dari backend
                const growthLabelsOriginal = @json($versionGrowth['labels'] ?? []);
                const growthTotalOriginal = @json($versionGrowth['total'] ?? []);
                const growthOrganicOriginal = @json($versionGrowth['organic'] ?? []);
                const growthOutsourcingOriginal = @json($versionGrowth['outsourcing'] ?? []);

                // Data yang akan ditampilkan (bisa difilter)
                let growthLabels = [...growthLabelsOriginal];
                let growthTotal = [...growthTotalOriginal];
                let growthOrganic = [...growthOrganicOriginal];
                let growthOutsourcing = [...growthOutsourcingOriginal];

                const growthCanvas = document.getElementById('growthChart');
                let growthChart = null;

                if (growthCanvas && growthLabels.length > 0) {
                    // Fungsi untuk membuat/update chart
                    function createGrowthChart(labels, total, organic, outsourcing) {
                        if (growthChart) {
                            growthChart.destroy();
                        }

                        growthChart = new Chart(growthCanvas.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                        label: 'Total Karyawan',
                                        data: total,
                                        borderColor: '#4f46e5',
                                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                        borderWidth: 3,
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 5,
                                        pointHoverRadius: 7,
                                        pointBackgroundColor: '#4f46e5',
                                        pointBorderColor: '#fff',
                                        pointBorderWidth: 2
                                    },
                                    {
                                        label: 'Organik',
                                        data: organic,
                                        borderColor: '#16a34a',
                                        backgroundColor: 'rgba(22, 163, 74, 0.1)',
                                        borderWidth: 2,
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 4,
                                        pointHoverRadius: 6,
                                        pointBackgroundColor: '#16a34a',
                                        pointBorderColor: '#fff',
                                        pointBorderWidth: 2
                                    },
                                    {
                                        label: 'Outsourcing',
                                        data: outsourcing,
                                        borderColor: '#f97316',
                                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                                        borderWidth: 2,
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 4,
                                        pointHoverRadius: 6,
                                        pointBackgroundColor: '#f97316',
                                        pointBorderColor: '#fff',
                                        pointBorderWidth: 2
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'index',
                                    intersect: false
                                },
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 15,
                                            font: {
                                                size: 12,
                                                weight: '600'
                                            }
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: '#1e293b',
                                        titleFont: {
                                            weight: 'bold',
                                            size: 13
                                        },
                                        bodyFont: {
                                            size: 12
                                        },
                                        padding: 12,
                                        cornerRadius: 8,
                                        displayColors: true,
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.dataset.label || '';
                                                const value = context.parsed.y;
                                                const totalAtIndex = total[context.dataIndex];
                                                const percentage = totalAtIndex > 0 ? ((value /
                                                        totalAtIndex) * 100)
                                                    .toFixed(1) : 0;
                                                return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                                            }
                                        }
                                    },
                                    datalabels: {
                                        display: false // Nonaktifkan datalabels untuk line chart
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0,
                                            callback: function(value) {
                                                return value.toLocaleString();
                                            }
                                        },
                                        grid: {
                                            color: '#e2e8f0',
                                            drawBorder: false
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            maxRotation: 45,
                                            minRotation: 45,
                                            font: {
                                                size: 10
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Fungsi untuk filter data berdasarkan periode
                    function filterDataByPeriod(period) {
                        if (period === 'all') {
                            // Tampilkan semua data
                            return {
                                labels: [...growthLabelsOriginal],
                                total: [...growthTotalOriginal],
                                organic: [...growthOrganicOriginal],
                                outsourcing: [...growthOutsourcingOriginal]
                            };
                        }

                        // Parse label untuk grouping
                        const grouped = {};

                        growthLabelsOriginal.forEach((label, index) => {
                            // Format label: "DD MMM YYYY" atau "Current Data"
                            if (label === 'Current Data') {
                                grouped[label] = {
                                    label: label,
                                    total: growthTotalOriginal[index],
                                    organic: growthOrganicOriginal[index],
                                    outsourcing: growthOutsourcingOriginal[index]
                                };
                                return;
                            }

                            // Parse tanggal dari label
                            const parts = label.split(' ');
                            if (parts.length >= 3) {
                                const day = parts[0];
                                const month = parts[1];
                                const year = parts[2];

                                let key;
                                if (period === 'year') {
                                    key = year; // Group by tahun
                                } else if (period === 'month') {
                                    key = `${month} ${year}`; // Group by bulan & tahun
                                }

                                if (!grouped[key]) {
                                    grouped[key] = {
                                        label: key,
                                        total: 0,
                                        organic: 0,
                                        outsourcing: 0,
                                        count: 0
                                    };
                                }

                                // Ambil data terakhir untuk periode tersebut (snapshot terbaru)
                                grouped[key].total = growthTotalOriginal[index];
                                grouped[key].organic = growthOrganicOriginal[index];
                                grouped[key].outsourcing = growthOutsourcingOriginal[index];
                                grouped[key].count++;
                            }
                        });

                        // Convert ke array
                        const result = Object.values(grouped);

                        return {
                            labels: result.map(r => r.label),
                            total: result.map(r => r.total),
                            organic: result.map(r => r.organic),
                            outsourcing: result.map(r => r.outsourcing)
                        };
                    }

                    // Inisialisasi chart dengan data lengkap
                    createGrowthChart(growthLabels, growthTotal, growthOrganic, growthOutsourcing);

                    // Event listener untuk filter
                    document.querySelectorAll('input[name="periodFilter"]').forEach(radio => {
                        radio.addEventListener('change', function() {
                            const filtered = filterDataByPeriod(this.value);
                            createGrowthChart(
                                filtered.labels,
                                filtered.total,
                                filtered.organic,
                                filtered.outsourcing
                            );
                        });
                    });
                }
            @endif

            const searchUnit = document.getElementById('searchUnit');
            const tbody = document.getElementById('unitTableBody');
            if (searchUnit && tbody) {
                searchUnit.addEventListener('input', function() {
                    const q = this.value.toLowerCase();
                    [...tbody.querySelectorAll('tr')].forEach(tr => {
                        const unit = (tr.children[0]?.textContent || '').toLowerCase();
                        tr.style.display = unit.includes(q) ? '' : 'none';
                    });
                });
            }

            // Chart Fungsi Jabatan (Horizontal Bar - Top 10)
            const fungsiJabatanLabels = @json($fungsiJabatanLabels ?? []);
            const fungsiJabatanData = @json($fungsiJabatanData ?? []);
            const fungsiJabatanCtx = document.getElementById('fungsiJabatanChart')?.getContext('2d');
            if (fungsiJabatanCtx && fungsiJabatanLabels.length > 0) {
                new Chart(fungsiJabatanCtx, {
                    type: 'bar',
                    data: {
                        labels: fungsiJabatanLabels,
                        datasets: [{
                            label: 'Jumlah Karyawan',
                            data: fungsiJabatanData,
                            backgroundColor: palette.slice(0, fungsiJabatanLabels.length),
                            borderWidth: 0,
                            barThickness: 22
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'right',
                                offset: 4,
                                color: '#64748b',
                                font: {
                                    weight: '700',
                                    size: 11
                                },
                                formatter: (value) => value.toLocaleString()
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Chart Instansi (Doughnut)
            const instansiLabels = @json($instansiLabels ?? []);
            const instansiData = @json($instansiData ?? []);
            const instansiCtx = document.getElementById('instansiChart')?.getContext('2d');
            if (instansiCtx && instansiLabels.length > 0) {
                new Chart(instansiCtx, {
                    type: 'doughnut',
                    data: {
                        labels: instansiLabels,
                        datasets: [{
                            label: 'Jumlah Karyawan',
                            data: instansiData,
                            backgroundColor: palette.slice(0, instansiLabels.length),
                            borderWidth: 0,
                            hoverBorderColor: '#fff',
                            hoverBorderWidth: 2,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'right'
                            },
                            datalabels: {
                                anchor: 'center',
                                align: 'center',
                                color: '#fff',
                                formatter: (value, ctx) => {
                                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a +
                                        b, 0);
                                    const pct = total ? (value / total * 100).toFixed(1) : 0;
                                    if (pct < 3) return '';
                                    return `${value}\n(${pct}%)`;
                                },
                                font: {
                                    weight: '700',
                                    size: 11
                                }
                            }
                        }
                    }
                });
            }
        });

        // Function untuk menampilkan detail jabatan lowong
        let currentModalLokasi = '';
        let currentModalLevel = '';
        let currentModalTotal = 0;

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function showDetailLowong(lokasi, level, total) {
            currentModalLokasi = lokasi;
            currentModalLevel = level;
            currentModalTotal = total;

            // Set data ke modal
            document.getElementById('modalLokasi').textContent = lokasi;
            document.getElementById('modalLevel').textContent = level;
            document.getElementById('modalTotal').textContent = total;

            // Show loading
            document.getElementById('loadingDetail').style.display = 'block';
            document.getElementById('detailContent').style.display = 'none';

            // Show modal
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('detailLowongModal'));
            modal.show();

            // AJAX request untuk mendapatkan detail
            fetch(`/dashboard/jabatan-lowong-detail?lokasi=${encodeURIComponent(lokasi)}&level=${encodeURIComponent(level)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Hide loading
                    document.getElementById('loadingDetail').style.display = 'none';
                    document.getElementById('detailContent').style.display = 'block';

                    const tableBody = document.getElementById('detailTableBody');

                    if (data.success && data.data.length > 0) {
                        let html = '';
                        data.data.forEach(item => {
                            const lowongCount = item.lowong_count !== undefined ? item.lowong_count : (item.formasi_count - item.karyawan_count);
                            const itemDataEncoded = encodeURIComponent(JSON.stringify(item));

                            let aksiHtml = '';
                            if (lowongCount > 0) {
                                aksiHtml = `
                                    <div class="dropdown text-center">
                                        <button class="btn btn-sm btn-primary dropdown-toggle px-2 py-1 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-bolt me-1"></i> Aksi
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2" style="min-width: 250px; z-index: 1060;">
                                            <li><h6 class="dropdown-header text-uppercase fs-7 text-muted fw-bold">Pilih Tindakan</h6></li>
                                            <li>
                                                <button type="button" class="dropdown-item d-flex align-items-center py-2" onclick="handleOpenAssignModal('definitif', '${itemDataEncoded}')">
                                                    <span class="badge bg-success-subtle text-success p-2 rounded-circle me-2">
                                                        <i class="fa-solid fa-user-check"></i>
                                                    </span>
                                                    <div>
                                                        <div class="fw-bold text-dark">Definitif</div>
                                                        <small class="text-muted">Isi permanen (jabatan lama lowong)</small>
                                                    </div>
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <button type="button" class="dropdown-item d-flex align-items-center py-2" onclick="handleOpenAssignModal('pgs', '${itemDataEncoded}')">
                                                    <span class="badge bg-warning-subtle text-warning p-2 rounded-circle me-2">
                                                        <i class="fa-solid fa-user-clock"></i>
                                                    </span>
                                                    <div>
                                                        <div class="fw-bold text-dark">PGS</div>
                                                        <small class="text-muted">Pelaksana tugas sementara</small>
                                                    </div>
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <button type="button" class="dropdown-item d-flex align-items-center py-2" onclick="handleOpenAssignModal('penugasan', '${itemDataEncoded}')">
                                                    <span class="badge bg-info-subtle text-info p-2 rounded-circle me-2">
                                                        <i class="fa-solid fa-file-signature"></i>
                                                    </span>
                                                    <div>
                                                        <div class="fw-bold text-dark">Penugasan</div>
                                                        <small class="text-muted">Surat tugas / penugasan sprint</small>
                                                    </div>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                `;
                            } else {
                                aksiHtml = `<span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="fa-solid fa-check me-1"></i> Terisi</span>`;
                            }

                            html += `
                            <tr>
                                <td><code>${escapeHtml(item.kode_jabatan)}</code></td>
                                <td>${escapeHtml(item.jabatan)}</td>
                                <td>${escapeHtml(item.unit)}</td>
                                <td class="text-center"><span class="badge bg-info">${item.formasi_count}</span></td>
                                <td class="text-center"><span class="badge bg-success">${item.karyawan_count}</span></td>
                                <td class="text-center"><span class="badge bg-danger">${lowongCount}</span></td>
                                <td class="text-center">${aksiHtml}</td>
                            </tr>
                        `;
                        });
                        tableBody.innerHTML = html;
                    } else {
                        tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center text-muted p-3">
                                <i class="fa-solid fa-exclamation-circle me-2"></i>
                                Tidak ada data detail untuk lokasi dan level ini
                            </td>
                        </tr>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loadingDetail').style.display = 'none';
                    document.getElementById('detailContent').style.display = 'block';

                    document.getElementById('detailTableBody').innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-danger p-3">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            Terjadi kesalahan saat memuat data
                        </td>
                    </tr>
                `;
                });
        }

        function handleOpenAssignModal(type, encodedItem) {
            const item = JSON.parse(decodeURIComponent(encodedItem));
            openAssignModal(type, item);
        }

        function openAssignModal(type, target) {
            // Sembunyikan detail modal sementara
            const detailModalEl = document.getElementById('detailLowongModal');
            const detailModal = bootstrap.Modal.getInstance(detailModalEl);
            if (detailModal) {
                detailModal.hide();
            }

            if (type === 'definitif') {
                document.getElementById('definitifTargetKode').textContent = target.kode_jabatan;
                document.getElementById('definitifTargetJabatan').textContent = target.jabatan;
                document.getElementById('definitifTargetUnit').textContent = target.unit || '-';
                document.getElementById('definitifTargetLokasi').textContent = target.lokasi || '-';
                document.getElementById('definitifTargetLevel').textContent = target.level || '-';
                document.getElementById('definitifKodeJabatan').value = target.kode_jabatan;
                clearKaryawanSelection('definitif');
                const modal = new bootstrap.Modal(document.getElementById('modalAssignDefinitif'));
                modal.show();
            } else if (type === 'pgs') {
                document.getElementById('pgsTargetKode').textContent = target.kode_jabatan;
                document.getElementById('pgsTargetJabatan').textContent = target.jabatan;
                document.getElementById('pgsTargetUnit').textContent = target.unit || '-';
                document.getElementById('pgsTargetLokasi').textContent = target.lokasi || '-';
                document.getElementById('pgsKodeJabatan').value = target.kode_jabatan;
                document.getElementById('pgsTanggalMulai').value = new Date().toISOString().split('T')[0];
                document.getElementById('pgsTanggalSelesai').value = '';
                clearKaryawanSelection('pgs');
                const modal = new bootstrap.Modal(document.getElementById('modalAssignPgs'));
                modal.show();
            } else if (type === 'penugasan') {
                document.getElementById('penugasanTargetKode').textContent = target.kode_jabatan;
                document.getElementById('penugasanTargetJabatan').textContent = target.jabatan;
                document.getElementById('penugasanTargetUnit').textContent = target.unit || '-';
                document.getElementById('penugasanTargetLokasi').textContent = target.lokasi || '-';
                document.getElementById('penugasanKodeJabatan').value = target.kode_jabatan;
                document.getElementById('penugasanTanggalMulai').value = new Date().toISOString().split('T')[0];
                document.getElementById('penugasanTanggalSelesai').value = '';
                document.getElementById('penugasanNomorSprint').value = '';
                document.getElementById('penugasanPic').value = '';
                document.getElementById('penugasanKeterangan').value = '';
                clearKaryawanSelection('penugasan');
                const modal = new bootstrap.Modal(document.getElementById('modalAssignPenugasan'));
                modal.show();
            }
        }

        function reopenDetailModal() {
            const detailModalEl = document.getElementById('detailLowongModal');
            const detailModal = bootstrap.Modal.getOrCreateInstance(detailModalEl);
            detailModal.show();
        }

        // Setup live search karyawan
        let searchDebounceTimer = null;
        function setupKaryawanSearch(type) {
            const input = document.getElementById(`${type}SearchInput`);
            const resultsContainer = document.getElementById(`${type}SearchResults`);
            const clearBtn = document.getElementById(`${type}BtnClearSearch`);
            if (!input || !resultsContainer) return;

            input.addEventListener('input', function() {
                const query = this.value.trim();
                clearTimeout(searchDebounceTimer);

                if (query.length === 0) {
                    resultsContainer.innerHTML = '';
                    resultsContainer.style.display = 'none';
                    if (clearBtn) clearBtn.style.display = 'none';
                    return;
                }

                if (clearBtn) clearBtn.style.display = 'block';

                searchDebounceTimer = setTimeout(() => {
                    resultsContainer.innerHTML = '<div class="p-3 text-center text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Mencari data karyawan...</div>';
                    resultsContainer.style.display = 'block';

                    fetch(`/dashboard/karyawan-search?q=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            let html = '';
                            data.data.forEach(k => {
                                const kEncoded = encodeURIComponent(JSON.stringify(k));
                                html += `
                                    <button type="button" class="list-group-item list-group-item-action py-2 text-start" onclick="handleSelectKaryawan('${type}', '${kEncoded}')">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-primary">${escapeHtml(k.nama)}</strong>
                                            <span class="badge bg-light text-dark border">${escapeHtml(k.nik)}</span>
                                        </div>
                                        <div class="small text-muted text-truncate">
                                            <i class="fa-solid fa-briefcase me-1"></i> ${escapeHtml(k.jabatan || '(Tanpa Jabatan)')}
                                        </div>
                                        <div class="small text-secondary">
                                            <i class="fa-solid fa-building me-1"></i> ${escapeHtml(k.unit_kerja || '-')} (${escapeHtml(k.lokasi_kerja || '-')})
                                        </div>
                                    </button>
                                `;
                            });
                            resultsContainer.innerHTML = html;
                        } else {
                            resultsContainer.innerHTML = '<div class="p-3 text-center text-muted"><i class="fa-solid fa-circle-exclamation me-1"></i> Tidak ditemukan karyawan dengan NIK / Nama tersebut</div>';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        resultsContainer.innerHTML = '<div class="p-3 text-center text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Gagal memuat data karyawan</div>';
                    });
                }, 300);
            });
        }

        function handleSelectKaryawan(type, encodedKaryawan) {
            const karyawan = JSON.parse(decodeURIComponent(encodedKaryawan));
            selectKaryawan(type, karyawan);
        }

        function selectKaryawan(type, karyawan) {
            document.getElementById(`${type}KaryawanId`).value = karyawan.id;
            document.getElementById(`${type}SearchResults`).style.display = 'none';
            document.getElementById(`${type}SearchInput`).value = `${karyawan.nama} (${karyawan.nik})`;

            if (type === 'definitif') {
                document.getElementById('definitifSelectedCard').style.display = 'block';
                document.getElementById('definitifSelectedNama').textContent = karyawan.nama;
                document.getElementById('definitifSelectedNik').textContent = karyawan.nik;
                document.getElementById('definitifSelectedOldJabatan').textContent = (karyawan.jabatan || 'Tidak ada') + (karyawan.kode_jabatan ? ` [${karyawan.kode_jabatan}]` : '');
                document.getElementById('definitifSelectedOldUnit').textContent = `${karyawan.unit_kerja || '-'} / ${karyawan.lokasi_kerja || '-'}`;
                document.getElementById('definitifWarningAlert').style.setProperty('display', 'flex', 'important');
                document.getElementById('btnSubmitDefinitif').disabled = false;
            } else if (type === 'pgs') {
                document.getElementById('pgsSelectedCard').style.display = 'block';
                document.getElementById('pgsSelectedNama').textContent = karyawan.nama;
                document.getElementById('pgsSelectedNik').textContent = karyawan.nik;
                document.getElementById('pgsSelectedJabatanDefinitif').textContent = karyawan.jabatan || '-';
                document.getElementById('pgsSelectedUnitDefinitif').textContent = `${karyawan.unit_kerja || '-'} / ${karyawan.lokasi_kerja || '-'}`;
                document.getElementById('btnSubmitPgs').disabled = false;
            } else if (type === 'penugasan') {
                document.getElementById('penugasanSelectedCard').style.display = 'block';
                document.getElementById('penugasanSelectedNama').textContent = karyawan.nama;
                document.getElementById('penugasanSelectedNik').textContent = karyawan.nik;
                document.getElementById('penugasanSelectedJabatanDefinitif').textContent = `${karyawan.jabatan || '-'} (${karyawan.unit_kerja || '-'})`;
                document.getElementById('btnSubmitPenugasan').disabled = false;
            }
        }

        function clearKaryawanSelection(type) {
            document.getElementById(`${type}KaryawanId`).value = '';
            document.getElementById(`${type}SearchInput`).value = '';
            document.getElementById(`${type}SearchResults`).innerHTML = '';
            document.getElementById(`${type}SearchResults`).style.display = 'none';
            document.getElementById(`${type}SelectedCard`).style.display = 'none';
            const clearBtn = document.getElementById(`${type}BtnClearSearch`);
            if (clearBtn) clearBtn.style.display = 'none';

            if (type === 'definitif') {
                document.getElementById('definitifWarningAlert').style.setProperty('display', 'none', 'important');
                document.getElementById('btnSubmitDefinitif').disabled = true;
            } else if (type === 'pgs') {
                document.getElementById('btnSubmitPgs').disabled = true;
            } else if (type === 'penugasan') {
                document.getElementById('btnSubmitPenugasan').disabled = true;
            }

            document.getElementById(`${type}SearchInput`).focus();
        }

        // Submit Assign Definitif
        function submitAssignDefinitif(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitDefinitif');
            const karyawanId = document.getElementById('definitifKaryawanId').value;
            const kodeJabatan = document.getElementById('definitifKodeJabatan').value;

            if (!karyawanId || !kodeJabatan) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Peringatan', 'Silakan pilih karyawan terlebih dahulu', 'warning');
                } else {
                    alert('Silakan pilih karyawan terlebih dahulu');
                }
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch('{{ route("dashboard.assign.definitif") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    karyawan_id: karyawanId,
                    kode_jabatan: kodeJabatan
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Simpan Penempatan Definitif';

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalAssignDefinitif')).hide();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#198754'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        alert(data.message);
                        location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Simpan Penempatan Definitif';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Terjadi kesalahan koneksi saat memproses data', 'error');
                } else {
                    alert('Terjadi kesalahan koneksi saat memproses data');
                }
            });
        }

        // Submit Assign PGS
        function submitAssignPgs(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitPgs');
            const karyawanId = document.getElementById('pgsKaryawanId').value;
            const kodeJabatan = document.getElementById('pgsKodeJabatan').value;
            const tanggalPgs = document.getElementById('pgsTanggalMulai').value;
            const tanggalSelesaiPgs = document.getElementById('pgsTanggalSelesai').value;

            if (!karyawanId || !kodeJabatan || !tanggalPgs) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Peringatan', 'Silakan lengkapi karyawan dan tanggal mulai PGS', 'warning');
                } else {
                    alert('Silakan lengkapi karyawan dan tanggal mulai PGS');
                }
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch('{{ route("dashboard.assign.pgs") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    karyawan_id: karyawanId,
                    kode_jabatan: kodeJabatan,
                    tanggal_pgs: tanggalPgs,
                    tanggal_selesai_pgs: tanggalSelesaiPgs || null
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Simpan Penugasan PGS';

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalAssignPgs')).hide();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#ffc107'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        alert(data.message);
                        location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Simpan Penugasan PGS';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Terjadi kesalahan koneksi saat memproses data', 'error');
                } else {
                    alert('Terjadi kesalahan koneksi saat memproses data');
                }
            });
        }

        // Submit Assign Penugasan
        function submitAssignPenugasan(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitPenugasan');
            const karyawanId = document.getElementById('penugasanKaryawanId').value;
            const kodeJabatan = document.getElementById('penugasanKodeJabatan').value;
            const tanggalMulai = document.getElementById('penugasanTanggalMulai').value;
            const tanggalSelesai = document.getElementById('penugasanTanggalSelesai').value;
            const nomorSprint = document.getElementById('penugasanNomorSprint').value;
            const pic = document.getElementById('penugasanPic').value;
            const keterangan = document.getElementById('penugasanKeterangan').value;

            if (!karyawanId || !kodeJabatan || !tanggalMulai) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Peringatan', 'Silakan lengkapi karyawan dan tanggal mulai', 'warning');
                } else {
                    alert('Silakan lengkapi karyawan dan tanggal mulai');
                }
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch('{{ route("dashboard.assign.penugasan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    karyawan_id: karyawanId,
                    kode_jabatan: kodeJabatan,
                    tanggal_mulai: tanggalMulai,
                    tanggal_selesai: tanggalSelesai || null,
                    nomor_sprint: nomorSprint || null,
                    pic: pic || null,
                    keterangan: keterangan || null
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Simpan Penugasan';

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalAssignPenugasan')).hide();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#0dcaf0'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        alert(data.message);
                        location.reload();
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Simpan Penugasan';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Terjadi kesalahan koneksi saat memproses data', 'error');
                } else {
                    alert('Terjadi kesalahan koneksi saat memproses data');
                }
            });
        }

        // Inisialisasi event listener search
        document.addEventListener('DOMContentLoaded', () => {
            setupKaryawanSearch('definitif');
            setupKaryawanSearch('pgs');
            setupKaryawanSearch('penugasan');
        });

        // Export detail function
        document.getElementById('btnExportDetail')?.addEventListener('click', function() {
            const lokasi = document.getElementById('modalLokasi').textContent;
            const level = document.getElementById('modalLevel').textContent;

            window.location.href =
                `/dashboard/jabatan-lowong-export?lokasi=${encodeURIComponent(lokasi)}&level=${encodeURIComponent(level)}`;
        });
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.4/dist/sweetalert2.all.min.js"></script>
@endpush
