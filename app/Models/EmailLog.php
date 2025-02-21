<?php

namespace App\Models;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailLog extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'todo_id',
        'subject',
        'recipient',
        'message',
        'status',
    ];

    protected $casts = [
        'id'         => 'integer',
        'todo_id'    => 'integer',
        'subject'    => 'string',
        'recipient'  => 'string',
        'message'    => 'string',
        'status'     => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the todo that owns the EmailLog
     *
     * @return BelongsTo
     */
    public function todo(): BelongsTo {
        return $this->belongsTo(Todo::class);
    }
}
