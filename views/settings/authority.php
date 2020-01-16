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
        $(".btn-del").click(function() {
            if (!confirm("是否刪除?")) {
                return;
            }
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
            $(".edit-aut").val($(".p-aut-" + id).html());
            $(".edit-name").val($(".name-" + id).html());
            $(".edit-ok").attr("data-id", id);
            inputToChk();
        });

        $(".chk-r").click(function() {
            var chk = $(".chk-inp-r[data-id='" + $(this).data("id") + "']");
            if (chk.attr("checked")) {
                chk.attr("checked", false);
            } else {
                chk.attr("checked", true);
            }
            chkToInput()
        });
    });

    function inputToChk() {
        var inp = $(".edit-aut");
        $(".chk-inp-r").attr("checked", false);
        var datas = JSON.parse(inp.val());
        datas["r"].forEach(x => $(".chk-inp-r[data-id='" + x + "']").attr("checked", true));
    }

    function chkToInput() {
        var inp = $(".edit-aut");
        var datas = {
            "r": "",
            "c": "",
            "u": "",
            "d": ""
        };
        var r = $(".chk-inp-r");
        var rdata = [];
        r.each(function(i, obj) {
            if ($(this).attr("checked")) {
                rdata.push($(this).data("id"));
            }
        });
        datas["r"] = rdata;
        inp.val(JSON.stringify(datas));
    }
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
                    <div class="col-12">權限</div>
                    <input class="edit-aut" style="display: none;">
                    <div class="col-12">
                        <?php
                        foreach ($menu as $m) {
                        ?>
                            <div class="row">
                                <div class="col-5" style="position: relative;">
                                    <div class="chk-r" data-id="<?= $m["id"] ?>" style="position:absolute;height:100%;width:100%;"></div>
                                    <input class="chk-inp-r" data-id="<?= $m["id"] ?>" type="checkbox"><?= $m["name"] ?>
                                </div>
                                <div class="col-7"></div>
                            </div>
                            <?php
                            if (isset($bel[$m["id"]])) {
                                foreach ($bel[$m["id"]] as $b) {
                            ?>
                                    <div class="row">
                                        <div class="col-1"></div>
                                        <div class="col-4" style="position: relative;">
                                            <div class="chk-r" data-id="<?= $b["id"] ?>" style="position:absolute;height:100%;width:100%;"></div>
                                            <input class="chk-inp-r" data-id="<?= $b["id"] ?>" type="checkbox"><?= $b["name"] ?>
                                        </div>
                                        <div class="col-7"></div>
                                    </div>
                            <?php
                                }
                            }
                            ?>
                        <?php
                        }
                        ?>
                    </div>
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
                                    <p class="p-aut-<?= $data["id"] ?>" style="display: none;"><?= $data["authority"] ?></p>
                                    <button class="btn-edit btn" data-toggle="modal" data-target=".aut-edit" data-id="<?= $data["id"] ?>"><i class="fa fa-pencil"></i></button>
                                    <button class="btn-del btn" data-id="<?= $data["id"] ?>"><i class="fa fa-trash"></i></button>
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