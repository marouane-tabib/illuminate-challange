<?php

namespace App\Helpers;

class NeighborhoodHelper
{
    /**
     * Compute the bounding‑box centre of a polygon from its text representation.
     * Points are in (latitude, longitude) order.
     *
     * Example input: "((33.32,44.34),(33.325,44.365),...)"
     * Returns ['lat' => float, 'lng' => float]
     */
    public static function polygonCentroid(string $polygonText): array
    {
        preg_match_all('/\(([\d.]+),([\d.]+)\)/', $polygonText, $matches, PREG_SET_ORDER);

        if (count($matches) === 0) {
            throw new \InvalidArgumentException("No vertices found in polygon: $polygonText");
        }

        $minLat = $maxLat = (float) $matches[0][1];
        $minLng = $maxLng = (float) $matches[0][2];

        foreach ($matches as $match) {
            $lat = (float) $match[1];
            $lng = (float) $match[2];
            if ($lat < $minLat) $minLat = $lat;
            if ($lat > $maxLat) $maxLat = $lat;
            if ($lng < $minLng) $minLng = $lng;
            if ($lng > $maxLng) $maxLng = $lng;
        }

        return [
            'lat' => ($minLat + $maxLat) / 2,
            'lng' => ($minLng + $maxLng) / 2,
        ];
    }
}