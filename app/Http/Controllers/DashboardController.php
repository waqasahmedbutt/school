<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
public function index()
{
$user = Auth::user();
switch ($user->role) {
case 'admin': return redirect()->route('dashboard.admin');
case 'teacher': return redirect()->route('dashboard.teacher');
case 'student': return redirect()->route('dashboard.student');
case 'parent': return redirect()->route('dashboard.parent');
default: return redirect()->route('home');
}
}


public function admin()
{
$this->authorizeRole('admin');
return view('dashboards.admin');
}


public function teacher()
{
$this->authorizeRole('teacher');
return view('dashboards.teacher');
}


public function student()
{
$this->authorizeRole('student');
return view('dashboards.student');
}


public function parent()
{
$this->authorizeRole('parent');
return view('dashboards.parent');
}


protected function authorizeRole($role)
{
$user = Auth::user();
if ($user->role !== $role) {
abort(403, 'Unauthorized');
}
}
}