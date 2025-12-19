@extends('layout')

@section('title', 'TimCockStore - Каталог')

@section('content')
    <div style="display: flex; gap: 20px;">
        <aside class="filters" style="flex: 0 0 200px;">
            <h3>Фильтры</h3>
            <div class="filter-group">
                <h4>Категории</h4>
                <ul>
                    <li><a href="{{ route('catalog') }}">Все категории</a></li>
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('catalog', ['category' => $category->id]) }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <section class="product-list" style="flex: 1;">
            <h1>Каталог товаров</h1>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                @forelse ($products as $product)
                    <div class="product-item">
                        <img src="{{ asset('img/' . $product->image) }}" alt="{{ $product->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                        <h3>{{ $product->name }}</h3>
                        <p>Цена: {{ $product->price }} руб.</p>
                        <a href="{{ route('cart.add', ['id' => $product->id]) }}" class="btn-buy">В корзину</a>
                    </div>
                @empty
                    <p>Товаров не найдено</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection

