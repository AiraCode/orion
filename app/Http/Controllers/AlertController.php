<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\GameAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();

        $alerts = GameAlert::where('company_id', $company->id)->latest('game_date')->latest('id')->get();

        return view('views.notifications', compact('company', 'alerts'));
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();

        GameAlert::where('company_id', $company->id)->update(['is_read' => true]);

        return redirect()->route('notifications.index')->with('success', 'All alerts marked as read.');
    }
}
