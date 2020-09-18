<?php
session_start();
require_once "mysql.php";
if(!isset($_SESSION['username'])){
  die('ACCESS DENIED');
}
$stmt = $mysql->prepare('SELECT type from post where postID =:pid');
$stmt->execute(array(":pid"=>$_GET['postID']));
$type = $stmt->fetch(PDO::FETCH_ASSOC);
$type = $type['type'];
if($type == 'assignment' || $type == 'announcement'){
  $type = $type.'s';
}

  if(isset($_POST['postID']) && isset($_POST['delete'])){
    $stmt1 = $mysql->prepare('SELECT * FROM post where postID = :pid');
    $stmt1->execute(array(
      ':pid' => $_POST['postID']
    ));
    $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
    if($row1['file']!=''){
      unlink($row1['file']);
    }

    $stmt = $mysql->prepare('DELETE FROM post WHERE postID = :pid');
    $stmt->execute(array(
      ':pid' => $_POST['postID']
    ));
    $_SESSION['success'] = "Successfully Deleted";
    header('Location:'.$type.'.php');
    return;
  }
  if(isset($_POST['cancel'])){
    $_SESSION['error'] = "Delete Cancel";
    header('Location:'.$type.'.php');
    return;
  }



 ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/Navigation-with-Button.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Deleting Post...</title>
  </head>
  <body>
    <nav class="navbar navbar-light navbar-expand-lg navigation-clean-button">
        <div class="container"><a class="navbar-brand" href="#">EACP</a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse"
                id="navcol-1">
                <ul class="nav navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="announcements.php">Announcements</a></li>
                    <li class="nav-item"><a class="nav-link" href="attendance.php">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="assignments.php">Assignments</a></li>
                    <li class="nav-item"><a class="nav-link" href="grade.php">Grades</a></li>
                    <li class="nav-item"><a class="nav-link" href="timetable.php">Time Table</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                </ul><span class="navbar-text actions"> <a class="btn btn-light action-button btn-logout" role="button" href="logout.php">Logout</a></span></div>
        </div>
    </nav>

<div class="card content-post">
    <div class="card-body">
      <h4>Deleting Post...</h4>
      <form method="post">
        <input type="hidden" name="postID" value="<?=$_GET['postID']?>">
        <button class="btn btn-logout btn-light action-button" type="submit" name="delete" style="color:white;">Delete</button>
        <button class="btn btn-logout btn-light action-button" type="cancel" name="cancel" style="color:white;">Cancel</button>
      </form>
    </div>
</div>
  </body>
</html>
