<div class="login_form_container aligner">
    <?php if( count($data->errors) ): ?>
        <?php echo "<div class=\"error\">"; ?>
        <?php foreach( $data->errors as $error ): ?>
        <?php echo $error."<br>"; ?>
        <?php endforeach; ?>
        <?php echo "</div>"; ?>
    <?php endif; ?>
    <div class="title">%common.admin.login.title%</div>
    <form method="post">
    <div class="label">%common.admin.login.username%:</div>
    <div class="input"><input type="text" name="username"></div><div class="clear"></div>
    <div class="label">%common.admin.login.email%:</div>
    <div class="input"><input type="text" name="email"></div><div class="clear"></div>
    <div class="label">%common.admin.login.password%:</div>
    <div class="input"><input type="password" name="password"></div><div class="clear"></div>
    <div style="float: center;"><input type="submit" name="login" value="%common.admin.login.login_button%"></div>
    </form>
</div>