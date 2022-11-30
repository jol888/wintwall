<?php
//过滤sql命令
/*function post_check($value) {
    if(!get_magic_quotes_gpc()) {
        // 进行过滤 
        $value = addslashes($value);
    }
    $value = str_replace("_", "\_", $value);
    $value = str_replace("%", "\%", $value);
    $value = nl2br($value);
    $value = htmlspecialchars($value);
    return $value;
}*/
$post_inf = "no";
if (isset($_COOKIE['email'])) 
{
    $email = $_COOKIE['email'];
    $is_email_set = "yes";
    echo "设置成功";
    if (isset($_POST["submit"])) {
        echo "2323";
        if (isset($_POST['title'], $_POST['note'], $_POST['c'], $_POST['contact'], $_POST['passwd'], $_COOKIE['email'])) {
            $post_inf = "yes";
            echo "123123";
            include("sql.php");
            $conn = new mysqli($SQLservername, $SQLusername, $SQLpassword, "whitewall");
            $conn->query("set names 'utf-8'");
            if ($conn->connect_error)
                die("连接失败: " . $conn->connect_error);
            $sql = "INSERT INTO White (title, note, nickname , contact , passwd , email)
VALUES (" . "'" . $_POST["title"] . '\',' . "'" . $_POST['note'] . '\',\'' . $_POST['c'] . '\',\'' . $_POST['contact'] . '\',\'' . $_POST['passwd'] . '\', \'' . $email . '\')';

            if ($conn->query($sql) === TRUE) {
                echo "新记录插入成功";
            }
            else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            $conn->close();
        }
        else {
            $is_all_filled = "no";
        }
    }
}
?>

<html>
    <meta charset="utf-8">
        <script src="./js/jquery.js"></script>
        <script src="./popper.min.js"></script>
        <script src="./js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="./css/bootstrap.min.css">
        </dev><script src="./masonry.pkgd.min.js"></script>
        <style>
            body{background:url('./bg.png') center no-repeat fixed; background-size:cover}
            .navbar{
                margin-bottom:20px
            }
            #lst{
                background-color:rgba(10,10,10,0.4);
                margin:30px;
                
            
            }
            .card{
                opacity: 0.6;
            }
            .navbar{
                opacity: 0.9;
                
            }
            
            </style>
        <meta name="viewport" content="width=device-width, user-scalable=no, 
initial-scale=1.0, maximumscale=1.0, minimum-scale=1.0">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">GWhintWall</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
          
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                  <a class="nav-link" href="/">表白墙</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="add.php">写纸条</a>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                    友链
                  </a>
                  <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">Jol888</a>
                    <a class="dropdown-item" href="#">DLL</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">加入我们</a>
                  </div>
                </li>
              </ul>
              <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="看看有咩有你的名字" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">查找</button>
              </form>
              <script>
function jump(){
 window.open("verify.php");
}
</script>
<button class="btn btn-outline-success my-2 my-sm-0" onclick = " jump() " >验证邮箱</button>
            </div>
          </nav>
<?php
if ($is_email_set == "yes") {
    $verifyemail = "<span style='color:red'><h1 align='center'>邮箱地址验证已完成，当前邮箱地址：" . $_COOKIE['email'] . "</h1></span>";
    echo $verifyemail;
}
else {
    $verifyemail = "<span style='color:red'><h1 align='center'>请点击右上角验证按钮验证邮箱!</h1></span>";
    echo $verifyemail;
}
if ($post_inf == "yes") {
    echo "<span style=\"color:red\">正在提交信息……</span>";
}
/*
 if (isset($_POST["ok"])) {
 echo "<script> alert('所有字段已填写完成');";
 }else{
 echo "<script> alert('尚未填完所有字段或为验证邮箱');";
 }
 if($is_all_filled = "no"){
 echo "<span style=\"color:red\">您还有字段尚未填写！</span>";
 }else{
 echo "<span style=\"color:red\">所有字段已填写完成</span>";
 }*/
?>
<div id="lst">
<form style="margin:50px" action="./add.php" method="post">
  <div class="form-group">
    <label for="exampleBT1" class="text-light"">标题</label>
    <input class="form-control bg-dark text-light" id="exampleBT1" name="title" aria-describedby="BTHelp">
    <small id="BTHelp" class="form-text text-muted">这将显示在您纸条的最上方。搜索功能将检索此栏，建议将对象的名字写在此处。</small>
  </div>
  <div class="form-group">
    <label for="exampleZW1" class="text-light">正文</label>
    <textarea class="form-control" id="exampleZW1" name="note" aria-describedby="ZWHelp"  rows="10"></textarea>
    <small id="ZWHelp" class="form-text text-muted">这是纸条的内容。支持HTML。（请勿滥用）</small>
  </div>
  
  <div class="form-group">
    <label for="exampleSM1" class="text-light">署名</label>
    <input class="form-control" id="exampleSM1" name="c"aria-describedby="SMHelp">
    <small id="SMHelp" class="form-text text-muted">这将显示在标题下方。</small>
  </div>
<div class="form-group">
    <label for="exampleInputPassword1" class="text-light">密钥</label>
    <input type="password" class="form-control" name="passwd"id="exampleInputPassword1">
    <small id="passHelp" class="form-text text-muted">这是您认领纸条的凭据。拥有了凭据，您便可以删除、修改该张纸条。请妥善保管它。</small>
  </div>
    <div class="form-group">
    <label for="exampleInputEmail1" class="text-light">联系方式</label>
    <input class="form-control" id="exampleInputEmail1" name="contact" aria-describedby="emailHelp>
    <small id="emailHelp" class="form-text text-muted>
    每一个对这张纸条感兴趣的用户都可以看到您在此栏填写的联系方式。若您不愿公开您的联系方式，可在此栏填“#”。请注意：即使您在此栏填写“#”，您的电子邮件地址仍会被安全私密地储存在数据库中</small>
  </div>
  
  <div class="form-group form-check">
    <input type="checkbox" class="form-check-input" name="ok" id="exampleCheck1">
    <label class="form-check-label text-light" for="exampleCheck1">已阅读并同意《用户条款》</label>
  </div>
  <input type="hidden" name="submit" value="233">
  <button type="submit" class="btn btn-primary">提交</button>
</form>
</div>
    </body>
</html>

