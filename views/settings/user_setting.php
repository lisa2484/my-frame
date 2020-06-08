<head>
    <?php include "./sys/head.php"
    ?>
</head>
<script>
    $(function() {
        $(".btn-edit").click(function() {
            var id = $(this).data("id");
            var act = $(".act-" + id).html();
            var name = $(".name-" + id).html();
            var aut = $(".aut-id-" + id).html();
            $(".edit-btn").attr("data-id", id);
            $(".edit-act").html(act);
            $(".edit-name").val(name);
            $(".edit-aut").val(aut);
            $(".user-edit").modal("show");
        });
        $(".btn-add").click(function() {
            $(".user-add").modal("show");
        });
        $(".btn-edit-pad").click(function() {
            $(".edit-pad-btn").attr("data-id", $(this).data("id"));
            $(".user-edit-pad").modal("show");
        });
        $(".add-btn").click(function() {
            var act = $(".add-act").val();
            if (act.replace(/(^\s*)|(\s*$)/g, "").length == 0) {
                alert("请输入帐号");
                return;
            }
            var pad = $(".add-pad").val();
            if (pad.replace(/\s/g, "").length == 0) {
                alert("请输入密码");
                return;
            }
            if (pad != $(".add-vpad").val()) {
                alert("密码与确认密码不同");
                return;
            }
            var name = $(".add-name").val();
            if (name.replace(/\s/g, "").length == 0) {
                alert("请输入使用者名称");
                return;
            }
            var aut = $(".add-aut").val();
            $.post("add", {
                    act: act,
                    pad: pad,
                    name: name,
                    aut: aut
                },
                function(data) {
                    switch (data) {
                        case "account-repeat":
                            alert("帐号重复");
                            break;
                        case "true":
                            alert("新增成功")
                            location.reload();
                            break;
                        default:
                            alert("新增失败");
                    }
                });
        });
        $(".edit-btn").click(function() {
            var id = $(this).attr("data-id");
            var name = $(".edit-name").val();
            var aut = $(".edit-aut").val();
            $.post("edit", {
                    id: id,
                    name: name,
                    aut: aut
                },
                function(data) {
                    alert(data);
                    location.reload();
                });
        });
        $(".edit-pad-btn").click(function() {
            var id = $(this).attr("data-id");
            var pad = $(".edit-pad-npad").val();
            var opad = $(".edit-pad-opad").val();
            var vpad = $(".edit-pad-vpad").val();
            if (pad.replace(/\s/g, "").length == 0 || (opad.replace(/\s/g, "").length == 0 || vpad.replace(/\s/g, "").length == 0)) {
                alert("密码不可为空");
                return;
            }
            if (pad == opad) {
                alert("旧密码不可与新密码相同");
                return;
            }
            if (pad == vpad) {
                $.post("pedit", {
                        id: id,
                        pad: pad,
                        opad: opad
                    },
                    function(req) {
                        switch (req) {
                            case "opad-false":
                                alert("密码错误");
                                break;
                            case "true":
                                alert("更新成功");
                                location.reload();
                                break;
                            default:
                                alert("更新失败");
                        }
                    });
            } else {
                alert("密码确认错误");
            }
        });
    });
</script>

<body>
    <div class="user-edit-pad modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                </div>
                <div class="modal-body container row">
                    <div class="col-3">旧密码</div>
                    <input class="col-9 edit-pad-opad" type="password">
                    <div class="col-3">新密码</div>
                    <input class="col-9 edit-pad-npad" type="password">
                    <div class="col-3">密码确认</div>
                    <input class="col-9 edit-pad-vpad" type="password">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-info edit-pad-btn">修改</button>
                    <button class="btn btn-secondary" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
    <div class="user-add modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                </div>
                <div class="modal-body container row">
                    <div class="col-4">帐号:</div>
                    <input class="col-8 add-act">
                    <div class="col-4">密码:</div>
                    <input class="col-8 add-pad" type="password">
                    <div class="col-4">确认密码:</div>
                    <input class="col-8 add-vpad" type="password">
                    <div class="col-4">使用者名称:</div>
                    <input class="col-8 add-name">
                    <div class="col-4">权限:</div>
                    <select class="col-8 add-aut">
                        <option value="0">请选择</option>
                        <?php
                        foreach ($authority as $aut) {
                        ?>
                            <option value="<?= $aut["id"] ?>"><?= $aut["authority_name"] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-info add-btn">新增</button>
                    <button class="btn btn-secondary" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
    <div class="user-edit modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                </div>
                <div class="modal-body container row">
                    <div class="col-4">帐号:</div>
                    <div class="col-8 edit-act"></div>
                    <div class="col-4">使用者名称:</div>
                    <input class="col-8 edit-name">
                    <div class="col-4">权限:</div>
                    <select class="col-8 edit-aut">
                        <option value="0">请选择</option>
                        <?php
                        foreach ($authority as $aut) {
                        ?>
                            <option value="<?= $aut["id"] ?>"><?= $aut["authority_name"] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-info edit-btn">修改</button>
                    <button class="btn btn-secondary" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
    <div>
        <button class="btn btn-info btn-add">新增使用者</button>
    </div>
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th>帐号</th>
                    <th>使用者名称</th>
                    <th>权限</th>
                    <th>功能</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($datas as $data) {
                ?>
                    <tr>
                        <td class="act-<?= $data["id"] ?>"><?= $data["account"] ?></td>
                        <td class="name-<?= $data["id"] ?>"><?= $data["user_name"] ?></td>
                        <td class="aut-<?= $data["id"] ?>">
                            <?php
                            foreach ($authority as $aut) {
                                if ($aut["id"] == $data["authority"]) {
                                    echo "<p class='aut-id-" . $data["id"] . "' style='display:none;'>" . $aut["id"] . "</p>";
                                    echo $aut["authority_name"];
                                    break;
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-edit" data-id="<?= $data["id"] ?>"><i class="fa fa-pencil"></i></button>
                            <button class="btn btn-edit-pad" data-id="<?= $data["id"] ?>"><i class="fa fa-key"></i></button>
                            <button class="btn btn-del"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</body>