<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // USERS
      Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('role', ['admin','teacher','parent','student'])->default('student');
    
    // Parent CNIC stays unique, but only for parents
    $table->string('cnic')->nullable(); 
    $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('cascade');
    
    $table->string('phone')->nullable();
    $table->text('bio')->nullable();
    $table->string('avatar')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamp('email_verified_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
});


        // GUARDIANS (parent <-> child links)
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
            $table->unique(['parent_id','child_id']);
            $table->timestamps();
        });

        // SCHOOLS (optional multi school support)
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        // CLASSROOMS (e.g. Grade 7, Grade 8, etc)
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->string('name'); // e.g. "Grade 7"
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->integer('capacity')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // SECTIONS inside classrooms (A, B, C...)
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->string('name'); // e.g. "A", "B"
            $table->string('code')->nullable(); // optional
            $table->integer('capacity')->nullable();
            $table->timestamps();
            $table->unique(['classroom_id','name']);
        });

        // SUBJECTS (Math, Physics, etc)
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // TEACHER_ASSIGNMENTS: which teacher teaches which subject in which classroom/section
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->timestamps();
            $table->unique(['teacher_id','subject_id','classroom_id','section_id'], 'unique_teacher_subject_class_section');
        });

        // CLASSROOM_STUDENTS pivot
        Schema::create('classroom_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('roll_no')->nullable();
            $table->timestamps();
            $table->unique(['classroom_id','student_id']);
        });

        // TIMETABLE / SCHEDULE entries per section
    

        // ASSIGNMENTS
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('total_marks')->default(100);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // teacher
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->timestamp('due_at')->nullable();
            $table->json('attachments')->nullable(); // array of storage paths
            $table->timestamps();
        });

        // ASSIGNMENT_SUBMISSIONS
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->json('files')->nullable();
            $table->text('answer_text')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('marks_awarded')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->unique(['assignment_id','student_id']);
        });

        // QUIZZES (online)
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // teacher
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->integer('duration_minutes')->default(30);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // QUIZ QUESTIONS
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['single_choice','multi_choice','true_false','short_text','long_text'])->default('single_choice');
            $table->integer('marks')->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        // QUIZ OPTIONS (for choices)
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('quiz_questions')->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false); // careful: store secure answers elsewhere for production
            $table->timestamps();
        });

        // QUIZ ATTEMPTS
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('score')->nullable();
            $table->json('answers')->nullable(); // snapshot of answers if desired
            $table->timestamps();
            $table->unique(['quiz_id','student_id']);
        });

        // PHYSICAL EXAMS
        Schema::create('physical_exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // teacher
            $table->date('exam_date')->nullable();
            $table->integer('total_marks')->default(100);
            $table->timestamps();
        });

        // PHYSICAL EXAM MARKS
        Schema::create('physical_exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('physical_exam_id')->constrained('physical_exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('marks_obtained')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['physical_exam_id','student_id']);
        });

        // ATTENDANCE
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->enum('status',['present','absent','late','excused'])->default('present');
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['student_id','date']);
        });

        // CONVERSATIONS and MESSAGES (chat)
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->boolean('is_group')->default(false);
            $table->timestamps();
        });

        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

     
        // NOTIFICATIONS (simple)
        Schema::create('notifications_custom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // SETTINGS (general key value)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // INDEXES / extra safety
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role','is_active']);
        });
    }

    public function down(): void
    {
        $tables = [
            'settings','notifications_custom','messages','conversation_user','conversations',
            'attendance_records','physical_exam_marks','physical_exams','quiz_attempts','quiz_options','quiz_questions','quizzes',
            'assignment_submissions','assignments','classroom_student','teacher_assignments','subjects','sections','classrooms','schools',
            'guardians','users'
        ];
        foreach ($tables as $t) {
            if (Schema::hasTable($t)) Schema::dropIfExists($t);
        }
    }
};
