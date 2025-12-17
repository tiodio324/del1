@extends('layout')

@section('title', 'TimCockStore - Главная')

@section('content')
    <section class="banner">
        <h1>Добро пожаловать в TimCockStore!</h1>
        <p>Лучшая техника Apple и других брендов по доступным ценам.</p>
    </section>

    <section class="popular-products">
        <h2>Популярные товары</h2>
        <div class="product-list">
            @forelse ($popularProducts as $product)
                <div class="product-item">
                    <img src="{{ asset('img/' . $product->image) }}" alt="{{ $product->name }}">
                    <h3>{{ $product->name }}</h3>
                    <p>Цена: {{ $product->price }} руб.</p>
                    <a href="{{ route('cart.add', ['id' => $product->id]) }}" class="btn-buy">В корзину</a>
                </div>
            @empty
                <p>Нет доступных товаров</p>
            @endforelse
        </div>
    </section>

    <section class="акции">
        <h2>Акции</h2>
        <div class="product-list">
            @forelse ($saleProducts as $product)
                <div class="product-item">
                    <img src="{{ asset('img/' . $product->image) }}" alt="{{ $product->name }}">
                    <h3>{{ $product->name }}</h3>
                    <p>Цена: {{ $product->price }} руб.</p>
                    <a href="{{ route('cart.add', ['id' => $product->id]) }}" class="btn-buy">В корзину</a>
                </div>
            @empty
                <p>Нет доступных товаров</p>
            @endforelse
        </div>
    </section>
@endsection

