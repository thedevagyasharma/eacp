<?php
  require_once "mysql.php";

  $stmt = $mysql->prepare('SELECT studentID FROM student order by studentID desc limit 1');
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $sid = $row['studentID'];
  $sid = ltrim($sid,"S");
  $sid = $sid + 1;
  $sid = "S".$sid;

  $stmt = $mysql->prepare('SELECT teacherID FROM teacher order by teacherID desc limit 1');
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $tid = $row['teacherID'];
  $tid = ltrim($tid,"T");
  $tid = $tid + 1;
  $tid = "T".$tid;

  if(isset($_POST['uid'])) {
    $pass = hash('md5',$_POST['pwd']);
    if(strpos($_POST['uid'],"T")!==FALSE){
      $ins = $mysql->prepare('INSERT INTO teacher(teacherID, email, password, name, address, dob, deptID, designation) VALUES(:tid, :em, :pwd, :nm, :addr, :dob, :did, :dsg)');
      $ins->execute(array(
        ":tid" => $_POST["uid"],
        ":em" => $_POST["email"],
        ":pwd" => $pass,
        ":nm" => $_POST["name"],
        ":addr" => $_POST["addr"],
        ":dob" => $_POST["dob"],
        ":did" => $_POST["dept"],
        ":dsg" => $_POST["desig"]
      ));
      while($i<=2){
        if($_POST["con".$i]){
          $ins = $mysql->prepare('INSERT INTO teacherphone VALUES(:tid,:phone)');
          $ins->execute(array(
            ":tid" => $_POST["uid"],
            ":phone" => $_POST["con".$i]
          ));
        }
        $i++;
      }
    }

    else if(strpos($_POST['uid'],"S")!==FALSE) {
      $ins = $mysql->prepare('INSERT INTO student(studentID, email, password, name, address, dob, yearjoin, yearpass, classID) VALUES(:sid, :em, :pwd, :nm, :addr, :dob, year(now()), (select year(DATE_ADD(now(), interval 4 year))), :cid)');
      $ins->execute(array(
        ":sid" => $_POST["uid"],
        ":em" => $_POST["email"],
        ":pwd" => $pass,
        ":nm" => $_POST["name"],
        ":addr" => $_POST["addr"],
        ":dob" => $_POST["dob"],
        ":cid" => $_POST["classID"]
      ));
      $up = $mysql->prepare('UPDATE class set noStudents = noStudents + 1 where classID = :cid');
      $up->execute(array(":cid"));
      while($i<=2){
        if($_POST["con".$i]){
          $ins = $mysql->prepare('INSERT INTO studentphone VALUES(:sid,:phone)');
          $ins->execute(array(
            ":sid" => $_POST["uid"],
            ":phone" => $_POST["con".$i]
          ));
        }
        $i++;
      }
    }

    $_SESSION['success'] = "Registration Successful";
    header('Location:login.php');
    return;
  }

 ?>


<!DOCTYPE html>

<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Register</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/Navigation-with-Button.css">
    <link rel="stylesheet" href="assets/css/styles.css">
  </head>
  <body>
    <div class="card content-post regform">
        <h1>Register</h1>
        <div class="card-body" >
            <form method="post" id ="gform">
              <div class="form-row">
                  <div class="col"><label>Your User ID is : <span id="userid"></span> </label>
                    <input type="hidden" name="uid" id="uidin" value="">
                  </div>
                  <div class="col"></div>
              </div>
                <div class="form-row">
                    <div class="col"><label class="col-form-label">Register as a</label></div>
                    <div class="col">
                        <div class="form-check"><input class="form-check-input" type="radio" id="formCheck-1" name="usertype" value="T"><label class="form-check-label" for="formCheck-1">Teacher</label></div>
                    </div>
                    <div class="col">
                        <div class="form-check"><input class="form-check-input" type="radio" id="formCheck-2" name="usertype" value="S"><label class="form-check-label" for="formCheck-2">Student</label></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col"><label>Full Name</label><input class="form-control" type="text" name="name" id="name"></div>
                    <div class="col"></div>
                </div>
                <div class="form-row">
                    <div class="col"><label>Date of Birth</label><input class="form-control" type="date" name="dob" id="dob"></div>
                    <div class="col"></div>
                </div>
                <div class="form-row">
                    <div class="col"><label>E-Mail</label><input class="form-control" type="email" name="email" id="email"></div>
                    <div class="col"></div>
                </div>
                <div class="form-row">
                    <div class="col"><label>Primary Contact Number</label><input class="form-control" type="text" name="con1" id="ph1" placeholder="Enter 10 Digit Mobile number"pattern="[0-9]{10}"></div>
                    <div class="col"><label>Secondary Contact Number</label><input class="form-control" type="text" name="con2" id="ph2" placeholder="Enter 10 Digit Mobile number" pattern="[0-9]{10}"></div>
                </div>
                <div class="form-row">
                    <div class="col"><label>Address</label><input class="form-control" type="text" name="addr"></div>
                    <div class="col"></div>
                </div>
                <div class="form-row">
                    <div class="col"><label>Password</label><input class="form-control" type="password" name="pwd" id="pwd"></div>
                    <div class="col"><label>Confirm Password</label><input class="form-control" type="password" name="cnfpwd" id="cnfpwd" pattern=".{8}"></div>
                </div>
                <div class="form-row">
                  <div class="col" id="rowch1"></div>
                  <div class="col" id="rowch2"></div>
                </div>
                  <div class="form-row">
                      <button class="btn btn-primary" type="submit" name="register" id="regbtn" disabled style="margin-top: 10px;">Register</button>
                  </div>
              </form>
        </div>
    </div>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
  var uid = "";
  $('.form-check-input').change(function(){
    var type = $(this).val();
    if(type == "T"){
      $('#utype').empty();
      $('#utype').append("Teacher");
      var code1 = ' <label>Department</label><select class="form-control" name="dept"> \
                    <?php
                      $stmtdept = $mysql->prepare('SELECT deptID,deptName FROM department');
                      $stmtdept->execute(array());
                      $rowdept = $stmtdept->fetch(PDO::FETCH_ASSOC);
                      while ($rowdept!==FALSE) {
                        echo'<option value="'.$rowdept['deptID'].'">'.$rowdept['deptName'].'</option>';
                        $rowdept = $stmtdept->fetch(PDO::FETCH_ASSOC);
                      }
                     ?>
                    </select>';
      var code2 = '<label>Designation</label><input class="form-control" type="text" name="desig">';
      uid = "<?= $tid ?>";
    }
    else if(type == "S"){
      var code1 = '<label>Class</label><select class="form-control" name="classID"> \
                    <?php
                      $stmtclass = $mysql->prepare('SELECT classID, year, division, deptName FROM class join department where class.deptID = department.deptID');
                      $stmtclass->execute(array());
                      $rowclass = $stmtclass->fetch(PDO::FETCH_ASSOC);
                      while($rowclass!==FALSE){
                          echo '<option value="'.$rowclass['classID'].'">'.htmlentities($rowclass['deptName'].' '.$rowclass['year'].' '.$rowclass['division']).'</option>';
                          $rowclass = $stmtclass->fetch(PDO::FETCH_ASSOC);
                      }
                     ?> \
                  </select>';

      uid = "<?= $sid ?>";
    }
    $('#rowch1').empty();
    $('#rowch1').append(code1);
    $('#rowch2').empty();
    $('#rowch2').append(code2);
    $('#userid').empty(uid);
    $('#userid').append(uid);
    $('#uidin').attr("value",uid);

  });

  $(document).change(function(){
    var name = $('#name').val();
    var dob = $('#dob').val();
    var email = $('#email').val();
    var pwd = $('#pwd').val();
    var cnfpwd = $('#cnfpwd').val();
    var ph1 = $('#ph1');
    var ph2 = $('#ph2');
    if(email.includes('@') && pwd!='' && cnfpwd!='' && name!='' && dob!='' && uid !=''){
      $('#regbtn').removeAttr("disabled");
  }
    if(pwd == cnfpwd){
      $('#pwd').css("background-color","#ddffdd");
      $('#cnfpwd').css("background-color","#ddffdd");
    }
    else if(pwd != cnfpwd){
      $('#cnfpwd').css("background-color","#ffdddd");
    }
});

</script>

  </body>
</html>
