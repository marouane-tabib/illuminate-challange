<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class IncidentPgRepository
{
    public static function all(): array
    {
        return DB::connection('pgsql_remote')
            ->select("
                SELECT 
                    location[0] AS latitude,
                    location[1] AS longitude,
                    metadata->'incident'->>'code' AS code
                FROM gis_data.incidents
            ");
    }
}