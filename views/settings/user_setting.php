<head>
    <?php //include "./sys/head.php" 
    ?>
    <link rel="stylesheet" type="text/css" href="./resources/css/bootstrap.css">
    <link rel="stylesheet" href="./resources/css/font-awesome.css">
    <script src="./resources/js/jquery-3.4.1.js"></script>
    <script src="./resources/js/bootstrap.js"></script>
</head>

<body>
    <div class="user-edit modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                </div>
                <div class="modal-body container row">
                    <div class="col-4">帳號:</div>
                    <input class="col-8">
                    <div class="col-4">密碼:</div>
                    <input class="col-8" type="password">
                    <div class="col-4">確認密碼:</div>
                    <input class="col-8" type="password">
                    <div class="col-4">使用者名稱:</div>
                    <input class="col-8">
                    <div class="col-4">權限:</div>
                    <input class="col-8">
                </div>
                <div class="modal-footer">
                    <button>新增</button>
                    <button class="btn btn-secondary" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
    <div>
        <button class="btn btn-info" data-toggle="modal" data-target=".user-edit">新增使用者</button>
    </div>
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th>帳號</th>
                    <th>使用者名稱</th>
                    <th>權限</th>
                    <th>功能</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($datas as $data) {
                ?>
                    <tr>
                        <td><?= $data["account"] ?></td>
                        <td><?= $data["user_name"] ?></td>
                        <td><?= $data["authority"] ?></td>
                        <td></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</body>