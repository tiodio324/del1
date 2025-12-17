@extends('layout')

@section('title', 'TimCockStore - Подтверждение заказа')

@section('content')
    <div class="container" style="max-width: 800px; margin: 50px auto; padding: 20px;">
        <h2>Спасибо за заказ!</h2>

        <div class="order-box" style="padding: 20px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px;">
            <p><strong>Номер заказа:</strong> №{{ $order->order_id }}</p>
            <p><strong>Статус:</strong> {{ $order->status }}</p>
            <p><strong>Дата:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
        </div>

        <h3>Состав заказа</h3>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="background-color: #f5f5f5;">
                <th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Товар</th>
                <th style="border: 1px solid #ddd; padding: 10px; text-align: center;">Количество</th>
                <th style="border: 1px solid #ddd; padding: 10px; text-align: right;">Цена</th>
                <th style="border: 1px solid #ddd; padding: 10px; text-align: right;">Сумма</th>
            </tr>
            @foreach ($items as $item)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 10px;">{{ $item->product->name }}</td>
                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">{{ $item->quantity }}</td>
                    <td style="border: 1px solid #ddd; padding: 10px; text-align: right;">{{ number_format($item->price, 2, '.', ' ') }} ₽</td>
                    <td style="border: 1px solid #ddd; padding: 10px; text-align: right;">{{ number_format($item->price * $item->quantity, 2, '.', ' ') }} ₽</td>
                </tr>
            @endforeach
        </table>

        <div class="order-total" style="margin-bottom: 20px; font-size: 18px; font-weight: bold;">
            <strong>Итого:</strong> {{ number_format($order->total_amount, 2, '.', ' ') }} ₽
        </div>

        <div class="order-actions">
            <a href="{{ route('profile') }}" class="button" style="display: inline-block; padding: 10px 20px; background-color: #5cb85c; color: white; text-decoration: none; border-radius: 3px; margin-right: 10px;">Мои заказы</a>
            <a href="{{ route('catalog') }}" class="button" style="display: inline-block; padding: 10px 20px; background-color: #0275d8; color: white; text-decoration: none; border-radius: 3px;">Продолжить покупки</a>
        </div>
    </div>
@endsection

