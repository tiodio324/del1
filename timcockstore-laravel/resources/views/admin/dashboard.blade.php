@extends('layout')

@section('title', 'TimCockStore - Административная панель')

@section('content')
    <div style="max-width: 1200px; margin: 50px auto; padding: 20px;">
        <h1>Административная панель</h1>

        @if (session('success'))
            <div style="padding: 15px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
            <div style="padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
                <h3>👥 Пользователи</h3>
                <p>Управление пользователями и их ролями</p>
                <a href="{{ route('admin.users') }}" style="display: inline-block; padding: 10px 20px; background-color: #0275d8; color: white; text-decoration: none; border-radius: 3px; margin-top: 10px;">Перейти</a>
            </div>

            <div style="padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
                <h3>🎫 Обращения в поддержку</h3>
                <p>Управление обращениями и назначение специалистов</p>
                <a href="{{ route('admin.support-tickets') }}" style="display: inline-block; padding: 10px 20px; background-color: #0275d8; color: white; text-decoration: none; border-radius: 3px; margin-top: 10px;">Перейти</a>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ route('home') }}" style="display: inline-block; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 3px;">← На сайт</a>
        </div>
    </div>
@endsection

