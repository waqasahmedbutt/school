<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class NotificationCustom extends Model
{
use HasFactory;


protected $table = 'notifications_custom';
protected $fillable = ['user_id','type','data','is_read'];
protected $casts = ['data' => 'array', 'is_read' => 'boolean'];


public function user() { return $this->belongsTo(User::class); }
}