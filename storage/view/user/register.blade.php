<!DOCTYPE html>
<html lang="en">

<body>
@include('common/header', ['title' => '注册'])
<style>
  .father {
    width: 1000px;
    height: auto;
    margin: 0 auto;
  }

  .layui-input {
    width: 300px;
  }

  .login-main {
    margin-top: 230px;
    margin-left: 350px;
    width: 300px;
    height: 400px; /* border:1px solid #e6e6e6; */
  }

  .layui-form {
    margin-top: 20px;
  }

  .layui-input-inline {
    margin-top: 30px;
  }

  button {
    width: 300px;
  }
</style>
<body>

<div class="father">
  <div class="login-main">
    <p style="color:#009688;font-size:25px;text-align:center;">欢迎注册</p>
    <form class="layui-form">
      <div class="layui-input-inline">
        <input type="text" class="layui-input" name="email" required lay-verify="required|email" placeholder="请输入邮箱"
               autocomplete="off"
               class="layui-input">
      </div>
      <br>
      <div class="layui-input-inline">
        <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" autocomplete="off"
               class="layui-input">
      </div>
      <br>
      <div class="layui-input-inline login-btn">
        <button lay-submit lay-filter="register" class="layui-btn">注册</button>
      </div>
      <hr/>

      <p><a href="/index/login" target="_self" class="fl">立即登录</a></p>
    </form>
  </div>
</div>
<script type="module">
  import {static_user_login, user_register} from '/public/chat/js/api.js';
  import {postRequest} from '/public/chat/js/request.js';

  layui.use(['form', 'layer', 'jquery'], function () {
    var form = layui.form;
    form.on('submit(register)', function (data) {
      postRequest(user_register, data.field, function (data) {

        setTimeout(function () {
          location.href = static_user_login;
        }, 1000);
      });
      return false;
    })
  });
</script>
</body>
</html>
