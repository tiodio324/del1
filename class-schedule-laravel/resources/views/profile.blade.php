@extends('layout')

@section('title', 'TimCockStore - Личный кабинет')

@section('content')
    <main class="profile" style="max-width: 900px; margin: 50px auto; padding: 20px;">
        <h1>Личный кабинет</h1>

        <section class="profile-info" style="padding: 20px; border: 1px solid #ddd; margin-bottom: 30px; border-radius: 5px;">
            <p><strong>ID:</strong> {{ $user->id }}</p>
            <p><strong>Имя:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Роль:</strong> {{ $user->role }}</p>
        </section>

        <h2>Мои заказы</h2>

        @if ($orders->isEmpty())
            <p>У вас пока нет заказов.</p>
        @else
            @foreach ($orders as $order)
                <div class="order-card" style="padding: 20px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px;">
                    <p><strong>Заказ №{{ $order->order_id }}</strong></p>
                    <p>Дата: {{ $order->created_at->format('d.m.Y H:i') }}</p>
                    <p>Статус: <span style="color: #d9534f; font-weight: bold;">{{ $order->status }}</span></p>
                    <p><strong>Сумма:</strong> {{ number_format($order->total_amount, 2, '.', ' ') }} руб.</p>

                    @if (!empty($orderItems[$order->order_id]))
                        <div class="order-items" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                            <h4>Состав заказа:</h4>
                            <ul style="list-style: none; padding: 0;">
                                @foreach ($orderItems[$order->order_id] as $item)
                                    <li style="padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                                        {{ $item['name'] }} — 
                                        {{ $item['quantity'] }} × 
                                        {{ number_format($item['price'], 2, '.', ' ') }} руб. = 
                                        <strong>{{ number_format($item['price'] * $item['quantity'], 2, '.', ' ') }} руб.</strong>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p style="color: #999; margin-top: 15px;">Нет данных о составе заказа.</p>
                    @endif
                    <hr>
                </div>
            @endforeach
        @endif

        <form action="{{ route('logout') }}" method="POST" style="margin-top: 30px;">
            @csrf
            <button type="submit" class="button" style="padding: 10px 20px; background-color: #d9534f; color: white; border: none; border-radius: 3px; cursor: pointer;">Выйти</button>
        </form>
    </main>
@endsection

