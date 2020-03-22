<html><head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>订阅项目</title>
    <script type="text/javascript" src="{{ url('/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('/js/swiper.min.js') }}"></script>
    <style>
        .desc{
            font-size: 32px;
            text-align: center;
            padding: 20px 50px;
        }
        .btn{
            width: 200px;
            height: 90px;
            cursor: pointer;
            color: #fff;
            font-size: 36px;
            letter-spacing: 1px;
            background: #3385ff;
            background-image: none;
            position: absolute;
            left: 39%;
        }
        .cancel{
            background: #ffac38;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#subscribe').on('click',function(){
                location.href="{{$url}}";
            });
            $('#cancel').on('click',function(){
                location.href="{{$url}}?cancel=1";
            });
        });
    </script>

</head>
<body>
<p class="desc">
    本页面提供算量之家平台项目的订阅功能，请提前关注公众号【算量之家】
    当您订阅成功后，平台有用户发布项目的时候，就会在公众号上推送项目消息，方便快捷
    致富方式：微信搜索小程序【算量之家】，记住是小程序哟！！！！
</p>
<div>
    @if($is_subscribe)
        <input id="cancel" class="btn cancel" value="取消订阅" type="button">
    @else
        <input id="subscribe" class="btn" value="订阅项目" type="button">
    @endif
</div>

</body></html>
