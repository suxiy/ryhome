<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/vConsole/3.3.4/vconsole.min.js"></script>
<script>
    var vConsole = new VConsole();
    $(document).ready(function(){
        if (typeof window.WeixinJSBridge == "undefined") {
            $(document).on("WeixinJSBridgeReady", function() {
                WeixinJSBridge.call('closeWindow');
            });
        }
    });
</script>
