<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Key extends Model
{
    use HasFactory;
    protected $fillable = ['key'];

    public function values(): HasMany {
        return $this->hasMany(KeyValue::class);
    }

    public function latestValue(): HasOne
    {
        return $this->hasOne(KeyValue::class)->latestOfMany('recorded_at');
    }
}
