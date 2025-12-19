<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    /**
     * Admin page to manage all support tickets
     */
    public function adminIndex(): View
    {
        $this->authorizeManager();
        
        $tickets = SupportTicket::with(['user', 'support'])->get();
        $supporters = User::where('role', 'support')->get();

        return view('admin.support-tickets', compact('tickets', 'supporters'));
    }

    /**
     * Assign support ticket to a support person (Fix for the bug!)
     */
    public function assign(Request $request): RedirectResponse
    {
        $this->authorizeManager();

        $validated = $request->validate([
            'ticket_id' => 'required|exists:support_tickets,id',
            'support_id' => 'required|exists:users,id',
        ]);

        $ticket = SupportTicket::findOrFail($validated['ticket_id']);
        $ticket->update(['support_id' => $validated['support_id']]);

        return redirect()->route('admin.support-tickets')->with('success', 'Специалист успешно назначен');
    }

    private function authorizeManager(): void
    {
        if (Auth::user()->role !== 'manager') {
            abort(403, 'Unauthorized - Manager role required');
        }
    }
}

