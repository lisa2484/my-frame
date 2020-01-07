<head>
    <?php include "./sys/head.php"; ?>
</head>
<script>

</script>
<body>
    <div class="aut-edit modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>權限設定</h3>
                </div>
                <div class="modal-body container row">
                    <div class="col-3">名稱:</div>
                    <input class="col-9 edit-name form-control">
                </div>
                <div class="modal-footer">
                    <button class="edit-ok btn btn-info" data-id="">修改</button>
                    <button class="edit-add btn btn-info">新增</button>
                    <button class="btn btn-secondary" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div>權限設定</div>
        <div>
            <button class="btn btn-info" data-toggle="modal" data-target=".aut-edit">新增</button>
            <table class="table">
                <thead>
                    <tr>
                        <th>名稱</th>
                        <th>編輯</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($datas)) {
                        foreach ($datas as $data) {
                    ?>
                            <tr>
                                <td><?= $data["name"] ?></td>
                                <td><button data-id="<?= $data["id"] ?>"><i class="fa fa-plus"></i></button></td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>