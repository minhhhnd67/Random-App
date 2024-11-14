<?php

namespace App\Console\Commands;

use App\Models\Random;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AnalyticData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AnalyticData {start_date} {end_date}';

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

        // Kiểm tra xem ngày bắt đầu có trước ngày kết thúc không
        if ($startDate->greaterThan($endDate)) {
            $this->error("Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc.");
            return 1;
        }

        // Danh sách 00 -> 99
        $arr = [];
        for ($i = 0; $i < 100; $i++) {
            $arr[] = str_pad($i, 2, "0", STR_PAD_LEFT);
        }

        $resData = [];
        for ($date = $startDate; $date->lessThanOrEqualTo($endDate); $date->addDay()) {
            $data = Random::where('rand_date', '>=', $date->copy()->format(format: 'Y-m-d'))
            ->where('rand_date', '<=', $date->copy()->addDays(7)->format(format: 'Y-m-d'))
            ->distinct()
            ->pluck('rand_value')
            ->toArray();
            $result = array_diff($arr, $data);
            $result = array_values($result);

            if ($result) {
                $checkRes = Random::where('rand_date', '>=', $date->copy()->addDays(value: 8)->format(format: 'Y-m-d'))
                ->where('rand_date', '<=', $date->copy()->addDays(value: 15)->format(format: 'Y-m-d'))
                ->where('rand_value', $result[0])
                ->orderBy('rand_date', 'asc')
                ->first();

                $resData[] = [
                    'result' => $result,
                    'checkRes' => $checkRes ? 1 : 0,
                ];
                if ($checkRes) {
                    $date = Carbon::createFromFormat('Y-m-d', $checkRes->rand_date)->subDays(8);
                } else {
                    $date->addDays(value: 15)->subDays(8);
                }
            }
        }

        $filteredArray = array_filter($resData, function($item) {
            return $item['checkRes'] === 0;
        });

        dd(round((count($filteredArray)/count($resData)), 4));

        dd($resData);

        return Command::SUCCESS;
    }
}
