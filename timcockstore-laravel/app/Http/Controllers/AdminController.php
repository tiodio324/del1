<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $this->authorizeManager();
        return view('admin.dashboard');
    }

    public function users(): View
    {
        $this->authorizeManager();
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request): RedirectResponse
    {
        $this->authorizeManager();

        $validated = $request->validate([
            'id' => 'required|exists:users,id',
            'role' => 'required|in:client,support,manager',
        ]);

        $user = User::findOrFail($validated['id']);
        $user->update(['role' => $validated['role']]);

        return redirect()->route('admin.users')->with('success', 'Роль успешно обновлена');
    }

    public function deleteUser($id): RedirectResponse
    {
        $this->authorizeManager();

        $user = User::findOrFail($id);
        
        // Не удаляем администратора если это последний менеджер
        if ($user->role === 'manager' && User::where('role', 'manager')->count() <= 1) {
            return redirect()->route('admin.users')->with('error', 'Нельзя удалить последнего менеджера');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Пользователь удалён');
    }

    private function authorizeManager(): void
    {
        if (Auth::user()->role !== 'manager') {
            abort(403, 'Unauthorized - Manager role required');
        }
    }
}

