<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    use HasFactory;

    protected $fillable = ['type_id', 'name', 'link', 'firmware_hash'];

    public function type()
    { 
        return $this->belongsTo(Type::class);
    }
}
