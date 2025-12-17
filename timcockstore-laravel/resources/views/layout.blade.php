<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TimCockStore')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header>
        <div class="logo">
            <a href="{{ route('home') }}">TimCockStore</a>
        </div>
        <nav>
            <ul>
                <li><a href="{{ route('catalog') }}">Каталог</a></li>
                <li><a href="{{ route('cart.index') }}">Корзина</a></li>
                @auth
                    @if(auth()->user()->role === 'manager')
                        <li><a href="{{ route('manager.dashboard') }}">Управление</a></li>
                    @elseif(auth()->user()->role === 'support')
                        <li><a href="{{ route('support.dashboard') }}">Поддержка</a></li>
                    @endif
                    <li><a href="{{ route('profile') }}">Личный кабинет</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; cursor: pointer; color: inherit;">Выйти</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Вход</a></li>
                    <li><a href="{{ route('register') }}">Регистрация</a></li>
                @endauth
            </ul>
        </nav>
        <div class="search">
            <input type="text" placeholder="Поиск...">
            <button>Найти</button>
        </div>
    </header>

    <main>
        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="success-message">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        <div class="container">
            <div class="footer-info">
                <p>© 2024 TimCockStore</p>
            </div>
            <div class="social-links"></div>
            <div class="contact-info"></div>
        </div>
    </footer>
</body>
</html>

