<?php

namespace App\Filament\Widgets;
use App\Models\Post;
use App\Models\Contact;
use App\Models\Banner;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('작업수', Post::count())
            ->description('프로젝트 작업 현황')
            ->chart([1,3,5,10,20,40])
            ->color('danger'),
            Stat::make('문의수', Contact::count())
            ->description('문의건 수 현황')
            ->chart([1,3,5,10,20,40])
            ->color('success'),
            Stat::make('팝업', Banner::count())
            ->description('팝업 베너 현황')
            ->chart([1,3,5,10,20,40])
            ->color('success')
        ];
    }
}
