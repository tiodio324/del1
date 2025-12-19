@extends('layout')

@section('title', 'TimCockStore - ' . $product->name)

@section('content')
    <div class="product-details" style="display: flex; gap: 40px; padding: 20px;">
        <div class="product-image" style="flex: 1;">
            <img src="{{ asset('img/' . $product->image) }}" alt="{{ $product->name }}" style="width: 100%; max-width: 500px;">
        </div>
        <div class="product-info" style="flex: 1;">
            <h1>{{ $product->name }}</h1>
            <p class="price" style="font-size: 24px; font-weight: bold; color: #d9534f;">Цена: {{ $product->price }} руб.</p>
            <p class="description">{{ $product->description }}</p>
            @if ($product->category)
                <p><strong>Категория:</strong> {{ $product->category->name }}</p>
            @endif
            <a href="{{ route('cart.add', ['id' => $product->id]) }}" class="button" style="display: inline-block; padding: 10px 20px; background-color: #5cb85c; color: white; text-decoration: none; border-radius: 5px;">В корзину</a>
        </div>
    </div>
@endsection

