@extends('layout')

@section('title', 'TimCockStore - Управление обращениями в поддержку')

@section('content')
    <div style="max-width: 1000px; margin: 50px auto; padding: 20px;">
        <h1>Обращения в поддержку</h1>

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

        @forelse ($tickets as $ticket)
            <div style="padding: 20px; border: 1px solid #ddd; margin-bottom: 15px; border-radius: 5px; background: #f9f9f9;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin: 0 0 5px 0;">
                            <strong>#{{ $ticket->id }}</strong> — {{ htmlspecialchars($ticket->subject) }}
                        </h3>
                        <p style="margin: 5px 0; color: #666;">
                            От: <strong>{{ $ticket->user->name }}</strong> ({{ $ticket->user->email }})
                        </p>
                        <p style="margin: 5px 0; color: #666;">
                            Статус: <span style="padding: 3px 8px; border-radius: 3px; background-color: #e3f2fd; color: #1976d2; font-size: 12px;">
                                {{ $ticket->status }}
                            </span>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        @if ($ticket->support)
                            <p style="margin: 0; color: #666; font-size: 12px;">Назначен:</p>
                            <p style="margin: 5px 0; font-weight: bold;">{{ $ticket->support->name }}</p>
                        @else
                            <p style="margin: 0; color: #999; font-size: 12px;">Не назначен</p>
                        @endif
                    </div>
                </div>

                <div style="padding: 15px; background-color: white; border: 1px solid #eee; border-radius: 3px; margin-bottom: 15px;">
                    <p style="margin: 0; white-space: pre-wrap;">{{ htmlspecialchars($ticket->description) }}</p>
                </div>

                <!-- ✨ ИСПРАВЛЕННЫЙ КОД: Предварительно получаем всех поддержчиков вне цикла -->
                <form action="{{ route('admin.assign-ticket') }}" method="POST" style="display: flex; gap: 10px; align-items: flex-end;">
                    @csrf
                    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                    
                    <div style="flex: 1;">
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">Назначить специалиста:</label>
                        <select name="support_id" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                            <option value="">-- Выберите специалиста --</option>
                            @foreach ($supporters as $supporter)
                                <option value="{{ $supporter->id }}" {{ $ticket->support_id === $supporter->id ? 'selected' : '' }}>
                                    {{ $supporter->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" style="padding: 8px 20px; background-color: #5cb85c; color: white; border: none; border-radius: 3px; cursor: pointer;">✓ Сохранить</button>
                </form>
            </div>
        @empty
            <div style="padding: 30px; text-align: center; color: #999; border: 1px solid #ddd; border-radius: 5px;">
                Нет обращений в поддержку
            </div>
        @endforelse

        <a href="{{ route('admin.dashboard') }}" style="display: inline-block; padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 3px; margin-top: 20px;">← Назад</a>
    </div>
@endsection

