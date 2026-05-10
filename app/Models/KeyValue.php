<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeyValue extends Model
{
    use HasFactory;
    protected $fillable = ['key_id','value','recorded_at'];
    protected $casts = ['value' => 'array', 'recorded_at' => 'datetime'];

    public function key(): BelongsTo
    {
        return $this->belongsTo(Key::class);
    }
}
