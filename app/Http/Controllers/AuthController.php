<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // =========================
    // FORM LOGIN
    // =========================
    public function loginForm()
    {
        return view('auth.login');
    }

    // =========================
    // PROSES LOGIN
    // =========================
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // DATA BENAR (SESUI PERMINTAAN)
        $emailBenar    = 'syarahyanti013@gmail.com';
        $passwordBenar = 'syarah1234';

        $emailInput    = $request->email;
        $passwordInput = $request->password;

        // =========================
        // EMAIL BENAR, PASSWORD SALAH
        // =========================
        if ($emailInput === $emailBenar && $passwordInput !== $passwordBenar) {
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah',
            ]);
        }

        // =========================
        // EMAIL SALAH, PASSWORD BENAR
        // =========================
        if ($emailInput !== $emailBenar && $passwordInput === $passwordBenar) {
            return back()->withErrors([
                'email' => 'Email yang Anda masukkan salah',
            ]);
        }

        // =========================
        // EMAIL & PASSWORD SALAH
        // =========================
        if ($emailInput !== $emailBenar && $passwordInput !== $passwordBenar) {
            return back()->withErrors([
                'login' => 'Email dan password salah',
            ]);
        }

        // =========================
        // LOGIN BERHASIL
        // =========================
        $user = User::where('email', $emailBenar)->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard')
                ->with('success', 'Anda berhasil masuk!');
        }

        return back()->withErrors([
            'login' => 'Login gagal',
        ]);
    }

    // =========================
    // LOGOUT
    // =========================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Anda berhasil logout.');
    }

    // =========================
    // DASHBOARD
    // =========================
    public function dashboard()
    {
        return view('dashboard');
    }
}
