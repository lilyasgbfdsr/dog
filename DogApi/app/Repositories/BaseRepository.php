<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

/**
 * 基础的数据仓库类
 *
 * Class BaseRepository
 * @package App\Repositories
 */
trait BaseRepository
{
    /**
     * 添加数据
     *
     * @param $param
     * @return bool
     */
    public function addData($param)
    {
        if (empty($param)) {
            return false;
        }

        return DB::table(static::$table)->insertGetId($param);
    }

    /**
     * 更新数据
     *
     * @param $where
     * @param $data
     * @return bool
     */
    public function updateData($where, $data)
    {
        if (empty($where) || empty($data)) {
            return false;
        }

        return  DB::table(static::$table)->where($where)->update($data);
    }

    /**
     * 更新数据
     *
     * @param $where
     * @param $data
     * @return bool
     */
    public function deleteData($where)
    {
        if (empty($where)) {
            return false;
        }

        return  DB::table(static::$table)->where($where)->delete();
    }

    /**
     * 查询一条记录
     *
     * @param $where
     * @return mixed
     */
    public function getOneData($where)
    {
        if (empty($where)) {
            return false;
        }

        return DB::table(static::$table)->where($where)->first();
    }

    /**
     * 获取总条数
     *
     * @param array $where
     * @return mixed
     */
    public function getCount($where = [])
    {
        if (empty($where)) {
            return DB::table(static::$table)->count();
        } else {
            return DB::table(static::$table)->where($where)->count();
        }
    }

    /**
     * 获取某个栏位的总数量
     *
     * @param array $where
     * @param string $field
     * @return mixed
     */
    public function getSum($where = [], $field = 'id')
    {
        if (empty($where)) {
            return DB::table(static::$table)->count();
        } else {
            return DB::table(static::$table)->where($where)->sum($field);
        }
    }

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
    public function getTimeCount($where = [], $start = '', $end = '', $startField = 'int_time', $endField = 'int_time')
    {
        if (empty($start) || empty($end)) {
            return false;
        }
        $db = DB::table(static::$table);
        if (!empty($where)) {
            $db = $db->where($where);
        }
        if (!empty($start)) {
            $db = $db->where($startField, '>=', $start);
            $db = $db->where($endField, '<=', $end);
        }
        return $db->count();
    }

    /**
     * 获取所有数据
     *
     * @param array $where
     * @param array $select
     * @return mixed
     * @author gpc
     */
    public function getAllData($where = [], $select = [])
    {
        if (empty($where)) {
            if (empty($select)) {
                return self::$db->table(static::$table)->get();
            }
            return self::$db->table(static::$table)->select($select)->get();
        }

        if (empty($select)) {
            return self::$db->table(static::$table)->where($where)->get();
        } else {
            return self::$db->table(static::$table)
                ->where($where)
                ->select($select)
                ->get();
        }
    }
}
