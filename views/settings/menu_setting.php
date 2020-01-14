<head>
    <?php include "./sys/head.php"; ?>
</head>
<script>
    function editBtnShowAndHide(btn) {
        $('.edit-child-add').hide();
        $('.edit-ok').hide();
        $('.edit-add').hide();
        $(btn).show();
    }

    function strAutToArrAutForChk(strAut) {
        $('.edit-aut').attr("checked", false);
        var arrAut = strAut.split(",");
        arrAut.forEach(arrAutToChk);

        function arrAutToChk(obj, i) {
            $('.edit-aut[value="' + obj + '"]').attr("checked", true);
        }
    }

    function inputAut() {
        var chk = $('.edit-aut');
        var chkArr = [];
        $.each(chk, function(i, obj) {
            if (obj.getAttribute('checked') == 'checked') {
                chkArr.push(obj.value);
            }
        });
        $('.edit-authority').val(chkArr);
    }
    $(function() {
        $('.edit-aut-chk').click(function() {
            var id = $(this).data('id');
            var chk = $('.edit-aut[value="' + id + '"]');
            if (chk.attr('checked')) {
                chk.attr('checked', false);
            } else {
                chk.attr('checked', true);
            }
            inputAut();
        });
        $('.menu-btn-edit').click(function() {
            editBtnShowAndHide('.edit-ok');
            $('.edit-name').val($(this).data('name'));
            $('.edit-url').val($(this).data('url'));
            $('.edit-icon').val($(this).data('icon'));
            $('.edit-ok').attr('data-id', $(this).data('id'));
            $('.edit-authority').val($('.menu-main-aut[data-id="' + $(this).data('id') + '"]').html());
            strAutToArrAutForChk($('.edit-authority').val());
        });
        $('.belong-btn-edit').click(function() {
            editBtnShowAndHide('.edit-ok');
            $('.edit-name').val($(this).data('name'));
            $('.edit-url').val($(this).data('url'));
            $('.edit-icon').val($(this).data('icon'));
            $('.edit-ok').attr('data-id', $(this).data('id'));
            $('.edit-authority').val($('.menu-sub-aut[data-id="' + $(this).data('id') + '"]').html());
            strAutToArrAutForChk($('.edit-authority').val());
        });
        $('.add-btn-edit').click(function() {
            editBtnShowAndHide('.edit-add');
            $('.edit-name').val('');
            $('.edit-url').val('');
            $('.edit-icon').val('');
            $('.edit-aut').attr("checked", false);
        });
        $('.child-add').click(function() {
            editBtnShowAndHide('.edit-child-add');
            $('.edit-name').val('');
            $('.edit-url').val('');
            $('.edit-icon').val('');
            $('.edit-child-add').attr('data-id', $(this).data('id'));
            $('.edit-aut').attr("checked", false);
        });
        $('.edit-ok').click(function() {
            var id = $(this).attr('data-id');
            var name = $('.edit-name').val();
            var url = $('.edit-url').val();
            var icon = $('.edit-icon').val();
            var aut = $('.edit-authority').val();
            $.post('./menu_edit', {
                    id: id,
                    name: name,
                    url: url,
                    icon: icon,
                    aut: aut
                },
                function(req) {
                    alert(req);
                    window.location.reload();
                });
        });
        $('.edit-add').click(function() {
            var name = $('.edit-name').val();
            var url = $('.edit-url').val();
            var icon = $('.edit-icon').val();
            var aut = $('.edit-authority').val();
            $.post('./menu_add', {
                    name: name,
                    url: url,
                    icon: icon,
                    aut: aut
                },
                function(req) {
                    alert(req);
                    window.location.reload();
                });
        });
        $('.btn-del').click(function() {
            var id = $(this).data('id');
            if (confirm('是否刪除?')) {
                $.post('./menu_del', {
                        id: id
                    },
                    function(req) {
                        alert(req);
                        window.location.reload();
                    });
            }
        });
        $('.edit-child-add').click(function() {
            var id = $(this).attr('data-id');
            var name = $('.edit-name').val();
            var url = $('.edit-url').val();
            var icon = $('.edit-icon').val();
            var aut = $('.edit-authority').val();
            $.post('./menu_child_add', {
                    id: id,
                    name: name,
                    url: url,
                    icon: icon,
                    aut: aut
                },
                function(req) {
                    alert(req);
                    window.location.reload();
                });
        });
    });
</script>

<body style="width:100%;padding:30px;">
    <div class="menu-edit modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>選單設定</h3>
                </div>
                <div class="modal-body container row">
                    <div class="col-3">選單名稱:</div>
                    <input class="col-9 edit-name form-control">
                    <div class="col-3">路徑:</div>
                    <input class="col-9 edit-url form-control">
                    <div class="col-3">icon:</div>
                    <input class="col-9 edit-icon form-control">
                    <div class="col-3">權限</div>
                    <input style="display:none;" class="edit-authority" value="">
                    <div class="col-9">
                        <?php
                        foreach ($authority as $aut) {
                            if ($aut["id"] != 1) {
                        ?>
                                <div style="position: relative;">
                                    <div class="edit-aut-chk" data-id="<?= $aut["id"] ?>" style="z-index: 10;position:absolute;width:100%;height:100%;"></div>
                                    <input class="edit-aut" type="checkbox" value="<?= $aut["id"] ?>"><?= $aut["authority_name"] ?>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="edit-ok btn btn-info" data-id="">修改</button>
                    <button class="edit-add btn btn-info">新增</button>
                    <button class="edit-child-add btn btn-info" data-id="">新增</button>
                    <button class="btn btn-secondary" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div>
            <button class="btn btn-info add-btn-edit" data-toggle="modal" data-target=".menu-edit">新增</button>
        </div>
        <div>
            <table class="table container">
                <thead>
                    <tr>
                        <th>名稱</th>
                        <th>編輯</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($menus as $menu) {
                    ?>
                        <tr>
                            <td>
                                <?= $menu["name"] ?>
                            </td>
                            <td>
                                <p class="menu-main-aut" data-id="<?= $menu["id"] ?>" style="display: none;"><?= $menu["authority"] ?></p>
                                <button class="btn btn-primary child-add" data-id="<?= $menu["id"] ?>" data-toggle="modal" data-target=".menu-edit"><i class="fa fa-plus"></i></button>
                                <button class="btn btn-info menu-btn-edit" data-id="<?= $menu["id"] ?>" data-name="<?= $menu["name"] ?>" data-url="<?= $menu["url"] ?>" data-icon="<?= $menu["icon"] ?>" data-toggle="modal" data-target=".menu-edit"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-danger btn-del" data-id="<?= $menu["id"] ?>"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php
                        if (isset($belongs[$menu["id"]])) {
                            foreach ($belongs[$menu["id"]] as $belong) {
                        ?>
                                <tr style="background: #f2f2f2;">
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;<?= $belong["name"] ?>
                                    </td>
                                    <td>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <p class="menu-sub-aut" data-id="<?= $belong["id"] ?>" style="display: none;"><?= $belong["authority"] ?></p>
                                        <button class="btn btn-info belong-btn-edit" data-id="<?= $belong["id"] ?>" data-name="<?= $belong["name"] ?>" data-url="<?= $belong["url"] ?>" data-icon="<?= $belong["icon"] ?>" data-toggle="modal" data-target=".menu-edit"><i class="fa fa-pencil"></i></button>
                                        <button class="btn btn-danger btn-del" data-id="<?= $belong["id"] ?>"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>