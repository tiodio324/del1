<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Создание тестовых пользователей
        $director = User::create([
            'name' => 'Директор Иванов',
            'email' => 'director@okhta.ru',
            'password' => Hash::make('password'),
            'role' => 'director',
        ]);

        $deputy = User::create([
            'name' => 'Зауч Петрова',
            'email' => 'deputy@okhta.ru',
            'password' => Hash::make('password'),
            'role' => 'deputy',
        ]);

        $student1 = User::create([
            'name' => 'Студент Сидоров',
            'email' => 'student@okhta.ru',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $student2 = User::create([
            'name' => 'Студент Смирнов',
            'email' => 'student2@okhta.ru',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        // Создание предметов
        $subjects = [
            ['name' => 'Математика', 'code' => 'MAT', 'description' => 'Алгебра и геометрия'],
            ['name' => 'История', 'code' => 'HIS', 'description' => 'История России и мира'],
            ['name' => 'Литература', 'code' => 'LIT', 'description' => 'Русская литература'],
            ['name' => 'Физика', 'code' => 'PHY', 'description' => 'Основы физики'],
            ['name' => 'Английский язык', 'code' => 'ENG', 'description' => 'Иностранный язык'],
            ['name' => 'Информатика', 'code' => 'CS', 'description' => 'Компьютерные технологии'],
        ];

        $subjectModels = [];
        foreach ($subjects as $subject) {
            $subjectModels[] = Subject::create($subject);
        }

        // Создание классов
        $classrooms = [
            ['name' => '10-А', 'level' => 10, 'capacity' => 30, 'room_number' => '201'],
            ['name' => '10-Б', 'level' => 10, 'capacity' => 28, 'room_number' => '202'],
            ['name' => '11-А', 'level' => 11, 'capacity' => 32, 'room_number' => '301'],
            ['name' => '11-Б', 'level' => 11, 'capacity' => 30, 'room_number' => '302'],
        ];

        $classroomModels = [];
        foreach ($classrooms as $classroom) {
            $classroomModels[] = ClassRoom::create($classroom);
        }

        // Создание расписания для класса 10-А (Пн-Пт)
        $class10a = $classroomModels[0];
        
        // Понедельник
        Schedule::create([
            'class_id' => $class10a->id,
            'subject_id' => $subjectModels[0]->id, // Математика
            'teacher_id' => $director->id,
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '09:45',
            'room_number' => '201',
            'is_active' => true,
        ]);

        Schedule::create([
            'class_id' => $class10a->id,
            'subject_id' => $subjectModels[1]->id, // История
            'teacher_id' => $deputy->id,
            'day_of_week' => 1,
            'start_time' => '09:55',
            'end_time' => '10:40',
            'room_number' => '201',
            'is_active' => true,
        ]);

        Schedule::create([
            'class_id' => $class10a->id,
            'subject_id' => $subjectModels[2]->id, // Литература
            'teacher_id' => $director->id,
            'day_of_week' => 1,
            'start_time' => '11:00',
            'end_time' => '11:45',
            'room_number' => '201',
            'is_active' => true,
        ]);

        // Вторник
        Schedule::create([
            'class_id' => $class10a->id,
            'subject_id' => $subjectModels[3]->id, // Физика
            'teacher_id' => $deputy->id,
            'day_of_week' => 2,
            'start_time' => '09:00',
            'end_time' => '09:45',
            'room_number' => '201',
            'is_active' => true,
        ]);

        Schedule::create([
            'class_id' => $class10a->id,
            'subject_id' => $subjectModels[4]->id, // Английский язык
            'teacher_id' => $director->id,
            'day_of_week' => 2,
            'start_time' => '09:55',
            'end_time' => '10:40',
            'room_number' => '201',
            'is_active' => true,
        ]);

        // Среда
        Schedule::create([
            'class_id' => $class10a->id,
            'subject_id' => $subjectModels[5]->id, // Информатика
            'teacher_id' => $deputy->id,
            'day_of_week' => 3,
            'start_time' => '09:00',
            'end_time' => '09:45',
            'room_number' => '201',
            'is_active' => true,
        ]);

        Schedule::create([
            'class_id' => $class10a->id,
            'subject_id' => $subjectModels[0]->id, // Математика
            'teacher_id' => $director->id,
            'day_of_week' => 3,
            'start_time' => '09:55',
            'end_time' => '10:40',
            'room_number' => '201',
            'is_active' => true,
        ]);

        // Четверг
        Schedule::create([
            'class_id' => $class10a->id,
            'subject_id' => $subjectModels[1]->id, // История
            'teacher_id' => $deputy->id,
            'day_of_week' => 4,
            'start_time' => '09:00',
            'end_time' => '09:45',
            'room_number' => '201',
            'is_active' => true,
        ]);

        Schedule::create([
            'class_id' => $class10a->id,
            'subject_id' => $subjectModels[2]->id, // Литература
            'teacher_id' => $director->id,
            'day_of_week' => 4,
            'start_time' => '09:55',
            'end_time' => '10:40',
            'room_number' => '201',
            'is_active' => true,
        ]);

        // Пятница
        Schedule::create([
            'class_id' => $class10a->id,
            'subject_id' => $subjectModels[3]->id, // Физика
            'teacher_id' => $deputy->id,
            'day_of_week' => 5,
            'start_time' => '09:00',
            'end_time' => '09:45',
            'room_number' => '201',
            'is_active' => true,
        ]);

        Schedule::create([
            'class_id' => $class10a->id,
            'subject_id' => $subjectModels[4]->id, // Английский язык
            'teacher_id' => $director->id,
            'day_of_week' => 5,
            'start_time' => '09:55',
            'end_time' => '10:40',
            'room_number' => '201',
            'is_active' => true,
        ]);
    }
}
