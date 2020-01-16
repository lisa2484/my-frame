<head>
    <?php include "./sys/head.php"; ?>
    <script src="../resources/js/jquery-ui.js"></script>
</head>
<script>
    function editBtnShowAndHide(btn) {
        $('.edit-child-add').hide();
        $('.edit-ok').hide();
        $('.edit-add').hide();
        $(btn).show();
    }

    $(function() {
        $('.menu-btn-edit').click(function() {
            editBtnShowAndHide('.edit-ok');
            $('.edit-name').val($(this).data('name'));
            $('.edit-url').val($(this).data('url'));
            $('.edit-icon').val($(this).data('icon'));
            $('.edit-ok').attr('data-id', $(this).data('id'));
        });
        $('.belong-btn-edit').click(function() {
            editBtnShowAndHide('.edit-ok');
            $('.edit-name').val($(this).data('name'));
            $('.edit-url').val($(this).data('url'));
            $('.edit-icon').val($(this).data('icon'));
            $('.edit-ok').attr('data-id', $(this).data('id'));
        });
        $('.add-btn-edit').click(function() {
            editBtnShowAndHide('.edit-add');
            $('.edit-name').val('');
            $('.edit-url').val('');
            $('.edit-icon').val('');
        });
        $('.child-add').click(function() {
            editBtnShowAndHide('.edit-child-add');
            $('.edit-name').val('');
            $('.edit-url').val('');
            $('.edit-icon').val('');
            $('.edit-child-add').attr('data-id', $(this).data('id'));
        });
        $('.edit-ok').click(function() {
            var id = $(this).attr('data-id');
            var name = $('.edit-name').val();
            var url = $('.edit-url').val();
            var icon = $('.edit-icon').val();
            $.post('./menu_edit', {
                    id: id,
                    name: name,
                    url: url,
                    icon: icon
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
            $.post('./menu_add', {
                    name: name,
                    url: url,
                    icon: icon
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
            $.post('./menu_child_add', {
                    id: id,
                    name: name,
                    url: url,
                    icon: icon
                },
                function(req) {
                    alert(req);
                    window.location.reload();
                });
        });
        $(".sortable-btn").click(function() {
            $(this).hide();
            $(".sortable-div").show();
            $("#sortable").sortable({
                axis: "y"
            });
            $("#sortable").disableSelection();
            $("#sortable > div > .sortable-sub").sortable({
                axis: "y"
            });
            $("#sortable > div > .sortable-sub").disableSelection();
        });
        $(".sortable-set").click(function() {
            var mainArr = [];
            var subArr = [];
            var main = $("#sortable").children();
            var sub = $(".sortable-sub");
            main.each(function(i, obj) {
                mainArr[i] = $(this).data("id");
            });
            sub.each(function(i, obj) {
                var mainID = $(this).attr("data-id");
                var subObj = $(this).children(".row");
                var subObjArr = [];
                subObj.each(function(i, obj) {
                    subObjArr[i] = $(this).data("id");
                });
                subArr[mainID] = subObjArr;
            });
            $.post("meun_sortable", {
                    main: JSON.stringify(mainArr),
                    sub: JSON.stringify(subArr)
                },
                function(req) {
                    parent.location.reload();
                }
            );
        });
    });
</script>

<body>
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
    <div class="container">
        <div>
            <button class="btn btn-info add-btn-edit" data-toggle="modal" data-target=".menu-edit">新增</button>
            <button class="btn sortable-btn">變更排序</button>
            <div class="sortable-div" style="display: none;">
                <button class="btn sortable-set">確定</button>
                <button class="btn" onclick="javascript:location.reload();">取消</button>
            </div>
        </div>
        <div id="sortable">
            <?php
            foreach ($menus as $menu) {
            ?>
                <div data-id="<?= $menu["id"] ?>">
                    <div class="row">
                        <div class="col-6"><?= $menu["name"] ?></div>
                        <button class="btn btn-primary child-add" data-id="<?= $menu["id"] ?>" data-toggle="modal" data-target=".menu-edit"><i class="fa fa-plus"></i></button>
                        <button class="btn btn-info menu-btn-edit" data-id="<?= $menu["id"] ?>" data-name="<?= $menu["name"] ?>" data-url="<?= $menu["url"] ?>" data-icon="<?= $menu["icon"] ?>" data-toggle="modal" data-target=".menu-edit"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-danger btn-del" data-id="<?= $menu["id"] ?>"><i class="fa fa-trash"></i></button>
                    </div>
                    <?php
                    if (isset($belongs[$menu["id"]])) {
                    ?>
                        <div class="sortable-sub" data-id="<?= $menu["id"] ?>">
                            <?php
                            foreach ($belongs[$menu["id"]] as $belong) {
                            ?>
                                <div class="row" data-id="<?= $belong["id"] ?>">
                                    <div class="col-1"></div>
                                    <div class="col-5"><?= $belong["name"] ?></div>
                                    <button class="btn btn-info belong-btn-edit" data-id="<?= $belong["id"] ?>" data-name="<?= $belong["name"] ?>" data-url="<?= $belong["url"] ?>" data-icon="<?= $belong["icon"] ?>" data-toggle="modal" data-target=".menu-edit"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger btn-del" data-id="<?= $belong["id"] ?>"><i class="fa fa-trash"></i></button>
                                </div>
                            <?php

                            }
                            ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</body>