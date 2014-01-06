<?php
require_once 'functions.php';
if(!isset($_SESSION)){  session_start(); }
if(!isset($_SESSION['applicant_over'])){  header("Location: applicant_login.php"); }
$applicant = $_SESSION['applicant_over'];
if(isset($_POST['button1'])){
  $filename = "PDF_FILES/file$applicant.pdf";
  header('Content-Type: application/pdf');
  header('Content-Disposition: attachment; filename="Application.pdf"');
  header('Content-Length:'.  filesize($filename));
  readfile($filename);
}
require_once 'header.php';
?>

<p>Thank You for your submission of the application. Please click the download Button to download a copy of your application.</p>
<div class="inputform">
  <input type="submit" name="button1" id="button1" value="Download" style="float: right;"/>
</div>

<?php
require_once 'footer.php';
?>