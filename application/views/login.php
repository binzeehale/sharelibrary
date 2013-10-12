<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>资料管理系统</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="zhengbin">

  <!-- Le styles -->
  <link href="<?=base_url('/resources/public/bootstrap/css/bootstrap.css')?>" rel="stylesheet">
  <link href="<?=base_url('/resources/css/global.css')?>" rel="stylesheet">
  <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
        font-family: "Microsoft YaHei";
      }

      .form-signin {
        max-width: 550px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }

      .form-signin-heading{
        text-align: center;
        margin: 0 auto;
        margin-bottom: 50px !important;
      }

      .bottom30 {
        margin-bottom: 30px;
      }

      .signUp {
        text-decoration: underline;
        vertical-align: bottom;
      }
    </style>

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="<?=base_url('/resources/public/bootstrap/assets/js/html5shiv.js')?>"></script>
  <script src="<?=base_url('/resources/public/bootstrap/assets/js/respond.min.js')?>"></script>
  <![endif]-->
<body>

  <div class="container">
  <div class="form-signin">
    <blockquote class="pull-right">
      <p>资料管理系统</p>
      <small>攀枝花市环保局</small>
    </blockquote>
    <div class="clearfix bottom30"></div>
    <form id="loginForm" class="form-horizontal" method="POST" action='<?=base_url("/login/signIn")?>'>
      <div class="control-group">
        <label class="control-label" for="username">账号</label>
        <div class="controls">
          <input type="text" id="username" name="username" placeholder="账号"/></div>
      </div>
      <div class="control-group">
        <label class="control-label" for="password">密码</label>
        <div class="controls">
          <input type="password" id="password" name="password" placeholder="密码"/></div>
      </div>
      <div class="control-group">
        <div class="controls">
          <?php if(isset($redirect)):?>
          <input type="hidden" name="redirect" value="<?=$redirect?>" />
          <?php endif; ?>
          <label class="checkbox">
            <input type="checkbox" name="remember">记住密码</label>
        </div>
      </div>
      <div class="control-group">
        <div class="controls">
        <button type="submit" class="btn btn-primary">登录</button>
        <a href="#signUpModal" role="button" class="signUp" data-toggle="modal">注册</a>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- /container -->

<!-- SignUp Modal -->
<div id="signUpModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">新用户注册</h3>
  </div>
  <form id='signUpForm' class="form-horizontal" method="POST" action="<?=base_url('/login/signUp')?>">
  <div class="modal-body">
      <div class="control-group">
        <label class="control-label" for="signusername">账号</label>
        <div class="controls">
          <input type="text" id="signusername" name="signusername" placeholder="账号"></div>
      </div>
      <div class="control-group">
        <label class="control-label" for="signpassword">密码</label>
        <div class="controls">
          <input type="password" id="signpassword" name="signpassword" placeholder="密码"></div>
      </div>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    <button type="submit" class="btn btn-primary">注册</button>
  </div>
  </form>
</div>
<!-- /SignUp Modal -->

<!-- Le javascript
    ================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?=base_url('/resources/public/bootstrap/assets/js/jquery.js')?>"></script>
<script src="<?=base_url('/resources/public/bootstrap/assets/js/jquery.validate.js')?>"></script>
<script src="<?=base_url('/resources/js/bootstrap.jquery.validate.js')?>"></script>
<script src="<?=base_url('/resources/language/jquery.validate.chinese.js')?>"></script>
<script src="<?=base_url('/resources/public/bootstrap/js/bootstrap.min.js')?>"></script>
<script type="text/javascript">

$(document).ready(function(){

  $('#loginForm').validate({
    rules:{
      username: {
        required: true,
        legalStringCN: true
      },
      password: {
        required: true,
        legalString: true,
        remote: {
          url: "<?=base_url('/login/verifyAccount')?>",
          type: "post",
          data: {
            username: function() {
              return $( "#username" ).val();
            },
            password: function(){
              return $( "#password" ).val();
            }
          }
        }
      }
    },
    messages: {
      password: {
        remote: "账号/密码不正确"
      }
    }
  });

  $('#signUpForm').validate({
    rules:{
      signusername: {
        required: true,
        legalStringCN: true,
        remote: {
          url: "<?=base_url('/login/checkUserName')?>",
          type: "post",
          data: {
            signusername: function() {
              return $( "#signusername" ).val();
            }
          }
        }
      },
      signpassword: {
        required: true,
        legalString: true,
      }
    },
    messages: {
      signusername: {
        remote: "用户名已存在"
      }
    }
  });
});
</script>
</html>