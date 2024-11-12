<?php

namespace App\Console\Commands;

use App\Models\Random;
use Illuminate\Console\Command;

class AnalyticData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AnalyticData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $arr = [];
        for ($i = 0; $i < 100; $i++) {
            $arr[] = str_pad($i, 2, "0", STR_PAD_LEFT);
        }

        $data = Random::where('rand_date', '>=', '2024-10-29')->where('rand_date', '<=', '2024-11-13')->distinct()->pluck('rand_value')->toArray();

        $result = array_diff($arr, $data);
        dd($result);

        return Command::SUCCESS;
    }
}
