<script type="text/javascript">
    //用户点击跳转地址（非静默授权）
    const appId = 'wx644c5c34353198dc';   // 公众号的id
    const redirectUri = encodeURIComponent("{{ $url }}");  // 微信回调接口
    window.location.href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=" + appId + "&redirect_uri=" + redirectUri + "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
</script>