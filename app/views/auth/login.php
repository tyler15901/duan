<h3>Đăng nhập</h3>
<?php if(!empty($registered)): ?><div class="alert alert-success">Đăng ký thành công! Đăng nhập ngay.</div><?php endif; ?>
<?php if(!empty($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="post">
    <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
    </div>
    <button type="submit" class="btn btn-primary">Đăng nhập</button>
</form>
<p>Chưa có tài khoản? <a href="/?url=auth/register">Đăng ký</a></p>