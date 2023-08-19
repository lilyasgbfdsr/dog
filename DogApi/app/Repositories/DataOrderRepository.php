<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DataOrderRepository
{
    use BaseRepository;

    protected static $table = 'data_order';

    public function getPageData($where) {
        return DB::table('data_order as a')
            ->leftJoin('data_reserve as b', 'a.reserve_id', '=', 'b.reserve_id')
            ->where($where)
            ->select('a.*', 'b.*')
            ->orderBy('b.int_time', 'desc')
            ->orderBy('b.hour', 'desc')
            ->orderBy('b.id', 'desc')
            ->get();
    }
}