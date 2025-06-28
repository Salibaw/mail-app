<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

         return redirect()->intended(
            $this->redirectPathByRole(Auth::user()->role->name)
        );
    }
    protected function redirectPathByRole(string $role): string
    {
        switch ($role) {
            case 'admin':
                return route('admin.dashboard');
            case 'pimpinan':
                return route('pimpinan.dashboard');
            case 'dosen':
                return route('staff.dashboard');
            case 'staff':
                return route('staff.dashboard');
            case 'mahasiswa':
                return route('mahasiswa.dashboard');
            default:
                return route('mahasiswa.dashboard');
        }
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
