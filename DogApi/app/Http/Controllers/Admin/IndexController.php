<?php

namespace App\Http\Controllers\Admin;

use App\Library\Tools\Common\Common;
use App\Repositories\DataReserveRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class IndexController extends BaseController
{
    private static $dataReserveRepository = null;

    public function __construct(DataReserveRepository $dataReserveRepository)
    {
        self::$dataReserveRepository = $dataReserveRepository;
    }

    public function index(Request $request) {
        $intTime = Common::getIntTime(time());
        $count = self::$dataReserveRepository->getSumData(['a.int_time' => $intTime], 'a.number');
        return ['ServerNo' => 200, 'ResultData' => [
            'int_time' => $intTime,
            'reserve_today' => $count ?? 0
        ]];
    }
}
