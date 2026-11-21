<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Guardian extends Model
{
use HasFactory;


protected $table = 'guardians';
protected $fillable = ['parent_id','child_id'];


public function parent() { return $this->belongsTo(User::class, 'parent_id'); }
public function child() { return $this->belongsTo(User::class, 'child_id'); }
}