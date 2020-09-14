<?php
session_start();
require_once "mysql.php";
if(!isset($_SESSION['username'])){
  die('ACCESS DENIED');
}
$error = isset($_SESSION['error']) ? $_SESSION['error']: false;

if(isset($_POST['edit'])){
    if(strlen($_POST['email']) < 1 || strlen($_POST['address']) < 1){
      $_SESSION['error'] = "All fields are required";
      header('Location:editProfile.php');
      return;
    }
		if(strpos($_SESSION['username'],"T")!==FALSE){
      $stmt1 = $mysql->prepare('SELECT password FROM teacher where teacherID = :tid');
      $stmt1->execute(array(':tid' => $_SESSION['username']));
      $row = $stmt1->fetch(PDO::FETCH_ASSOC);
      $check = hash('md5',$_POST['oldpassword']);
      $new = hash('md5',$_POST['newpassword']);
      if($check!==$row['password']){
        $_SESSION['error'] = "Incorrect Old Password";
        header('Location:editProfile.php');
        return;
      }
      else{
        $stmt = $mysql->prepare('UPDATE teacher set email = :eid, password = :pwd, address = :addr WHERE teacherID = :tid');
        $stmt->execute(array(
          ":eid" => $_POST['email'],
          ":pwd" => $new,
          ":addr" => $_POST['address'],
          ":tid" => $_SESSION['username']
        ));
      }
		}
		else if(strpos($_SESSION['username'],"S")!==FALSE){
      $stmt1 = $mysql->prepare('SELECT password FROM student where studentID = :sid');
      $stmt1->execute(array(':sid' => $_SESSION['username']));
      $row = $stmt1->fetch(PDO::FETCH_ASSOC);
      $check = hash('md5',$_POST['oldpassword']);
      $new = hash('md5',$_POST['newpassword']);
      if($check!==$row['password']){
        $_SESSION['error'] = "Incorrect Old Password";
        header('Location:editProfile.php');
        return;
      }
      else{
        $stmt = $mysql->prepare('UPDATE student set email = :eid, password = :pwd, address = :addr WHERE studentID = :sid');
        $stmt->execute(array(
          ":eid" => $_POST['email'],
          ":pwd" => $new,
          ":addr" => $_POST['address'],
          ":sid" => $_SESSION['username']
        ));
        $_SESSION['success'] = "Changes Saved";
        header("Location:profile.php");
        return;
      }
	}
  $_SESSION['success'] = "Changes Saved";
  header("Location:profile.php");
  return;
	}
	//}


?>
<!DOCTYPE html>
   <html lang="en" dir="ltr">


   <head>
       <meta charset="utf-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
       <title>Profile</title>
       <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
       <link rel="stylesheet" href="assets/css/Navigation-with-Button.css">
       <link rel="stylesheet" href="assets/css/styles.css">
   </head>

   <body>
       <nav class="navbar navbar-light navbar-expand-lg navigation-clean-button navbar-expand-lg!important">
           <div class="container"><a class="navbar-brand" href="#">EACP</a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
               <div class="collapse navbar-collapse"
                   id="navcol-1">
                   <ul class="nav navbar-nav mr-auto">
                       <li class="nav-item"><a class="nav-link" href="announcements.php">Announcements</a></li>
                       <li class="nav-item"><a class="nav-link" href="attendance.php">Attendance</a></li>
                       <li class="nav-item"><a class="nav-link" href="assignments.php">Assignments</a></li>
                       <li class="nav-item"><a class="nav-link" href="grade.php">Grades</a></li>
                       <li class="nav-item"><a class="nav-link" href="#">Time Table</a></li>
                       <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
                   </ul><span class="navbar-text actions"> <a class="btn btn-light action-button btn-logout" role="button" href="logout.php">Logout</a></span></div>
           </div>
       </nav>
     	<div class="container-fluid">
        <div class="card content-post">
        <?php  if(isset($_SESSION['error'])) {
            echo('<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n");
            unset($_SESSION["error"]);
          } ?>
     	<form method="post" id="editform">
		    <?php
        if(strpos($_SESSION['username'],"T")!==FALSE){
          $stmt = $mysql->prepare('SELECT * FROM teacher, teacherphone WHERE teacher.teacherID = :tid AND teacherphone.teacherID = teacher.teacherID');
          $stmt->execute(array(':tid' => $_SESSION['username']));
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        else if(strpos($_SESSION['username'],"S")!==FALSE){
          $stmt = $mysql->prepare('SELECT * FROM student,studentphone WHERE student.studentID = :sid AND studentphone.studentID = student.studentID');
          $stmt->execute(array(':sid' => $_SESSION['username']));
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        echo '
        <div class="form-group">
          <label for="InputEmail1">Email :</label><input type="email" name="email" class="form-control" id="InputEmail1" value="'.htmlentities($row['email']).' "></div>
        <div class="form-group">
          <label for="oldPassword1">Enter Old Password :</label>
          <input type="password" name="oldpassword" class="form-control" id="OldInputPassword1">
        </div>
        <div class="form-group">
          <label for="InputPassword1">Enter New Password :</label>
          <input type="password" name="newpassword" class="form-control" id="InputPassword1">
        </div>
        <div class="form-group">
          <label for="InputAddress">Address :</label>
          <input type="address" name="address" class="form-control" id="InputAddress" value="'.htmlentities($row['address']).'">
        </div>';
      /* <div class="form-group">
        <label for="InputPhone">Contact :</label>';
        while($row){
          echo '<p><input type="phone" class="form-control" id="InputPhone" value="'.htmlentities($row['phone']).'"></p>';
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        echo '</div></div>';*/
?>
      <?php
            if(isset($_SESSION['error'])) {
              echo('<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n");
              unset($_SESSION['error']);
            }

 			?>
		  <button type="edit" class="btn btn-primary" name="edit" id="editbtn" disabled>Edit</button>
    <a class="btn btn-primary" role="button" href="profile.php">Cancel</a>
		</form>

     	</div>
     	<script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    $('#InputEmail1').focusout(function(){
        var email = $('#InputEmail1').val();
        if(!email.includes('@') || email == ''){
          $('#InputEmail1').css("background-color","#ffdddd");
          $('#InputEmail1').attr("placeholder","Please enter email");
          $('#editbtn').attr("disabled","true");
        }
        else if(email.includes('@') && email != ''){
          $('#InputEmail1').css("background-color","#ddffdd");
        }
        else{
          $('#InputEmail1').css("background-color","#ffffff");
        }
    });
    $('#OldInputPassword1').focusout(function(){
      var op = $('#OldInputPassword1').val();
      if(op == ''){
        $('#OldInputPassword1').css("background-color","#ffdddd");
        $('#OldInputPassword1').attr("placeholder","Old Password required");
        $('#editbtn').attr("disabled","true");
      }
      else{
        $('#OldInputPassword1').css("background-color","#ffffff");
      }
    });
    $('#InputPassword1').focusout(function(){
      var op = $('#InputPassword1').val();
      if(op == ''){
        $('#InputPassword1').css("background-color","#ffdddd");
        $('#InputPassword1').attr("placeholder","New Password required");
        $('#editbtn').attr("disabled","true");
      }
      else{
        $('#InputPassword1').css("background-color","#ffffff");
      }
    });
    $(document).change(function(){
      var email = $('#InputEmail1').val();
      var op = $('#OldInputPassword1').val();
      var op1 = $('#InputPassword1').val();
      if(email.includes('@') && op!='' && op1!=''){
        $('#editbtn').removeAttr("disabled");
    }});
    </script>

     </body>
     </html>
