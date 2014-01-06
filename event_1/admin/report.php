<?php
require_once '../functions.php';
if (!isset($db)) {
  $db = new MySQLDataBase();
}

////////////////////////////////////////////
$scruittable = "scruitning_1";
$apptable = "applicant_1";

$query = "CREATE TABLE IF NOT EXISTS `$scruittable` (`applicant` int(11) PRIMARY KEY)";
$db->query($query);

///////////////////////////////////////////

if (isset($_POST['shortlist'])) {
  $query = "DELETE FROM `$scruittable`";
  $db->query($query);

  foreach ($_POST['shortlisted'] as $val) {
    $query = "INSERT INTO `$scruittable` values ('$val')";
    $db->query($query);
  }
}



$data=array();
$headerrow=array('Reff #','Email');
$headstatus=true;
$checkbox = array();

$query="SELECT * FROM `$apptable` left outer join `$scruittable` ON (`$apptable`.`id` = `$scruittable`.`applicant`) where `status` = 'submited'";
if(isset($_POST['downloadshrt'])){
  $query.=" AND `applicant` IS NOT NULL ";
}
$db->query($query);
$applicants=$db->last_result;
foreach ($applicants as $applicant) {
  
  $chkcode = "<input type=\"checkbox\" name=\"shortlisted[]\" value=\"$applicant->id\"";
  if ($applicant->applicant != "") {
    $chkcode.= " checked=\"\" ";
  }
  $chkcode.="/>";
  $checkbox[] = $chkcode;
  
  $temprow=array();
  
  $temprow[]= APP_REF_PREFIX. $applicant->id.APP_REF_SUBFIX;
  $temprow[]=$applicant->username;
  
  $query="SELECT `query`, `no_of_cols` FROM `report_1` ORDER BY `order`";
  $db->query($query);
  $rep_queries= $db->last_result;
  foreach ($rep_queries as $rquery) {
    $tmp_query=$rquery->query. " AND `applicant` = $applicant->id";
    $db->query($tmp_query);
    
    /*
     * Header
     */
    if($headstatus) {
      $startpos=7;
      $endpos=  strpos($tmp_query, "FROM");
      $headstring=  substr($tmp_query, $startpos, $endpos - $startpos);
      $headstring=  str_replace("`","",$headstring);
      $heads=str_getcsv($headstring, ",");
      
      foreach($heads as $hcell){
        $headerrow[]=  ucwords(str_replace("_"," ",$hcell));
      }
      $headerrow[]="";
    }
    
    $temprow[]="";
    for ($i=0;$i<$rquery->no_of_cols;$i++)  {
      $temprow[]="-";
    }
    $sno=count($temprow)-($rquery->no_of_cols)-1;
    foreach($db->last_result as $row) {
      foreach($row as $val){
        $temprow[$sno++] = $val;
      }
      $temprow[$sno++] = "";
    }
    
  }
  
  if($headstatus) {
    $data[]=$headerrow;
  }
  
  $data[] = $temprow;
  $headstatus=false;
}

if (isset($_POST['download']) || isset($_POST['downloadshrt'])) {

  header('Content-Type: application/csv');
  header('Content-Disposition: attachment; filename="Report.csv"');
  echo ArrayToCSV($data);
  die();
}


?>
<?php require_once 'header.php'; ?>
<div style="overflow: auto; height: 600px;">
<?php
$data2 = array();


$sno = -1;
foreach ($data as $key => $row) {
  $sno++;
  if($sno!=0)  {
    $temprow = array($sno . " .", $checkbox[$key-1]);
  }else{
    $temprow=array('','');
  }

  foreach ($row as $value) {
    $temprow[] = $value;
  }

  $data2[] = $temprow;
}
drawHTMLTABLE($data2);
?>
</div>
<table style="width: 100%;text-align: center;">
  <tr>
    <td>
      <input type="submit" name="download" Value="Download All" /></td>
    <td>
      <input type="submit" name="shortlist" Value="Short List" /></td>
    <td>
      <input type="submit" name="downloadshrt" Value="Download Shortlisted" /></td>
  </tr>
</table>
<?php require_once 'footer.php'; ?>