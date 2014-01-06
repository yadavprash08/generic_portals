<?php
require_once 'functions.php';
if(!$db){ $db = new MySQLDataBase(); }
if(!isset($_SESSION)){    session_start(); }

$nowtime = strtotime(date('Y-m-d'));
$starttime = strtotime(date(APP_START_TIME));
$endtime = strtotime(date(APP_END_TIME));
if(isset($_POST['button1'])){
  if(isset($_SESSION['applicant_login'])){
    $applicant = $_SESSION['applicant_login'];
    $query = "SELECT `status` FROM `applicant_1` WHERE `id` = '$applicant'";
    $db->query($query);
    if($db->num_rows==1){
      $status = $db->last_result[0]->status;
      if( $status != "submited"){
        if($status=="registered"){
          $query = "UPDATE `applicant_1` SET `status`='login' WHERE `id` = '$applicant'";
          $db->query($query);
        }
        if($nowtime >= $starttime && $nowtime<=$endtime){
          $_SESSION['applicant'] = $applicant;
          header("Location: PAGE_PERSONAL_INFORMATION.php");
        }else{
          $msg="<p>The portal dates are closed and now you  are not allowed to fill any further information.</p>";
        }
      }else{
        $_SESSION['applicant_over'] = $applicant;
        header("Location: downloadapplication.php");
      }
      
    }
  }else{
    header("Location: applicant_login.php");
  }
}
require_once 'header.php';
?>
<p>&nbsp;</p>
<div>
  <p>This is the sample event just to test the functionality of the generic portal.</p>
</div>
<?php 
if(isset($msg)){
  echo $msg;
}
?>
<div class="inputform">
  <input type="submit" name="button1" id="button1" value="Next" style="float: right;" />
  <div style="clear:both;"></div>
</div>
<?php
require_once 'footer.php';
?>
