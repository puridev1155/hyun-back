<?php

namespace App\Models\Statistic;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserStat extends Model
{
    use HasFactory;

    public static function getChartData($type = 'month', $dates = [])
    {
        /*$type = 'day'; $dates = ['2023-03-01', '2023-03-31'];
        $type = 'year'; $dates = ['2022-03-01', '2023-03-31'];
        $type = 'month'; $dates = ['2023-01-01', '2023-03-31'];*/

        $query = UserStat::query();
        if ($type === 'year') {
            $label = "DATE_FORMAT(MAX(date), '%Y')";
            $query->whereBetween('date', $dates)->groupByRaw("YEAR(date)");
        }

        if ($type === 'month') {
            $label = "DATE_FORMAT(MAX(date), '%Y-%m')";
            $query->whereBetween('date', $dates)->groupByRaw("YEAR(date)")->groupByRaw("MONTH(date)");
        }

        if ($type === 'week') {
            $label = "DATE_FORMAT(MAX(date), '%Y-%m-%d')";
            $query->whereBetween('date', $dates)->groupByRaw("YEAR(date)")->groupByRaw("WEEK(date)");
        }

        if ($type === 'day') {
            $label = "DATE_FORMAT(MAX(date), '%Y-%m-%d')";
            $query->whereBetween('date', $dates)->groupBy("date");
        }

        $newWithdrawnCount = $query->selectRaw($label . " AS label, SUM(new_count) AS new_count, SUM(withdrawn_count) AS withdrawn_count")->get();

        $period = CarbonPeriod::create($dates[0], '1 ' . $type, $dates[1]);

        // $period = Carbon::parse($dates[0])->startOfDay()->toPeriod('1 day', $dates[1]->startOfDay());


        $labels = [];
        $newCount = [];
        $withdrawnCount = [];

        foreach ($period as $date) {
            if ($type === 'year') {
                $labels[] = $date->format('Y년');
                $label = $date->format('Y');
            }

            if ($type === 'month') {
                $labels[] = $date->format('Y년 m월');
                $label = $date->format('Y-m');
            }

            // Add condition for week type
            if ($type === 'week') {
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                $labels[] = $startOfWeek->format('Y년 m월 d일') . ' ~ ' . $endOfWeek->format('Y년 m월 d일');
                $label = [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')];
            }

            if ($type === 'day') {
                $labels[] = $date->format('Y년 m월 d일');
                $label = $date->format('Y-m-d');
            }

            //storing the count in value of weeks, days, months, years
            if ($type === 'week') {
                $value = $newWithdrawnCount->whereBetween('label', $label)->first();
            } else {
                $value = $newWithdrawnCount->where('label', $label)->first();
            }

            $newCount[] = $value->new_count ?? 0;
            $withdrawnCount[] = $value->withdrawn_count?? 0;

        }

        return [
            'labels' => $labels,//['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
            'datasets' => [
                ['label' => '가입수', 'data' => $newCount],
                // ['label' => '탈퇴수', 'backgroundColor' => '#ff9502', 'borderColor' => '#ff9502', 'tension' => 0.4, 'data' => $withdrawnCount/*[4, 3, 1, 4, 3, 8, 4]*/]
            ]
        ];
    }

}
