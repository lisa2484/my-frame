<html>

<head>
    <link rel="stylesheet" type="text/css" href="./resources/css/bootstrap.css">
    <script src="./resources/js/jquery-3.4.1.js"></script>
    <script src="./resources/js/bootstrap.js"></script>
</head>
<script>
    $(function() {
        $('.login').click(function() {
            $('p').hide();
            var act = $('input[name="account"]');
            if (act.val().replace(/(^\s*)|(\s*$)/g, "").length == 0) {
                $('.empty-act').show();
                return;
            }
            var pad = $('input[name="password"]');
            if (pad.val().replace(/(^\s*)|(\s*$)/g, "").length == 0) {
                $('.empty-pad').show();
                return;
            }
            $.post('./', {
                    account: act.val(),
                    password: pad.val()
                },
                function(req) {
                    if (req == 'true') {
                        window.location.reload();
                    } else {
                        $('.false-login').show();
                    }
                });
        });
    });
</script>
<style>
    p {
        display: none;
    }

    .dd {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        min-width: 300px;
        max-width: 600px;
    }
</style>

<body>
    <div class="container dd">
        <div class="container">
            <div class="row">
                <div class="col-3">帳號:</div>
                <input name="account" class="form-control col-9">
                <p class="empty-act col-12">帳號不可為空</p>
                <div class="col-3">密碼:</div>
                <input name="password" type="password" class="form-control col-9">
                <p class="empty-pad col-12">密碼不可為空</p>
                <button class="login btn btn-primary col-12">登入</button>
                <p class="false-login col-12">帳號或密碼錯誤</p>
            </div>
        </div>
    </div>
</body>

</html>