<?php

use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Admin\TimetableController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Models\Classroom;
use App\Http\Controllers\ExamReportController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Parent\ParentWebController;


Route::middleware(['auth'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/', [ParentWebController::class, 'index'])->name('children.index'); // list children
    Route::get('/child/{child}', [ParentWebController::class, 'overview'])->name('child.overview');

    // Sections
    Route::get('/child/{child}/attendance', [ParentWebController::class, 'attendance'])->name('child.attendance');
    Route::get('/child/{child}/assignments', [ParentWebController::class, 'assignments'])->name('child.assignments');
    Route::get('/child/{child}/quizzes', [ParentWebController::class, 'quizzes'])->name('child.quizzes');
    Route::get('/child/{child}/physical-exams', [ParentWebController::class, 'physicalExams'])->name('child.physical_exams');

    // Exams
    Route::get('/child/{child}/exams', [ParentWebController::class, 'exams'])->name('child.exams');
    Route::get('/child/{child}/exams/{exam}', [ParentWebController::class, 'examShow'])->name('child.exams.show');
});



Route::get('/', function(){ return view('welcome'); })->name('home');
Route::get('/features', function(){ return view('feature'); })->name('feature');
Route::get('/about', function(){ return view('aboutus'); })->name('aboutus');
Route::get('/contact', function(){ return view('contactus'); })->name('contactus');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function(){
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
Route::get('/dashboard/teacher', [DashboardController::class, 'teacher'])->name('dashboard.teacher');
Route::get('/dashboard/student', [DashboardController::class, 'student'])->name('dashboard.student');
Route::get('/dashboard/parent', [DashboardController::class, 'parent'])->name('dashboard.parent');
});
// Admin Dashboard
Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

// Users
Route::resource('/admin/users', UserController::class)->names('admin.users')->except(['show']);

// Subjects
Route::resource('/admin/subjects', SubjectController::class)->names('admin.subjects')->except(['show']);


Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('classrooms', ClassroomController::class);
    // section-specific actions (show / add student / remove student / add teacher / remove teacher / destroy)
    Route::get('classrooms/{classroom}/sections/{section}', [SectionController::class, 'show'])->name('classrooms.sections.show');
    Route::post('classrooms/{classroom}/sections/{section}/students', [SectionController::class, 'addStudent'])->name('classrooms.sections.students.add');
    Route::delete('classrooms/{classroom}/sections/{section}/students/{pivot}', [SectionController::class, 'removeStudent'])->name('classrooms.sections.students.remove');

    Route::post('classrooms/{classroom}/sections/{section}/teachers', [SectionController::class, 'addTeacher'])->name('classrooms.sections.teachers.add');
    Route::delete('classrooms/{classroom}/sections/{section}/teachers/{assignment}', [SectionController::class, 'removeTeacher'])->name('classrooms.sections.teachers.remove');

    Route::delete('classrooms/{classroom}/sections/{section}', [SectionController::class, 'destroy'])->name('classrooms.sections.destroy');
});

Route::prefix('admin/schools')->name('admin.schools.')->group(function () {
    Route::get('/', [SchoolController::class, 'index'])->name('index');
    Route::get('/create', [SchoolController::class, 'create'])->name('create');
    Route::post('/', [SchoolController::class, 'store'])->name('store');
    Route::get('/{school}', [SchoolController::class, 'show'])->name('show');
    Route::get('/{school}/edit', [SchoolController::class, 'edit'])->name('edit');
    Route::put('/{school}', [SchoolController::class, 'update'])->name('update');
    Route::delete('/{school}', [SchoolController::class, 'destroy'])->name('destroy');
});
// index & create & store & export (filter via query string)
Route::get('admin/timetable', [TimetableController::class,'index'])->name('admin.timetable.index');
Route::get('admin/timetable/create', [TimetableController::class,'create'])->name('admin.timetable.create');
Route::post('admin/timetable', [TimetableController::class,'store'])->name('admin.timetable.store');
Route::get('admin/timetable/export', [TimetableController::class,'exportPdf'])->name('admin.timetable.export');

// section-specific view
Route::get('admin/timetable/{timetable}/edit', [TimetableController::class, 'edit'])
    ->name('admin.timetable.edit');

Route::put('admin/timetable/{timetable}', [TimetableController::class, 'update'])
    ->name('admin.timetable.update');

Route::delete('admin/timetable/{timetable}', [TimetableController::class, 'destroy'])
    ->name('admin.timetable.destroy');

// Keep this one LAST (most general)
Route::get('admin/timetable/{classroom}/{section}', [TimetableController::class, 'show'])
    ->name('admin.timetable.show');




Route::prefix('teacher')->name('teacher.')->middleware(['auth'])->group(function(){
    // dashboard
    Route::get('/', [TeacherController::class,'dashboard'])->name('dashboard');

    // classes list (sidebar uses teacher.classes.index)
    Route::get('classes', [TeacherController::class,'classrooms'])->name('classes.index');

    // classroom detail (keep /classrooms/{classroom} for detail)
    Route::get('classrooms/{classroom}', [TeacherController::class,'classroomShow'])->name('classrooms.show');
Route::get('students/{student}', [TeacherController::class, 'show'])->name('students.show');
    // ASSIGNMENTS - index + create/edit flows already exist in controller for create/store/edit/update/delete
    Route::get('assignments', [TeacherController::class,'assignmentsIndex'])->name('assignments.index');
    Route::get('classrooms/{classroom}/assignments/create', [TeacherController::class,'createAssignment'])->name('assignments.create');
    Route::post('classrooms/{classroom}/assignments', [TeacherController::class,'storeAssignment'])->name('assignments.store');
    Route::get('assignments/{assignment}/edit', [TeacherController::class,'editAssignment'])->name('assignments.edit');
    Route::put('assignments/{assignment}', [TeacherController::class,'updateAssignment'])->name('assignments.update');
    Route::delete('assignments/{assignment}', [TeacherController::class,'destroyAssignment'])->name('assignments.destroy');

    // QUIZZES - index + create/edit/publish etc.
    Route::get('quizzes', [TeacherController::class,'quizzesIndex'])->name('quizzes.index');
    Route::get('classrooms/{classroom}/quizzes/create', [TeacherController::class,'createQuiz'])->name('quizzes.create');
    Route::post('classrooms/{classroom}/quizzes', [TeacherController::class,'storeQuiz'])->name('quizzes.store');
    Route::get('quizzes/{quiz}/edit', [TeacherController::class,'editQuiz'])->name('quizzes.edit');
    Route::put('quizzes/{quiz}', [TeacherController::class,'updateQuiz'])->name('quizzes.update');
    Route::delete('quizzes/{quiz}', [TeacherController::class,'destroyQuiz'])->name('quizzes.destroy');
    Route::post('quizzes/{quiz}/publish', [TeacherController::class,'publishQuiz'])->name('quizzes.publish');

    // ATTENDANCE - index + form + store
    Route::get('attendance', [TeacherController::class,'attendanceIndex'])->name('attendance.index');
    Route::get('classrooms/{classroom}/sections/{section}/attendance', [TeacherController::class,'attendanceForm'])->name('attendance.form');
    Route::post('classrooms/{classroom}/sections/{section}/attendance', [TeacherController::class,'storeAttendance'])->name('attendance.store');
    Route::get('classrooms/{classroom}/sections/{section}/attendance/records', [TeacherController::class,'attendanceRecords'])->name('attendance.records');
    Route::put(
    'classrooms/{classroom}/sections/{section}/attendance/update/{date}',
    [TeacherController::class,'updateAttendance']
)->name('attendance.update');
Route::get(
    'classrooms/{classroom}/sections/{section}/attendance/details/{date}',
    [TeacherController::class,'attendanceDetails']
)->name('attendance.details');

    // CHAT (simple list of chats)
    Route::get('chat', [TeacherController::class,'chatIndex'])->name('chat.index');

    // physical exams & marks (kept as before)
    Route::get('classrooms/{classroom}/physical-exams/create', [TeacherController::class,'createPhysicalExam'])->name('physical_exams.create');
    Route::post('classrooms/{classroom}/physical-exams', [TeacherController::class,'storePhysicalExam'])->name('physical_exams.store');
    Route::get('physical-exams/{exam}', [TeacherController::class,'showPhysicalExam'])->name('physical_exams.show');
    Route::post('physical-exams/{exam}/marks', [TeacherController::class,'storePhysicalExamMarks'])->name('physical_exams.marks.store');

    // notifications
    Route::get('notifications', [TeacherController::class,'notifications'])->name('notifications.index');
    Route::post('notifications/{id}/read', [TeacherController::class,'markNotificationRead'])->name('notifications.read');

    // timetable export
    Route::get('classrooms/{classroom}/timetable/export', [TeacherController::class,'exportTimetablePdf'])->name('timetable.export');
});


Route::prefix('student')->name('student.')->middleware(['auth'])->group(function(){
    Route::get('/', [StudentDashboardController::class, 'dashboard'])->name('dashboard');

    // Classrooms
    Route::get('classrooms', [StudentDashboardController::class,'classroomsIndex'])->name('classrooms.index');
    Route::get('classrooms/{classroom}', [StudentDashboardController::class,'classroomShow'])->name('classrooms.show');

    // Assignments
    Route::get('assignments', [StudentDashboardController::class,'assignmentsIndex'])->name('assignments.index');
    Route::get('assignments/{assignment}', [StudentDashboardController::class,'assignmentShow'])->name('assignments.show');
    Route::post('assignments/{assignment}/submit', [StudentDashboardController::class,'submitAssignment'])->name('assignments.submit');

    // Quizzes
    Route::get('quizzes', [StudentDashboardController::class,'quizzesIndex'])->name('quizzes.index');
    Route::get('quizzes/{quiz}', [StudentDashboardController::class,'quizShow'])->name('quizzes.show');
    Route::post('quizzes/{quiz}/start', [StudentDashboardController::class,'startQuizAttempt'])->name('quizzes.start');
    Route::post('quizzes/{quiz}/submit', [StudentDashboardController::class,'submitQuizAttempt'])->name('quizzes.submit');

    // Attendance
    Route::get('attendance', [StudentDashboardController::class,'attendanceIndex'])->name('attendance.index');
    Route::get('attendance/classroom/{classroom}/section/{section}', [StudentDashboardController::class,'attendanceDetail'])->name('attendance.detail');

    // Marks (physical exams)
    Route::get('marks', [StudentDashboardController::class,'marksIndex'])->name('marks.index');
    Route::get('marks/exam/{exam}', [StudentDashboardController::class,'marksExam'])->name('marks.exam');

    // Notifications
    Route::get('notifications', [StudentDashboardController::class,'notifications'])->name('notifications.index');
    Route::post('notifications/{id}/read', [StudentDashboardController::class,'markNotificationRead'])->name('notifications.read');
});
// Assignment grading
Route::get('assignments/{assignment}/submissions', [\App\Http\Controllers\Teacher\MarkingController::class,'assignmentSubmissionsIndex'])
    ->name('assignments.submissions.index');
Route::get('assignments/submissions/{submission}/edit', [\App\Http\Controllers\Teacher\MarkingController::class,'editSubmission'])
    ->name('assignments.submissions.edit');
Route::put('assignments/submissions/{submission}', [\App\Http\Controllers\Teacher\MarkingController::class,'updateSubmission'])
    ->name('assignments.submissions.update');

// Quiz attempts
Route::get('quizzes/{quiz}/attempts', [\App\Http\Controllers\Teacher\MarkingController::class,'quizAttemptsIndex'])
    ->name('quizzes.attempts.index');
Route::get('quizzes/attempts/{attempt}', [\App\Http\Controllers\Teacher\MarkingController::class,'quizAttemptShow'])
    ->name('quizzes.attempts.show');
Route::put('quizzes/attempts/{attempt}', [\App\Http\Controllers\Teacher\MarkingController::class,'quizAttemptUpdate'])
    ->name('quizzes.attempts.update');


/*
 * Admin exam routes
 */
Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function(){
    Route::get('exams', [ExamReportController::class,'index'])->name('exams.index');
    Route::get('exams/create', [ExamReportController::class,'create'])->name('exams.create');
    Route::post('exams', [ExamReportController::class,'store'])->name('exams.store');
    Route::get('exams/{exam}', [ExamReportController::class,'adminShow'])->name('exams.show');
    Route::delete('exams/{exam}', [ExamReportController::class,'destroy'])->name('exams.destroy');
});
Route::delete('exams/{exam}', [StudentDashboardController::class, 'destroy'])
    ->name('exams.destroy');

Route::get('admin/dashboard', [SubjectController::class, 'dashboard'])
    ->name('admin.dashboards')
    ->middleware('auth');

Route::prefix('teacher')->name('teacher.')->middleware(['auth','role:teacher'])->group(function(){
    Route::get('exams', [ExamReportController::class,'teacherIndex'])->name('exams.index');
    Route::get('exams/{exam}', [ExamReportController::class,'teacherShow'])->name('exams.show');

    // mark entry (you already have these)
    Route::get('exams/{exam}/subject/{subject}/enter-marks', [ExamReportController::class,'enterMarks'])->name('exams.enter-marks');
    Route::post('exams/{exam}/subject/{subject}/save', [ExamReportController::class,'saveMarks'])->name('exams.save-marks');
});
Route::get('admin/students/{student}/exams/{exam}/result', [ExamReportController::class, 'studentExamResult'])
    ->name('admin.students.examResult')
    ;

/*
 * Student exam routes
 */
Route::prefix('student')->name('student.')->group(function(){
    Route::get('exams', [ExamReportController::class,'studentIndex'])->name('exams.index');
    Route::get('exams/{exam}/show', [ExamReportController::class,'studentShow'])->name('exams.show');
    Route::get('exams/{exam}/result', [ExamReportController::class,'resultCard'])->name('exams.result-card');
    // if you want download that requires student param (teacher/admin view of student's pdf)
    Route::get('exams/{exam}/result/{student}/pdf', [ExamReportController::class, 'downloadPdf'])->name('exams.download');
});



Route::middleware(['auth', 'role:teacher'])->prefix('teacher/attendance')->name('teacher.attendance.')->group(function () {

    // 1️⃣ Index: List classrooms assigned to teacher
    Route::get('/', [AttendanceController::class, 'index'])->name('index');

    // 2️⃣ Select Section within classroom
    Route::get('/{classroom}/select-section', [AttendanceController::class, 'selectSection'])->name('selectSection');

    // 3️⃣ Attendance Records for a section & optional subject
    Route::get('/{classroom}/{section}/records', [AttendanceController::class, 'records'])->name('records');

    // 4️⃣ Add / Mark Attendance Form
    Route::get('/{classroom}/{section}/form', [AttendanceController::class, 'attendanceForm'])->name('form');

    // 5️⃣ Store Attendance
    Route::post('/{classroom}/{section}/store', [AttendanceController::class, 'storeAttendance'])->name('store');

    // 6️⃣ View / Update Attendance Details for a specific date
    Route::get('/{classroom}/{section}/details/{date}', [AttendanceController::class, 'attendanceDetails'])->name('details');

    // 7️⃣ Update Attendance for a specific date
    Route::put('/{classroom}/{section}/update/{date}', [AttendanceController::class, 'updateAttendance'])->name('update');
});





Route::middleware('auth')->group(function () {
    // Students list (with optional section filter)
    Route::get('/classroom/{classroom}/chat/students/{section?}', [ChatController::class, 'listStudents'])
        ->name('chats.students');

    // Parents list (with optional section filter)
    Route::get('/classroom/{classroom}/chat/parents/{section?}', [ChatController::class, 'listParents'])
        ->name('chats.parents');
});
Route::get('/classroom/{classroom}/chat/teachers/{section?}', [ChatController::class, 'listTeachers'])
    ->name('chats.teachers');
Route::get('/parent/chat', [ChatController::class, 'listChildren'])
    ->name('parent.chat.children');

// Step 2: Show teachers for a selected child + classroom
Route::get('/parent/chat/{child}/{classroom}', [ChatController::class, 'listTeacher'])
    ->name('parent.chat.teachers');
// ensure auth middleware is applied (adjust as per your app)
Route::middleware('auth')->group(function () {
    // 1. List all chats
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');

    // 2. Open or create a private chat inside a classroom
    Route::get('/classroom/{classroom}/chat/{otherUser}', [ChatController::class, 'open'])
        ->name('chats.open');

    // 3. Show a specific chat with messages
    Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chats.show');

    // 4. Send message to chat
    Route::post('/chats/{chat}/messages', [MessageController::class, 'store'])
        ->name('messages.store');
});

// routes/web.php (or api.php if you prefer)
Route::get('/chats/{chat}/poll', [App\Http\Controllers\ChatController::class, 'poll']);
Route::get('/messages/latest/{chat}', function(App\Models\Chat $chat) {
    return response()->json(['messages' => $chat->messages()->with('sender')->latest()->take(50)->get()]);
});