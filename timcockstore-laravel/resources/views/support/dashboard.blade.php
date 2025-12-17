@extends('layout')

@section('title', 'TimCockStore - Поддержка')

@section('content')
    <main style="max-width: 1200px; margin: 50px auto; padding: 20px;">
        <h1>Панель поддержки</h1>
        <p>Добро пожаловать, {{ auth()->user()->name }}!</p>
        
        <div style="margin-top: 30px;">
            <h2>Функции поддержки:</h2>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 10px; border-bottom: 1px solid #eee;">
                    <a href="#tickets" style="text-decoration: none; color: #0275d8;">🎫 Мои обращения</a>
                </li>
                <li style="padding: 10px; border-bottom: 1px solid #eee;">
                    <a href="#new-ticket" style="text-decoration: none; color: #0275d8;">➕ Создать обращение</a>
                </li>
                <li style="padding: 10px; border-bottom: 1px solid #eee;">
                    <a href="#faq" style="text-decoration: none; color: #0275d8;">❓ FAQ</a>
                </li>
            </ul>
        </div>
    </main>
@endsection

