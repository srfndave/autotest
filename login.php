<?php
session_start();
$location = "/index.php";
if(session_status() === PHP_SESSION_ACTIVE and isset($_SESSION["user"])) {
    header("Location: $location");
}
$title = "User Login";
?>

<?php
    require_once 'header.php';
    require_once 'dbinit.php';
    require_once 'user.php';
?>

<?php
if(isset($_POST["submit"]) and $_POST["submit"] === "Submit") {
    if(isset($_POST["login"]) and isset($_POST["password"])) {
        $sql = 'SELECT id, login, email, name FROM user WHERE login = ? AND password = ?';
        $sth = $pdo->prepare($sql);
        $ret = $sth->execute([$_POST["login"],$_POST["password"]]);
        $user = $sth->fetchObject('User');
        if(isset($user->{"id"})) {
            $_SESSION["user"] = $user;
            header("Location: $location");
        } else {
            $error_msg = "Login or Password is incorrect";
       }
    } else {
        $error_msg = "Login and Password not set";
    }
}
?>
<?php include "topnav.php"; ?>
<div class="container-sm">
     <h1>Login</h1>
<?php if(isset($error_msg)) { ?>
    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
<?php } ?>
    <form action="/login.php" method="post" class="was-validated">
        <div class="form-group row">
            <label for="login"class="col-sm-1 col-form-label">Login</label>
            <div >
                <input type="text" id="login" name="login" class="form-control" placeholder="Enter login" required>
                <div class="valid-feedback">Valid.</div>
                <div class="invalid-feedback">Please enter your login.</div>
            </div>
        </div>
        <div class="form-group row">
            <label for="password"class="col-sm-1 col-form-label">Password</label>
            <div >
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                <div class="valid-feedback">Valid.</div>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-5">
                <button type="submit" name="submit" id="submit" value="Submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
<?php include 'footer.php' ?>
