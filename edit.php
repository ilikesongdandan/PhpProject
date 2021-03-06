<?php

session_start();
$login=FALSE;

if(!isset($_GET['entry']))
{
    echo '请求参数错误';
    exit();
}

if(empty($_SESSION['user']) ||  $_SESSION['user']!='admin')
{
    echo '请<a href="login.php">登录</a>后执行该操作';
    exit();
}


$path= substr($_GET['entry'], 0,6);
$entry=  substr($_GET['entry'], 7,9);
$file_name='contents/'.$path.'/'.$entry.'.txt';

if(file_exists($file_name))
{
    $fp=@fopen($file_name, 'r');
    if($fp)
    {
        flock($fp,LOCK_SH);
        $result=  fread($fp, 1024);
    }
    flock($fp, LOCK_UN);
    fclose($fp);
    $content_array=  explode('|', $result);
}

if(isset($_POST['title']) && isset($_POST['content']))
{
    $title = trim($_POST['title']);
    $content=  trim($_POST['content']);
    
    if(file_exists($file_name))
    {
        $blog_temp= str_replace($content_array[0], $title, $result);
        $blog_str = str_replace($content_array[2], $content, $blog_temp);
        $fp=@fopen($file_name, 'w');
        if($fp)
        {
            flock($fp, LOCK_EX);
            $result = fwrite($fp, $blog_str);
            $lock = flock($fp, LOCK_UN);
            fclose($fp);
        }
    }
    
    if(strlen($result)>0)
    {
        $login=true;
        $msg='日志修改成功，<a href="post.php?entry='.$_GET['entry'].'">查看该文章</a>';
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>我的BLOG</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div id="container">
            <div id="header">
                 <h1>我的BLOG</h1>
            </div>
            <div id="title">
                ----I have a dream....
            </div>
            <div id="left">
                <div id="blog_entry">
                    <div id="blog_title">编辑日志</div>
                    <div id="blog_body">
                        <?php if($login == FALSE)
                        {
                        ?>
                    <div id="blog_date"></div>
                    <table border="0">
                        <form method="post" action="edit.php?entry=<?php echo $_GET['entry']; ?>">
                            <tr><td>日志标题：</td></tr>
                            <tr><td><input type="text" name="title" size="50" value="<?php echo $content_array[0];?>"</td></tr>
                            <tr><td>日志内容：</td></tr>
                            <tr><td><textarea name="content" cols="49" rows="10"><?php echo $content_array[2];?></textarea></td></tr>
                            <tr><td>创建于：<?php echo date('Y-m-d H:i:s',$content_array[1]);?></td></tr>
                            <tr><td><input type="submit" value="提交"/></td></tr>
                        </form>
                    </table>
                        <?php } ?>
                    <?php if($login == true){ echo $msg;}?>
                     </div> 
                </div>
            </div>    
            <div id="right">
                <div id="side_bar">
                    <div id="menu_title">关于我</div>
                    <div id="menu_body">我是个PHP爱好者</div>
                    <br/><br/>
                        <?php  
                        if(!$login)
                        {
                            echo '<a href="logout.php">退出</a>';
                        }
                        else
                        {
                            echo '<a href="login.php">登录</a>';
                        }
                        ?>
                </div>
            </div>
        </div>
        <div id="footer">CopyRight@ 2015 01 19</div>
    </body>
</html>
