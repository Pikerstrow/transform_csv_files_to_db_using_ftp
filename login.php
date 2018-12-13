<?php
ob_start();

require_once("classes/init.php");

$user = new User();

if (isset($_POST['user_login'])) {

    $login        = trim($_POST['login']);
    $password     = trim($_POST['password']);

    $user_from_db = User::find_using_query("SELECT * FROM `users` WHERE login = '{$login}'");

    if ($user_from_db != []) {

        $user_form_db      = array_shift($user_from_db);
        $user_pass_from_db = $user_form_db->password;

        if(password_verify($password, $user_pass_from_db)){
            $_SESSION['user'] = $login;
            header('Location:admin/index.php');
        } else {
            $pass_error = "Password is incorrect!";
        }

    } else {
        $login_error = "There is no such login in database!";
        $login = '';
    }

}

?>

<?php include_once('includes/register_header.php'); ?>
<body>
<div class="signup-form">
    <form action="" method="post">
		<h2>LOG IN</h2>
		<p class="hint-text">Please, type in your login and password. </p>
        <div class="form-group">
            <span style="color:red;"><?php echo isset($login_error) ? $login_error . "<br>" : '' ?></span>
            <input type="text" class="form-control" name="login" placeholder="Login" required="required"
                   value="<?php echo isset($login) ? $login : ''; ?>">
        </div>
		<div class="form-group">
            <span style="color:red;"><?php echo isset($pass_error) ? $pass_error . "<br>" : '' ?></span>
            <input type="password" class="form-control" name="password" placeholder="Password" required="required">
        </div>

		<div class="form-group">
            <button type="submit" name="user_login" class="btn btn-success btn-lg btn-block">Log In</button>
        </div>
    </form>
</div>
</body>
</html>

