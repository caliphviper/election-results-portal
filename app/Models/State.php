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
}