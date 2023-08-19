<?php

namespace App\Http\Controllers\Admin;

use App\Library\Tools\Common\Common;
use App\Repositories\DataAdminRepository;
use Illuminate\Http\Request;
use App\Library\Tools\Redis\RedisTool;
use Gregwar\Captcha\PhraseBuilder;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Routing\Controller as BaseController;

class LoginController extends BaseController
{
    private static $adminRepository = null;
    protected $redis;

    public function __construct(DataAdminRepository $adminRepository, RedisTool $redisTool)
    {
        self::$adminRepository = $adminRepository;
        $this->redis = $redisTool;
    }

    public function login(Request $request) {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['username']) || empty($input['password'])) {
            return ['ServerNo' => 400, 'ResultData' => '用户名与密码不能为空'];
        }

        // 密码加盐
        $password = Common::md($input['password']);
        // 查询数据库
        $data = self::$adminRepository->getOneData(['username' => $input['username']]);
        // 判断用户是否存在或密码是否正确
        if (empty($data) || ($password != $data->password)) {
            return ['ServerNo' => 400, 'ResultData' => '用户名与密码不正确'];
        }

        // 更新token
        $token = Common::getUuid();
        $result = self::$adminRepository->updateData(['guid' => $data->guid], ['token' => $token]);
        if (empty($result)) {
            return ['ServerNo' => 400, 'ResultData' => '登陆失败,请重新登录!'];
        }

        // 返回结果
        return ['ServerNo' => 200, 'ResultData' => [
            'guid'     => $data->guid,
            'username' => $data->username,
            'token'    => $token
        ]];
    }

    public function info(Request $request)
    {
        $input = $request->all();
        // 查询用户
        $result = self::$adminRepository->getOneData(['guid' => $input['guid']]);
        if ($result->token != $input['token']) {
            return ['ServerNo' => 501, 'ResultData' => '用户在其它地方登录'];
        }
        return ['ServerNo' => 200, 'ResultData' => ['name' => $result->username]];
    }

    /**
     * 验证码
     *
     * @param Request $request
     * @author lvchang
     */
    public function getCheckVerifyCode(Request $request)
    {
        $phrase = new PhraseBuilder;
        // 设置验证码位数
        $code = $phrase->build(4);
        // 生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder($code, $phrase);
        $builder->setBackgroundColor(255, 255, 255);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build($width = 160, $height = 47, $font = null);
        $phrase = $builder->getPhrase();
        $this->redis->sEteX('verify_code' . $request['number'], 180, $phrase);
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/jpeg");
        $builder->output();
    }

    /**
     * 验证码验证逻辑
     *
     * @param  $params
     * @return array|false|string
     */
    private function verifyCode($params)
    {
        //验证码验证
        $key = 'verify_code';
        //验证码是否存在
        if (!$this->redis->exists($key . $params['number'])) {
            return ['status' => false, 'msg' => '验证码已过期'];
        }
        //验证码是否正确
        $code = $this->redis->get($key . $params['number']);
        if (strtolower($code) != strtolower($params['captcha'])) {
            return ['status' => false, 'msg' => '验证码错误'];
        }

        return ['status' => true, 'msg' => ''];
    }
}
