@extends('layout')

@section('title', 'TimCockStore - Оформление заказа')

@section('content')
    <main class="checkout" style="max-width: 500px; margin: 50px auto; padding: 20px;">
        <h1>Оформление заказа</h1>

        <form method="post" action="{{ route('checkout.post') }}" style="margin-top: 20px;">
            @csrf
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Имя:</label>
                <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                @error('name')
                    <span style="color: #d9534f; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>Email:</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                @error('email')
                    <span style="color: #d9534f; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label>Адрес доставки:</label>
                <input type="text" name="address" value="{{ old('address') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                @error('address')
                    <span style="color: #d9534f; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label>Телефон:</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                @error('phone')
                    <span style="color: #d9534f; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="button" style="width: 100%; padding: 12px; background-color: #5cb85c; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 16px;">Подтвердить заказ</button>
        </form>
    </main>
@endsection

