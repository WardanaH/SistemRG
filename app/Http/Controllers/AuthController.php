<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->hasRole('manajemen')) {
                return redirect()
                    ->route('manajemen.dashboard');
            } elseif ($user->hasAnyRole(['operator indoor', 'operator outdoor', 'operator multi', 'operator dtf'])) {
                return redirect()
                    ->route('operator.dashboard');
            } elseif ($user->hasRole('designer')) {
                return redirect()
                    ->route('designer.dashboard');
            } elseif ($user->hasRole('admin')) {
                return redirect()
                    ->route('admin.dashboard');
            } elseif ($user->hasRole('advertising')) {
                return redirect()
                    ->route('advertising.dashboard');
            } elseif ($user->hasRole('inventory utama')) {
                return redirect()
                    ->route('gudangpusat.dashboard');
            } elseif ($user->hasRole('inventory cabang')) {
                return redirect()
                    ->route('gudangcabang.dashboard');
            } elseif ($user->hasRole('adminprofil')) {
                return redirect()
                    ->route('profil.admin.dashboard');
            } elseif ($user->hasrole('profil2')) {
                return redirect()->route('profil2.dashboard');
            }
        }

        $users = User::all();

        return view('auth.login', compact('users'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Cek role user dan redirect sesuai peran
            if ($user->hasRole('operator indoor') || $user->hasRole('operator outdoor') || $user->hasRole('operator multi') || $user->hasRole('operator dtf')) {
                $isi = Auth::user()
                    ->username . " telah login dicabang " . Auth::user()->cabang->nama . ".";
                // $this->log($isi, "Login");
                return redirect()->route('operator.dashboard')
                    ->with('success', 'Selamat datang kembali!');
            } elseif ($user->hasRole('designer')) {
                $isi = Auth::user()
                    ->username . " telah login dicabang " . Auth::user()->cabang->nama . ".";
                // $this->log($isi, "Login");
                return redirect()->route('designer.dashboard')
                    ->with('success', 'Selamat datang kembali!');
            } elseif ($user->hasRole('adversting')) {
                $isi = Auth::user()
                    ->username . " telah login dicabang " . Auth::user()->cabang->nama . ".";
                // $this->log($isi, "Login");
                return redirect()->route('adversting.index')
                    ->with('success', 'Selamat datang kembali!');
            } elseif ($user->hasRole('inventory utama')) {
                $isi = Auth::user()
                    ->username . " telah login dicabang " . Auth::user()->cabang->nama . ".";
                // $this->log($isi, "Login");
                return redirect()->route('gudang-pusat.dashboard')
                    ->with('success', 'Selamat datang kembali!');
            } elseif ($user->hasRole('inventory cabang')) {
                $isi = Auth::user()
                    ->username . " telah login dicabang " . Auth::user()->cabang->nama . ".";
                // $this->log($isi, "Login");
                return redirect()->route('gudang-cabang.dashboard')
                    ->with('success', 'Selamat datang kembali!');
            } elseif ($user->hasRole('manajemen')) {
                $isi = Auth::user()
                    ->username . " telah login dicabang " . Auth::user()->cabang->nama . ".";
                // $this->log($isi, "Login");
                return redirect()->route('manajemen.dashboard')
                    ->with('success', 'Selamat datang kembali!');
            } elseif ($user->hasRole('advertising')) {
                $isi = Auth::user()
                    ->username . " telah login dicabang " . Auth::user()->cabang->nama . ".";
                // $this->log($isi, "Login");
                return redirect()->route('advertising.dashboard')
                    ->with('success', 'Selamat datang kembali!');
            } elseif ($user->hasRole('adminprofil')) {
                return redirect()->route('profil.admin.dashboard')
                    ->with('success', 'Selamat datang kembali!');
            } elseif ($user->hasRole('admin')) {
                $isi = Auth::user()
                    ->username . " telah login dicabang " . Auth::user()->cabang->nama . ".";
                // $this->log($isi, "Login");
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Selamat datang kembali!');
            } elseif ($user->hasrole('profil2')) {
                $isi = Auth::user()
                    ->username . " telah login dicabang " . Auth::user()->cabang->nama . ".";
                // $this->log($isi, "Login");
                return redirect()->route('profil2.dashboard')
                    ->with('success', 'Selamat datang kembali!');
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}
