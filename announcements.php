<?php
session_start();
require_once "mysql.php";
if(!isset($_SESSION['username'])){
  die('ACCESS DENIED');
}

if(isset($_POST['title']) && isset($_POST['type']) && isset($_POST['description']) && isset($_POST['class'])){
  if(strlen($_POST['title']) < 1 || strlen($_POST['type']) < 1 || strlen($_POST['description']) < 1 || strlen($_POST['class']) < 1){
    $_SESSION["error"] = "All fields are required";
    header('Location: announcements.php');
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
      $location = "uploads/".$file_name;
      move_uploaded_file($file_tmp,$location);
    }
    $stmt = $mysql->prepare('INSERT INTO post(title,posttext,file,posttime,type,classID) values(:title, :posttext, :file, now(), :type, :classID)');
    $stmt->execute(array(
      ':title' => $_POST['title'],
      ':type' => $_POST['type'],
      ':posttext' => $_POST['description'],
      ':file' => $location,
      ':classID' => $_POST['class']
    ));
    header('Location: announcements.php');
    return;

  }

}




 ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>announcement</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/Navigation-with-Button.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <nav class="navbar navbar-light navbar-expand-md navigation-clean-button">
        <div class="container"><a class="navbar-brand" href="#">EACP</a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse"
                id="navcol-1">
                <ul class="nav navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Announcements</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Assignments</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Grades</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Time Table</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                </ul><span class="navbar-text actions"> <a class="btn btn-light action-button btn-logout" role="button" href="logout.php">Logout</a></span></div>
        </div>
    </nav>
    <div class="container-fluid">
      <?php
        if($_SESSION['who'] === "teacher"){
          echo '<div class="card content-post">
              <div class="card-body">
                  <h4 class="card-title">New Announcement</h4>';?>
                  <?php
                    if(isset($_SESSION['error'])) {
                      echo('<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n");
                      unset($_SESSION["error"]);
                    }
                  ?>
                  <?php
                  echo'<form method="post" enctype="multipart/form-data">
                      <div class="form-group row"><label class="col-md-3" style="margin: 0px;">Title</label>
                          <div class="col-sm-9"><input class="form-control" type="text" name="title">
                          <input class="form-control" type="hidden" name="type" value="announcement">
                          <input class="form-control" type="hidden" name="author" value="'.htmlentities($_SESSION['username '])'"></div>
                      </div>';

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
                }
       ?>

        <div class="card content-post">
            <div class="card-body">
                <h4 class="card-title">Announcement Title</h4>
                <h6 class="text-muted card-subtitle mb-2">Teacher's Name</h6>
                <h6 class="text-muted card-subtitle mb-2">Date</h6>
                <p class="card-text">Content</p><a class="card-link" href="#">Link</a><a class="card-link" href="#">Link</a></div>
        </div>
        <div class="card content-post">
            <div class="card-body">
                <h4 class="card-title">Announcement Title</h4>
                <h6 class="text-muted card-subtitle mb-2">Teacher's Name</h6>
                <h6 class="text-muted card-subtitle mb-2">Date</h6>
                <p class="card-text">Content</p><a class="card-link" href="#">Link</a><a class="card-link" href="#">Link</a></div>
        </div>
        <div class="card content-post">
            <div class="card-body">
                <h4 class="card-title">Announcement Title</h4>
                <h6 class="text-muted card-subtitle mb-2">Teacher's Name</h6>
                <h6 class="text-muted card-subtitle mb-2">Date</h6>
                <p class="card-text">Content</p><a class="card-link" href="#">Link</a><a class="card-link" href="#">Link</a></div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>