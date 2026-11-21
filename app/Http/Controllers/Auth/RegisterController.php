<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

  public function register(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'email'       => ['required','email','max:255', Rule::unique('users','email')],
            'password'    => ['required','string','min:6','confirmed'],
            'role'        => ['required', Rule::in(['admin','teacher','parent','student'])],
            'cnic'        => ['nullable','string','regex:/^(\d{13}|\d{5}-\d{7}-\d)$/'],
            'parent_cnic' => ['nullable','string','regex:/^(\d{13}|\d{5}-\d{7}-\d)$/'],
        ]);

        // If role is student or parent, CNIC is required
        if (in_array($data['role'], ['student','parent']) && empty($data['cnic'])) {
            return back()
                ->withErrors(['cnic' => 'CNIC is required for Students and Parents'])
                ->withInput();
        }

        // Create new user
        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => $data['role'],
            'cnic'        => $data['cnic'] ?? null,
            'parent_cnic' => $data['parent_cnic'] ?? null, // save student's provided parent CNIC
            'is_active'   => true,
        ]);

        // If student → try to link to parent
        if ($user->role === 'student' && $user->parent_cnic) {
            $parent = User::where('role', 'parent')
                          ->where('cnic', $user->parent_cnic)
                          ->first();

            if ($parent) {
                $user->parent_id = $parent->id;
                $user->save();
            }
        }

        // If parent → try to link to all students who referenced this CNIC
        if ($user->role === 'parent' && $user->cnic) {
            $students = User::where('role', 'student')
                            ->where('parent_cnic', $user->cnic)
                            ->get();

            foreach ($students as $student) {
                $student->parent_id = $user->id;
                $student->save();
            }
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
