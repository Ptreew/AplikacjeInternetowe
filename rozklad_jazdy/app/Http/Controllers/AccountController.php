<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user account page.
     */
    public function index()
    {
        $user = Auth::user();
        return view('account.index', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('account.index')
                ->withErrors($validator)
                ->withInput()
                ->with('activeTab', 'profile');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()
            ->route('account.index')
            ->with('success', 'Profil został zaktualizowany pomyślnie.')
            ->with('activeTab', 'profile');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Aktualne hasło jest nieprawidłowe.');
                    }
                },
            ],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('account.index')
                ->withErrors($validator)
                ->withInput()
                ->with('activeTab', 'password');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()
            ->route('account.index')
            ->with('success', 'Hasło zostało zmienione pomyślnie.')
            ->with('activeTab', 'password');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Hasło jest nieprawidłowe.');
                    }
                },
            ],
            'confirm_deletion' => ['required', 'in:1'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('account.index')
                ->withErrors($validator)
                ->withInput()
                ->with('activeTab', 'delete');
        }

        // Logout user
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Delete account
        $user->delete();

        return redirect()
            ->route('home')
            ->with('success', 'Twoje konto zostało pomyślnie usunięte.');
    }
}
