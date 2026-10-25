<aside class="d-flex flex-column text-slate-700 shadow-xl position-fixed h-100"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    style="width: 320px; left: 0; top: 0; z-index: 1060; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);">

    <style>
        /* PERFORMANCE FIX: Use CSS instead of inline JavaScript */
        .nav-link {
            transition: transform 0.15s ease !important;
        }

        .nav-link:not(.is-active):hover {
            transform: translateX(4px) !important;
        }

        .nav-link.is-active {
            transform: translateX(8px) !important;
        }

        /* Close button hover effect */
        .sidebar-close-btn {
            transition: all 0.2s ease;
        }

        .sidebar-close-btn:hover {
            background-color: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
            transform: rotate(90deg);
        }
    </style>

    {{-- Close Button (X) - Inside sidebar top-right corner --}}
    <button @click="sidebarOpen = false"
        class="sidebar-close-btn position-absolute d-flex align-items-center justify-content-center rounded-circle border-0"
        style="top: 12px; right: 12px; width: 32px; height: 32px; z-index: 10; color: #64748b; background: rgba(255, 255, 255, 0.8); box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
        aria-label="Tutup Sidebar">
        <i class="bi bi-x-lg" style="font-size: 14px; font-weight: bold;"></i>
    </button>

    {{-- Company Branding Header --}}
    <div class="d-flex flex-column p-2 border-bottom border-slate-200" style="min-height: 100px;">
        {{-- Logo Section --}}
        <div class="d-flex justify-content-center mb-4">
            <div class="p-3 rounded-xl bg-white shadow-md border"
                style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-color: #cbd5e1;">
                <img src="{{ asset('images/logo/company-logo.png') }}" alt="Angkasa Pura Indonesia"
                    class="company-logo d-block" style="width: 150px; height: 50px; object-fit: contain;">
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-grow-1 p-4 sidebar-scroll overflow-y-auto">
        <div class="mb-4">
            <h6 class="text-slate-500 text-uppercase fw-bold small mb-3 px-3"
                style="font-size: 11px; letter-spacing: 1px;">
                Menu Utama
            </h6>
            <ul class="nav flex-column gap-1">
                {{-- Dashboard Utama --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard.index') }}" @click="sidebarOpen = false"
                        class="nav-link d-flex align-items-center rounded-xl px-4 py-3 {{ request()->routeIs('dashboard.index') ? 'text-white shadow-lg is-active' : 'text-slate-600' }}"
                        style="{{ request()->routeIs('dashboard.index') ? 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);' : '' }}">
                        <div class="d-flex align-items-center justify-content-center rounded-lg me-3 {{ request()->routeIs('dashboard.index') ? 'bg-white/20' : 'bg-blue-100' }}"
                            style="width: 32px; height: 32px; min-width: 32px;">
                            <i class="bi bi-grid-1x2-fill {{ request()->routeIs('dashboard.index') ? 'text-white' : 'text-blue-600' }}"
                                style="font-size: 14px;"></i>
                        </div>
                        <span class="fw-medium">Dashboard Utama</span>
                        @if (request()->routeIs('dashboard.index'))
                            <i class="bi bi-chevron-right ms-auto opacity-75"></i>
                        @endif
                    </a>
                </li>


            </ul>
        </div>

        <div class="mb-4">
            <h6 class="text-slate-500 text-uppercase fw-bold small mb-3 px-3"
                style="font-size: 11px; letter-spacing: 1px;">
                Analitik
            </h6>
            <ul class="nav flex-column gap-1">
                {{-- Analitik Karyawan Organik --}}
                <li class="nav-item">
                    <a href="{{ route('analitik.organic') }}" @click="sidebarOpen = false"
                        class="nav-link d-flex align-items-center rounded-xl px-4 py-3 {{ request()->routeIs('analitik.organic') ? 'text-white shadow-lg is-active' : 'text-slate-600' }}"
                        style="{{ request()->routeIs('analitik.organic') ? 'background: linear-gradient(135deg, #10b981 0%, #059669 100%);' : '' }}">
                        <div class="d-flex align-items-center justify-content-center rounded-lg me-3 {{ request()->routeIs('analitik.organic') ? 'bg-white/20' : 'bg-green-100' }}"
                            style="width: 32px; height: 32px; min-width: 32px;">
                            <i class="bi bi-person-badge-fill {{ request()->routeIs('analitik.organic') ? 'text-white' : 'text-green-600' }}"
                                style="font-size: 14px;"></i>
                        </div>
                        <span class="fw-medium">Karyawan Organik</span>
                        @if (request()->routeIs('analitik.organic'))
                            <i class="bi bi-chevron-right ms-auto opacity-75"></i>
                        @endif
                    </a>
                </li>


                {{-- Analitik Karyawan Outsourcing --}}
                <li class="nav-item">
                    <a href="{{ route('analitik.outsourcing') }}" @click="sidebarOpen = false"
                        class="nav-link d-flex align-items-center rounded-xl px-4 py-3 {{ request()->routeIs('analitik.outsourcing') ? 'text-white shadow-lg is-active' : 'text-slate-600' }}"
                        style="{{ request()->routeIs('analitik.outsourcing') ? 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);' : '' }}">
                        <div class="d-flex align-items-center justify-content-center rounded-lg me-3 {{ request()->routeIs('analitik.outsourcing') ? 'bg-white/20' : 'bg-amber-100' }}"
                            style="width: 32px; height: 32px; min-width: 32px;">
                            <i class="bi bi-person-workspace {{ request()->routeIs('analitik.outsourcing') ? 'text-white' : 'text-amber-600' }}"
                                style="font-size: 14px;"></i>
                        </div>
                        <span class="fw-medium">Karyawan Outsourcing</span>
                        @if (request()->routeIs('analitik.outsourcing'))
                            <i class="bi bi-chevron-right ms-auto opacity-75"></i>
                        @endif
                    </a>
                </li>
            </ul>
        </div>

        <div class="mb-4">
            <h6 class="text-slate-500 text-uppercase fw-bold small mb-3 px-3"
                style="font-size: 11px; letter-spacing: 1px;">
                Manajemen Data
            </h6>
            <ul class="nav flex-column gap-1">
                {{-- Data Formasi --}}
                <li class="nav-item">
                    <a href="{{ route('formasi.index') }}" @click="sidebarOpen = false"
                        class="nav-link d-flex align-items-center rounded-xl px-4 py-3 {{ request()->routeIs('formasi.*') ? 'text-white shadow-lg is-active' : 'text-slate-600' }}"
                        style="{{ request()->routeIs('formasi.*') ? 'background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);' : '' }}">
                        <div class="d-flex align-items-center justify-content-center rounded-lg me-3 {{ request()->routeIs('formasi.*') ? 'bg-white/20' : 'bg-indigo-100' }}"
                            style="width: 32px; height: 32px; min-width: 32px;">
                            <i class="bi bi-building-fill {{ request()->routeIs('formasi.*') ? 'text-white' : 'text-indigo-600' }}"
                                style="font-size: 14px;"></i>
                        </div>
                        <span class="fw-medium">Data Formasi</span>
                        @if (request()->routeIs('formasi.*'))
                            <i class="bi bi-chevron-right ms-auto opacity-75"></i>
                        @endif
                    </a>
                </li>


                {{-- Data Karyawan --}}
                <li class="nav-item">
                    <a href="{{ route('karyawan.index') }}" @click="sidebarOpen = false"
                        class="nav-link d-flex align-items-center rounded-xl px-4 py-3 {{ request()->routeIs('karyawan.index') ? 'text-white shadow-lg is-active' : 'text-slate-600' }}"
                        style="{{ request()->routeIs('karyawan.index') ? 'background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);' : '' }}">
                        <div class="d-flex align-items-center justify-content-center rounded-lg me-3 {{ request()->routeIs('karyawan.index') ? 'bg-white/20' : 'bg-sky-100' }}"
                            style="width: 32px; height: 32px; min-width: 32px;">
                            <i class="bi bi-people-fill {{ request()->routeIs('karyawan.index') ? 'text-white' : 'text-sky-600' }}"
                                style="font-size: 14px;"></i>
                        </div>
                        <span class="fw-medium">Data Karyawan</span>
                        @if (request()->routeIs('karyawan.index'))
                            <i class="bi bi-chevron-right ms-auto opacity-75"></i>
                        @endif
                    </a>
                </li>

                {{-- Tabel Realisasi --}}
                <li class="nav-item">
                    <a href="{{ route('realisasi.index') }}" @click="sidebarOpen = false"
                        class="nav-link d-flex align-items-center rounded-xl px-4 py-3 {{ request()->routeIs('realisasi.*') ? 'text-white shadow-lg is-active' : 'text-slate-600' }}"
                        style="{{ request()->routeIs('realisasi.*') ? 'background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);' : '' }}">
                        <div class="d-flex align-items-center justify-content-center rounded-lg me-3 {{ request()->routeIs('realisasi.*') ? 'bg-white/20' : 'bg-green-100' }}"
                            style="width: 32px; height: 32px; min-width: 32px;">
                            <i class="bi bi-table {{ request()->routeIs('realisasi.*') ? 'text-white' : 'text-green-600' }}"
                                style="font-size: 14px;"></i>
                        </div>
                        <span class="fw-medium">Tabel Realisasi</span>
                        @if (request()->routeIs('realisasi.*'))
                            <i class="bi bi-chevron-right ms-auto opacity-75"></i>
                        @endif
                    </a>
                </li>

                {{-- Data PGS --}}
                <li class="nav-item">
                    <a href="{{ route('data-pgs.index') }}" @click="sidebarOpen = false"
                        class="nav-link d-flex align-items-center rounded-xl px-4 py-3 {{ request()->routeIs('data-pgs.*') ? 'text-white shadow-lg is-active' : 'text-slate-600' }}"
                        style="{{ request()->routeIs('data-pgs.*') ? 'background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);' : '' }}">
                        <div class="d-flex align-items-center justify-content-center rounded-lg me-3 {{ request()->routeIs('data-pgs.*') ? 'bg-white/20' : 'bg-violet-100' }}"
                            style="width: 32px; height: 32px; min-width: 32px;">
                            <i class="bi bi-person-gear {{ request()->routeIs('data-pgs.*') ? 'text-white' : 'text-violet-600' }}"
                                style="font-size: 14px;"></i>
                        </div>
                        <span class="fw-medium">Data PGS Pejabat</span>
                        @if (request()->routeIs('data-pgs.*'))
                            <i class="bi bi-chevron-right ms-auto opacity-75"></i>
                        @endif
                    </a>
                </li>

                {{-- Data Penugasan --}}
                <li class="nav-item">
                    <a href="{{ route('data-penugasan.index') }}" @click="sidebarOpen = false"
                        class="nav-link d-flex align-items-center rounded-xl px-4 py-3 {{ request()->routeIs('data-penugasan.*') ? 'text-white shadow-lg is-active' : 'text-slate-600' }}"
                        style="{{ request()->routeIs('data-penugasan.*') ? 'background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);' : '' }}">
                        <div class="d-flex align-items-center justify-content-center rounded-lg me-3 {{ request()->routeIs('data-penugasan.*') ? 'bg-white/20' : 'bg-pink-100' }}"
                            style="width: 32px; height: 32px; min-width: 32px;">
                            <i class="bi bi-arrow-left-right {{ request()->routeIs('data-penugasan.*') ? 'text-white' : 'text-pink-600' }}"
                                style="font-size: 14px;"></i>
                        </div>
                        <span class="fw-medium">Data Penugasan Karyawan</span>
                        @if (request()->routeIs('data-penugasan.*'))
                            <i class="bi bi-chevron-right ms-auto opacity-75"></i>
                        @endif
                    </a>
                </li>

                {{-- Kelola User --}}
                @can(abilities: 'admin')
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" @click="sidebarOpen = false"
                            class="nav-link d-flex align-items-center rounded-xl px-4 py-3 {{ request()->routeIs('users.*') ? 'text-white shadow-lg is-active' : 'text-slate-600' }}"
                            style="{{ request()->routeIs('users.*') ? 'background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);' : '' }}">
                            <div class="d-flex align-items-center justify-content-center rounded-lg me-3 {{ request()->routeIs('users.*') ? 'bg-white/20' : 'bg-red-100' }}"
                                style="width: 32px; height: 32px; min-width: 32px;">
                                <i class="bi bi-person-plus-fill {{ request()->routeIs('users.*') ? 'text-white' : 'text-red-600' }}"
                                    style="font-size: 14px;"></i>
                            </div>
                            <span class="fw-medium">Kelola User</span>
                            @if (request()->routeIs('users.*'))
                                <i class="bi bi-chevron-right ms-auto opacity-75"></i>
                            @endif
                        </a>
                    </li>

                    {{-- Audit Log --}}
                    <li class="nav-item">
                        <a href="{{ route('audit-logs.index') }}" @click="sidebarOpen = false"
                            class="nav-link d-flex align-items-center rounded-xl px-4 py-3 {{ request()->routeIs('audit-logs.*') ? 'text-white shadow-lg is-active' : 'text-slate-600' }}"
                            style="{{ request()->routeIs('audit-logs.*') ? 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);' : '' }}">
                            <div class="d-flex align-items-center justify-content-center rounded-lg me-3 {{ request()->routeIs('audit-logs.*') ? 'bg-white/20' : 'bg-amber-100' }}"
                                style="width: 32px; height: 32px; min-width: 32px;">
                                <i class="bi bi-clock-history {{ request()->routeIs('audit-logs.*') ? 'text-white' : 'text-amber-600' }}"
                                    style="font-size: 14px;"></i>
                            </div>
                            <span class="fw-medium">Audit Log</span>
                            @if (request()->routeIs('audit-logs.*'))
                                <i class="bi bi-chevron-right ms-auto opacity-75"></i>
                            @endif
                        </a>
                    </li>
                @endcan
            </ul>
        </div>

        @can(abilities: 'admin')
            <div class="mb-4">
                <h6 class="text-slate-500 text-uppercase fw-bold small mb-3 px-3"
                    style="font-size: 11px; letter-spacing: 1px;">
                    Database
                </h6>
                <ul class="nav flex-column gap-1">
                    {{-- Versions --}}
                    <li class="nav-item">
                        <a href="{{ route('versions.index') }}" @click="sidebarOpen = false"
                            class="nav-link d-flex align-items-center rounded-xl px-4 py-3 {{ request()->routeIs('versions.*') ? 'text-white shadow-lg is-active' : 'text-slate-600' }}"
                            style="{{ request()->routeIs('versions.*') ? 'background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);' : '' }}">
                            <div class="d-flex align-items-center justify-content-center rounded-lg me-3 {{ request()->routeIs('versions.*') ? 'bg-white/20' : 'bg-violet-100' }}"
                                style="width: 32px; height: 32px; min-width: 32px;">
                                <i class="bi bi-archive-fill {{ request()->routeIs('versions.*') ? 'text-white' : 'text-violet-600' }}"
                                    style="font-size: 14px;"></i>
                            </div>
                            <span class="fw-medium">Versions</span>
                            @if (request()->routeIs('versions.*'))
                                <i class="bi bi-chevron-right ms-auto opacity-75"></i>
                            @endif
                        </a>
                    </li>


                </ul>
            </div>
        @endcan
    </nav>

    {{-- Footer / User Profile & Settings --}}

    <div class="p-4 border-top border-slate-300 mt-auto">
        {{-- User Profile Section --}}
        <a href="{{ route('profile.index') }}" @click="sidebarOpen = false"
            class="d-flex align-items-center mb-3 p-3 rounded-xl bg-white/80 border border-slate-200 text-decoration-none shadow-sm">
            <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
                style="width: 40px; height: 40px; min-width: 40px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                <i class="bi bi-person-fill text-white" style="font-size: 18px;"></i>
            </div>
            <div class="flex-grow-1 text-truncate">
                <p class="mb-1 fw-semibold text-slate-700 small">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="mb-0 text-slate-500" style="font-size: 12px;">
                    {{ Auth::user()->hasRole('admin') ? 'Administrator' : 'User' }}
                </p>
            </div>
            <div class="ms-2">
                <i class="bi bi-chevron-right text-slate-400" style="font-size: 12px;"></i>
            </div>
        </a>

    </div>
</aside>
