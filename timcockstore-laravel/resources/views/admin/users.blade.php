@extends('layout')

@section('title', 'TimCockStore - Управление пользователями')

@section('content')
    <div style="max-width: 900px; margin: 50px auto; padding: 20px;">
        <h1>Управление пользователями</h1>

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

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                        <th style="padding: 12px; text-align: left; border-right: 1px solid #ddd;">Email</th>
                        <th style="padding: 12px; text-align: left; border-right: 1px solid #ddd;">Имя</th>
                        <th style="padding: 12px; text-align: left; border-right: 1px solid #ddd;">Роль</th>
                        <th style="padding: 12px; text-align: left;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; border-right: 1px solid #ddd;">{{ $user->email }}</td>
                            <td style="padding: 12px; border-right: 1px solid #ddd;">{{ $user->name }}</td>
                            <td style="padding: 12px; border-right: 1px solid #ddd;">
                                <form action="{{ route('admin.update-role') }}" method="POST" style="display: flex; gap: 5px;">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $user->id }}">
                                    <select name="role" style="padding: 5px; border: 1px solid #ddd; border-radius: 3px;">
                                        <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Клиент</option>
                                        <option value="support" {{ $user->role === 'support' ? 'selected' : '' }}>Поддержка</option>
                                        <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Менеджер</option>
                                    </select>
                                    <button type="submit" style="padding: 5px 10px; background-color: #5cb85c; color: white; border: none; border-radius: 3px; cursor: pointer;">✓ Сохранить</button>
                                </form>
                            </td>
                            <td style="padding: 12px;">
                                @if ($user->id !== auth()->id())
                                    <a href="{{ route('admin.delete-user', $user->id) }}" 
                                       onclick="return confirm('Вы уверены?')" 
                                       style="padding: 5px 10px; background-color: #d9534f; color: white; text-decoration: none; border-radius: 3px; cursor: pointer;">❌ Удалить</a>
                                @else
                                    <span style="color: #999;">(Это вы)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 20px; text-align: center; color: #999;">Нет пользователей</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <a href="{{ route('admin.dashboard') }}" style="display: inline-block; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 3px;">← Назад</a>
    </div>
@endsection

