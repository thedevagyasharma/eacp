<?php
session_start();
require_once "mysql.php";
if(!isset($_SESSION['username'])){
  die('ACCESS DENIED');
}

if(isset($_POST['title']) && isset($_POST['type']) && isset($_POST['description']) && isset($_POST['class'])){
  if(strlen($_POST['title']) < 1 || strlen($_POST['type']) < 1 || strlen($_POST['description']) < 1 || strlen($_POST['class']) < 1){
    $_SESSION["error"] = "All fields are required";
    header('Location: assignments.php');
    return;
  }

  else{
    $location = '';
    if(isset($_FILES['filein'])){
      $file_name = $_FILES['filein']['name'];
      $file_size = $_FILES['filein']['size'];
      $file_tmp = $_FILES['filein']['tmp_name'];
      $file_type = $_FILES['filein']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['filein']['name'])));
      $location = "uploads/assignments/".$file_name;
      move_uploaded_file($file_tmp,$location);
    }
    $stmt = $mysql->prepare('INSERT INTO post(title,posttext,file,posttime,type,classID,teacherID) values(:title, :posttext, :file, now(), :type, :classID, :tid)');
    $stmt->execute(array(
      ':title' => $_POST['title'],
      ':type' => $_POST['type'],
      ':posttext' => $_POST['description'],
      ':file' => $location,
      ':classID' => $_POST['class'],
      ':tid' => $_SESSION['username']
    ));
    header('Location: assignments.php');
    return;

  }

}




 ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Assignments</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/Navigation-with-Button.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <nav class="navbar navbar-light navbar-expand-lg navigation-clean-button">
        <div class="container"><a class="navbar-brand" href="#">EACP</a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse"
                id="navcol-1">
                <ul class="nav navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="announcements.php">Announcements</a></li>
                    <li class="nav-item"><a class="nav-link" href="attendance.php">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Assignments</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Grades</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Time Table</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                </ul><span class="navbar-text actions"> <a class="btn btn-light action-button btn-logout" role="button" href="logout.php">Logout</a></span></div>
        </div>
    </nav>
    <div class="container-fluid">
      <?php
        if(strpos($_SESSION['username'],"T") !==FALSE){
          echo '<div class="card content-post">
              <div class="card-body">
                  <h4 class="card-title">New Assignment</h4>';

                    if(isset($_SESSION['error'])) {
                      echo('<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n");
                      unset($_SESSION["error"]);
                    }
                  echo'<form method="post" enctype="multipart/form-data">
                      <div class="form-group row"><label class="col-md-3" style="margin: 0px;">Title</label>
                          <div class="col-sm-9"><input class="form-control" type="text" name="title">
                          <input class="form-control" type="hidden" name="type" value="assignment">
                          <input class="form-control" type="hidden" name="author" value="'.htmlentities($_SESSION['username']).'"></div></div>';

                      $stmt = $mysql->prepare('SELECT class.classID, year, division, deptName FROM class join teaches_in on class.classID = teaches_in.classID join department where teacherID = :tid and class.deptID = department.deptID');
                      $stmt->execute(array(
                        ':tid' => $_SESSION['username']
                      ));
                      $row = $stmt->fetch(PDO::FETCH_ASSOC);
                      echo'<div class="form-group row"><label class="col-md-3" style="margin: 0px;">Class</label><div class="col-sm-9"><select id="class" name="class">';
                      while($row!==false){
                        echo '<option value="'.$row['classID'].'">'.htmlentities($row['deptName'].' '.$row['year'].' '.$row['division']).'</option>';
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                      }
                  echo'</select></div></div>';
                    echo '<div class="form-group row"><label class="col-md-3" style="margin: 0px;">Description</label>
                          <div class="col-sm-9"><textarea class="form-control" name="description"></textarea></div>
                      </div>
                      <div class="form-group row"><label class="col-md-3" style="margin: 0px;">Attach File</label>
                          <div class="col-sm-9"><input type="file" name="filein"></div>
                      </div>
                      <button class="btn btn-primary offset-md-3" type="submit">Submit</button>
                  </form></div>
                  </div>';

                  $stmt = $mysql->prepare('SELECT * FROM post WHERE type="assignment" AND teacherID = :id');
                  $stmt->execute(array(
                    ':id' => $_SESSION['username']
                  ));
                  $row = $stmt->fetch(PDO::FETCH_ASSOC);
                  while($row!==FALSE){
                    echo '<div class="card content-post">
                        <div class="card-body">
                            <h4 class="card-title">'.htmlentities($row['title']).'</h4>
                            <h6 class="text-muted card-subtitle mb-2">'.htmlentities($row['posttime']).'</h6>
                            <p class="card-text">'.htmlentities($row['posttext']).'</p>';
                            if($row['file']!=='uploads/assignments/'){
                                echo'<a class="card-link" href="'.htmlentities($row['file']).'" download>Download Attachment</a>';
                            }

                    echo'</div>
                    <div class="offset-11"><a class="btn btn-light action-button btn-logout" role="button" style="color:white;" href="delete.php?postID='.$row['postID'].'">Delete</a></div>
                    </div>';
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                  }
                }
                else{
                  $stmt = $mysql->prepare('SELECT title,posttime,posttext,file,teacher.name as author FROM post,student,teacher WHERE type="assignment" AND studentID = :id AND post.classID = student.classID AND post.teacherID = teacher.teacherID');
                  $stmt->execute(array(
                    ':id' => $_SESSION['username']
                  ));
                  $row = $stmt->fetch(PDO::FETCH_ASSOC);
                  if($row===FALSE){
                    echo'<div class="card content-post"><h4>No Assignments</h4></div>';
                  }
                  while($row!==FALSE){
                    echo '<div class="card content-post">
                        <div class="card-body">
                            <h4 class="card-title">'.htmlentities($row['title']).'</h4>
                            <h6 class="text-muted card-subtitle mb-2">'.htmlentities($row['author']).'</h6>
                            <h6 class="text-muted card-subtitle mb-2">'.htmlentities($row['posttime']).'</h6>
                            <p class="card-text">'.htmlentities($row['posttext']).'</p>';
                            if($row['file']!=='uploads/assignments/'){
                                echo'<a class="card-link" href="'.htmlentities($row['file']).'" download>Download Attachment</a>';
                            }
                    echo'</div></div>';
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                  }
                }
       ?>


    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
