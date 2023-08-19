<?php

namespace App\Http\Controllers\Home;

use App\Http\Traits\DateTimeTraits;
use App\Library\Tools\Common\Common;
use App\Models\ConfigPrice;
use App\Models\DateTime;
use App\Models\Tip;
use App\Repositories\DataReserveInfoRepository;
use App\Services\ReserveService;
use App\Services\WeChatPayService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReserveController extends Controller
{
    use DateTimeTraits;

    private static $weChatPayService = null;
    private static $reserveService = null;
    private static $dataReserveInfoRepository = null;

    const APPID = 'wx64xxxxxx8dc';
    const APPSECRET = '416xxxxxxxxxxxxxxxx4f';

    public function __construct(
        WeChatPayService $weChatPayService,
        ReserveService $reserveService,
        DataReserveInfoRepository $dataReserveInfoRepository
    ) {
        self::$weChatPayService = $weChatPayService;
        self::$reserveService = $reserveService;
        self::$dataReserveInfoRepository = $dataReserveInfoRepository;
    }

    public function index(Request $request) {
        return view('home.home', ['app_id' => self::APPID, 'url' => 'http://api.xxxxxx.cn/reserveShow']);
    }

    public function getConfig(Request $request) {
        // appid
        $wx['appid'] = self::APPID;
        //时间戳
        $wx['timestamp'] = time();
        //生成签名的随机串
        $wx['noncestr'] = md5(time());
        //jsapi_ticket是公众号用于调用微信JS接口的临时票据。正常情况下，jsapi_ticket的有效期为7200秒，通过access_token来获取。
        $wx['jsapi_ticket'] = $this->actionTicket();
        //分享的地址，注意：这里是指当前网页的URL，不包含#及其后面部分，曾经的我就在这里被坑了，所以小伙伴们要小心了
        $wx['url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $string = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s", $wx['jsapi_ticket'], $wx['noncestr'], $wx['timestamp'], $wx['url']);
        //生成签名
        $wx['signature'] = sha1($string);
        return $wx;
    }

    public function actionTicket() {
        @$file = file_get_contents('./ticket');
        $info = json_decode($file,1);
        if ($info && $info['time'] > time())
            return $info['ticket'];

        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$this->actionAccessToken()."&type=jsapi";
        $info = file_get_contents($url);
        $info = json_decode($info,1);
        if ($info){
            $info['time'] = time()+$info['expires_in'];
            file_put_contents('./ticket', json_encode($info));
            return $info['ticket'];
        }else{
            return '失败';
        }
    }

    public function actionAccessToken() {
        @$file = file_get_contents('./token');
        $info = json_decode($file,1);
        if ($info && $info['time'] > time())
            return $info['access_token'];

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . self::APPID . "&secret=" . self::APPSECRET;
        $info = file_get_contents($url);
        $info = json_decode($info,1);
        if ($info) {
            $info['time'] = time()+$info['expires_in'];
            file_put_contents('./token',json_encode($info));
            return $info['access_token'];
        } else {
            return '失败';
        }
    }

    public function show(Request $request)
    {
        $input = $request->all();
        $openId = '';

        // 用code拿openid
        if (!empty($input['code'])) {
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . self::APPID . '&secret=' . self::APPSECRET . '&Code=' . $input['code'] . '&grant_type=authorization_code';
            $info = file_get_contents($url);
            $info = json_decode($info,1);
            if ($info && !empty($info['openid'])) {
                $openId = $info['openid'];
            }
        }

        $tips = Tip::orderBy('sort', 'asc')->select('tip')->get();
        $configPrice = ConfigPrice::first();

        return view('home.reserve', ['tips' => $tips, 'price' => $configPrice->price ?? 0, 'openid' => $openId]);
    }

    public function h5Show(Request $request)
    {
        $input = $request->all();
        $openId = 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8';

        $tips = Tip::orderBy('sort', 'asc')->select('tip')->get();
        $configPrice = ConfigPrice::first();

        return view('home.h5Reserve', ['tips' => $tips, 'price' => $configPrice->price ?? 0, 'openid' => $openId]);
    }

    public function getDate()
    {
        $data = $this->getDateTime();
        return [
            'hour' => [$data['hour']],
            'tempHour' => [$data['temp_hour']]
        ];
    }

    /**
     * 预约下单
     *
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function store(Request $request) {
        $input = $request->all();
        // 数据验证
        $validator = Validator::make($input, [
            'openid'      => 'required|string|between:5,255',
            'hour'        => 'required|integer',
            'name'        => 'required|string|between:1,32',
            'number'      => 'required|numeric',
            'date'        => 'required|numeric',
            'telephone'   => 'regex:/^1[3456789][0-9]{9}$/',
            'order_price' => 'required|numeric'
        ], [
            'required' => ':attribute参数缺失',
            'string'   => ':attribute不合法',
            'between'  => ':attribute区间不合法',
            'numeric'  => ':attribute必须是数字',
            'integer'  => ':attribute必须是整数',
            'telephone.regex' => '手机号不合法',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        if (empty($input['openid'])) {
            return $this->error('参数异常');
        }

        // 当天时间
        $intValTime = Common::intValTime($input['date']);
        $intTime = Common::getIntTime($intValTime);

        // 查询手机号是否当天已预约
        $ruleWhere = [
            'a.telephone' => $input['telephone'],
            'a.int_time'  => $intTime,
            'b.status'    => 1
        ];
        $checkRes = self::$reserveService->checkRule($ruleWhere);
        if (!empty($checkRes)) {
            return $this->error('手机号当天只能成功预约一次');
        }

        // 判断时间段是否不能预约
        $infoDay = self::$dataReserveInfoRepository->getCount(['int_time' => $intTime, 'type' => 1]);
        $infoHour = self::$dataReserveInfoRepository->getCount(['int_time' => $intTime, 'hour' => $input['hour'], 'type' => 2]);
        if ($infoDay || $infoHour) {
            return $this->error('该时间段暂停预约');
        }

        // 时间段逻辑判断
        $number = self::$reserveService->getHourNumber($intValTime, $input['hour']);

        // 判断某人时间人数加起来人数
        $configNumber = config('config.reserve_number');
        if ($number >= $configNumber) {
            return $this->error('该时间段预约已满');
        }
        if (($number + $input['number']) > $configNumber) {
            return $this->error('只剩余' . ($configNumber - $number)  . '个位置');
        }

        // 创建订单
        $reserveId = Common::getUuid();
        $orderId   = Common::getUuid();

        // 查询当前时间
        $dateTime = DateTime::where(['id' => $input['hour']])->first();
        if (!$dateTime) {
            return $this->error('时间异常');
        }

        $data = [
            'reserve' => [
                'reserve_id' => $reserveId,
                'int_time'   => $intTime,
                'hour'       => $input['hour'],
                'name'       => $input['name'],
                'telephone'  => $input['telephone'],
                'number'     => $input['number'],
                'add_time'   => $_SERVER['REQUEST_TIME'],
                'openid'     => $input['openid'],
                'date_time'  => $dateTime->start_time . '-' . $dateTime->end_time
            ],
            'order' => [
                'reserve_id'  => $reserveId,
                'order_id'    => $orderId,
                'order_price' => $input['order_price'],
                'status'      => 2,
            ]
        ];

        $result = self::$reserveService->ReserveStore($data);
        if (empty($result['ServerNo']) || $result['ServerNo'] != 200) {
            return $result;
        }

        // 调用微信支付授权接口
        return self::$weChatPayService->pay($orderId, $input['order_price'], $input['openid']);
    }

    public function h5Store(Request $request) {
        $input = $request->all();
        // 数据验证
        $validator = Validator::make($input, [
            'openid'      => 'required|string|between:5,255',
            'hour'        => 'required|integer',
            'name'        => 'required|string|between:1,32',
            'number'      => 'required|numeric',
            'date'        => 'required|numeric',
            'telephone'   => 'regex:/^1[3456789][0-9]{9}$/',
            'order_price' => 'required|numeric'
        ], [
            'required' => ':attribute参数缺失',
            'string'   => ':attribute不合法',
            'between'  => ':attribute区间不合法',
            'numeric'  => ':attribute必须是数字',
            'integer'  => ':attribute必须是整数',
            'telephone.regex' => '手机号不合法',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        if (empty($input['openid'])) {
            return $this->error('参数异常');
        }

        // 当天时间
        $intValTime = Common::intValTime($input['date']);
        $intTime = Common::getIntTime($intValTime);

        // 查询手机号是否当天已预约
        $ruleWhere = [
            'a.telephone' => $input['telephone'],
            'a.int_time'  => $intTime,
            'b.status'    => 1
        ];
        $checkRes = self::$reserveService->checkRule($ruleWhere);
        if (!empty($checkRes)) {
            return $this->error('手机号当天只能成功预约一次');
        }

        // 判断时间段是否不能预约
        $infoDay = self::$dataReserveInfoRepository->getCount(['int_time' => $intTime, 'type' => 1]);
        $infoHour = self::$dataReserveInfoRepository->getCount(['int_time' => $intTime, 'hour' => $input['hour'], 'type' => 2]);
        if ($infoDay || $infoHour) {
            return $this->error('该时间段暂停预约');
        }

        // 时间段逻辑判断
        $number = self::$reserveService->getHourNumber($intValTime, $input['hour']);

        // 判断某人时间人数加起来人数
        $configNumber = config('config.reserve_number');
        if ($number >= $configNumber) {
            return $this->error('该时间段预约已满');
        }
        if (($number + $input['number']) > $configNumber) {
            return $this->error('只剩余' . ($configNumber - $number)  . '个位置');
        }

        // 创建订单
        $reserveId = Common::getUuid();
        $orderId   = Common::getUuid();

        // 查询当前时间
        $dateTime = DateTime::where(['id' => $input['hour']])->first();
        if (!$dateTime) {
            return $this->error('时间异常');
        }

        $data = [
            'reserve' => [
                'reserve_id' => $reserveId,
                'int_time'   => $intTime,
                'hour'       => $input['hour'],
                'name'       => $input['name'],
                'telephone'  => $input['telephone'],
                'number'     => $input['number'],
                'add_time'   => $_SERVER['REQUEST_TIME'],
                'openid'     => $input['openid'],
                'date_time'  => $dateTime->start_time . '-' . $dateTime->end_time
            ],
            'order' => [
                'reserve_id'     => $reserveId,
                'order_id'       => $orderId,
                'order_price'    => $input['order_price'],
                'status'         => 1,
                'appid'          => 'wx644xxxxxxxx98dc',
                'bank_type'      => 'OTHERS',
                'cash_fee'       => number_format($input['number'] * 5, 2, '.', ','),
                'fee_type'       => 'CNY',
                'is_subscribe'   => 'Y',
                'mch_id'         => '1572202221',
                'nonce_str'      => '5ebe411cdd1aa',
                'out_trade_no'   => '95f21938967b11ea8fcb00163e16b93e',
                'openid'         => 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8',
                'result_code'    => 'SUCCESS',
                'sign'           => '5F78FCC9E4B9F909BBC6F0D248594F0D',
                'time_end'       => $_SERVER['REQUEST_TIME'],
                'total_fee'      => $input['number'] * 5 * 100,
                'trade_type'     => 'JSAPI',
                'transaction_id' => '4200000530202005158951784087',
            ]
        ];

        $result = self::$reserveService->ReserveStore($data);
        if (empty($result['ServerNo']) || $result['ServerNo'] != 200) {
            return $result;
        }

        // 调用微信支付授权接口
        return self::$weChatPayService->h5Pay($orderId, $input['order_price'], $input['openid']);
    }

    public function loding(Request $request) {
        $input = $request->all();
        return view('home.loding', ['config' => $input]);
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

        $int_time = Common::intValTime($input['date']);

        return $this->success($reserveService->getCheckList($int_time));
    }

    /**
     * 支付回调地址
     *
     * @return mixed
     */
    public function notify() {
        $payment = app('wechat.payment');
        $response = $payment->handlePaidNotify(function ($message, $fail) {
            Log::debug('支付宝回调开始');
            Log::debug($message);
            Log::debug('支付宝回调结束');
            if ($message['return_code'] === 'SUCCESS' && $message['result_code'] === 'SUCCESS') {
                Log::debug($message);
                //支付后，微信会在此处返回支付状态，就是$message，回调里面打印不出来，可通过写入日志里面查看，支付成功后更改订单状态。当然你也可以进行其他操作。
                $result = self::$reserveService->orderNotify($message);
                if (!empty($result) && ($result['ServerNo'] == 200)) {
                    Log::debug($message['out_trade_no']. '回调成功,更新成功');
                } else {
                    Log::debug($message['out_trade_no']. '回调成功,更新失败');
                }
                return true;
            } else {
                Log::debug($message['out_trade_no']. '回调失败');
                return $fail('失败');
            }
        });
        return $response;
    }

    public function orderIndex() {
        return view('home.home', ['app_id' => self::APPID, 'url' => 'http://api.xxxxxx.cn/order/list']);
    }

    public function orderH5Index(Request $request) {
        $input = $request->all();
        $openId = 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8';
        $order = [];

        if ($openId) {
            // 查询订单
            $order = self::$reserveService->getOrderList($openId);
        }

        return view('home.order', ['order' => $order]);
    }

    public function orderList(Request $request) {
        $input = $request->all();
        $openId = '';
        $order = [];

        // 用code拿openid
        if (!empty($input['code'])) {
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . self::APPID . '&secret=' . self::APPSECRET . '&Code=' . $input['code'] . '&grant_type=authorization_code';
            $info = file_get_contents($url);
            $info = json_decode($info,1);
            if ($info && !empty($info['openid'])) {
                $openId = $info['openid'];
            }
        }

        if ($openId) {
            // 查询订单
            $order = self::$reserveService->getOrderList($openId);
        }

        return view('home.order', ['order' => $order]);
    }
}
