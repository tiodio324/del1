@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">📚 Расписание Охтинского Колледжа</h1>
    
    <div class="row">
        @forelse($classrooms as $classroom)
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Класс {{ $classroom->name }}</h5>
                    </div>
                    <div class="card-body">
                        @if($classroom->schedules->count() > 0)
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>День</th>
                                        <th>Время</th>
                                        <th>Предмет</th>
                                        <th>Учитель</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classroom->schedules as $schedule)
                                        <tr>
                                            <td>
                                                {{ ['', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт'][$schedule->day_of_week] }}
                                            </td>
                                            <td>{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                                            <td>{{ $schedule->subject->name }}</td>
                                            <td>{{ $schedule->teacher->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">Расписание отсутствует</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                Классы не добавлены
            </div>
        @endforelse
    </div>

    @if(auth()->check() && auth()->user()->role === 'director')
        <div class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                ⚙️ Управление расписанием
            </a>
        </div>
    @endif
</div>
@endsection

