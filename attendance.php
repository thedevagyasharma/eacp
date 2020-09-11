<?php
session_start();
require_once "mysql.php";


if(!isset($_SESSION['username'])){
  die('ACCESS DENIED');
}

if(isset($_POST['forclass'])){
  $_SESSION['class'] = $_POST['forclass'];
  header("Location: attendance.php");
  return;
}

if(isset($_POST['fordate'])){
  $stmt = $mysql->prepare('SELECT subjectID from teaches_in where teacherID = :tid');
  $stmt->execute(array(":tid"=>$_SESSION['username']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $stmt1 = $mysql->prepare('SELECT noStudents from class where classID = :cid');
  $stmt1->execute(array(":cid"=>$_SESSION['class']));
  $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
  $count = $row1['noStudents'];
  for($i=1; $i<=$count; $i++) {
    $attended = 0;
    if (isset($_POST['att'.$i]) ){
          $attended = 1;
    }
    $stmt = $mysql->prepare('INSERT INTO attendance VALUES(:cid,:ondate,:suid,:stid,:att)');
    $stmt->execute(array(
      ":cid" => $_SESSION['class'],
      ":ondate" => $_POST['fordate'],
      ":suid" => $row['subjectID'],
      ":stid" => $_POST['st'.$i],
      ":att" => $attended
    ));
  }
  $_SESSION['success'] = "Attendance Marked";
  unset($_SESSION["class"]);
  header("Location:attendance.php");
  return;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Attendance</title>
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
                    <li class="nav-item"><a class="nav-link" href="#">Attendance</a></li>
                    <li class="nav-item"><a class="nav-link" href="assignments.php">Assignments</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Grades</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Time Table</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                </ul><span class="navbar-text actions"> <a class="btn btn-light action-button btn-logout" role="button" href="logout.php">Logout</a></span></div>
        </div>
    </nav>
    <div class="container-fluid">

      <?php
      //Teacher's UI
      if (strpos($_SESSION['username'],"T") !==FALSE) {
          if(!isset($_SESSION['class'])){
          echo '<div class="card content-post">
              <div class="card-body">'; ?>
              <?php
                if(isset($_SESSION['success'])) {
                  echo('<p style="color:green;">'.htmlentities($_SESSION['success'])."</p>\n");
                  unset($_SESSION["success"]);
                }
                ?><?php
                  echo'<h4 class="card-title">Add Attendance</h4>
                  <form method="post">
                  <div class="row">
                      <div class="col align-self-center"><select name="forclass">
                      <option>Class</option>';
                      $stmt = $mysql->prepare('SELECT * FROM teaches_in,class,department WHERE teacherID =:tid AND teaches_in.classID = class.classID AND class.deptID = department.deptID');
                      $stmt->execute(array(":tid" => $_SESSION['username']));
                      $row = $stmt->fetch(PDO::FETCH_ASSOC);
                      while($row!==FALSE){
                        echo'<option value="'.htmlentities($row['classID']).'">'.$row['year'].' '.$row['deptName'].' '.$row['division'].'</option>';
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                      }
                      echo'</select></div>

                      <div class="col align-self-center"><button class="btn btn-primary" type="submit">Get Students List</div>
                      </div>
                    </form>
              </div>
              </div>';
            }

              else{
                echo '<div class="card content-post">
                    <div class="card-body">
                        <h4 class="card-title">Add Attendance</h4>
                        <form method="post">
                        <div class="row">
                            <div class="col-10 align-self-center"><select name="forclass">
                            <option>Class</option>';
                            $stmt = $mysql->prepare('SELECT * FROM teaches_in,class,department WHERE teacherID =:tid AND teaches_in.classID = class.classID AND class.deptID = department.deptID');
                            $stmt->execute(array(":tid" => $_SESSION['username']));
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            while($row!==FALSE){
                              if($row['classID']===$_SESSION['class']){
                                echo'<option selected value="'.htmlentities($row['classID']).'">'.$row['year'].' '.$row['deptName'].' '.$row['division'].'</option>';
                              }
                              else{
                                echo'<option value="'.htmlentities($row['classID']).'">'.$row['year'].' '.$row['deptName'].' '.$row['division'].'</option>';
                              }

                              $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            }
                            echo'</select></div>
                            <div class="col align-self-center"><button class="btn btn-primary" type="submit">Get Students List</button></div>
                            </div>
                          </form>
                    </div>
                    </div>';
                  echo '<div class="card content-post">
                  <div class="table-responsive">
                  <form method="post">
                      <table class="table">
                          <thead>
                              <tr>
                                  <th>Student ID</th>
                                  <th>Name</th>
                                  <th>Attended</th>
                              </tr>
                          </thead>
                          <tbody>';
                          $stmt = $mysql->prepare('SELECT studentID,name FROM student WHERE classID = :cid');
                          $stmt->execute(array(":cid" => $_SESSION['class']));
                          $row = $stmt->fetch(PDO::FETCH_ASSOC);
                          $count=1;
                          while($row!==FALSE){
                            echo'<tr>
                                <td>'.$row['studentID'].'</td>
                                <td>'.$row['name'].'</td>
                                <td><input type="checkbox" name="att'.$count.'">
                                  <input type="hidden" name="st'.$count.'" value="'.$row['studentID'].'">
                                </td>

                            </tr>';
                            $count++;
                            $row = $stmt->fetch(PDO::FETCH_ASSOC); ;
                          }
                          echo'</tbody>
                      </table>
                      <input type="date" name="fordate" id="atDate" onload="getDate()">
                      <button class="btn btn-primary" type="submit">Mark Attendance</button>
                      </form>
                  </div>
                  </div>';
              }
      } //Teacher's UI END ?>

      <?php //Student's UI
        if (strpos($_SESSION['username'],"S") !==FALSE) {
          echo'<div class="card content-post">
              <div class="card-body">
                  <h4 class="card-title">Attendance</h4>
                  <table class="table">
                      <thead>
                          <tr>
                              <th>Subject</th>
                              <th>Attendance</th>
                              <th>Percentage</th>
                          </tr>
                      </thead>
                      <tbody>';
                  $stmt = $mysql->prepare('SELECT sum(attended) as att, count(attended) as tot ,attendance.subjectID,subjName FROM attendance,subject WHERE studentID = :sid AND attendance.subjectID = subject.subjectID GROUP BY attendance.subjectID');
                  $stmt->execute(array(":sid"=>$_SESSION['username']));
                  $row = $stmt->fetch(PDO::FETCH_ASSOC);
                  while($row!==FALSE){
                    echo'<tr>
                        <td>'.$row['subjName'].'</td>
                        <td>'.$row['att'].'/'.$row['tot'].'</td>
                        <td>'.($row['att']/$row['tot'] * 100).'%</td>
                    </tr>';
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                  }

              echo'</tbody>
          </table></div>
          </div>';


        }
       ?>



    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        var today = new Date();
        document.getElementById("atDate").value = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
    </script>
</body>

</html>
