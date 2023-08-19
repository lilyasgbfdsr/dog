<?php

namespace App\Http\Traits;
use App\Models\DateTime;

trait DateTimeTraits
{
    public function getDateTime()
    {
        // 查询时间段
        $hour = DateTime::orderBy('sort', 'asc')->select(['id', 'start_time', 'end_time'])->get();
        $hourCount = DateTime::count();
        $tempHour = DateTime::orderBy('sort', 'asc')->select(['id', 'start_time'])->get();
        return [
            'hour' => $hour,
            'count' => $hourCount,
            'temp_hour' => $tempHour
        ];
    }
}
