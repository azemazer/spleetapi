<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Receipt extends Model
{
    protected $fillable = [
        'total',
        'image',
        'name',
        'group_id'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('creator');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function group(): HasOne
    {
        return $this->hasOne(Group::class);
    }
}
