<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Conversation extends Model
{
use HasFactory;


protected $fillable = ['title','is_group'];
protected $casts = ['is_group' => 'boolean'];


public function users() { return $this->belongsToMany(User::class, 'conversation_user', 'conversation_id', 'user_id')->withTimestamps(); }
public function messages() { return $this->hasMany(Message::class, 'conversation_id'); }
}