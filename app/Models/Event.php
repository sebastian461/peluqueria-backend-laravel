<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
  use HasFactory;

  protected $fillable = [
    "name",
    "amount"
  ];

  public function users(): BelongsToMany
  {
    return $this->belongsToMany(User::class);
  }
}
