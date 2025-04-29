<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'expected_pickup_date',
        'expected_return_date',
        'number_of_items',
        'category_id',
        'subcategory_id',
        'group_id',
        'item_id',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    //Requisition has many items
    public function items()
    {
     return $this->belongsToMany(Item::class, 'item_requisition');   
    }
    
}
