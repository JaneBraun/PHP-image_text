<?php
header('Content-Type: text/html; charset=utf-8');
//编辑商品
include_once './lib/fun.php';
//检测登录
if(!checkLogin())
{
    msg(2,'请登录','login.php');
}

//表单进行了提交处理
if(!empty($_POST['name']))
{

    $con = mysqlInit('127.0.0.1','root', 'root', 'image_text');

    if(!$goodsId = intval($_POST['id']))
    {
        msg(2,'参数非法');
    }

    //根据商品id校验商品信息
    $con = mysqlInit('127.0.0.1','root', 'root', 'image_text');

    $sql = "SELECT * FROM im_goods WHERE id = '{$goodsId}'";
    $obj = mysql_query($sql);

    //当根据id查询商品信息为空 跳转商品列表页
    if(!$goods = mysql_fetch_assoc($obj))
    {
        msg(2,'东西不存在','index.php');
    }

    //处理表单数据
    //名称
    $name = mysql_real_escape_string(trim($_POST['name']));
    //价格
    $price = intval($_POST['price']);
    //简介
    $des = mysql_real_escape_string(trim($_POST['des']));
    //详情
    $content = mysql_real_escape_string(trim($_POST['content']));

    $nameLength = mb_strlen($name,'utf-8');
    if($nameLength <= 0 || $nameLength > 30)
    {
        msg(2,'名称应在1-30字符之内');
    }

    if($price <= 0 || $price > 999999999 )
    {
        msg(2,'价格应小于999999999');
    }

    $desLength = mb_strlen($des,'utf-8');
    if ($desLength <=0 || $desLength > 100)
    {
        msg(2,'简介应在1-100字符之内');
    }

    if(empty($content))
    {
        msg(2,'详情不能为空');
    }

    $update = array(
        'goods_name'=> $name,
        'price'=> $price,
        'des'  => $des,
        'content'=> $content
    );

    //仅当用户选择上传图片 才进行图片上传处理
    if($_FILES['file']['size'] > 0)
    {
        $pic = imgUpload($_FILES['file']);
        $update['pic'] = $pic;
    }

    //只更新被更改的信息

    foreach ($update as $k=>$v)
    {
        if($goods[$k] == $v)
        {
            unset($update[$k]);
        }
    }
    //对比2个数组 如果没有需要更新的字段
    if(empty($update))
    {
        msg(1,'操作成功','edit.php?id=' . $goodsId);
    }


    //更新sql处理
    $updateSql = '';
    foreach ($update as $k=>$v)
    {
        $updateSql .= " {$k} = '{$v}',";
    }
    //去除多余，
    $updateSql = rtrim($updateSql,',');
    unset($sql,$obj,$result);
    $sql = "UPDATE im_goods SET {$updateSql} where id = {$goodsId}";
    echo $sql;
    //当更新成功
    if($result = mysql_query($sql))
    {
       //mysql_affected_rows();//影响行数
        msg(1, '操作成功','index.php');
    }
    else
    {
        msg(2,'操作失败','edit.php?id=' . $goodsId);
    }

}
else
{
    msg(2, '路由非法','index.php');
}