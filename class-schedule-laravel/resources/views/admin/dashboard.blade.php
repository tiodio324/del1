@extends('layouts.app')

@section('title', 'Администрация - Расписание')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">⚙️ Администрация</h1>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">📚 Классы</h5>
                    <p class="display-6">{{ \App\Models\ClassRoom::count() }}</p>
                    <a href="{{ route('admin.classrooms') }}" class="btn btn-primary btn-sm">Управление</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">📖 Предметы</h5>
                    <p class="display-6">{{ \App\Models\Subject::count() }}</p>
                    <a href="{{ route('admin.subjects') }}" class="btn btn-primary btn-sm">Управление</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">⏰ Расписание</h5>
                    <p class="display-6">{{ \App\Models\Schedule::where('is_active', true)->count() }}</p>
                    <a href="{{ route('admin.schedules') }}" class="btn btn-primary btn-sm">Управление</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">🔄 Замены</h5>
                    <p class="display-6">{{ \App\Models\Schedule::where('is_active', false)->count() }}</p>
                    <a href="{{ route('admin.substitutions') }}" class="btn btn-primary btn-sm">Управление</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
