<?php
header('Content-Type: text/html; charset=utf-8');
include_once './lib/fun.php';
//检查登录
if(!checkLogin())
{
    msg(2,'请登录','login.php');
}

$user = $_SESSION['user'];

if(!empty($_POST['name']))
{
    $con = mysqlInit('127.0.0.1','root', 'root', 'image_text');

    $name = mysql_real_escape_string(trim($_POST['name']));

    $price = intval($_POST['price']);

    $des = mysql_real_escape_string(trim($_POST['des']));

    $content = mysql_real_escape_string(trim($_POST['content']));

    $nameLength = mb_strlen($name,'utf-8');
    if($nameLength <= 0 || $nameLength > 30)
    {
        msg(2,'画品名应在1-30字符之内');
    }

    if($price <= 0 || $price > 999999999 )
    {
        msg(2,'商品价格应小于999999999');
    }

    $desLength = mb_strlen($des,'utf-8');
    if ($desLength <=0 || $desLength > 100)
    {
        msg(2,'画品简介应在1-100字符之内');
    }

    if(empty($content))
    {
        msg(2,'画品详情不能为空');
    }

    $userId = $user['id'];

    $now = $_SERVER['REQUEST_TIME'];

    $pic = imgUpload($_FILES['file']);

    //建议做商品名称唯一性验证处理
    //判断画品的名称是否存在
    $sql = "SELECT COUNT('id') as total FROM im_goods where goods_name ='{$name}' ";
    $obj = mysql_query($sql);
    $result = mysql_fetch_assoc($obj);
    //验证画品是否存在同名
    if(isset($result['total']) && $result['total'] > 0)
    {
        msg(2,'画品名称存在同名，请重新输入');
    }
    unset($sql,$obj,$result);

    //入库处理
    $sql = "INSERT INTO im_goods(goods_name,price,des,content,pic,user_id,create_time,update_time,goods_view) VALUES ('{$name}','{$price}','{$des}','{$content}','{$pic}','{$userId}',
    '{$now}','{$now}',0)";

    if($obj = mysql_query($sql))
    {
        msg(1,'操作成功','index.php');
    }
    else
    {
        echo mysql_error();exit;
    }
    //move_uploaded_file();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>配图短文|发布</title>
    <link type="text/css" rel="stylesheet" href="./static/css/common.css">
    <link type="text/css" rel="stylesheet" href="./static/css/add.css">
</head>
<body>
<div class="header">
    <div class="logo f1">
        <img src="./static/image/logo.png">
    </div>
    <div class="auth fr">
        <ul>
            <li><span>管理员: <?php echo $user['username'] ?></span></li>
            <li><a href="login_out.php">退出</a></li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="addwrap">
        <div class="addl fl">
            <header>发布</header>
            <form name="publish-form" id="publish-form" action="publish.php" method="post"
                  enctype="multipart/form-data">
                <div class="additem">
                    <label id="for-name">名称：</label><input type="text" name="name" id="name" placeholder="请输入名称">
                </div>
                <div class="additem">
                    <label id="for-price">价值：</label><input type="text" name="price" id="price" placeholder="请输入价值">
                </div>
                <div class="additem">
                    <!-- 使用accept html5属性 声明仅接受png gif jpeg格式的文件                -->
                    <label id="for-file">图片：</label><input type="file" accept="image/png,image/gif,image/jpeg" id="file"
                                                          name="file">
                </div>
                <div class="additem textwrap">
                    <label class="ptop" id="for-des">简介：</label><textarea id="des" name="des"
                                                                           placeholder="请输入简介"></textarea>
                </div>
                <div class="additem textwrap">
                    <label class="ptop" id="for-content">详情：</label>
                    <div style="margin-left: 120px" id="container">
                        <textarea id="content" name="content"></textarea>
                    </div>

                </div>
                <div style="margin-top: 20px">
                    <button type="submit">发布</button>
                </div>

            </form>
        </div>
        <div class="addr fr">
            <img src="./static/image/index_banner.png">
        </div>
    </div>

</div>
<div class="footer">
    <p><span>配图短文</span>©2019 POWERED BY 云亦然</p>
</div>
</body>
<script src="./static/js/jquery-1.10.2.min.js"></script>
<script src="./static/js/layer/layer.js"></script>
<script src="./static/js/kindeditor/kindeditor-all-min.js"></script>
<script src="./static/js/kindeditor/lang/zh_CN.js"></script>
<script>
    var K = KindEditor;
    K.create('#content', {
        width      : '475px',
        height     : '400px',
        minWidth   : '30px',
        minHeight  : '50px',
        items      : [
            'undo', 'redo', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'clearhtml',
            'fontsize', 'forecolor', 'bold',
            'italic', 'underline', 'link', 'unlink', '|'
            , 'fullscreen'
        ],
        afterCreate: function () {
            this.sync();
        },
        afterChange: function () {
            //编辑器失去焦点时直接同步，可以取到值
            this.sync();
        }
    });
</script>

<script>
    $(function () {
        $('#publish-form').submit(function () {
            var name = $('#name').val(),
                price = $('#price').val(),
                file = $('#file').val(),
                des = $('#des').val(),
                content = $('#content').val();
            if (name.length <= 0 || name.length > 30) {
                layer.tips('名称应在1-30字符之内', '#name', {time: 2000, tips: 2});
                $('#name').focus();
                return false;
            }
            //验证为正整数
            if (!/^[1-9]\d{0,8}$/.test(price)) {
                layer.tips('请输入最多9位正整数', '#price', {time: 2000, tips: 2});
                $('#price').focus();
                return false;
            }

            if (file == '' || file.length <= 0) {
                layer.tips('请选择图片', '#file', {time: 2000, tips: 2});
                $('#file').focus();
                return false;

            }

            if (des.length <= 0 || des.length >= 100) {
                layer.tips('简介应在1-100字符之内', '#content', {time: 2000, tips: 2});
                $('#des').focus();
                return false;
            }

            if (content.length <= 0) {
                layer.tips('请输入详情信息', '#container', {time: 2000, tips: 3});
                $('#content').focus();
                return false;
            }
            return true;

        })
    })
</script>
</html>
