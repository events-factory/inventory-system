<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',                  // Item Name
        'category_id',            // Category (foreign key)
        'subcategory_id',         // Sub Category (foreign key)
        'group_id',               // Group (foreign key)
        'model',                  // Model
        'serial_number',          // Serial Number
        'unit',                   // Unit (e.g., pieces, sets, etc.)
        'quantity',               // Quantity available
        'flight_case_number',     // Flight Case Number
        'remarks',
        'image',                // Remarks
    ];

    // 🔥 Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Many-to-many relationship with requisitions
    public function requisitions()
    {
        return $this->belongsToMany(Requisition::class, 'item_requisition');
    }
}
