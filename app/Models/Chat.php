<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['classroom_id','created_by','type','subject'];

    public function participants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_participants', 'chat_id','user_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at','asc');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
