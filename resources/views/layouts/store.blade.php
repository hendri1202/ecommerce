<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Toko Online'))</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    @stack('styles')
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('home') }}">
                {{ config('app.name', 'Toko Online') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}">Admin Produk</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.orders.index') }}">Admin Pesanan</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('messages.index') }}">Pesan</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('profile.edit') }}">Profil</a></li>
                        @else
                            <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">Cart</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('wishlist.index') }}">Wishlist</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('orders.history') }}">Riwayat Belanja</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('messages.index') }}">Pesan</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('profile.edit') }}">Profil</a></li>
                        @endif
                    @endauth
                </ul>
                <div class="d-flex gap-2">
                    @auth
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-top py-3">
        <div class="container d-flex justify-content-between text-muted small">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'Toko Online') }}</span>
            <span>Teknofo Storefront</span>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

    @stack('scripts')
</body>
</html>
