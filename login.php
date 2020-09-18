<?php
session_start();
require_once "mysql.php";

if(isset($_POST['username'])  && isset($_POST['password'])){
  unset($_SESSION["username"]);
  if(strlen($_POST['username']) < 1 || strlen($_POST['password']) < 1){
    $_SESSION["error"] = "Username and Password are required";
    header('Location: login.php');
    return;
  }

  else {
    $check = hash('md5',$_POST['password']);
    if(strpos($_POST['username'],"S") !== FALSE){
      $stmt = $mysql->prepare('SELECT studentID, name FROM student WHERE studentID = :id AND password = :pw');
      $stmt->execute(array(':id'=> $_POST['username'], ':pw' => $check));
      $row = $stmt->fetch(PDO::FETCH_ASSOC); //$row['name'] $row['studentID']
      if ( $row !== false ) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['username'] = $row['studentID'];
        header("Location: profile.php");
        return;
      }
      else{
          error_log("Login fail ".$_POST['username']." $check");
          $_SESSION["error"] = "Incorrect password";
          header('Location: login.php');
          return;
      }
    }
    else if(strpos($_POST['username'],"T") !==FALSE){
      $stmt = $mysql->prepare('SELECT teacherID, name FROM teacher WHERE teacherID = :id AND password = :pw');
      $stmt->execute(array( ':id' => $_POST['username'], ':pw' => $check));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ( $row !== false ) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['username'] = $row['teacherID'];
        header("Location: profile.php");
        return;
      }
      else{
          error_log("Login fail ".$_POST['username']." $check");
          $_SESSION["error"] = "Incorrect password";
          header('Location: login.php');
          return;
      }
    }


  }
}

 ?>
<!DOCTYPE html>


<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/Navigation-with-Button.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <div class="bg-img" style="background: url(&quot;assets/img/812.jpg&quot;);"></div>
    <div class="login-form d-flex justify-content-center align-items-center">
        <form method="post">
          <?php
            if(isset($_SESSION['error'])) {
              echo('<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n");
              unset($_SESSION["error"]);
            }
            ?>
            <div class="illustration d-flex justify-content-center align-self-center"></div>
            <h1 class="d-sm-none">Login</h1>
            <div class="form-group"><input class="form-control" type="text" placeholder="Username" name="username"></div>
            <div class="form-group"><input class="form-control" type="password" placeholder="Password" name="password"></div>
            <div class="form-group"><button class="btn btn-primary btn-login" type="submit">Log In</button></div>
            <a href="#">Forgot username or password?</a>
            <span style="color:#fff">Not registered? <a href="register.php">Register here</a></span>
          </form>

      </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
