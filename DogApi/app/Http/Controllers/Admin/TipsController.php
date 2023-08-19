<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tip;
use Illuminate\Http\Request;

class TipsController extends Controller
{
    public function index(Request $request)
    {
        $data = Tip::orderBy('sort', 'asc')
            ->paginate($request->query('per_page', 10));

        return ['ServerNo' => 200, 'ResultData' => $data];
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if ($data['sort'] <= 0) {
            return ['ServerNo' => 400, 'ResultData' => '排序字段必须大于0'];
        }
        // 查询sort是否存在
        $result = Tip::where('sort', $data['sort'])->count();
        if ($result) {
            return ['ServerNo' => 400, 'ResultData' => '排序字段不能重复'];
        }
        Tip::create($data);
        return ['ServerNo' => 200, 'ResultData' => '创建成功'];
    }

    public function deleteTip(Request $request)
    {
        $id = $request->input('id', 0);
        Tip::where('id', $id)->delete();
        return ['ServerNo' => 200, 'ResultData' => '删除成功'];
    }
}
