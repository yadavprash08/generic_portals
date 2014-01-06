<?php
require_once 'functions.php';
require_once 'Classes/PortalMailer.php';
if(!isset($_SESSION)){ session_start(); }
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if(!isset($db)){ $db = new MySQLDataBase(); }

$tablename = "applicant_1";

if(isset($_POST['button1'])){
  $oldpassword = $_POST['textbox1'];
  $newpassword = $_POST['textbox2'];
  
  $applicant = $_SESSION['applicant_login'];
  $query = "SELECT * FROM `$tablename` WHERE `id` = '$applicant' ";
  $db->query($query);
  
  if($db->last_result[0]->password == $oldpassword){
    $query = "UPDATE `$tablename` SET `password` = '".str_replace("'", "''", $newpassword)."' WHERE `id` = '$applicant' ";
    $db->query($query);
    echo $db->last_error;
    $changepasswdStatus = "yes";
  }else{
    $changepasswdStatus = "no";
  }
    
}

if(isset($_POST['button2'])){
  $username = strtolower(trim($_POST['textbox4']));
  $query = "SELECT * FROM `$tablename` WHERE `username` = '$username' ";
  $db->query($query);
  if($db->num_rows == 1){

    $password = $db->last_result[0]->password;
    $mail_msg = "<p>Whelcome to the portal. Thank you for registration in the portal.
        To proceede further in the portal you will be required for the username 
        and password at the login page.</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p style=\"padding-left:30px;\"><strong>Username :: </strong> $username</p>
        <p style=\"padding-left:30px;\"><strong>Password :: </strong> $password</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>This in an auto generated mail. Please do not reply to the same.</p>
        ";
    $mailor = new PortalMailer();
    $mailor->SetSubject("Password for the registration to the Portal");
    $mailor->AddAddress($username);
    $mailor->AddMessage($mail_msg);
    $mailor->SendMail();

    $mailorstatus = TRUE;
    
  }
}

require_once 'header.php';

if(isset($_SESSION['applicant_login'])){
?>
<script type="text/javascript">
function matchPassword(obj){
  var src = document.getElementById('textbox2');
  var msg = document.getElementById('passwordmatch');
  if(obj.value != src.value){
    msg.innerHTML = "Passwords do not match.";
  }else{
    msg.innerHTML = "";
  }
  
}
</script>
<div class="inputform">
  <fieldset>
    <legend>Change Password</legend>
    
    <label>Old Password</label>
    <input type="password" name="textbox1" id="textbox1" value=""/>
    
    <label>New Password</label>
    <input type="password" name="textbox2" id="textbox2" value=""/>
    
    <label>Retype Password</label>
    <input type="password" name="textbox3" id="textbox3" value="" onkeyup="matchPassword(this);"/>
    <label id="passwordmatch" style="color: #f00; display: inline-block;"></label>
    <br/>
    <input type="submit" id="button1" name="button1" value="Change"/>
    
  </fieldset>
</div>
<?php
}else{
?>
<div class="inputform">
  <fieldset>
    <legend>Forgot Password</legend>
    <label>Username/Email</label>
    <input type="text" id="textbox4" name="textbox4" value=""/>
    <br/>
    <input type="submit" id="button2" name="button2" value="Submit"/>
  </fieldset>
</div>
<?php
}
if(isset($mailorstatus)){
?>
<p>The login details are been sent to your mail address. Please check the mail 
  and proceed with the provided login details.</p>
<?php
}
if(isset($changepasswdStatus)){
  if($changepasswdStatus=="yes"){
    ?>
<p>Password changed successfully.</p>
      <?php
  }else{
    ?>
<P style="color: #f00;">
  Unable to change password. Please provide the correct old password.
</P>
      <?php
  }
}
?>
<?php
require_once 'footer.php';
?>