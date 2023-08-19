<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigPrice;
use Illuminate\Http\Request;

class ConfigPriceController extends Controller
{
    public function index(Request $request)
    {
        $data = ConfigPrice::latest()
            ->paginate($request->query('per_page', 10));

        return ['ServerNo' => 200, 'ResultData' => $data];
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if ($data['price'] <= 0) {
            return ['ServerNo' => 400, 'ResultData' => '预约金额不能小于0'];
        }
        // 查询sort是否存在
        $record = ConfigPrice::first();
        if (!$record) {
            ConfigPrice::create($data);
        } else {
            ConfigPrice::where('id', $record->id)->update(['price' => $data['price']]);
        }
        return ['ServerNo' => 200, 'ResultData' => '设置成功'];
    }
}
