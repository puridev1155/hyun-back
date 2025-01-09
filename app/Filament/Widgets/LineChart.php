<?php

namespace App\Filament\Widgets;
use App\Models\User;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class LineChart extends ChartWidget
{
    protected static ?string $heading = '일별 방문자 차트';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
         $data = Trend::model(User::class)
        ->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
        ->perDay()
        ->count();
        
        return [
            'datasets' => [
                [
                    'label' => '일별 방문자 수',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ]; 
    }

    protected function getType(): string
    {
        return 'line';
    }
}
