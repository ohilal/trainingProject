<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;
    protected $fillable = [
        'EventTypeID',
        'eventName'
    ];
    public function event()
    {
        return $this->hasMany(Event::class, 'eventTypeID');
    }
}
