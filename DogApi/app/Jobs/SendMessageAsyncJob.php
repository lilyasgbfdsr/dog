<?php

namespace App\Jobs;

use Exception;
use Curder\LaravelAliyunSms\AliyunSms;

class SendMessageAsyncJob extends Job
{
    private $phoneNumber = null;
    private $template = null;

    public function __construct($phoneNumber, $template)
    {
        $this->phoneNumber = $phoneNumber;
        $this->template = $template;
    }

    /**
     * @param SendMessage $sendMessage
     * @return bool
     */
    public function handle()
    {
        $this->sendSms($this->phoneNumber, $this->template);
    }

    public function sendSms($phoneNumber, $template = 'SMS_206880230', $variable = [])
    {
        $send = new AliyunSms();
        $response = $send->send($phoneNumber, $template, ['code' => '1234']);
        $message = $response->Message;
        if ($message == 'OK') {
            return ['status'=>true,'msg'=>'发送成功'];// 成功
        } else {
            app('log')->error(date('Y-m-d H:i:s') . ':\'' . $phoneNumber . '\'短信发送失败.失败原因是:' . $message);
            return ['status' => false, 'msg' => '发送失败'];// 失败
        }
    }

    /**
     * @param Exception $exception
     */
    public function failed(Exception $exception)
    {
        app('log')->error('短信发送失败：' . date('Y-m-d H:i:s'). json_encode($exception));
    }
}
