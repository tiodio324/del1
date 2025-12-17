@extends('layout')

@section('title', 'TimCockStore - Корзина')

@section('content')
    <h1>Корзина</h1>

    @if (empty($products))
        <p>Ваша корзина пуста.</p>
        <a href="{{ route('catalog') }}" class="button">Вернуться в каталог</a>
    @else
        <div class="cart-items">
            @foreach ($products as $product)
                <div class="cart-item" style="display: flex; gap: 20px; padding: 15px; border: 1px solid #ddd; margin-bottom: 10px; align-items: center;">
                    <img src="{{ asset('img/' . $product['image']) }}" alt="{{ $product['name'] }}" style="width: 100px; height: 100px; object-fit: cover;">
                    <div class="cart-item-info" style="flex: 1;">
                        <h3>{{ $product['name'] }}</h3>
                        <p>Цена: {{ number_format($product['price'], 2, '.', ' ') }} руб.</p>
                        <p>Количество: {{ $product['quantity'] }}</p>
                        <p><strong>Сумма: {{ number_format($product['sum'], 2, '.', ' ') }} руб.</strong></p>
                    </div>
                    <a href="{{ route('cart.remove', ['id' => $product['id']]) }}" class="button" style="padding: 8px 15px; background-color: #d9534f; color: white; text-decoration: none; border-radius: 3px;">Удалить</a>
                </div>
            @endforeach
        </div>

        <div class="cart-total" style="margin-top: 20px; padding: 20px; border-top: 2px solid #333;">
            <p style="font-size: 20px; font-weight: bold;">Итого: {{ number_format($totalPrice, 2, '.', ' ') }} руб.</p>

            @auth
                <form action="{{ route('checkout.post') }}" method="post" style="margin-top: 15px;">
                    @csrf
                    <input type="hidden" name="total_amount" value="{{ $totalPrice }}">
                    <button type="submit" class="button" style="padding: 12px 30px; background-color: #5cb85c; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Оформить заказ</button>
                </form>
            @else
                <p><a href="{{ route('login') }}">Войдите</a>, чтобы оформить заказ</p>
            @endauth
        </div>
    @endif
@endsection

