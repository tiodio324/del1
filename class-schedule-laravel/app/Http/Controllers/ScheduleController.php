<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\Subject;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Show the main schedule page
     */
    public function index()
    {
        $classrooms = ClassRoom::with('schedules.subject', 'schedules.teacher')->get();
        return view('schedule.index', compact('classrooms'));
    }

    /**
     * Show schedule for specific classroom
     */
    public function show(ClassRoom $classroom)
    {
        $classroom->load('schedules.subject', 'schedules.teacher');
        return view('schedule.show', compact('classroom'));
    }

    /**
     * Director dashboard
     */
    public function admin()
    {
        return view('admin.dashboard');
    }

    /**
     * Show classrooms management page
     */
    public function classrooms()
    {
        $classrooms = ClassRoom::all();
        return view('admin.classrooms', compact('classrooms'));
    }

    /**
     * Store a new classroom
     */
    public function storeClassroom(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:classrooms',
            'level' => 'required|integer',
            'capacity' => 'required|integer',
            'room_number' => 'nullable|string',
        ]);

        ClassRoom::create($validated);
        return redirect()->route('admin.classrooms')->with('success', 'Класс добавлен');
    }

    /**
     * Update classroom
     */
    public function updateClassroom(Request $request, ClassRoom $classroom)
    {
        $validated = $request->validate([
            'name' => 'required|unique:classrooms,name,' . $classroom->id,
            'level' => 'required|integer',
            'capacity' => 'required|integer',
            'room_number' => 'nullable|string',
        ]);

        $classroom->update($validated);
        return redirect()->route('admin.classrooms')->with('success', 'Класс обновлен');
    }

    /**
     * Delete classroom
     */
    public function destroyClassroom(ClassRoom $classroom)
    {
        $classroom->delete();
        return redirect()->route('admin.classrooms')->with('success', 'Класс удален');
    }

    /**
     * Show subjects management page
     */
    public function subjects()
    {
        $subjects = Subject::all();
        return view('admin.subjects', compact('subjects'));
    }

    /**
     * Store a new subject
     */
    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:subjects',
            'code' => 'nullable|unique:subjects',
            'description' => 'nullable|string',
        ]);

        Subject::create($validated);
        return redirect()->route('admin.subjects')->with('success', 'Предмет добавлен');
    }

    /**
     * Update subject
     */
    public function updateSubject(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|unique:subjects,name,' . $subject->id,
            'code' => 'nullable|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
        ]);

        $subject->update($validated);
        return redirect()->route('admin.subjects')->with('success', 'Предмет обновлен');
    }

    /**
     * Delete subject
     */
    public function destroySubject(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects')->with('success', 'Предмет удален');
    }

    /**
     * Show schedules management page
     */
    public function schedules()
    {
        $schedules = Schedule::with('classroom', 'subject', 'teacher')->get();
        $classrooms = ClassRoom::all();
        $subjects = Subject::all();
        $teachers = \App\Models\User::where('role', 'director')->orWhere('role', 'deputy')->get();
        
        return view('admin.schedules', compact('schedules', 'classrooms', 'subjects', 'teachers'));
    }

    /**
     * Store a new schedule
     */
    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|integer|between:1,5',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'room_number' => 'nullable|string',
        ]);

        Schedule::create($validated);
        return redirect()->route('admin.schedules')->with('success', 'Расписание добавлено');
    }

    /**
     * Update schedule
     */
    public function updateSchedule(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|integer|between:1,5',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'room_number' => 'nullable|string',
        ]);

        $schedule->update($validated);
        return redirect()->route('admin.schedules')->with('success', 'Расписание обновлено');
    }

    /**
     * Delete schedule
     */
    public function destroySchedule(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules')->with('success', 'Расписание удалено');
    }

    /**
     * Show substitutions management page
     */
    public function substitutions()
    {
        $substitutions = Schedule::where('is_active', false)->with('classroom', 'subject', 'teacher')->get();
        $schedules = Schedule::where('is_active', true)->get();
        
        return view('admin.substitutions', compact('substitutions', 'schedules'));
    }

    /**
     * Store a new substitution
     */
    public function storeSubstitution(Request $request)
    {
        $validated = $request->validate([
            'original_schedule_id' => 'required|exists:schedules,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        // Create substitution (mark original as inactive and create new schedule)
        $original = Schedule::find($validated['original_schedule_id']);
        
        $substitution = Schedule::create([
            'class_id' => $original->class_id,
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'day_of_week' => $original->day_of_week,
            'start_time' => $original->start_time,
            'end_time' => $original->end_time,
            'room_number' => $original->room_number,
            'is_active' => false, // Waiting for approval
        ]);

        return redirect()->route('admin.substitutions')->with('success', 'Замена создана');
    }

    /**
     * Update substitution
     */
    public function updateSubstitution(Request $request, Schedule $substitution)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $substitution->update($validated);
        return redirect()->route('admin.substitutions')->with('success', 'Замена обновлена');
    }

    /**
     * Delete substitution
     */
    public function destroySubstitution(Schedule $substitution)
    {
        $substitution->delete();
        return redirect()->route('admin.substitutions')->with('success', 'Замена удалена');
    }

    /**
     * Deputy (Зауч) dashboard
     */
    public function deputyDashboard()
    {
        $substitutions = Schedule::where('is_active', false)->with('classroom', 'subject', 'teacher')->get();
        return view('deputy.dashboard', compact('substitutions'));
    }

    /**
     * Approve substitution
     */
    public function approveSubstitution(Schedule $substitution)
    {
        $substitution->update(['is_active' => true]);
        return redirect()->route('deputy.dashboard')->with('success', 'Замена одобрена');
    }

    /**
     * Reject substitution
     */
    public function rejectSubstitution(Schedule $substitution)
    {
        $substitution->delete();
        return redirect()->route('deputy.dashboard')->with('success', 'Замена отклонена');
    }
}

