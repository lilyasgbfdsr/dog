<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DataReserveInfoRepository
{
    use BaseRepository;

    protected static $table = 'data_reserve_info';

    public function getPageCount($where = [], $start = '', $end = '', $startField = 'a.int_time', $endField = 'a.int_time')
    {
        $db = DB::table('data_reserve_info as a');
        if (!empty($where)) {
            $db = $db->where($where);
        }
        if (!empty($start)) {
            $db = $db->where($startField, '>=', $start);
            $db = $db->where($endField, '<=', $end);
        }
        return $db->count();
    }

    public function getPageData($where, $nowPage, $offset, $start = '', $end = '') {
        $db = DB::table('data_reserve_info as a')
            ->where($where)
            ->orderBy('a.int_time', 'desc')
            ->orderBy('a.hour', 'asc')
            ->orderBy('a.id', 'desc')
            ->forPage($nowPage, $offset);

        if (!empty($start) && !empty($end)) {
            $db = $db->where('a.int_time', '>=', $start);
            $db = $db->where('a.int_time', '<=', $end);
        }

        return $db->get();
    }
}