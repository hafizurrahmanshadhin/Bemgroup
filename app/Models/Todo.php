<?php

namespace App\Models;

use App\Models\EmailLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'email',
        'description',
        'due_date',
        'reminder_email_sent',
        'status',
    ];

    protected $casts = [
        'id'                  => 'integer',
        'title'               => 'string',
        'email'               => 'string',
        'description'         => 'string',
        'due_date'            => 'datetime',
        'reminder_email_sent' => 'boolean',
        'status'              => 'string',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'deleted_at'          => 'datetime',
    ];

    public function emailLogs(): HasMany {
        return $this->hasMany(EmailLog::class);
    }
}
