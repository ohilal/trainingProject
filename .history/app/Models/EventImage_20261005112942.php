<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventImage extends Model
{
   protected $fillable = [

        'event_id',

        'image',

        'caption',

    ];

    /**
25
     * The event this gallery image belongs to.

     */
27
    public function event()

    {

        return $this->belongsTo(Event::class);

    }

    /**

     * Full URL of the stored image.

     */

    public function getUrlAttribute(): string

    {

        return asset('storage/files/events/' . $this->image);

    }
}
