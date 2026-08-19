<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'title',
        'description',
        'image',
        'start_date',
        'end_date',
        'event_type_id',
        'imageFolder',

       
    ];
    public function event()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }
}
