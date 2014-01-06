<?php
if(!isset($_SESSION)){  session_start(); }
if(!isset($_SESSION['admin'])){ header("Location: ../applicant_login.php"); }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
  <head>
    <title>
      Home Page
    </title>
    <link href="site.css" media="all" type="text/css" rel="stylesheet"/>
    <script type="text/javascript" src="script.js"></script>
  </head>
  <body>
    <form method="post" action="" id="frmForm1">
      <div class="page">
        <div class="header">
          <div class="title">
            <h1>Generic Portal</h1>
          </div>
          <div class="clear hideSkiplink">
            <div class="menu" id="navigationMenu1">
              <ul class="level1">
                <li style="float: left;"><a href="index.php" tabindex="-1">HOME</a></li>
                <li style="float: left;"><a href="files.php" tabindex="-1">APPLICATIONS</a></li>
                <li style="float: left;"><a href="createreporting.php" tabindex="-1">CREATE REPORT</a></li>
                <li style="float: left;"><a href="report.php" tabindex="-1">VIEW REPORT</a></li>
                <li style="float: left;"><a href="../about.php" tabindex="-1">ABOUT</a></li>
                <li style="float: left;"><a href="../contact.php" tabindex="-1">CONTACT</a></li>
              </ul>
            </div>
            <div style="clear: left;"></div>
          </div>
        </div>
        <div class="main">
        