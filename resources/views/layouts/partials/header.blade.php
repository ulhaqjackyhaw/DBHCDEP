<header class="navbar navbar-expand-lg bg-white shadow-sm"
    style="position: sticky; top: 0; z-index: 1030; height: 64px; padding: 0 1.5rem;">

    <div class="container-fluid d-flex align-items-center justify-content-between p-0">
        {{-- Tombol untuk membuka/menutup sidebar --}}
        <button @click="sidebarOpen = !sidebarOpen" class="btn btn-light me-3" type="button">
            <i class="bi bi-list fs-5"></i>
        </button>

        {{-- Title --}}
        <div class="flex-grow-1 d-none d-md-block text-center" style="min-width: 0;">
            <h1 class="mb-0" style="font-size: 1.25rem; font-weight: 600; color: #334155;">
                @yield('header-title')
            </h1>
        </div>

        {{-- Dropdown Pengguna --}}
        @auth
            <div class="dropdown" style="margin-left: auto;">
                <button class="btn btn-link text-decoration-none d-flex align-items-center p-0" type="button"
                    id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                    style="border: none; background: none;">
                    <img src="{{ auth()->user()->getPhotoUrl() }}" alt="user" class="rounded-circle me-2" width="40"
                        height="40" style="object-fit: cover;">
                    <span class="d-none d-lg-inline text-slate-700" style="font-weight: 500;">
                        {{ Auth::user()->name }}
                    </span>
                    <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 0.75rem;"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="userDropdown"
                    style="min-width: 200px; margin-top: 0.5rem;">
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('profile.index') }}">
                            <i class="bi bi-person-circle me-2"></i> Profil
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0" id="logout-form"
                            onsubmit="handleLogout(event)">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger py-2" id="logout-btn"
                                style="border: none; background: none; width: 100%; text-align: left;">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth
    </div>
</header>

<script>
    function handleLogout(event) {
        event.preventDefault();
        const form = event.target;
        const btn = document.getElementById('logout-btn');

        // Tampilkan loading state
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm me-2"></i> Keluar...';

        // Submit form dengan fetch untuk handle error dengan baik
        fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value
                },
                body: new URLSearchParams(new FormData(form))
            })
            .then(response => {
                if (response.ok || response.redirected) {
                    // Berhasil, redirect ke login
                    window.location.href = '{{ route('login') }}';
                } else if (response.status === 419) {
                    // CSRF token expired
                    window.location.href = '{{ route('login') }}?session_expired=1';
                } else {
                    throw new Error('Logout gagal');
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                // Redirect ke login meskipun error
                window.location.href = '{{ route('login') }}';
            });

        return false;
    }
</script>
