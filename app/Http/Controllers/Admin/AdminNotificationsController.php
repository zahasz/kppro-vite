<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationsController extends Controller
{
    /**
     * Wyświetla listę powiadomień administracyjnych
     */
    public function index()
    {
        $notifications = AdminNotification::orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Oznacza wszystkie powiadomienia jako przeczytane
     */
    public function markAllRead()
    {
        AdminNotification::where('read', false)
            ->update(['read' => true]);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Wszystkie powiadomienia zostały oznaczone jako przeczytane.');
    }

    /**
     * Oznacza pojedyncze powiadomienie jako przeczytane
     */
    public function markRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->read = true;
        $notification->save();

        return redirect()->back()
            ->with('success', 'Powiadomienie zostało oznaczone jako przeczytane.');
    }

    /**
     * Usuwa powiadomienie
     */
    public function delete($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Powiadomienie zostało usunięte.');
    }

    /**
     * Wyświetla stronę ustawień powiadomień
     */
    public function settings()
    {
        return view('admin.notifications.settings');
    }

    /**
     * Aktualizuje ustawienia powiadomień
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'invoice_notifications' => 'boolean',
            'subscription_notifications' => 'boolean',
            'report_notifications' => 'boolean',
        ]);

        // Zapisz ustawienia dla zalogowanego administratora
        $admin = auth()->user();
        $admin->notification_settings = $request->only([
            'email_notifications',
            'invoice_notifications', 
            'subscription_notifications',
            'report_notifications'
        ]);
        $admin->save();

        return redirect()->route('admin.notifications.settings')
            ->with('success', 'Ustawienia powiadomień zostały zaktualizowane.');
    }
} 