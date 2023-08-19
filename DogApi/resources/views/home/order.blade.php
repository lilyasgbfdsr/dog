<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
    <title>我的订单</title>
    <link rel="stylesheet" href="/home/css/all.css">
    <link rel="stylesheet" href="https://res.wx.qq.com/open/libs/weui/1.1.3/weui.min.css">
</head>
<body>
<header>
    我的订单
</header>
<div class="order_content">
    @if ($order)
        @foreach ($order as $v)
        <div class="order_item {{ ($v->number <= 0) ? 'order_item_gray' : ($v->time_out ? 'order_item_gray' : '') }}">
            <p>{{ date('Y-m-d', $v->int_time) }} <span class="times_box">{{ $v->date_time }}</span></p>
            <p>预约姓名:{{ $v->name . ' ' . $v->telephone }}</p>
            <p>预约人数:{{ $v->number ?: (int)($v->order_price/5) }}人</p>
            @if ($v->number <= 0)
                <div class="img_box done">
                    <img src="/home/images/after.png" />
                    <p>已取消</p>
                </div>
            @else
                @if (!$v->time_out)
                    <div class="img_box success">
                        <img src="/home/images/after.png" />
                        <p>预约成功</p>
                    </div>
                @else
                    <div class="img_box done">
                        <img src="/home/images/after.png" />
                        <p>预约过期</p>
                    </div>
                @endif
            @endif
        </div>
        @endforeach
    @else
        <div style="text-align: center;line-height: 50px;">暂无数据</div>
    @endif
</div>
</body>
</html>
