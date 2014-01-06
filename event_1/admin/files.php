<?php
require_once '../functions.php';
if(!isset($db)){ $db = new MySQLDataBase(); }
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$data = array(array('#','Applicants','Email','STATUS','TIME'));
$query = "SELECT `username`, `status`,`cr_time` FROM `applicant_1`";
$db->query($query);
$results = $db->last_result;
$sno =1;
foreach($results as $row){
  $datarow = array("$sno",'');
  foreach($row as $cell){
    $datarow[] =$cell;
  }
  $data[] = $datarow;
  $sno++;
}

$query = "SELECT * FROM `applicant_1` WHERE `status` = 'submited'";
$db->query($query);
$applicants = $db->last_result;



$data1 = array();
$datarow = array('#','Username');

$datarow[] = "First Name";
$datarow[] = "middle Name";
$datarow[] = "Last Name";
$datarow[] = "Date of Birth";
$data1[] = $datarow;
$sno = 1;
foreach($applicants as $applicant){
$data1[] = array('',$applicant->username);
$query = "SELECT t.`first_name` , t.`middle_name` , t.`last_name` , t.`date_of_birth`  FROM `tab_1_page_1` as t 
          WHERE `applicant` = '$applicant->id' ";
$db->query($query);
$results = $db->last_result;
foreach($results as $row){
$datarow = array($sno,'');
foreach($row as $cell){
$datarow[] = $cell;
}
$sno++;
$data1[] = $datarow;
}
}

if(isset($_POST['button1'])) {
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename="Report.csv"');
	echo ArrayToCSV($data);
die();}


require_once 'header.php';
?>
<h2>Summary of Registrations</h2>
<?php

drawHTMLTABLE($data);
echo "<h2>Summary for Personal Information </h2>";
drawHTMLTABLE($data1);

?>
<div class="inputform">
  <input type="submit" id="button1" name="button1" value="Download"/>
</div>
<?php
require_once 'footer.php';
?>