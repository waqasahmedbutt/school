<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->get('role');
        $q = User::query()->when($role, fn($qq) => $qq->where('role', $role));
        $users = $q->orderBy('id','desc')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users','role'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

public function store(Request $request)
{
    $rules = [
        'name'     => ['required','string','max:255'],
        'email'    => ['required','email','max:255','unique:users,email'],
        'password' => ['required','string','min:6'],
        'role'     => ['required', Rule::in(['admin','teacher','parent','student'])],
    ];

    // Common CNIC regex: 13 digits OR dashed format 12345-1234567-1
    $cnicRegex = 'regex:/^(\d{13}|\d{5}-\d{7}-\d{1})$/';

    // Role-specific validation
    if ($request->role === 'parent') {
        $rules['cnic'] = ['required','string',$cnicRegex,'unique:users,cnic'];
    }

    if ($request->role === 'student') {
        $rules['cnic']        = ['required','string',$cnicRegex,'unique:users,cnic'];
        $rules['parent_cnic'] = ['required','string',$cnicRegex];
    }

    $data = $request->validate($rules);
    $data['password'] = Hash::make($data['password']);

    // Create user
    $user = User::create($data);

    // If student → attach to parent if exists
    if ($user->role === 'student' && !empty($data['parent_cnic'])) {
        $parent = User::where('role','parent')
                      ->where('cnic',$data['parent_cnic'])
                      ->first();

        if ($parent) {
            $user->parent_id = $parent->id;
            $user->save();
        }
    }

    // If parent → attach students waiting with this parent CNIC
    if ($user->role === 'parent' && !empty($user->cnic)) {
        User::where('role','student')
            ->where('parent_cnic',$user->cnic)
            ->update(['parent_id' => $user->id]);
    }

    return redirect()->route('admin.users.index')->with('success','User created.');
}


public function update(Request $request, User $user)
{
    $data = $request->validate([
        'name'  => ['required','string','max:255'],
        'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
        'role'  => ['required', Rule::in(['admin','teacher','parent','student'])],
        'cnic'  => ['nullable','string','regex:/^(\d{13}|\d{5}-\d{7}-\d)$/','unique:users,cnic,'.$user->id],
        'parent_cnic' => ['nullable','string','regex:/^(\d{13}|\d{5}-\d{7}-\d)$/'],
        'password' => ['nullable','string','min:6'],
    ]);

    if (!empty($data['password'])) {
        $data['password'] = Hash::make($data['password']);
    } else {
        unset($data['password']);
    }

    $user->update($data);

    // If student, update parent_id if parent_cnic is set
    if ($user->role === 'student' && !empty($data['parent_cnic'])) {
        $parent = User::where('role', 'parent')
            ->where('cnic', $data['parent_cnic'])
            ->first();

        $user->parent_id = $parent ? $parent->id : null;
        $user->save();
    }

    // If parent, update linked students
    if ($user->role === 'parent' && !empty($user->cnic)) {
        User::where('role', 'student')
            ->where('parent_cnic', $user->cnic)
            ->update(['parent_id' => $user->id]);
    }

    return redirect()->route('admin.users.index')->with('success','User updated.');
}

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

   
    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success','User deleted.');
    }
}
