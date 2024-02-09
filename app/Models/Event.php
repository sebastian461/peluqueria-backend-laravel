<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    "name",
    "amount"
  ];

  public function users(): BelongsToMany
  {
    return $this->belongsToMany(User::class)->withPivot('created_at', 'updated_at');
  }
}
