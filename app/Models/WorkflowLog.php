<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowLog extends Model
{
    use HasFactory;

    protected $table = 'workflow_logs';

    protected $fillable = [
        'formulaire_id',
        'user_id',
        'user_role',
        'action',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function formulaire(): BelongsTo
    {
        return $this->belongsTo(Formulaire::class, 'formulaire_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

