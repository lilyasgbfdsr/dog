<?php declare(strict_types=1);
namespace App\Services;

use App\Http\Traits\DateTimeTraits;
use App\Library\Tools\Common\Common;
use App\Models\DateTime;
use App\Repositories\DataOrderRepository;
use App\Repositories\DataReserveRepository;
use App\Repositories\DataReserveInfoRepository;
use App\Services\WeChatPayService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReserveService {
    use DateTimeTraits;

    private static $dataReserveRepository = null;
    private static $dataReserveInfoRepository = null;
    private static $dataOrderRepository = null;
    private static $weChatPayService = null;

    public function __construct(
        DataReserveRepository $dataReserveRepository,
        DataReserveInfoRepository $dataReserveInfoRepository,
        DataOrderRepository $dataOrderRepository,
        WeChatPayService $weChatPayService
    ) {
        self::$dataReserveRepository = $dataReserveRepository;
        self::$dataReserveInfoRepository = $dataReserveInfoRepository;
        self::$dataOrderRepository = $dataOrderRepository;
        self::$weChatPayService = $weChatPayService;
    }

    public function checkRule($where)
    {
        return self::$dataReserveRepository->getRuleData($where);
    }

    public function getCheckList($int_time)
    {
        $data = $this->getDateTime();

        $temp = [];
        foreach ($data['hour'] as $v) {
            // 时间段逻辑判断
            $number = $this->getHourNumber($int_time, $v->id);
            $gtCount = $this->getHourGTCount($int_time, $v->id);
            $dataType = 0;
            $countNum = 0;
            // 判断某人时间人数加起来人数
            $infoDay = self::$dataReserveInfoRepository->getCount(['int_time' => Common::getIntTime($int_time), 'type' => 1]);
            $infoHour = self::$dataReserveInfoRepository->getCount(['int_time' => Common::getIntTime($int_time), 'hour' => $v->id, 'type' => 2]);
            if ($infoDay || $infoHour) {
                $dataType = 2;
            } else if ($number >= config('config.reserve_number') || ($gtCount == 0 && $number >= 16)) {
                $dataType = 1;
            } else {
                $countNum = config('config.reserve_number') - $number;
            }
            $temp[] = ['time' => $v->start_time . '-' . $v->end_time, 'play' => $v->id, 'data_type' => $dataType, 'count' => $countNum];
        }
        return $temp;
    }

    /**
     * 查询同一时间段的总预约数(同一时间段多少桌)
     *
     * @param $date
     * @param $hour
     * @return array
     */
    public function getHourCount($date, $hour)
    {
        $where = ['a.int_time' => Common::getIntTime($date), 'a.hour' => $hour, 'b.status' => 1];
        return self::$dataReserveRepository->getHourCount($where);
    }

    public function getHourGTCount($date, $hour)
    {
        $where = ['a.int_time' => Common::getIntTime($date), 'a.hour' => $hour, 'b.status' => 1];
        return self::$dataReserveRepository->getHourGTCount($where);
    }

    /**
     * 查询同一时间段的总预约数(同一时间段多少桌)
     *
     * @param $date
     * @param $hour
     * @return array
     */
    public function getHourNumber($date, $hour)
    {
        $where = ['a.int_time' => Common::getIntTime($date), 'a.hour' => $hour, 'b.status' => 1];
        return self::$dataReserveRepository->getHourSum($where, 'number');
    }

    /**
     * 添加数据
     *
     * @param $data
     * @return array
     */
    public function ReserveStore($data)
    {
        if (empty($data['reserve']) || empty($data['order'])) {
            return ['ServerNo' => 400, 'ResultData' => '参数有误'];
        }

        DB::beginTransaction();
        try {
            $reserveRes = self::$dataReserveRepository->addData($data['reserve']);
            $orderRes   = self::$dataOrderRepository->addData($data['order']);
            if (!empty($reserveRes) && !empty($orderRes)) {
                DB::commit();
                return ['ServerNo' => 200, 'ResultData' => '操作成功'];
            } else {
                DB::rollBack();
                return ['ServerNo' => 400, 'ResultData' => '操作失败'];
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return ['ServerNo' => 400, 'ResultData' => '操作失败'];
        }
    }

    /**
     * 回调更新订单
     *
     * @param $data
     * @return array
     */
    public function orderNotify($data)
    {
        $order = [
            'status'         => 1,
            'appid'          => $data['appid'],
            'bank_type'      => $data['bank_type'],
            'cash_fee'       => $data['cash_fee'],
            'fee_type'       => $data['fee_type'],
            'is_subscribe'   => $data['is_subscribe'],
            'mch_id'         => $data['mch_id'],
            'nonce_str'      => $data['nonce_str'],
            'out_trade_no'   => $data['out_trade_no'],
            'openid'         => $data['openid'],
            'result_code'    => $data['result_code'],
            'sign'           => $data['sign'],
            'time_end'       => $data['time_end'],
            'total_fee'      => $data['total_fee'],
            'trade_type'     => $data['trade_type'],
            'transaction_id' => $data['transaction_id'],
        ];

        $where = ['order_id' => $data['out_trade_no']];

        // 查询这个订单对应的预约
        $reserve = self::$dataReserveRepository->getReserveOrder(['b.order_id' => $data['out_trade_no']]);
        \Log::info(json_encode($reserve));

        // 更新订单
        $result = self::$dataOrderRepository->updateData($where, $order);
        if (!empty($result)) {
            // 发送消息
            if (!empty($reserve)) {
                // $date = config('config.hour');
                $validTime = date('Y-m-d', $reserve->int_time) . '（ ' . $reserve->date_time . ' ）';
                self::$weChatPayService->templateMessage($reserve->name, $reserve->number, $validTime, $data['openid']);
            }
            return ['ServerNo' => 200, 'ResultData' => '操作成功'];
        } else {
            return ['ServerNo' => 400, 'ResultData' => '操作失败'];
        }
    }

    public function getOrderList($openId)
    {
        if (empty($openId)) {
            return [];
        }

        $data = self::$dataOrderRepository->getPageData(['a.openid' => $openId, 'a.status' => 1]);
        if (count($data) > 0) {
            $temp = config('config.hour');
            $tempHour = config('config.temp_hour');
            foreach ($data as $v) {
                $dateTime = DateTime::where(['id' => $v->hour])->first();
                $firstTime = date('Y-m-d', $v->int_time) . ' ' . ($dateTime->start_time ?? '');
                $v->time_out = (time() > strtotime($firstTime)) ? true : false;
            }
        }

        return count($data) > 0 ? $data : [];
    }

    public function getSendSms()
    {
        $intTime = strtotime(date("Y-m-d"),time());
        $reserve = self::$dataReserveRepository->getSendSms(['b.status' => 1, 'a.int_time' => $intTime]);
        return $reserve;
    }
}