@extends('layout')

@section('title', 'TimCockStore - Регистрация')

@section('content')
    <main class="register" style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h1>Регистрация</h1>

        <form method="post" action="{{ route('register.post') }}" style="margin-top: 20px;">
            @csrf
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="name">Имя:</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                @error('name')
                    <span style="color: #d9534f; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                @error('email')
                    <span style="color: #d9534f; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                @error('password')
                    <span style="color: #d9534f; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="password_confirmation">Подтвердите пароль:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
            </div>

            <button type="submit" class="button" style="width: 100%; padding: 10px; background-color: #5cb85c; color: white; border: none; border-radius: 3px; cursor: pointer;">Зарегистрироваться</button>
        </form>

        <p style="margin-top: 20px; text-align: center;">
            Уже есть аккаунт? <a href="{{ route('login') }}">Войдите</a>
        </p>
    </main>
@endsection

