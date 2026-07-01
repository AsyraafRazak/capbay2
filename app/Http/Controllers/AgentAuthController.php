<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentAuthController extends Controller
{
    /**
     * Show the agent login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('agent.index');
        }

        return view('agent.login');
    }

    /**
     * Handle the agent login attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('agent.index'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid email or password.']);
    }

    /**
     * Log the agent out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('agent.login');
    }
}
