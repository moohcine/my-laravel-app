<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Models\InternshipRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InternAuthController extends Controller
{
    public function showLanding()
    {
        return view('intern.landing');
    }

    public function showRegisterForm()
    {
        return view('intern.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:8|confirmed',
            'phone'          => 'required|string|max:30',
            'school'         => 'required|string|max:255',
            'field_of_study' => 'required|string|max:255',
            'filiere'        => 'nullable|string|max:255',
            'period_start'   => 'required|date',
            'period_end'     => 'required|date|after_or_equal:period_start',
            'cv'             => 'required|mimes:pdf|max:2048',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'intern',
        ]);

        $cvPath = $request->file('cv')->store('cvs', 'public');

        InternshipRequest::create([
            'user_id'        => $user->id,
            'phone'          => $data['phone'],
            'school'         => $data['school'],
            'field_of_study' => $data['field_of_study'],
            'filiere'        => $data['filiere'] ?? null,
            'period_start'   => $data['period_start'],
            'period_end'     => $data['period_end'],
            'cv_path'        => $cvPath,
        ]);

        Auth::login($user);

        return redirect()->route('intern.dashboard')
            ->with('status', __('Application submitted. Status is now pending approval.'));
    }

    public function showLoginForm()
    {
        return view('intern.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role !== 'intern') {
                Auth::logout();
                return back()->withErrors(['email' => __('Only interns can login here.')]);
            }

            return redirect()->route('intern.dashboard');
        }

        return back()->withErrors(['email' => __('Invalid credentials.')]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
