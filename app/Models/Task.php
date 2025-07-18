<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Разрешённые для массового присваивания поля
    protected $fillable = ['title', 'description', 'completed', 'position'];
}
