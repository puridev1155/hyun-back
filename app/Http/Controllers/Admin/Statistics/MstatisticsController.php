<?php

namespace App\Http\Controllers\Admin\Statistics;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Statistic\UserStat;
use App\Http\Controllers\Controller;
use App\Models\Statistic\Mstatistic;
use Spatie\QueryBuilder\QueryBuilder;

class MstatisticsController extends Controller
{
    public function index() {

        // filter (in url): ?filter[key]=value&filter[key]=value...
        // sort (in url): ?sort=key, key... (decending if -key)
        $postsPerMember = QueryBuilder::for(Mstatistic::class)
        ->allowedFilters([
            'name',
            'email',
            ])
        ->defaultSort('-total_posts')
        ->allowedSorts(['name', 'email'])
        ->paginate();

        $totalMember = User::count();

        $dailyRegisteredMember = UserStat::getChartData('day', [Carbon::now()->startOfMonth(), Carbon::now()]);
        $weeklyRegisteredMember = UserStat::getChartData('week', [Carbon::now()->subWeeks(10), Carbon::now()]);
        $monthlyRegisteredMember = UserStat::getChartData('month', [Carbon::now()->startOfYear(), Carbon::now()]);

        return response()->json(compact('totalMember', 'dailyRegisteredMember', 'weeklyRegisteredMember',
        'monthlyRegisteredMember'));

    }

}
