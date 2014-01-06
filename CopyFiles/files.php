<?php
require_once '../functions.php';
if(!isset($db)){ $db = new MySQLDataBase(); }
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$data = array(array('#','Applicants','Email','STATUS','TIME'));
$query = "SELECT `username`, `status`,`cr_time` FROM `%tablename%`";
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

//%codepage%

require_once 'header.php';
?>
<h2>Summary of Registrations</h2>
<?php

drawHTMLTABLE($data);
//%codepage2%
?>
<div class="inputform">
  <input type="submit" id="button1" name="button1" value="Download"/>
</div>
<?php
require_once 'footer.php';
?>