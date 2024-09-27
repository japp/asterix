<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'radeg', 'decdeg', 'vmag', 'orbital_elements', 'notes', 'user_id']; 
}
