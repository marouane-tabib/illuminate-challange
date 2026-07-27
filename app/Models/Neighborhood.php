<?php

namespace App\Models;

use App\Relations\DonutRelation;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    protected $fillable = ['name', 'centroid_lat', 'centroid_lng'];

    
    public function incidents(): DonutRelation
    {
        return new DonutRelation(
            Incident::query(),
            $this,
            $this->centroid_lat,
            $this->centroid_lng,
            0.5,   // inner radius km
            2.0    // outer radius km
        );
    }
}
