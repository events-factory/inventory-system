<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // 🔥 Relationship: A Category has many Subcategories
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
    // 🔥 Relationship: Category with Requisition
    public function requisitions()
    {
        return $this->hasMany(Requisition::class);
    }
    //Relationship with items
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
