@extends('layouts.app')

@section('title', 'Мои замены - Расписание')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">👤 Зауч - Одобрение замен</h1>

    @if($substitutions->count() > 0)
        <div class="row">
            @foreach($substitutions as $substitution)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <strong>{{ $substitution->classroom->name }} - {{ $substitution->subject->name }}</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>День:</strong> {{ ['', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница'][$substitution->day_of_week] }}</p>
                            <p><strong>Время:</strong> {{ $substitution->start_time }} - {{ $substitution->end_time }}</p>
                            <p><strong>Учитель:</strong> {{ $substitution->teacher->name }}</p>
                            <p><strong>Аудитория:</strong> {{ $substitution->room_number ?? 'Не указана' }}</p>
                            
                            <div class="btn-group" role="group">
                                <form method="POST" action="{{ route('substitutions.approve', $substitution) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">✅ Одобрить</button>
                                </form>

                                <form method="POST" action="{{ route('substitutions.reject', $substitution) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Вы уверены?')">❌ Отклонить</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            Нет замен для одобрения
        </div>
    @endif
</div>
@endsection

