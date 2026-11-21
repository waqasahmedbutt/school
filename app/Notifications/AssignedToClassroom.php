<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignedToClassroom extends Notification
{
    use Queueable;

    protected $classroom;
    protected $section;
    protected $subject;

    public function __construct($classroom, $section = null, $subject = null)
    {
        $this->classroom = $classroom;
        $this->section = $section;
        $this->subject = $subject;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // optional: mail if you have mail configured
    }

 public function toMail($notifiable)
{
    return (new \Illuminate\Notifications\Messages\MailMessage)
        ->subject('Assigned to classroom: ' . ($this->classroom->name ?? 'Classroom'))
        ->line('You have been assigned to a classroom.')
        ->line('Classroom: ' . ($this->classroom->name ?? '—'))
        ->line('Section: ' . ($this->section->name ?? '—'))
        ->line('Subject: ' . ($this->subject->name ?? '—')) // ✅ subject name
        ->action('Open Dashboard', url('/teacher'))
        ->line('Thank you for using SchoolSys.');
}

public function toArray($notifiable)
{
    return [
        'message' => 'You have been assigned to ' . ($this->classroom->name ?? 'a classroom') 
            . ' / Section: ' . ($this->section->name ?? '—') 
            . ' / Subject: ' . ($this->subject->name ?? '—'), // ✅ subject name
        'classroom_id' => $this->classroom->id ?? null,
        'section_id' => $this->section->id ?? null,
        'subject_id' => $this->subject->id ?? null,
    ];
}

}
