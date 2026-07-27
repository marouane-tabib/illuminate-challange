<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class NeighborhoodPgRepository
{
    public static function all(): array
    {
        return DB::connection('pgsql_remote')
            ->table('gis_data.neighborhoods')
            ->select('name', 'boundary')
            ->get()
            ->all();
    }
}