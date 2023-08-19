<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DateTime;
use Illuminate\Http\Request;

class DateTimesController extends Controller
{
    public function index(Request $request)
    {
        $data = DateTime::orderBy('sort', 'asc')
            ->paginate($request->query('per_page', 10));

        return ['ServerNo' => 200, 'ResultData' => $data];
    }

    public function store(Request $request)
    {
        $data = $request->all();
        // 查询sort是否存在
        if ($data['sort'] <= 0) {
            return ['ServerNo' => 400, 'ResultData' => '排序字段必须大于0'];
        }
        $result = DateTime::where('sort', $data['sort'])->count();
        if ($result) {
            return ['ServerNo' => 400, 'ResultData' => '排序字段不能重复'];
        }
        DateTime::create($data);
        return ['ServerNo' => 200, 'ResultData' => '创建成功'];
    }

    public function deleteDateTime(Request $request)
    {
        $id = $request->input('id', 0);
        DateTime::where('id', $id)->delete();
        return ['ServerNo' => 200, 'ResultData' => '删除成功'];
    }

    public function editDateTime(Request $request)
    {
        $id = $request->input('id', 0);
        if ($request->input('sort') <= 0) {
            return ['ServerNo' => 400, 'ResultData' => '排序字段必须大于0'];
        }
        // 查询sort是否存在
        $result = DateTime::where('sort', $request->input('sort'))->first();
        if ($result && $result->id != $id) {
            return ['ServerNo' => 400, 'ResultData' => '排序字段不能重复'];
        }
        DateTime::where('id', $id)
            ->update([
                'sort' => $request->input('sort'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time')
            ]);
        return ['ServerNo' => 200, 'ResultData' => '编辑成功'];
    }
}
