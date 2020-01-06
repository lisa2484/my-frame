<head>
    <link rel="stylesheet" type="text/css" href="./resources/css/bootstrap.css">
    <link rel="stylesheet" href="./resources/css/font-awesome.css">
    <script src="./resources/js/jquery-3.4.1.js"></script>
    <script src="./resources/js/bootstrap.js"></script>
</head>
<script>
    function contentSwith(url) {
        if (url != null && url != "" && url != 0) {
            $("iframe").attr("src", "./" + url);
        }
    }
</script>
<style>
    .title {
        position: fixed;
        height: 50px;
        width: 100%;
        z-index: 21;
    }

    body {
        margin: 0;
    }

    .row {
        margin: 0;
        padding: 0;
    }

    .all-collapse {
        background-color: #8d8d8d;
        height: 100%;
        padding-top: 10px;
    }

    .sub {
        position: fixed;
        overflow: hidden;
        padding-top: 50px;
        width: 200px;
        height: 100%;
        -webkit-transition: width 0.4s;
        transition: width 0.4s;
        z-index: 21;
    }

    .content {
        border: none;
        padding: 0;
        -webkit-transition: margin 0.4s;
        transition: margin 0.4s;
        z-index: 20;
    }

    .middle {
        height: 100%;
        padding-top: 50px;
        padding-left: 200px;
    }

    a {
        text-decoration: none;
        color: black;
    }

    a:hover {
        text-decoration: none;
        color: black;
    }

    .menu-btn {
        height: 40px;
        background-color: #6d6d6d;
    }

    .menu-btn-text {
        position: absolute;
        min-width: 160px;
        padding-left: 40px;
        height: 40px;
        display: flex;
        align-items: center;
    }

    .menu-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
    }

    .all-collapse {
        background-color: #8d8d8d;
        width: 100%;
        height: 100%;
        padding-top: 10px;
    }

    .menu-btn {
        z-index: 30;
    }

    .menu-ul {
        margin: 0;
        width: 200px;
        overflow: hidden;
        list-style: none;
    }

    @media only screen and (max-width:992px) {
        .sub {
            width: 40px;
        }

        .sub:hover {
            width: 200px;
        }

        .middle {
            padding-left: 40px;
        }
    }
</style>

<body>
    <div>
        <div class="title">
            <div class="col" style="background-color: #494949;height:100%;">
                <!-- ようこそ　ジャパリパークへ！！ -->
            </div>
        </div>
        <div class="sub">
            <div class="all-collapse">
                <?php
                foreach ($menus as $menu) {
                    if (isset($belongs[$menu["id"]])) {
                ?>
                        <a href=".menu-ul-<?= $menu["id"] ?>" data-toggle="collapse" class="row menu-btn menu-btn-<?= $menu["id"] ?>">
                            <div class="menu-icon">
                                <i class="fa fa-<?= $menu["icon"] ?>"></i>
                            </div>
                            <div class="col menu-btn-text">
                                <?= $menu["name"] ?>
                            </div>
                        </a>
                        <ul class="menu-ul menu-ul-<?= $menu["id"] ?> collapse" data-parent=".all-collapse">
                            <?php
                            foreach ($belongs[$menu["id"]] as $belong) {
                            ?>
                                <li><a type="button" onclick="contentSwith('<?= $belong['url'] ?>')" class=""><?= $belong["name"] ?></a></li>
                            <?php
                            }
                            ?>
                        </ul>
                    <?php
                    } else {
                    ?>
                        <a type="button" class="row menu-btn" onclick="contentSwith('<?= $menu['url'] ?>')">
                            <div class="menu-icon">
                                <i class="fa fa-<?= $menu["icon"] ?>"></i>
                            </div>
                            <div class="col menu-btn-text">
                                <?= $menu["name"] ?>
                            </div>
                        </a>
                <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="row middle">
            <iframe class="col content" src="./welcome"></iframe>
        </div>
    </div>
</body>