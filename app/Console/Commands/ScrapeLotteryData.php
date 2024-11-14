<?php

namespace App\Console\Commands;

use App\Models\Random;
use Illuminate\Console\Command;
use Goutte\Client;
use Carbon\Carbon;

class ScrapeLotteryData extends Command
{
    protected $signature = 'scrape:lottery-data {start_date} {end_date}';
    protected $description = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Lấy `start_date` và `end_date` từ tham số
        $startDate = Carbon::createFromFormat('Y-m-d',$this->argument('start_date'));
        $endDate = Carbon::createFromFormat($this->argument('end_date'));

        // Kiểm tra xem ngày bắt đầu có trước ngày kết thúc không
        if ($startDate->greaterThan($endDate)) {
            $this->error("Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc.");
            return 1;
        }

        $client = new Client();

        // Lặp qua từng ngày trong khoảng thời gian
        for ($date = $startDate; $date->lessThanOrEqualTo($endDate); $date->addDay()) {
            $formattedDate = $date->format('d-m-Y');
            $divId = 'loto_1_' . $date->format('Y-m-d');

            $url = 'https://www.kqxs.vn/mien-bac?date=' . $formattedDate;
            $this->info("Đang lấy dữ liệu cho ngày: $formattedDate");

            // Gửi yêu cầu đến trang web
            $crawler = $client->request('GET', $url);

            // Lấy dữ liệu từ bảng trong div có ID động
            $data = $crawler->filter("#$divId .table-fixed.tbldata.table-result-lottery tbody tr")->each(function ($row) {
                return $row->filter('.number')->each(function ($number) {
                    return $number->text();
                });
            });

            // Gộp các mảng con và giới hạn 27 giá trị đầu tiên
            $data = array_slice(array_merge(...$data), 0, 27);

            // Hiển thị kết quả cho ngày hiện tại
            $this->info("Kết quả ngày $formattedDate: Done");
            foreach ($data as $number) {
                Random::create([
                    'rand_date' => $date->format('Y-m-d'),
                    'rand_value' => $number,
                ]);
            }
        }

        return 0;
    }
}
