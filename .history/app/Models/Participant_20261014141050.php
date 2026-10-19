<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;


class Participant extends Pivot
{
    use HasFactory;

    protected $table = 'term_user';
    protected $guarded = [];


    /**
     * scopeLearners
     *
     * @param  Builder $builder
     * @return Builder
     */
    public function scopeLearners(Builder $builder)
    {
        $user = auth()->user();
        $builder->where('role_id', 4);

        if (!$user->hasRole(['Super-Admin'])) {
            $builder->whereIn('term_id', $this->newQuery()->select('term_id')->where('user_id', $user->id));
        }

        return $builder;
    }

    public function User(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function Term(): HasOne
    {
        return $this->hasOne(Term::class, 'id', 'term_id');
    }

    public function Workout(): HasMany
    {
        return $this->hasMany(Workout::class, 'participant_id', 'id');
    }



}
