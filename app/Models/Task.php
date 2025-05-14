<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    // Menentukan bahwa primary key adalah UUID
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($task) {
            if (empty($task->id)) {
                $task->id = (string) Str::uuid();
            }
        });
    }

    // Menentukan kolom yang dapat diisi mass-assignment
    protected $fillable = [
        'title',
        'description',
        'assigned_to',
        'status',
        'due_date',
        'created_by',
    ];

    // Relasi dengan User (Assigned To)
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relasi dengan User (Created By)
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
