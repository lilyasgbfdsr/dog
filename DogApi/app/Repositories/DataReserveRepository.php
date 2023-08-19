<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DataReserveRepository
{
    use BaseRepository;

    protected static $table = 'data_reserve';

    /**
     * 获取带时间总条数
     *
     * @param array $where
     * @param string $start
     * @param string $end
     * @param $startField
     * @param $endField
     * @return bool
     */
    public function getPageCount($where = [], $start = '', $end = '', $startField = 'a.int_time', $endField = 'a.int_time')
    {
        $db = DB::table('data_reserve as a')
            ->leftJoin('data_order as b', 'a.reserve_id', '=', 'b.reserve_id');
        if (!empty($where)) {
            $db = $db->where($where);
        }
        if (!empty($start)) {
            $db = $db->where($startField, '>=', $start);
            $db = $db->where($endField, '<=', $end);
        }
        return $db->count();
    }

    public function getSumData($where, $field) {
        return DB::table('data_reserve as a')
            ->leftJoin('data_order as b', 'a.reserve_id', '=', 'b.reserve_id')
            ->where($where)
            ->where(['b.status' => 1])
            ->sum($field);
    }

    public function getPageData($where, $nowPage, $offset, $start = '', $end = '') {
        $db = DB::table('data_reserve as a')
            ->leftJoin('data_order as b', 'a.reserve_id', '=', 'b.reserve_id')
            ->where($where)
            ->select('a.*', 'b.*')
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

    public function getRuleData($where) {
        return DB::table('data_reserve as a')
            ->leftJoin('data_order as b', 'a.reserve_id', '=', 'b.reserve_id')
            ->where($where)
            ->count();
    }

    public function getReserveOrder($where) {
        return DB::table('data_reserve as a')
            ->leftJoin('data_order as b', 'a.reserve_id', '=', 'b.reserve_id')
            ->where($where)
            ->first();
    }

    public function getHourCount($where) {
        return DB::table('data_reserve as a')
            ->leftJoin('data_order as b', 'a.reserve_id', '=', 'b.reserve_id')
            ->where($where)
            ->where('a.operate_status', '!=', 2)
            ->count();
    }

    /**
     * 返回某个时段一单$num人以上的单数
     */
    public function getHourGTCount($where, $num = 2) {
        return DB::table('data_reserve as a')
            ->leftJoin('data_order as b', 'a.reserve_id', '=', 'b.reserve_id')
            ->where($where)
            ->where('a.operate_status', '!=', 2)
            ->where('a.number', '>', $num)
            ->count();
    }

    public function getHourSum($where, $field = 'id') {
        return DB::table('data_reserve as a')
            ->leftJoin('data_order as b', 'a.reserve_id', '=', 'b.reserve_id')
            ->where($where)
            ->where('a.operate_status', '!=', 2)
            ->lockForUpdate()
            ->sum($field);
    }

    public function getDateSumData($where, $field, $start = '', $end = '') {
        $db = DB::table('data_reserve as a')
            ->leftJoin('data_order as b', 'a.reserve_id', '=', 'b.reserve_id')
            ->where($where)
            ->where('a.operate_status', '!=', 2)
            ->where(['b.status' => 1]);

        if (!empty($start) && !empty($end)) {
            $db = $db->where('a.int_time', '>=', $start);
            $db = $db->where('a.int_time', '<=', $end);
        }

        return $db->sum($field);
    }

    public function getDateHourCount($where, $start = '', $end = '') {
        $db = DB::table('data_reserve as a')
            ->leftJoin('data_order as b', 'a.reserve_id', '=', 'b.reserve_id')
            ->where($where)
            ->where('a.operate_status', '!=', 2);

        if (!empty($start) && !empty($end)) {
            $db = $db->where('a.int_time', '>=', $start);
            $db = $db->where('a.int_time', '<=', $end);
        }

        return $db->count();
    }

    public function getSendSms($where) {
        return DB::table('data_reserve as a')
            ->leftJoin('data_order as b', 'a.reserve_id', '=', 'b.reserve_id')
            ->where($where)
            ->where('a.operate_status', '!=', 2)
            ->select('a.telephone')
            ->get();
    }
}