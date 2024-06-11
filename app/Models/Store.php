<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'type_store',
        'category',
        'updated_at',
        'created_at'
    ];

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'cities_has_stores', 'store_id', 'city_id');
    }
}
