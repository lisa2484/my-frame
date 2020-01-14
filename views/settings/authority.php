<head>
    <?php include "./sys/head.php"; ?>
</head>
<script>
    $(function() {
        $(".edit-add").click(function() {
            var name = $(".edit-name").val();
            if (name.replace(/(^\s*)|(\s*$)/g, "").length == 0) {
                alert("不可空白");
                return;
            }
            $.post("add", {
                    name: name
                },
                function(data) {
                    alert(data);
                    location.reload();
                });
        });
        $(".edit-ok").click(function() {
            var name = $(".edit-name").val();
            if (name.replace(/(^\s*)|(\s*$)/g, "").length == 0) {
                alert("不可空白");
                return;
            }
            var id = $(this).attr("data-id");
            $.post("edit", {
                    id: id,
                    name: name
                },
                function(data) {
                    alert(data);
                    location.reload();
                });
        });
        $(".btn-del").click(function() {
            var id = $(this).data("id");
            $.post("del", {
                    id: id
                },
                function(data) {
                    alert(data);
                    location.reload();
                });
        })
        $(".btn-add").click(function() {
            $(".edit-ok").hide();
            $(".edit-add").show();
            $(".edit-name").val("");
        });
        $(".btn-edit").click(function() {
            $(".edit-add").hide();
            $(".edit-ok").show();
            var id = $(this).data("id");
            $(".edit-name").val($(".name-" + id).html());
            $(".edit-ok").attr("data-id", id);
        });
    });
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
            <button class="btn btn-info btn-add" data-toggle="modal" data-target=".aut-edit">新增</button>
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
                                <td class="name-<?= $data["id"] ?>"><?= $data["authority_name"] ?></td>
                                <td>
                                    <button class="btn-edit btn" data-toggle="modal" data-target=".aut-edit" data-id="<?= $data["id"] ?>"><i class="fa fa-pencil"></i></button>
                                    <?php
                                    if ($data["id"] != 1) {
                                    ?>
                                        <button class="btn-del btn" data-id="<?= $data["id"] ?>"><i class="fa fa-trash"></i></button>
                                    <?php
                                    }
                                    ?>
                                </td>
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