<h3>Đăng ký tài khoản</h3>
<?php if(!empty($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="post">
    <div class="mb-3">
        <input type="text" name="name" class="form-control" placeholder="Họ và tên" required>
    </div>
    <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
    </div>
    <div class="mb-3">
        <select name="role" class="form-control" required>
            <option value="candidate">Ứng viên</option>
            <option value="recruiter">Nhà tuyển dụng</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Đăng ký</button>
</form>
<p>Đã có tài khoản? <a href="/?url=auth/login">Đăng nhập</a></p>