@extends('layout')

@section('title', 'TimCockStore - Управление')

@section('content')
    <main style="max-width: 1200px; margin: 50px auto; padding: 20px;">
        <h1>Панель управления (Менеджер)</h1>
        <p>Добро пожаловать, {{ auth()->user()->name }}!</p>
        
        <div style="margin-top: 30px;">
            <h2>Функции управления:</h2>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 10px; border-bottom: 1px solid #eee;">
                    <a href="#products" style="text-decoration: none; color: #0275d8;">📦 Управление товарами</a>
                </li>
                <li style="padding: 10px; border-bottom: 1px solid #eee;">
                    <a href="#categories" style="text-decoration: none; color: #0275d8;">📂 Управление категориями</a>
                </li>
                <li style="padding: 10px; border-bottom: 1px solid #eee;">
                    <a href="#orders" style="text-decoration: none; color: #0275d8;">📋 Управление заказами</a>
                </li>
                <li style="padding: 10px; border-bottom: 1px solid #eee;">
                    <a href="#users" style="text-decoration: none; color: #0275d8;">👥 Управление пользователями</a>
                </li>
                <li style="padding: 10px; border-bottom: 1px solid #eee;">
                    <a href="#statistics" style="text-decoration: none; color: #0275d8;">📊 Статистика</a>
                </li>
            </ul>
        </div>
    </main>
@endsection

