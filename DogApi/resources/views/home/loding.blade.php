<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"
    />
    <title>柯基预约系统</title>
    <link rel="stylesheet" href="css/all.css" />
    <link
      rel="stylesheet"
      href="http://res.wx.qq.com/open/libs/weui/1.1.3/weui.min.css"
    />
  </head>
  <body>
    <div style="text-align: center;margin-top: 100px;" class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg-primary"></i></div>
    <div style="text-align: center;" class="weui-msg__text-area">
      <h2 class="weui-msg__title">正在支付请稍等...</h2>
    </div>
  </body>
</html>

<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>


<!--方法调用-->
<script>
  $(document).ready(function() {
    function onBridgeReady(){
      WeixinJSBridge.invoke(
              'getBrandWCPayRequest', {
                "appId": '{{ $config['appId'] }}',     //公众号名称，由商户传入
                "timeStamp": '{{ $config['timeStamp'] }}',         //时间戳，自1970年以来的秒数
                "nonceStr": '{{ $config['nonceStr'] }}', //随机串
                "package": '{{ $config['package'] }}',
                "signType": '{{ $config['signType'] }}',         //微信签名方式：
                "paySign": '{{ $config['paySign'] }}' //微信签名
              },
              function(res) {
                if (res.err_msg == "get_brand_wcpay_request:ok") {
                      // 使用以上方式判断前端返回,微信团队郑重提示：
                      //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠
                      alert('预约成功');
                      WeixinJSBridge.call("closeWindow");
                  } else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                      alert('预约不成功，因为您尚未支付');
                  } else {
                      alert('支付失败');
                  }
              });
      }
    if (typeof WeixinJSBridge == "undefined"){
      if( document.addEventListener ){
        document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
      }else if (document.attachEvent){
        document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
        document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
      }
    }else{
      onBridgeReady();
    }
  });
</script>

