<?php declare(strict_types=1);
namespace App\Services;

use EasyWeChat\Factory;
use Illuminate\Support\Facades\Log;

class WeChatPayService {
    /**
     * 获取config值
     * @return array
     */
    public static function getConfig() {
        return $config = [
            'app_id' => env('WECHAT_PAYMENT_APPID', ''),
            'mch_id' => env('WECHAT_PAYMENT_MCH_ID', ''),
            'key' => env('WECHAT_PAYMENT_KEY', ''),
            'cert_path' => env('WECHAT_PAYMENT_CERT_PATH', ''),
            'key_path' => env('WECHAT_PAYMENT_KEY_PATH', ''),
            'notify_url' => env('NOTIFY_URL'), // 默认支付结果通知地址
        ];
    }

    public function h5Pay($orderId, $orderPrice, $openId) {
        return ['ServerNo' => 200, 'ResultData' => 'xxxxxxxxx'];
    }

    /**
     * 获取支付授权签名
     *
     * @param $orderId
     * @param $orderPrice
     * @param $openId
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function pay($orderId, $orderPrice, $openId) {
        if (empty($openId)) {
            return ['ServerNo' => 400, 'ResultData' => '用户openid未找到'];
        }
        // debug
        if ($openId == 'oU-PAs-LC2-hGiJ-TndYiyaT5Sm8') {
            $orderPrice = 0.01;
        }
        $app = Factory::payment(self::getConfig());
        $result = $app->order->unify([
            'body'         => '豆柴咖啡休闲体验',
            'out_trade_no' => $orderId,
            'total_fee'    => $orderPrice * 100,  // 以分为单位
            'notify_url'   => url('order/notify'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'   => 'JSAPI',
            'openid'       => $openId,
        ]);
        $jsSdk = $app->jssdk;
        $config = $jsSdk->sdkConfig($result['prepay_id']);
        return ['ServerNo' => 200, 'ResultData' => $config];
    }

    public function templateMessage($name, $number, $validTime, $openId) {
        Log::debug('开始模板消息');
        $app = Factory::officialAccount([
            'app_id'    => 'wx6xxxxxxxxxdc',
            'secret'    => '41xxxxxxxxxxxxxxxxxxxxxxxxxf',
            'token'     => 'easywechat',
        ]);
        $result = $app->template_message->send([
            'touser' => $openId,//用户openid
            'template_id' => 'OEl7zY_xxxxxxxxxxxj63bg',//发送的模板id
            'url' => '',//发送后用户点击跳转的链接
            'data' => [
                'first' => [
                    'value' => $name . '，您已预定成功！建议您提前到店准备入场。如有迟到，本场座位可能无法为您保留。'
                ],
                'productType' => [
                    'value' => '商品名：柯基互动体验50分钟'
                ],
                'number' => [
                    'value' => $number . '人'
                ],
                'expDate' => [
                    'value' => $validTime
                ],
                'remark' => [
                    'value' => '备注：如有疑问请致电客服咨询：13400000000。柯基期待您的光临~~'
                ]
            ],
        ]);
        Log::debug(json_encode($result));
    }
}