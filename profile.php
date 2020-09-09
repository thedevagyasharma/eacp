<?php
session_start();
require_once "mysql.php";
if(!isset($_SESSION['username'])){
  die('ACCESS DENIED');
}
$error = isset($_SESSION['error']) ? $_SESSION['error']: false;

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Profile</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/Navigation-with-Button.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <nav class="navbar navbar-light navbar-expand-md navigation-clean-button navbar-expand-lg!important">
        <div class="container"><a class="navbar-brand" href="#">EACP</a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse"
                id="navcol-1">
                <ul class="nav navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="announcements.php">Announcements</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Assignments</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Grades</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Time Table</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
                </ul><span class="navbar-text actions"> <a class="btn btn-light action-button btn-logout" role="button" href="logout.php">Logout</a></span></div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col col-xl-6">
                <div class="card content-post">
                    <div class="row">
                        <div class="col-lg-4 justify-content-center d-flex"><img class="rounded-circle img-thumbnail" src="assets/img/blank-profile-picture-973460_640.png" style="height:150px;width:150px;"></div>
                        <div class="col-lg-8 justify-content-center justify-content-lg-start d-flex">
                            <div class="row justify-content-center">
                                <div class="col-lg-12 justify-content-center text-center text-lg-left">
                                  <?php
                                    echo "<h4>".htmlentities($_SESSION['name'])."</h4>";
                                    if(strpos($_SESSION['username'],"T")!==FALSE){
                                      $stmt = $mysql->prepare('SELECT * FROM teacher,department WHERE teacherID = :tid AND teacher.deptID = department.deptID');
                                      $stmt->execute(array(':tid' => $_SESSION['username']));
                                      $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                      echo '<h6 class="text-muted card-subtitle mb-2">Teacher ID : '.htmlentities($row['teacherID']).'</h6>';
                                      echo '<p>'.htmlentities($row['designation']).' | '.htmlentities($row['deptName']).'</p>';
                                      echo '<p>Date of Birth : '.htmlentities($row['dob']).'</p>';
                                    }
                                   ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
