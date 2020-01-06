<head>
    <?php include "./sys/head.php";?>
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
            id = $(this).attr('data-id');
            name = $('.edit-name').val();
            url = $('.edit-url').val();
            icon = $('.edit-icon').val();
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
            name = $('.edit-name').val();
            url = $('.edit-url').val();
            icon = $('.edit-icon').val();
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
            id = $(this).data('id');
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
            id = $(this).attr('data-id');
            name = $('.edit-name').val();
            url = $('.edit-url').val();
            icon = $('.edit-icon').val();
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
    <!-- <div>すごい　たのしい</div> -->
    <div>
        <div>
            <button class="add-btn-edit" data-toggle="modal" data-target=".menu-edit">新增</button>
        </div>
        <div>
            <?php
            foreach ($menus as $menu) {
            ?>
                <div>
                    <?= $menu['name'] ?>
                    <button class="btn btn-primary child-add" data-id="<?= $menu['id'] ?>" data-toggle="modal" data-target=".menu-edit"><i class="fa fa-plus"></i></button>
                    <button class="btn btn-info menu-btn-edit" data-id="<?= $menu['id'] ?>" data-name="<?= $menu['name'] ?>" data-url="<?= $menu['url'] ?>" data-icon="<?= $menu['icon'] ?>" data-toggle="modal" data-target=".menu-edit"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btn-del" data-id="<?= $menu['id'] ?>"><i class="fa fa-trash"></i></button>
                </div>
                <?php
                if (isset($belongs[$menu['id']])) {
                    foreach ($belongs[$menu['id']] as $belong) {
                ?>
                        <div>
                            <?= $belong['name'] ?>
                            <button class="btn btn-info belong-btn-edit" data-id="<?= $belong['id'] ?>" data-name="<?= $belong['name'] ?>" data-url="<?= $belong['url'] ?>" data-icon="<?= $belong['icon'] ?>" data-toggle="modal" data-target=".menu-edit"><i class="fa fa-pencil"></i></button>
                            <button class="btn btn-danger btn-del" data-id="<?= $belong['id'] ?>"><i class="fa fa-trash"></i></button>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>
    </div>
</body>
<script></script>