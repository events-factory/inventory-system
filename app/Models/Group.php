<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subcategory_id',
    ];

    // Group Model
public function subcategory()
{
    return $this->belongsTo(Subcategory::class);
}
    public function requisitions()
    {
        return $this->hasMany(Requisition::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
