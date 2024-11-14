<?php

namespace App\Console\Commands;

use App\Models\Random;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AnalyticData1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AnalyticData1 {start_date} {end_date}';

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

        // Lấy `start_date` và `end_date` từ tham số
        $startDate = Carbon::createFromFormat('Y-m-d',$this->argument('start_date'));
        $endDate = Carbon::createFromFormat('Y-m-d', $this->argument('end_date'));

        // Danh sách 00 -> 99
        $arr = [];
        for ($i = 0; $i < 100; $i++) {
            $arr[] = str_pad($i, 2, "0", STR_PAD_LEFT);
        }



        $resData = [];
        for ($date = $startDate; $date->lessThanOrEqualTo($endDate); $date->addDay()) {
            $res = $arr[rand(0, 99)];
            $checkRes = Random::where('rand_date', '>=', $date->copy()->format(format: 'Y-m-d'))
                ->where('rand_date', '<=', $date->copy()->addDays(value: 7)->format(format: 'Y-m-d'))
                ->where('rand_value', $res)
                ->orderBy('rand_date', 'asc')
                ->first();
            $resData[] = [
                'value' => $res,
                'checkRes' => $checkRes ? 1 : 0,
            ];
            if ($checkRes) {
                $date =  $startDate = Carbon::createFromFormat('Y-m-d', $checkRes->rand_date);
            } else {
                $date->addDays(value: 7);
            }
        }
        $filteredArray = array_filter($resData, function($item) {
            return $item['checkRes'] === 0;
        });

        dd(round((count($filteredArray)/count($resData)), 4));


        return Command::SUCCESS;
    }
}
