<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>柯基预约系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="/home/css/all.css">
    <link rel="stylesheet" href="https://res.wx.qq.com/open/libs/weui/1.1.3/weui.min.css">
</head>

<body>
<header>柯基预约系统</header>
<div class="form">
    <div class="row">
        <label for="date">预约日期</label>
        <div class="time_box">
            <button id="time_1">读取中...</button>
            <button id="time_2">读取中...</button>
            <button id="time_3">读取中...</button>
            <button id="time_4">读取中...</button>
            <button id="time_5">读取中...</button>
            <button id="time_6">读取中...</button>
            <button id="time_7">读取中...</button>
            <button id="time_8">读取中...</button>
            <button id="time_9">读取中...</button>
        </div>
    </div>
    <div class="row">
        <label for="time">预约时间</label>
        <select id="time">
            <option disabled selected value>请选择预约时间</option>
        </select>
        <p class="tit" id="tit2" style="display: none; margin-left: 30%;font-size: 12px;">距开场15分钟内及逾期场次不可预约</p>
    </div>
    <div class="row">
        <label>预约资料<small>到店需核对手机号码哟</small></label>
        <div class="row2" style="margin-top: 20px;">
            <label for="name">姓名&emsp;&emsp;</label>
            <input id="name" type="text">
        </div>
        <div class="row2">
            <label>电话&emsp;&emsp;</label>
            <input type="text" id="phone">
        </div>
        <div class="row2">
            <label>人数&emsp;&emsp;</label>
            <select id="number">
                <option value="0">请选择</option>
                <option value="1">1人</option>
                <option value="2">2人</option>
                <option value="3">3人</option>
                <option value="4">4人</option>
                <option value="5">5人</option>
            </select>
        </div>
    </div>
    <div class="row">
        <label>预约付款<small>预约费用到店支付入场费时予以抵扣</small></label>
        <div class="row2" style="margin-top: 20px;">
            <label>预约费用</label>
            <input type="text" id="mony" disabled="true" value="{{ $price }}">
        </div>
    </div>
    <div class="bz">
        <p style='color: #333;'>
            <span style="color:red;font-weight:bold;">温馨提示：</span><br/>
            @foreach ($tips as $v)
                <p style="margin-bottom: 5px;color: #333;">{{ $v->tip }}</p>
            @endforeach
    </div>
    <div style="text-align: center;">
        <button id="button">提交预约并支付</button>
    </div>

    <!-- loading toast -->
    <div id="loadingToast" style="display:none;">
        <div class="weui-mask_transparent"></div>
        <div class="weui-toast">
            <i class="weui-loading weui-icon_toast"></i>
            <p class="weui-toast__content">数据加载中</p>
        </div>
    </div>

    <p style="margin-top:20px;text-align: center;color: #ffb866;">最终解释权归店铺所有</p>
</div>
</body>
</html>
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="https://cdn.bootcss.com/jquery/2.1.4/jquery.js"></script>
<!--方法调用-->
<script>
    /**
     * 在Safari和IE8上执行 new Date('2017-12-8 11:36:45'); 会得到Invalid Date
     * 本函数重写默认的Date函数，以解决其在Safari，IE8上的bug
     */
    Date = function (Date) {
        MyDate.prototype = Date.prototype;
        return MyDate;

        function MyDate() {
            // 当只有一个参数并且参数类型是字符串时，把字符串中的-替换为/
            if (arguments.length === 1) {
                let arg = arguments[0];
                if (Object.prototype.toString.call(arg) === '[object String]' && arg.indexOf('T') === -1) {
                    arguments[0] = arg.replace(/-/g, "/");
                    // console.log(arguments[0]);
                }
            }
            let bind = Function.bind;
            let unbind = bind.bind(bind);
            return new (unbind(Date, null).apply(null, arguments));
        }
    }(Date);

    $(function() {
        var time_flag = 1
        var h_flag = 1
        var time_list;  // 写死
        var temp_time_list;
        var price  = '{{ $price }}';

        // 获取时间
        $.ajax({
            url: "getDate",  // 请求的url地址
            dataType:"json",
            async: true,
            type:"GET",    // 请求方式
            success: function(data) {
                time_list = data.hour[0]
                temp_time_list = data.tempHour[0]
            }
        });

        function GetDateStr(AddDayCount) {
            var dd = new Date();
            dd.setDate(dd.getDate() + AddDayCount);//获取AddDayCount天后的日期
            var m = dd.getMonth() + 1;//获取当前月份的日期
            var d = dd.getDate();
            return m + "月" + d;
        }
        function GetDate(AddDayCount) {
            var dd = new Date();
            var tt = dd.setDate(dd.getDate() + AddDayCount);//获取AddDayCount天后的日期
            return tt;
        }
        function math_time(tt){
            var date1 = new Date(); //现在时间
            var date2 = new Date(tt); //场次时间
            var date3 = date2.getTime() - date1.getTime() //时间差的毫秒
            var minutes = Math.floor(date3 / (60*1000)+1)
            return minutes
        }
        function Getselect() {
            var t_val;
            $('.tit').hide()
            var t_play = $("#time").find("option:selected").attr('data_t');
            var my_d = $('.time_box').find('.act').attr('id')
            var myDate = new Date();
            var year=myDate.getFullYear();        //获取当前年
            var month=myDate.getMonth()+1;   //获取当前月
            var date=myDate.getDate();            //获取当前日

            // switch(t_play){
            //     case "1": t_val=year+"-"+month+"-"+date+" 12:00:00" ;break;
            //     case "2": t_val=year+"-"+month+"-"+date+" 13:20:00" ;break;
            //     case "3": t_val=year+"-"+month+"-"+date+" 14:40:00" ;break;
            //     case "4": t_val=year+"-"+month+"-"+date+" 16:00:00" ;break;
            //     case "5": t_val=year+"-"+month+"-"+date+" 17:25:00" ;break;
            //     case "6": t_val=year+"-"+month+"-"+date+" 18:40:00" ;break;
            //     case "7": t_val=year+"-"+month+"-"+date+" 18:00:00" ;break;
            // }
            t_val = year + "-" + month + "-" + date + " " + temp_time_list[0].start_time;
            const index = temp_time_list.findIndex(item => parseInt(item.id) === parseInt(t_play));
            if (index != -1) {
                t_val = year + "-" + month + "-" + date + " " + temp_time_list[index].start_time;
            }
            console.log(t_val, temp_time_list, t_play);
            if (my_d == 'time_1' && (math_time(t_val) <= 15)) {
                $('#tit2').show();
                h_flag = 0; 
                return 
            } else { 
                $('#tit2').hide(); 
                h_flag = 1 
            }

            var data_type = $("#time").find("option:selected").attr("data_type")
            if (data_type == "1" || data_type == "2") {
                time_flag = 0
                $('#tit1').show()
            } else {
                time_flag = 1
                $('#tit1').hide()
            }
        }
        function Clearselect(){
            $('#time').html('')
        }
        $('#time_1').html(GetDateStr(0));
        $('#time_1').attr('times', GetDate(0));
        $('#time_2').html(GetDateStr(1));
        $('#time_2').attr('times', GetDate(1));
        $('#time_3').html(GetDateStr(2));
        $('#time_3').attr('times', GetDate(2));
        $('#time_4').html(GetDateStr(3));
        $('#time_4').attr('times', GetDate(3));

        $('#time_5').html(GetDateStr(4));
        $('#time_5').attr('times', GetDate(4));

        $('#time_6').html(GetDateStr(5));
        $('#time_6').attr('times', GetDate(5));

        $('#time_7').html(GetDateStr(6));
        $('#time_7').attr('times', GetDate(6));
        $('#time_8').html(GetDateStr(7));
        $('#time_8').attr('times', GetDate(7));

        $('#time_9').html(GetDateStr(8));
        $('#time_9').attr('times', GetDate(8));

        $('.time_box button').click(function () {
            $('.time_box').find('button').removeClass('act')
            $(this).addClass('act')
            /*这里用ajax获取select*/
            $.ajax({
                url:"reserveCheck",    //请求的url地址
                dataType:"json",
                async:true,
                data:{
                    "date":$('.time_box').find('.act').attr('times'),
                    '_token': '{{csrf_token()}}'
                },    //这里向后台发送的是所需日期的时间戳
                type:"POST",   //请求方式
                success: function(data) {
                    if (data.ServerNo == '200') {
                        // req为成功获取对应日期预约数据的demo  play:当日场次； data_type：是否约满 1是 0否 2设置暂停预约了
                        var req = data.ResultData
                        Clearselect()
                        $('#time').append(`<option disabled selected value>请选择预约时间</option>`)
                        for(let i = 0; i < req.length; i++) {
                            let item = req[i]
                            let tip = ''
                            if (item.data_type == 0) {
                                tip = item.count <= 5 ? ('剩余' + item.count + '座位') : '未约满'
                            } else if (item.data_type == 1) {
                                tip = '已约满'
                            } else {
                                tip = '不可预约'
                            }
                            if (tip == '已约满') {
                                $('#time').append(`<option data_t="${item.play}" data_type="${item.data_type}" style="color:red">${time_list[i].start_time} - ${time_list[i].end_time}(${tip})</option>`)
                            } else {
                                $('#time').append(`<option data_t="${item.play}" data_type="${item.data_type}">${time_list[i].start_time} - ${time_list[i].end_time}(${tip})</option>`)
                            }
                        }
                        Getselect()
                    }
                }
            });
        })
        $("#number").change(function () {
            var opt = $("#number").val();
            $('#mony').val(opt * price)
            alert("目前暂不对12岁以下朋友开放敬请谅解")
        })
        $("#time").change(function () {
            Getselect()
        })
        $('#button').click(function () {
            var time = $("#time").find("option:selected").attr('data_t');//发送的是场次 1234567
            var date = $('.time_box').find('.act').attr('times')//发送的日期的时间戳
            var name = $("#name").val();//姓名
            var telephone = $("#phone").val();//电话
            var number = $("#number").find("option:selected").val()//人数
            var mony = $("#mony").val();//钱
            // if (time_flag == 0) { alert("抱歉当前时段不可预约"); return }
            // if (h_flag == 0) { alert("距开场15分钟内及逾期场次不可预约"); return }
            // 判断
            console.log(number)
            console.log(mony)
            if(!time || !date || !name || !telephone || !number || !mony){
                alert("表单均为必填项哟！");
                return
            }
            if (parseInt(number) <= 0) {
                alert("请选择预约人数！");
                return
            }
            if (parseInt(mony) <= 0) {
                alert("预约金额错误！");
                return
            }
            if(!(/^[\u0391-\uFFE5A-Za-z]+$/.test(name))){
                alert("姓名必须是中文或英文");
                return false;
            }
            if(!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(telephone))){
                alert("手机号码错误");
                return false;
            }
            let param = {
                'hour': time,
                'date': date,
                'name': name,
                'telephone': telephone,
                'number': number,
                'order_price': mony,
                "openid": 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8',
                '_token': '{{csrf_token()}}'
            }
            $.ajax({
                url: "reserveH5Store",  // 请求的url地址
                dataType:"json",
                async: true,
                beforeSend: function () {
                    $("#loadingToast").show();
                },
                data: param,    // 这里向后台发送的是所需日期的时间戳
                type:"POST",    // 请求方式
                success: function(data) {
                    if (data.ServerNo == '200') {
                        let config = data.ResultData;
                        window.location.href = 'orderH5Index'
                    } else {
                        alert(data.ResultData);
                    }
                },
                complete: function () {
                    $("#loadingToast").hide();
                },
                error: function (data) {
                    alert(222);
                    $("#loadingToast").hide();
                }
            });
        })

        // 选中第二项
        $('#number').find('option:eq(0)').attr('selected','selected');
        $('#mony').val(0)
    });
</script>
