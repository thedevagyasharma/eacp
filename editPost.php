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

  if(isset($_POST['postID']) && isset($_POST['title']) && isset($_POST['description'])){
    if(strlen($_POST['title']) < 1 || strlen($_POST['description']) < 1){
      $_SESSION["error"] = "All fields are required";
      header('Location: editPost.php?'.$_POST['postID']);
      return;
    }
    $stmt = $mysql->prepare('UPDATE post SET title = :title, posttext = :desc, posttime = now() where postID = :pid');
    $stmt->execute(array(
      ":title" => $_POST['title'],
      ":desc" => $_POST['description'],
      ':pid' => $_POST['postID']
    ));
    $_SESSION['success'] = "Successfully Edited";
    header('Location:'.$type.'.php');
    return;
  }
  if(isset($_POST['cancel'])){
    $_SESSION['error'] = "Edit Cancel";
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
    <title>Edit Post</title>
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
      <h4>Edit Post</h4>
      <?php
        $stmt = $mysql->prepare('SELECT * FROM post where postID = :pid AND teacherID = :tid');
      $stmt->execute(array(":pid" => $_GET['postID'],":tid" => $_SESSION['username']));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if(isset($_SESSION['error'])) {
          echo('<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n");
          unset($_SESSION["error"]);
        }
      echo'<form method="post" enctype="multipart/form-data">
          <div class="form-group row"><label class="col-md-3" style="margin: 0px;">Title</label>
              <div class="col-sm-9"><input class="form-control" type="text" name="title" value="'.htmlentities($row['title']).'">
              <input type="hidden" name="postID" value="'.$_GET['postID'].'">
              </div></div>';

          $stmt1 = $mysql->prepare('SELECT class.classID, year, division, deptName FROM class join teaches_in on class.classID = teaches_in.classID join department where teacherID = :tid and class.deptID = department.deptID');
          $stmt1->execute(array(
            ':tid' => $_SESSION['username']
          ));
          $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
          echo'<div class="form-group row"><label class="col-md-3" style="margin: 0px;">Class</label><div class="col-sm-9">'.htmlentities($row1['deptName'].' '.$row1['year'].' '.$row1['division']).'</div></div>';
        echo '<div class="form-group row"><label class="col-md-3" style="margin: 0px;">Description</label>
              <div class="col-sm-9"><textarea class="form-control" name="description">'.htmlentities($row["posttext"]).'</textarea></div>
          </div>
          <div class="form-group row"><label class="col-md-3" style="margin: 0px;">Attached File</label>
              <div class="col-sm-9">';
              if($row['file']==''){
                echo'No File Attached';
              }
              else{
                echo'<a class="card-link" href="'.htmlentities($row['file']).'" download>Download Attachment</a>';
              }
              echo'</div>
          </div>
          <button class="btn btn-primary offset-md-3" type="submit">Save Changes</button>
          <button class="btn btn-primary" type="submit" name="cancel">Cancel</button>
      </form></div>
      </div>';
      ?>
    </div>
</div>
  </body>
</html>
