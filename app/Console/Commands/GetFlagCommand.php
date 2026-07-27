<?php

namespace App\Console\Commands;

use App\Models\Neighborhood;
use Illuminate\Console\Command;
use Throwable;

class GetFlagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flag:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and output the flag from incidents around NB-7A2F';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $neighborhood = Neighborhood::where('name', 'NB-7A2F')->firstOrFail();
            $incidents = $neighborhood->incidents()->getResults();

            $flag = $incidents->pluck('code')->implode('');

            $this->info("Flag: " . $flag);
        } catch (Throwable $e) {
            logger()->error('GetFlagCommand failed', ['exception' => $e]);
            $this->fail('Failed to retrieve the flag. Check logs for details.');
        }
    }
}
