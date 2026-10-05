<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventImage extends Model
{
   protected $fillable = [
20
        'event_id',
21
        'image',
22
        'caption',
23
    ];
24
    /**
25
     * The event this gallery image belongs to.
26
     */
27
    public function event()
28
    {
29
        return $this->belongsTo(Event::class);
30
    }
31
    /**
32
     * Full URL of the stored image.
33
     */

    public function getUrlAttribute(): string

    {

        return asset('storage/files/events/' . $this->image);

    }
}
