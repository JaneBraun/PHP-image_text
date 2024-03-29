<?php
header('Content-Type: text/html; charset=utf-8');
include_once './lib/fun.php';

//检查登录
if($login = checkLogin())
{
    $user = $_SESSION['user'];
}



//检查page参数
$page = isset($_GET['page'])?intval($_GET['page']):1;
//把page与1对比 取中间最大值
$page = max($page,1);
//每页显示条数
$pageSize = 6;
//如果每页两条的话
// page = 1 limit 0,2
// page = 2 limit 2,2
// page = 3 limit 4,2

$offset = ($page-1)*$pageSize;

$con = mysqlInit('127.0.0.1','root', 'root', 'image_text');

$sql = "SELECT COUNT('id') as total from im_goods";
$obj = mysql_query($sql);
$result = mysql_fetch_assoc($obj);

$total = isset($result['total'])?$result['total']:0;

unset($sql, $obj, $result);
//只查询需要的数据
$sql = "SELECT id,goods_name,pic,des FROM im_goods  ORDER BY 'id' asc,'goods_view' desc limit {$offset},{$pageSize}";

$obj = mysql_query($sql);

$goods = array();

while($result = mysql_fetch_assoc($obj))
{
    $goods[] = $result;
}

$pages = pages($total,$page,$pageSize,6);



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>配图短文|首页</title>
    <link rel="stylesheet" type="text/css" href="./static/css/common.css"/>
    <link rel="stylesheet" type="text/css" href="./static/css/index.css"/>
</head>
<body>
<div class="header">
    <div class="logo f1">
        <img src="./static/image/logo.png">
    </div>
    <div class="auth fr">
        <ul>
            <?php if($login):?>
            <li><span>管理员: <?php echo $user['username'] ?></span></li>
            <li><a href="publish.php">发布</a></li>
            <li><a href="login_out.php">退出</a></li>
            <?php else: ?>
            <li><a href="login.php">登录</a></li>
            <li><a href="register.php">注册</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<div class="content">
    <div class="banner">
        <img class="banner-img" src="./static/image/welcome.png" width="732px" height="372" alt="图片描述">
    </div>
    <div class="img-content">
        <ul>
            <?php foreach($goods as $v):?>
            <li>
                <img class="img-li-fix" src="<?php echo $v['pic']?>" alt="<?php echo $v['goods_name']?>">
                <div class="info">
                    <a href="detail.php?id=<?php echo $v['id']?>"><h3 class="img_title"><?php echo $v['goods_name']?></h3></a>
                    <p>
                        <?php echo $v['des']?>
                    </p>
                    <div class="btn">
                        <a href="edit.php?id=<?php echo $v['id']?>" class="edit">编辑</a>
                        <a href="delete.php?id=<?php echo $v['id']?>" class="del">删除</a>
                    </div>
                </div>
            </li>
            <?php endforeach;?>

        </ul>
    </div>
    <?php echo $pages ?>
</div>

<div class="footer">
    <p><span>配图短文</span>©2019 POWERED BY 云亦然</p>
</div>
</body>
<script src="./static/js/jquery-1.10.2.min.js"></script>
<script>
    $(function () {
        $('.del').on('click',function () {
            if(confirm('确认删除吗?'))
            {
                window.location = $(this).attr('href');
            }
            return false;
        })
    })
</script>
</html>

