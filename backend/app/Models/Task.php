<?php
// app/Models/Task.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'priority',
        'status',
        'order',
        'project_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'priority' => 'string',
        'status' => 'string',
        'order' => 'integer',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
