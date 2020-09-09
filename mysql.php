<?php
  $mysql = new PDO('mysql:host=localhost;port=3307;dbname=miniproject', 'dev', 'password');
  $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 ?>
