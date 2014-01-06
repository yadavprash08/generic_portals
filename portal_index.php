<?php
require_once 'functions.php';
if(!$db){ $db = new MySQLDataBase(); }

if(!isset($_SESSION)){    session_start(); }
if(isset($_POST['button1'])){
  if(isset($_SESSION['applicant_login'])){
    $applicant = $_SESSION['applicant_login'];
    $query = "SELECT `status` FROM `%tablename%` WHERE `id` = '$applicant'";
    $db->query($query);
    if($db->num_rows==1){
      $status = $db->last_result[0]->status;
      if( $status != "submited"){
        if($status=="registered"){
          $query = "UPDATE `%tablename%` SET `status`='login' WHERE `id` = '$applicant'";
          $db->query($query);
        }
        $_SESSION['applicant'] = $applicant;
        header("Location: %pagename%");
      }else{
        $_SESSION['applicant_over'] = $applicant;
        header("Location: submitapplication.php");
      }
      
    }
  }else{
    header("Location: applicant_login.php");
  }
}
require_once 'header.php';
?>
<p>
  This is the home page of the %event_name%.
</p>
<p>&nbsp;</p>
<p>
  %event_description%
</p>
<div class="inputform">
  <input type="submit" name="button1" id="button1" value="Next" style="float: right;" />
  <div style="clear:both;"></div>
</div>
<?php
require_once 'footer.php';
?>