<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollingUnit extends Model
{
    protected $table = 'polling_unit';
    protected $primaryKey = 'uniqueid';
    public $timestamps = false;

    protected $fillable = [
        'polling_unit_id', 'ward_id', 'lga_id', 'uniquewardid',
        'polling_unit_number', 'polling_unit_name', 'polling_unit_description',
        'lat', 'long',
    ];

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'uniquewardid', 'uniqueid');
    }

    public function lga()
    {
        return $this->belongsTo(Lga::class, 'lga_id', 'lga_id');
    }

    
    public function results()
    {
        return $this->hasMany(AnnouncedPuResult::class, 'polling_unit_uniqueid', 'uniqueid');
    }
}