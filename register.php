<?php
require_once("classes/init.php");

$user = new User();

if (isset($_POST['user_register'])) {

    $user->login     = trim($_POST['login']);
    $user->email     = trim($_POST['email']);
    $user->password  = trim($_POST['password']);
    $password_conf   = trim($_POST['confirm_password']);

    if($user->password !== $password_conf){
        $pass_conf_err = "Fields 'password' and 'confirm password' don't match each other!";
    } else {
        $user->password = password_hash($user->password, PASSWORD_BCRYPT, array('cost' => 12));
        $user->save();
        header('Location:login.php');
    }
}


?>

<?php include_once('includes/register_header.php'); ?>
<body>
<div class="signup-form">
    <form action="" method="post">
		<h2>Register</h2>
		<p class="hint-text">Create your account. It's free and only takes a minute.</p>
        <div class="form-group">
            <input type="text" class="form-control" name="login" placeholder="Login" required="required"
                   value="<?php echo isset($user->login) ? $user->login : ''; ?>">
        </div>
        <div class="form-group">
        	<input type="email" class="form-control" name="email" placeholder="Email" required="required"
                   value="<?php echo isset($user->email) ? $user->email : ''; ?>">
        </div>
		<div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password" required="required">
        </div>
		<div class="form-group">
            <span style="color:red;"><?php echo isset($pass_conf_err) ? $pass_conf_err . "<br>" : '' ?></span>
            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required="required">
        </div>
		<div class="form-group">
            <button type="submit" name="user_register" class="btn btn-success btn-lg btn-block">Register Now</button>
        </div>
    </form>
	<div class="text-center">Already have an account? <a href="login.php">Sign in</a></div>
</div>
</body>
</html>

