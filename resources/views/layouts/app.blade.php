<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Invoice App')</title> {{-- Judul halaman dinamis, defaultnya 'Invoice App' --}}

    {{-- Menggunakan CDN (Content Delivery Network) untuk Bootstrap CSS agar tidak perlu setup lokal --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Style kustom tambahan --}}
    <style>
        body {
            padding-top: {{ request()->routeIs('login') || request()->routeIs('register') ? '0' : '20px' }}; /* Memberi sedikit ruang di atas, kecuali di login/register */
            background-color: #f8f9fa; /* Warna latar belakang body sedikit abu-abu */
        }
        .container {
            background-color: #fff; /* Kontainer utama berwarna putih */
            padding: 20px;
            border-radius: 8px; /* Sudut melengkung */
            box-shadow: 0 0 10px rgba(0,0,0,0.1); /* Bayangan halus */
        }
        .table th, .table td {
            vertical-align: middle; /* Menengahkan teks secara vertikal di sel tabel */
        }
        .action-buttons a, .action-buttons button {
            margin-right: 5px; /* Jarak antar tombol aksi di tabel */
        }
        /* Anda bisa menambahkan lebih banyak style kustom di sini */
    </style>

    @stack('styles') {{-- Placeholder untuk menambahkan CSS spesifik per halaman --}}
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100"> {{-- Atau class body biasa Anda --}}
        {{-- Conditionally display navbar --}}
        @if (!request()->routeIs('login') && !request()->routeIs('register'))
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ auth()->check() ? route('dashboard') : url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('invoices.index') || request()->routeIs('invoices.show') ? 'active' : '' }}" href="{{ route('invoices.index') }}">View Invoices</a>
                            </li>
                            @if(Auth::user()->role === 'admin')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('invoices.create') ? 'active' : '' }}" href="{{ route('invoices.create') }}">Create Invoice</a>
                                </li>
                                {{-- Link lain khusus admin --}}
                            @endif
                        @endauth
                    </ul> {{-- This closing </ul> was misplaced in your original code, I've moved it here --}}
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        @guest {{-- Hanya tampil jika user adalah tamu (belum login) --}}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                                </li>
                            @endif
                        @else {{-- Tampil jika user sudah login --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                                    {{-- Jika Anda membuat halaman profile --}}
                                    {{-- <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li> --}}
                                    {{-- <li><hr class="dropdown-divider"></li> --}}
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                                    onclick="event.preventDefault();
                                                                this.closest('form').submit();">
                                                {{ __('Logout') }}
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        @endif

        <main>
            {{--
                The class for main container was 'container' in your custom CSS,
                but 'main-container' in the HTML. I've used 'container' to match the CSS.
                Adjust if 'main-container' has other specific styles not shown.
            --}}
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Whoops! Something went wrong.</strong>
                        <ul class="mt-2 mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
