<?php

namespace App\Relations;

use App\Helpers\IncidentHelper;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class DonutRelation extends Relation
{
    public function __construct(
        Builder $query,
        Model $parent,
        protected float $centerLat,
        protected float $centerLng,
        protected float $innerRadius,
        protected float $outerRadius,
    ) {
        parent::__construct($query, $parent);
    }

    public function addConstraints() {}
    public function addEagerConstraints(array $models) {}
    public function initRelation(array $models, $relation) {}
    public function match(array $models, Collection $results, $relation) {}

    public function getResults()
    {
        $incidents = $this->query
            ->select('id', 'latitude', 'longitude', 'code')
            ->get();

        $filtered = $incidents->filter(function ($incident) {
            $distance = IncidentHelper::haversineDistance(
                $this->centerLat,
                $this->centerLng,
                $incident->latitude,
                $incident->longitude
            );
            $incident->distance = $distance;
            return $distance > $this->innerRadius && $distance < $this->outerRadius;
        });

        return $filtered->sortBy('distance')->values();
    }
}