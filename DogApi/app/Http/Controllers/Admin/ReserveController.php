<?php

namespace App\Http\Controllers\Admin;

use App\Http\Traits\DateTimeTraits;
use App\Library\Tools\Common\Common;
use App\Models\DateTime;
use App\Repositories\DataReserveInfoRepository;
use App\Repositories\DataReserveRepository;
use App\Services\ReserveService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReserveController extends Controller
{
    use DateTimeTraits;

    private static $dataReserveRepository = null;
    private static $reserveService = null;
    private static $dataReserveInfoRepository = null;

    public function __construct(
        DataReserveRepository $dataReserveRepository,
        ReserveService $reserveService,
        DataReserveInfoRepository $dataReserveInfoRepository
    )
    {
        self::$dataReserveRepository = $dataReserveRepository;
        self::$reserveService = $reserveService;
        self::$dataReserveInfoRepository = $dataReserveInfoRepository;
    }

    public function index(Request $request) {
        $input = $request->all();
        $start = 0;
        $end = 0;
        $where = [];
        if (!empty($input['where']['hour'])) {
            $where['a.hour'] = $input['where']['hour'];
        }
        if (!empty($input['where']['status'])) {
            $where['b.status'] = $input['where']['status'];
        }
        // 增加时间搜索条件
        if (!empty($input['where']['searchDate'])) {
            $start = strtotime(date('Y-m-d 00:00:00', strtotime($input['where']['searchDate'][0])));
            $end = strtotime(date('Y-m-d 00:00:00', strtotime($input['where']['searchDate'][1])));
        }

        // 生成最近七天日期
        $date = [];
        for ($i= 0; $i< 9; $i++){
            $date[$i] = date('Y-m-d' ,strtotime( '+' . $i .' days', time()));
        }

        // 循环查出今天每个时间段的场次
        $intTimeWhere = ['b.status' => 1];
        $todayCount = self::$dataReserveRepository->getDateSumData($intTimeWhere, 'number', $start, $end);
        $dateTime = $this->getDateTime();
        foreach ($dateTime['hour'] as $v) {
            $intTimeWhere['a.hour'] = $v->id;
            $v->sum = self::$dataReserveRepository->getDateSumData($intTimeWhere, 'number', $start, $end);
            $v->count = self::$dataReserveRepository->getDateHourCount($intTimeWhere, $start, $end);
            $v->glt_count = self::$reserveService->getHourGTCount($start, $v->id);
        }

        $count = self::$dataReserveRepository->getPageCount($where, $start, $end);
        if (empty($count)) {
            return [
                'ServerNo' => 200, 'ResultData' => [
                    'total' => 0,
                    'data' => [],
                    'list' => $dateTime['hour'],
                    'date' => $date,
                    'count' => $todayCount
                ]
            ];
        }
        $data = self::$dataReserveRepository->getPageData($where, $input['page'], $input['limit'], $start, $end);
        $weekArray =["日", "一", "二", "三", "四", "五", "六"];
        $tempHour = $this->getDateTime()['hour'];
        foreach ($data as $v) {
            $v->week = $weekArray[date('w', $v->int_time)];
        }

        return ['ServerNo' => 200, 'ResultData' => [
            'total' => $count,
            'data' => $data,
            'list' => $dateTime['hour'],
            'date' => $date,
            'count' => $todayCount
        ]];
    }

    public function delete(Request $request)
    {
        $input = $request->all();
        if (empty($input['reserve_id'])) {
            ['ServerNo' => 400, 'ResultData' => '参数缺失'];
        }

        $result = self::$dataReserveRepository->updateData(
            ['reserve_id' => $input['reserve_id']],
            ['operate_status' => 2, 'number' => 0]
        );
        if (empty($result)) {
            return ['ServerNo' => 400, 'ResultData' => '操作失败'];
        } else {
            return ['ServerNo' => 200, 'ResultData' => '操作成功'];
        }
    }

    public function sign(Request $request)
    {
        $input = $request->all();
        if (empty($input['reserve_id'])) {
            ['ServerNo' => 400, 'ResultData' => '参数缺失'];
        }

        $result = self::$dataReserveRepository->updateData(
            ['reserve_id' => $input['reserve_id']],
            ['check_status' => 1]
        );
        if (empty($result)) {
            return ['ServerNo' => 400, 'ResultData' => '操作失败'];
        } else {
            return ['ServerNo' => 200, 'ResultData' => '操作成功'];
        }
    }

    /**
     * 检查预约人数
     *
     * @param Request $request
     * @return array
     */
    public function check(Request $request, ReserveService $reserveService)
    {
        $input = $request->all();

        if (empty($input['date'])) {
            return ['ServerNo' => 400, 'ResultData' => '参数错误'];
        }

        $int_time = strtotime($input['date']);

        return $this->success($reserveService->getCheckList($int_time));
    }

    public function update(Request $request)
    {
        $input = $request->all();
        // 数据验证
        $validator = Validator::make($input, [
            'reserve_id'  => 'required',
            'hour'        => 'required|integer',
            'date'        => 'required',
        ], [
            'required' => ':attribute参数缺失',
            'between'  => ':attribute区间不合法',
            'integer'  => ':attribute必须是整数',
        ]);
        if ($validator->fails()) {
            return response()->json(['ServerNo' => 400, 'ResultData' => $validator->errors()->first()]);
        }

        // 查询数据是否存在
        $data = self::$dataReserveRepository->getOneData(['reserve_id' => $input['reserve_id']]);
        if (empty($data)) {
            return response()->json(['ServerNo' => 400, 'ResultData' => '数据不存在']);
        }

        // 当天时间
        $intValTime = strtotime($input['date']);
        $intTime = Common::getIntTime($intValTime);

        if ($data->int_time == $intTime && $data->hour == $input['hour']) {
            return response()->json(['ServerNo' => 400, 'ResultData' => '不能修改为原日期的原时间段']);
        }


        // 时间段逻辑判断
        $number = self::$reserveService->getHourNumber($intValTime, $input['hour']);
        
        // 判断某人时间人数加起来人数
        $configNumber = config('config.reserve_number');
        if ($number >= $configNumber) {
            return ['ServerNo' => 400, 'ResultData' => '该时间段预约已满'];
        }
        if (($number + $data->number) > $configNumber) {
            return ['ServerNo' => 400, 'ResultData' => '只剩余' . ($configNumber - $number)  . '个位置'];
        }

        // 查询当前时间
        $dateTime = DateTime::where(['id' => $input['hour']])->first();
        if (!$dateTime) {
            return $this->error('时间异常');
        }

        $result = self::$dataReserveRepository->updateData(
            ['reserve_id' => $input['reserve_id']],
            ['int_time'   => $intTime, 'hour' => $input['hour'], 'date_time'  => $dateTime->start_time . '-' . $dateTime->end_time]
        );
        if (empty($result)) {
            return ['ServerNo' => 400, 'ResultData' => '操作失败'];
        } else {
            return ['ServerNo' => 200, 'ResultData' => '操作成功'];
        }
    }

    public function infoList(Request $request) {
        $dateTimes = $this->getDateTime();

        $input = $request->all();
        $start = 0;
        $end = 0;
        $where = [];
        if (!empty($input['where']['hour'])) {
            $where['a.hour'] = $input['where']['hour'];
        }
        // 增加时间搜索条件
        if (!empty($input['where']['searchDate'])) {
            $start = strtotime(date('Y-m-d 00:00:00', strtotime($input['where']['searchDate'][0])));
            $end = strtotime(date('Y-m-d 00:00:00', strtotime($input['where']['searchDate'][1])));
        }

        $count = self::$dataReserveInfoRepository->getPageCount($where, $start, $end);
        if (empty($count)) {
            return [
                'ServerNo' => 200, 'ResultData' => [
                    'total' => 0,
                    'data' => [],
                    'time_array' => $dateTimes['hour']
                ]
            ];
        }
        $data = self::$dataReserveInfoRepository->getPageData($where, $input['page'], $input['limit'], $start, $end);
        $weekArray =["日", "一", "二", "三", "四", "五", "六"];
        foreach ($data as $v) {
            $v->week = $weekArray[date('w', $v->int_time)];
        }

        return ['ServerNo' => 200, 'ResultData' => [
            'total' => $count,
            'data' => $data,
            'time_array' => $dateTimes['hour']
        ]];
    }

    public function infoStore(Request $request)
    {
        $input = $request->all();
        // 数据验证
        $validator = Validator::make($input, [
            'type'        => 'required|integer|between:1,2',
            'date'        => 'required',
        ], [
            'required' => ':attribute参数缺失',
            'between'  => ':attribute区间不合法',
            'integer'  => ':attribute必须是整数',
        ]);
        if ($validator->fails()) {
            return response()->json(['ServerNo' => 400, 'ResultData' => $validator->errors()->first()]);
        }

        // 当天时间
        $intValTime = strtotime($input['date']);
        $intTime = Common::getIntTime($intValTime);

        // 查询数据是否存在
        $checkWhere = ['int_time' => $intTime];
        $resWhere = ['a.int_time' => $intTime];
        if ($input['type'] == 2) {
            // 查询整天是否存在
            $data = self::$dataReserveInfoRepository->getOneData(['int_time' => $intTime, 'type' => 1]);
            if (!empty($data)) {
                return response()->json(['ServerNo' => 400, 'ResultData' => '不能添加预约时间段配置,已存在整天预约时间配置']);
            }
            $checkWhere['hour'] = $input['hour'];
            $resWhere['a.hour'] = $input['hour'];
        }
        $resWhere['b.status'] = 1;
        $checkWhere['type'] = $input['type'];
        $data = self::$dataReserveInfoRepository->getOneData($checkWhere);
        if (!empty($data)) {
            return response()->json(['ServerNo' => 400, 'ResultData' => '数据已存在']);
        }

        // 检查当天是否有预约
        $res = self::$dataReserveRepository->getHourCount($resWhere);
        if ($res) {
            return response()->json(['ServerNo' => 400, 'ResultData' => '当天或当时间段已经有预约']);
        }

        $checkWhere['add_time'] = time();
        $result = self::$dataReserveInfoRepository->addData($checkWhere);
        if (empty($result)) {
            return ['ServerNo' => 400, 'ResultData' => '操作失败'];
        } else {
            return ['ServerNo' => 200, 'ResultData' => '操作成功'];
        }
    }

    public function infoDelete(Request $request)
    {
        $input = $request->all();
        // 数据验证
        $validator = Validator::make($input, [
            'id'        => 'required|integer'
        ], [
            'required' => ':attribute参数缺失',
            'integer'  => ':attribute必须是整数',
        ]);
        if ($validator->fails()) {
            return response()->json(['ServerNo' => 400, 'ResultData' => $validator->errors()->first()]);
        }

        $data = self::$dataReserveInfoRepository->getOneData(['id' => $input['id']]);
        if (empty($data)) {
            return response()->json(['ServerNo' => 400, 'ResultData' => '数据不存在']);
        }

        $result = self::$dataReserveInfoRepository->deleteData(['id' => $input['id']]);
        if (empty($result)) {
            return ['ServerNo' => 400, 'ResultData' => '操作失败'];
        } else {
            return ['ServerNo' => 200, 'ResultData' => '操作成功'];
        }
    }
}
