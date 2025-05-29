{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'HilockSandy'))</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>



    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <!-- Font Awesome (jika Anda menggunakannya untuk ikon) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        main {
            flex: 1;
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        /* Jika navbar tidak ada, main content tidak perlu padding-top sebanyak itu */
        body.no-navbar main {
            padding-top: 0; /* Atau sesuaikan dengan kebutuhan */
        }
        .footer {
            background-color: #e9ecef;
            padding: 1rem 0;
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
        }
        .alert {
            margin-bottom: 1.5rem;
        }
    </style>

    @stack('styles')
</head>
{{-- Tambahkan class 'no-navbar' ke body jika route adalah login atau register --}}
<body class="{{ (request()->routeIs('login') || request()->routeIs('register')) ? 'no-navbar' : '' }}">
    <div id="app">

        {{-- Kondisi untuk menampilkan navbar --}}
        {{-- Navbar hanya akan ditampilkan jika route saat ini BUKAN 'login' DAN BUKAN 'register' --}}
        @if (!request()->routeIs('login') && !request()->routeIs('register'))
            @include('layouts.partials.navbar')
        @endif

        <!-- Page Content -->
        <main>
            {{-- Untuk halaman login dan register, kita mungkin ingin layout yang berbeda (misalnya, konten di tengah) --}}
            {{-- Jika route adalah login atau register, gunakan kontainer yang mungkin lebih sederhana atau full-width --}}
            @if (request()->routeIs('login') || request()->routeIs('register'))
                <div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 70vh;">
                    {{-- Wrapper tambahan untuk card login/register agar bisa di-style --}}
                    <div style="width: 100%; max-width: 420px;">
                        @yield('content')
                    </div>
                </div>
            @else
                {{-- Untuk halaman lain, gunakan kontainer standar --}}
                <div class="container">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading"><i class="fas fa-times-circle me-2"></i>Whoops! Ada beberapa masalah:</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            @endif
        </main>

        {{-- Kondisi untuk menampilkan footer --}}
        {{-- Footer hanya akan ditampilkan jika route saat ini BUKAN 'login' DAN BUKAN 'register' --}}
        @if (!request()->routeIs('login') && !request()->routeIs('register'))
            <footer class="footer mt-auto py-3 bg-light">
                <div class="container text-center">
                    <span class="text-muted">© {{ date('Y') }} {{ config('app.name', 'HilockSandy') }}. All rights reserved.</span>
                </div>
            </footer>
        @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    @stack('scripts')
</body>
</html>