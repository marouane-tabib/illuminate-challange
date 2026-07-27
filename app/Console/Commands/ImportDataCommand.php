<?php

namespace App\Console\Commands;

use App\Helpers\NeighborhoodHelper;
use App\Models\Incident;
use App\Models\Neighborhood;
use App\Repositories\IncidentPgRepository;
use App\Repositories\NeighborhoodPgRepository;
use Illuminate\Console\Command;
use Throwable;

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
        try {
            $this->importNeighborhoods();
            $this->info('Neighborhoods imported.');

            $this->importIncidents();
            $this->info('Incidents imported.');
        } catch (Throwable $e) {
            logger()->error('ImportDataCommand failed', ['exception' => $e]);
            $this->fail('Import failed...');
        }
    }

    private function importNeighborhoods(): void
    {
        $neighborhoods = NeighborhoodPgRepository::all();

        if (empty($neighborhoods)) {
            throw new \RuntimeException('No neighborhoods found in remote database.');
        }

        foreach ($neighborhoods as $nb) {
            $centroid = NeighborhoodHelper::polygonCentroid($nb->boundary);

            Neighborhood::create([
                'name'         => $nb->name,
                'centroid_lat' => $centroid['lat'],
                'centroid_lng' => $centroid['lng'],
            ]);
        }
    }

    private function importIncidents(): void
    {
        $incidentsData = IncidentPgRepository::all();

        if (empty($incidentsData)) {
            throw new \RuntimeException('No incidents found in remote database.');
        }

        $incidentsArray = array_map(fn($row) => (array) $row, $incidentsData);
        Incident::insert($incidentsArray);
    }
}
