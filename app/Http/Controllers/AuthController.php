<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => __('Sila masukkan alamat emel rasmi YKN.'),
            'password.required' => __('Sila masukkan kata laluan anda.'),
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && ! $user->is_active) {
            return back()
                ->withErrors(['email' => __('Akaun anda tidak aktif. Sila hubungi Unit Pentadbiran YKN.')])
                ->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->flash('show_splash', true);

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['email' => __('Emel atau kata laluan tidak tepat. Sila cuba semula.')])
            ->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', __('Anda telah berjaya log keluar daripada sistem e-Pilot YKN.'));
    }
}
