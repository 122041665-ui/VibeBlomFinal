<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Memory extends Model
{
    protected $fillable = ['user_id','title','description','memory_date','location'];

    public function photos()
    {
        return $this->hasMany(MemoryPhoto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
