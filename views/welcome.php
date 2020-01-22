<html>

<head>
    <link rel="stylesheet" type="text/css" href="./resources/css/bootstrap.css">
    <link rel="stylesheet" href="./resources/css/font-awesome.css">
    <script src="./resources/js/jquery-3.4.1.js"></script>
    <script src="./resources/js/bootstrap.js"></script>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
</head>

<style>
    body {
        font-family: 宋体, Arial;
        font-size: 12px;
    }

    .row {
        margin: 20px;
        padding: 20px;
        background: rgb(238, 238, 238);
    }

    .row>div {
        display: flex;
        align-items: center;
        border-right: #D4D4D4 1px solid;
        border-bottom: #D4D4D4 1px solid;
        padding: 10px;
        background: #FFFFFF;
    }

    .maintop {
        width: auto;
        font-weight: bold;
        background-color: #FFFFFF;
        border-bottom: 1px solid #D4D4D4;
        line-height: 28px;
    }

    @media only screen and (max-width:500px) {
        .row {
            margin: 0;
        }

        .container-fluid {
            padding: 0;
        }
    }
</style>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="maintop col-12">当前位置：管理首页</div>
            <div class="col-12">系统检测</div>
            <div class="col-3 col-md-2">服务器信息：</div>
            <div class="col-9 col-md-4"><?php echo php_uname() ?></div>
            <div class="col-3 col-md-2">服务器信操作系统：</div>
            <div class="col-9 col-md-4"><?php echo explode(" ", php_uname())[0]; ?></div>
            <div class="col-3 col-md-2">服务器域名：</div>
            <div class="col-9 col-md-4"><?php echo $_SERVER["SERVER_NAME"]; ?></div>
            <div class="col-3 col-md-2">服务器IP：</div>
            <div class="col-9 col-md-4"><?php echo $_SERVER["SERVER_ADDR"] ?></div>
            <div class="col-3 col-md-2">服务器端口：</div>
            <div class="col-9 col-md-4"><?php echo $_SERVER["SERVER_PORT"]; ?></div>
            <div class="col-3 col-md-2">服务器时间：</div>
            <div class="col-9 col-md-4"><?php echo date("Y-m-d H:i:s"); ?></div>
            <div class="col-3 col-md-2">客户端IP：</div>
            <div class="col-9 col-md-4"><?php echo $_SERVER["REMOTE_ADDR"]; ?></div>
            <div class="col-3 col-md-2">浏览器版本：</div>
            <div class="col-9 col-md-4"><?php echo $_SERVER['HTTP_USER_AGENT']; ?></div>
            <div class="col-12">系统信息</div>
            <div class="col-3">系统名称：</div>
            <div class="col-9">后台管理系统</div>
            <div class="col-3">程序版本：</div>
            <div class="col-9"></div>
            <div class="col-3">官方网站：</div>
            <div class="col-9"></div>
        </div>
    </div>
</body>

</html>