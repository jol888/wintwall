<html>
<head>
<meta charset="utf-8">
<title>验证</title><!–标题–>
<style>
.error{color:red;}
</style>
</head>
<body>
<?php

$preg = '/^[a-zA-Z\x{4e00}-\x{9fa5}]{6,20}$/u';
$preg2 = '/^[a-zA-Z]+$/u';
$preg3 = '/^[\x{4e00}-\x{9fa5}]+$/u';
$isInfoCanUse = false;
$email = "";
$emailErr = "必填项目";
$verifyErr = "必填项目";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
function dealInfo($data)
{
    $data = trim($data);
    $data = htmlspecialchars($data);
    $data = stripslashes($data);
    return $data;
}
function codestr()
{
    $arr = array_merge(range('a', 'b'), range('A', 'B'), range('0', '9'));
    shuffle($arr);
    $arr = array_flip($arr);
    $arr = array_rand($arr, 6);
    $res = '';
    foreach ($arr as $v) {
        $res .= $v;
    }
    return $res;
}
function verify($code)
{
    $mail = new PHPMailer(true);
    try {
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.qq.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jeffery.lyu@qq.com';
        $mail->Password = 'ripbxgbbtimfdcib';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->From = 'jeffery.lyu@qq.com';
        $mail->FromName = 'Registery';
        $mail->addAddress($_POST['email'], 'User');
        $mail->addReplyTo('jeffery.lui@qq.com', 'info');
        $yanzheng = $code;
        $mail->isHTML(true);
        $mail->Subject = '小纸条邮箱验证';
        $mail->Body = '<h1>欢迎使用小纸条</h1><h3>您的验证码是：<span>' . $yanzheng . '</span></h3>' . date('Y-m-d H:i:s');
        $mail->AltBody = '欢迎使用小纸条,您的身份验证码是：' . $yanzheng . date('Y-m-d H:i:s');

        $mail->send();
        echo '验证邮件发送成功，请注意查收！';
    }
    catch (FFI\Exception $e) {
        echo '邮件发送失败: ', $mail->ErrorInfo;
    }
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($_POST['yanzheng'] == '' && isset($_POST['email'])) {
        verify(substr(hash('ripemd160', $_POST['email']), 0, 6));
    }
    if (empty($_POST['email'])) {
        $isInfoCanUse = false;
        $emailErr = "验证邮箱不能为空";

    }
    else {

        if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $_POST['email'])) {

            if ($_SERVER['REQUEST_METHOD'] == "POST") {

                if ($_POST['yanzheng'] == substr(hash('ripemd160', $_POST['email']), 0, 6)) {

                    if (!empty($_POST['yanzheng'])) {

                        $email = dealInfo($_POST['email']);
                        register();
                    }
                }
                else {
                    echo("7");
                    $verifyErr = "验证码错误";
                }
            }
        }
        else {
            echo("8");
            $emailErr = "非法邮箱格式";
            $isInfoCanUse = false;
        }
    }
}

function register()
{
    echo "验证成功!";
    setcookie("email", $_POST['email'], time() + 60 * 60 * 24 * 30);
}
?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" >
验证邮箱：<input type="text" id="em" name="email" />
<?php echo "<span class=error>*" . $emailErr . "</span>"; ?><br/>
<button id="sub">发送验证码</button>
</br>
验证码：<input type="text" name="yanzheng" />
<?php echo "<span class=error>*" . $verifyErr . "</span>"; ?><br/>
<input type="submit" value="登录" />
</form>
<script scr="./js/jquery.js">
    $('#sub').click(function(){
               $.ajax({
             type: "POST",
             url: "/verify.php",
             data: {email:$("#em").val()},
             dataType: "json",
             async:true,
             beforeSend: function() {
              //请求前的处理操作
             },
             success: function(data){
                 
             },
             error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //请求出错处理操作
                  alert(XMLHttpRequest.status);
                  alert(XMLHttpRequest.readyState);
                  alert(textStatus);
             },
             complete: function(XMLHttpRequest, textStatus) {
                  this;
             }
         });
    })

</script>
</body>
</html>