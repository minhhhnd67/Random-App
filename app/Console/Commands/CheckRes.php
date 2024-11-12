<?php

namespace App\Console\Commands;

use App\Models\Random;
use Illuminate\Console\Command;

class CheckRes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CheckRes';

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
        $data = Random::where('rand_date', '>=', '2024-11-07')
            ->where('rand_date', '<=', '2024-11-13')
            ->where('rand_value', '33')
            ->orderBy('rand_date', 'asc')
            ->get()
            ->toArray();

        dd($data);

        return Command::SUCCESS;
    }
}
