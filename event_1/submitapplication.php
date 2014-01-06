<?php
require_once 'functions.php';
if(!$db){ $db = new MySQLDataBase(); }
if(!isset($_SESSION)){ session_start(); }
if(isset($_SESSION['applicant'])){ $applicant = $_SESSION['applicant']; }else{ header("Location: index.php "); }

$status_code = TRUE;
$url_links =array();
/*
 * Checking the minimum requirements for each page
 */
$query = "SELECT count(*) as 'count' FROM `tab_1_page_1` WHERE `applicant` = '$applicant' "; 
$db->query($query);
if($db->last_result[0]->count < 1){ $status_code = FALSE; $url_links[] = 'PAGE_PERSONAL_INFORMATION.php'; }

/*
 * 
 */
if($status_code){
  $query = "UPDATE `applicant_1` SET status='submited' WHERE `id` = '$applicant'";
  $db->query($query);
  header("Location: createPDF.php");
}
require_once 'header.php';
?>
<h1>Complete the application form before submission</h1>
<p>
  Please complete the application form before submission. 
  If submission is refused, some of the mandatory fields are probably left empty or not in correct format.
  Kindly Note::  Once the application is 
  submitted there is no option to change any data. Any information found to be 
  incorrect or improper is solely the responsibility of applicant. Incomplete and/or incorrectly filled applications shall be summarily rejected. 
  MNIT reserves rights to dismiss the application without any further intimation.
</p>
<p>
  Some of the entries on following links are to be filled before submission.
</p>
<ol>
<?php
  foreach($url_links as $pageurl){
    echo "\t<li><a href=\"$pageurl\">$pageurl</a></li>\n";
  }
?>
</ol>
<h2 style="text-align: center;">You'll be redirected back in just <label id="label1">30</label> seconds</h2>
<script type="text/javascript">
function changeCounter() {
  var dest = document.getElementById('label1');
  var val = dest.innerHTML;
  val-=1;
  dest.innerHTML = val;
  
  if(val > 0){
    setTimeout('changeCounter()',1000);
  }else{
    window.location = '<?= $url_links[0] ?>';
  }
}

changeCounter();
</script>
<?php
require_once 'footer.php';
?>