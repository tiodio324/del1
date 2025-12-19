<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Расписание Охтинского Колледжа')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #3b82f6;
            --accent-color: #f59e0b;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 0.5px;
        }

        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-color) -10%, var(--secondary-color) 110%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(30, 58, 138, 0.3);
        }

        .table {
            background-color: white;
        }

        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 30px 0;
            margin-top: 60px;
        }

        .badge-student {
            background-color: #06b6d4;
        }

        .badge-deputy {
            background-color: #f59e0b;
        }

        .badge-director {
            background-color: #ef4444;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">📚 Расписание</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Главная</a>
                    </li>
                    @auth
                        @if(auth()->user()->role === 'director')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">⚙️ Администрация</a>
                            </li>
                        @elseif(auth()->user()->role === 'deputy')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('deputy.dashboard') }}">👤 Мои замены</a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }} 
                                <span class="badge badge-{{ auth()->user()->role === 'student' ? 'student' : (auth()->user()->role === 'deputy' ? 'deputy' : 'director') }}">
                                    @if(auth()->user()->role === 'student')
                                        Ученик
                                    @elseif(auth()->user()->role === 'deputy')
                                        Зауч
                                    @elseif(auth()->user()->role === 'director')
                                        Директор
                                    @endif
                                </span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#">Профиль</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Выход</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Вход</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @unless(request()->is('/') || request()->is('login') || request()->is('register'))
    @else
        @if(request()->is('/'))
            <div class="hero">
                <div class="container">
                    <h1>📚 Расписание Охтинского Колледжа</h1>
                    <p class="lead">Эффективное управление расписанием и заменами</p>
                </div>
            </div>
        @endif
    @endunless

    <!-- Main Content -->
    <main>
        @if($message = Session::get('success'))
            <div class="container mt-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="container mt-4">
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>📚 О системе</h5>
                    <p>Современная система управления расписанием для Охтинского колледжа.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>👥 Роли</h5>
                    <ul class="list-unstyled">
                        <li>👨‍🎓 Ученик - просмотр расписания</li>
                        <li>👤 Зауч - одобрение замен</li>
                        <li>⚙️ Директор - полное управление</li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>📞 Контакты</h5>
                    <p>
                        Охтинский колледж<br>
                        Email: info@okhta.ru<br>
                        Тел: +7 (XXX) XXX-XX-XX
                    </p>
                </div>
            </div>
            <hr class="bg-white my-4">
            <div class="text-center">
                <p>&copy; 2025 Расписание Охтинского Колледжа. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

