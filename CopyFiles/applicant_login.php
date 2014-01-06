<?php
require_once 'Classes/PortalMailer.php';
require_once 'functions.php';
require_once 'header.php';
if (!isset($_SESSION)) {
  session_start();
}
if (!$db) {
  $db = new MySQLDataBase();
}

$url_redirect = "index.php";
if (isset($_GET['redirecturl'])) {
  $url_redirect = $_GET['redirecturl'];
}

$errmsg = "";
$registermsg = "";
$mailorstatus = FALSE;

$tablename = "%tablename%";
if (isset($_POST['button1'])) {
  $username = str_replace("'", "''", strtolower(trim($_POST['textbox1'])));
  $password = str_replace("'", "''", $_POST['textbox2']);

  if ($username == "administrator" && $password == ADMIN_PASSWD) {
    $_SESSION['admin'] = "Administrator";
    header("Location: admin/");
  }


  $query = "SELECT `id` FROM `$tablename` WHERE `username` = '$username' AND `password` = '$password';";
  $db->query($query);
  if ($db->num_rows == 1) {
    $_SESSION['applicant_login'] = $db->last_result[0]->id;
    header('Location: ' . $url_redirect);
  }
  $errmsg = "Invalid username or password.";
}

if (isset($_POST['button2'])) {

  $nowtime = strtotime(date('Y-m-d'));
  $starttime = strtotime(date(APP_START_TIME));
  $endtime = strtotime(date(APP_END_TIME));
  if ($nowtime >= $starttime && $nowtime <= $endtime) {
    $username = str_replace("'", "''", strtolower(trim($_POST['textbox3'])));

    $random_password = "";
    for ($i = 0; $i < 16; $i++) {
      $random_char = rand(65, 117);
      $random_password.= chr($random_char);
    }
    $random_password = str_replace("'", "''", $random_password);

    $query = "INSERT INTO `$tablename` (`username`,`password`,`status`) VALUES ('$username','$random_password','registered')";
    $db->query($query);

    if ($db->last_error != "") {
      $registermsg = "The email address is already registerd with us. Please use the forgot password link to reciev e back your previous password.";
    } else {
      $random_password = str_replace("''", "'", $random_password);
      $mail_msg = "<p>Whelcome to the portal. Thank you for registration in the portal.
        To proceede further in the portal you will be required for the username 
        and password at the login page.</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p style=\"padding-left:30px;\"><strong>Username :: </strong> $username</p>
        <p style=\"padding-left:30px;\"><strong>Password :: </strong> $random_password</p>
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
  }else{
    $registermsg = "Sorry! the registration process has been closed. The dated of 
      the events are from <strong>". APP_START_TIME ."</strong> to <strong>
        ". APP_END_TIME ."</strong>." ;
  }
}
?>
<pre>
<?php //print_r($_POST);  ?>
</pre>
<p>
  Existing users need to provide with valid username and password for login. 
  The username is the email address, used while registering for the portal. 
  New users shall need to Register first with a valid email address for submission. 
</p>
<div class="inputform">
  <fieldset style="width: 400px;float: left;height: 200px;">
    <legend>Login</legend>
    <label>Username</label>
    <input type="text" name="textbox1" id="textbox1" value=""/>
    <label>Password</label>
    <input type="password" name="textbox2" id="textbox2" value=""/>

    <label>&nbsp;</label>
    <p>
      <span style="color: red;"><?= $errmsg ?></span>
    </p>
    <input type="submit" value="Login" id="button1" name="button1" style="float: right;"/>
  </fieldset>

  <fieldset style="width: 400px;float: left;margin-left: 10px;height: 200px;">
    <legend>Register (New User)</legend>
    <label>Email</label>
    <input type="text" name="textbox3" id="textbox3" value="" title="username@domain.com"/>

    <label>&nbsp;</label>
    <p style="text-align: justify;">
      <span style="color: red;"><?= $registermsg ?></span>
    </p>
<?php if ($mailorstatus) { ?>
      <p style="text-align: justify;">
        <span style="color: 060;">Thanks for registration with the portal. 
          An email with your username and password is send to the above stated 
          mail address.</span>
      </p>
<?php } else { ?>
      <input type="submit" value="Register" id="button2" name="button2" style="float: right;"/>
<?php } ?>
  </fieldset>
</div>
<?php
require_once 'footer.php';
?>
