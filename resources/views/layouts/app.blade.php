<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Judul halaman akan diisi oleh halaman anak --}}

    <!-- Favicon logo -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo/logo-title.png') }}">
    <title>@yield('title') - Analitik Kepegawaian</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.10/dist/cdn.min.js" defer></script>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            overflow-x: hidden !important;
            max-width: 100vw !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
        }

        header {
            flex-shrink: 0 !important;
        }

        /* Sidebar selalu di paling depan */
        aside {
            z-index: 1060 !important;
        }

        main {
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Dropdown fix untuk Bootstrap */
        .dropdown-menu {
            z-index: 1050 !important;
        }

        .dropdown-toggle::after {
            display: none !important;
        }

        /* Modern Scrollbar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(71, 85, 105, 0.2);
            border-radius: 12px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%);
            border-radius: 12px;
            border: 2px solid rgba(30, 41, 59, 0.3);
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #94a3b8 0%, #cbd5e1 100%);
        }

        /* Smooth transitions */
        .transition-all {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Gradient utilities */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            aside {
                width: 100% !important;
                max-width: 320px;
            }
        }

        /* Enhanced hover effects */
        .nav-link:hover .bi {
            transform: scale(1.1);
        }

        /* Logo animation */
        .company-logo {
            transition: transform 0.3s ease-in-out;
        }

        .company-logo:hover {
            transform: scale(1.05) rotate(2deg);
        }
    </style>

    {{-- Tempat untuk script atau style tambahan dari halaman anak --}}
    @stack('styles')
    @stack('head-scripts')
</head>

<body x-data="{ sidebarOpen: false }" class="h-screen overflow-hidden">
    <div class="d-flex h-100 position-relative">

        {{-- 1. Memanggil file partial sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Overlay backdrop ketika sidebar buka --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="position-fixed top-0 start-0 w-100 h-100 bg-black"
            style="z-index: 1050; opacity: 0.5;" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-50"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-50"
            x-transition:leave-end="opacity-0" x-cloak></div>

        {{-- Main content - Full width, sidebar overlay di atasnya --}}
        <div class="flex-grow-1 d-flex flex-column h-100 w-100" style="max-width: 100vw; overflow-x: hidden;">

            {{-- 2. Memanggil file partial header --}}
            @include('layouts.partials.header')

            {{-- 3. Konten utama yang akan diisi oleh setiap halaman anak --}}
            <main class="flex-grow-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8" style="max-width: 100vw;">
                <div style="max-width: 100%; overflow-x: hidden;">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Session Timeout Warning --}}
    <script>
        // Konfigurasi waktu session (dalam menit)
        const SESSION_LIFETIME = {{ config('session.lifetime') }}; // dari config/session.php
        const WARNING_TIME = 5; // Peringatan 5 menit sebelum expired

        let lastActivity = Date.now();
        let sessionWarningShown = false;
        let timeoutTimer = null;
        let warningTimer = null;

        // Reset timer saat ada aktivitas
        function resetSessionTimer() {
            lastActivity = Date.now();
            sessionWarningShown = false;

            // Clear existing timers
            if (timeoutTimer) clearTimeout(timeoutTimer);
            if (warningTimer) clearTimeout(warningTimer);

            // Set warning timer (5 menit sebelum expired)
            const warningMs = (SESSION_LIFETIME - WARNING_TIME) * 60 * 1000;
            warningTimer = setTimeout(showSessionWarning, warningMs);

            // Set logout timer (saat session expired)
            const expiredMs = SESSION_LIFETIME * 60 * 1000;
            timeoutTimer = setTimeout(handleSessionExpired, expiredMs);
        }

        // Tampilkan peringatan session akan expired
        function showSessionWarning() {
            if (sessionWarningShown) return;
            sessionWarningShown = true;

            const notification = document.createElement('div');
            notification.id = 'session-warning';
            notification.className = 'alert alert-warning alert-dismissible fade show position-fixed';
            notification.style.cssText =
                'top: 80px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
            notification.innerHTML = `
                <div class="d-flex align-items-start">
                    <i class="bi bi-clock-history me-2" style="font-size: 1.5rem;"></i>
                    <div class="flex-grow-1">
                        <strong>Peringatan Session</strong>
                        <p class="mb-2 small">Session Anda akan berakhir dalam ${WARNING_TIME} menit. Lakukan aktivitas untuk memperpanjang session.</p>
                        <button class="btn btn-sm btn-warning" onclick="extendSession()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Perpanjang Session
                        </button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.body.appendChild(notification);
        }

        // Handle session expired
        function handleSessionExpired() {
            // Redirect ke login dengan pesan
            window.location.href = '{{ route('login') }}?session_expired=1';
        }

        // Perpanjang session dengan request ke server
        function extendSession() {
            fetch('{{ route('dashboard.index') }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(() => {
                resetSessionTimer();
                const warning = document.getElementById('session-warning');
                if (warning) warning.remove();

                // Tampilkan notifikasi sukses
                showToast('Session berhasil diperpanjang', 'success');
            });
        }

        // Toast notification helper
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 80px; right: 20px; z-index: 9999; max-width: 300px;';
            toast.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Track user activity
        const activityEvents = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'];
        activityEvents.forEach(event => {
            document.addEventListener(event, () => {
                const now = Date.now();
                // Reset hanya jika sudah lebih dari 1 menit sejak aktivitas terakhir
                if (now - lastActivity > 60000) {
                    resetSessionTimer();
                }
            }, {
                passive: true
            });
        });

        // Initialize pada page load
        resetSessionTimer();

        // Check jika ada parameter session_expired di URL
        if (window.location.search.includes('session_expired=1')) {
            showToast('Session Anda telah berakhir. Silakan login kembali.', 'warning');
        }
    </script>

    {{-- Tempat untuk JavaScript spesifik dari halaman anak --}}
    @stack('scripts')
    @stack('body-scripts')
</body>

</html>
