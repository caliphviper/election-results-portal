<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $table = 'ward';
    protected $primaryKey = 'uniqueid';
    public $timestamps = false;

    protected $fillable = ['ward_id', 'ward_name', 'lga_id', 'ward_description'];

    public function lga()
    {
        return $this->belongsTo(Lga::class, 'lga_id', 'lga_id');
    }

    public function pollingUnits()
    {
        return $this->hasMany(PollingUnit::class, 'uniquewardid', 'uniqueid');
    }
}