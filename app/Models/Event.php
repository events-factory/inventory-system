<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name',
        'event_date',
        'event_location',
        'event_type',
        'customer',
        'responsible_person_name',
        'responsible_person_phone',
        'responsible_person_email',
        'urgency',
        'notes',
    ];

    //Relationship of Event to Requisition
    public function requisitions()
    {
        return $this->hasOne(Requisition::class);
    }

}
