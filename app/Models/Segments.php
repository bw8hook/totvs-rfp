<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Segments extends Model
{
    protected $table = 'rfp_segments';
    protected $fillable = ['name', 'active'];

    public function rfpBundles()
    {
        return $this->belongsToMany(RFPBundle::class, 'rfp_bundle_segment', 'segment_id', 'bundle_id');
    }
}
