<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemoryPhoto extends Model
{
    protected $fillable = ['memory_id','path'];

    public function memory()
    {
        return $this->belongsTo(Memory::class);
    }
}
