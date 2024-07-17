<?php

namespace App\Models\Csdb;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
  use HasFactory;

  protected $fillable = ['code', 'description', 'user_id', 'csdb_id'];

  
}
