<?php

namespace App\Console\Commands;

use App\Models\Incident;
use App\Models\Neighborhood;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import neighborhoods and incidents from remote PostgreSQL into local SQLite';

    /**
     * Execute the console command.
     */    
    public function handle()
    {
        // 1. Import neighborhoods – correct coordinate order
        $neighborhoods = DB::connection('pgsql_remote')
            ->table('gis_data.neighborhoods')
            ->get();
        
        foreach ($neighborhoods as $nb) {
            // $nb->boundary is already a string like "((33.32,44.34),...)"
            $centroid = $this->polygonCentroid($nb->boundary);

            Neighborhood::create([
                'name'         => $nb->name,
                'centroid_lat' => $centroid['lat'],
                'centroid_lng' => $centroid['lng'],
            ]);
        }

        $this->info('Neighborhoods imported.');

        // 2. Import incidents – correct: lat = location[0], lng = location[1]
        $this->info('Fetching incidents...');
        $incidentsData = DB::connection('pgsql_remote')
            ->select("
                SELECT 
                    location[0] AS latitude,
                    location[1] AS longitude,
                    metadata->'incident'->>'code' AS code
                FROM gis_data.incidents
            ");

        $this->info('Inserting ' . count($incidentsData) . ' incidents...');
            
        $incidentsArray = array_map(fn($row) => (array) $row, $incidentsData);
        Incident::insert($incidentsArray);

        $this->info('Incidents imported.');
    }

    /**
     * Compute centroid of polygon from text representation.
     * Points are in (latitude, longitude) order.
     */
    private function polygonCentroid(string $polygonText): array
    {
        // Mat  ch all (lat,lng) pairs
        preg_match_all('/\(([\d.]+),([\d.]+)\)/', $polygonText, $matches, PREG_SET_ORDER);
        
        if (count($matches) === 0) {
            throw new \Exception("No vertices found in polygon: $polygonText");
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
