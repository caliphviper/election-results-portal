<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';
    protected $primaryKey = 'state_id';
    public $incrementing = false; 
    public $timestamps = false;

    protected $fillable = ['state_id', 'state_name'];

    public function lgas()
    {
        return $this->hasMany(Lga::class, 'state_id', 'state_id');
    }

    /**
     * The supplied dataset only covers Delta, so listing all 37 states gives
     * the user 36 dead ends. Offer only the states that actually have LGAs.
     */
    public function scopeWithLgaData($query)
    {
        return $query->whereIn('state_id', Lga::query()->select('state_id'))
            ->orderBy('state_name');
    }
}