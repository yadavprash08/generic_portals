<?php
require_once '../functions.php';
if(!isset($db)){ $db = new MySQLDataBase(); }
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$query = "SELECT count(*) as 'count' FROM `%tablename%`";
$db->query($query);
$total_applicants = $db->last_result[0]->count;
require_once 'header.php';
?>

<h1>Welcome <strong>Administrator</strong></h1>
<h3>Status of the site</h3>
<p>
  Total Applicant Registered :: <strong><?= $total_applicants; ?></strong>
</p>
<table>
  <thead>
    <tr>
      <th style="padding: 5px;">Applicant Type</th>
      <th style="padding: 5px;">Applicant Count</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $query = "SELECT `status`, count(*) as 'count' from `%tablename%` GROUP BY `status`";
    $db->query($query);
    $results = $db->last_result;
    foreach($results as $row) {
      
    ?>
    <tr>
      <td style="text-transform: capitalize;"><?= $row->status; ?></td>
      <td style="text-align: center;"><?= $row->count; ?></td>
    </tr>
    <?php
    }
    ?>
  </tbody>
</table>
<?php
require_once 'footer.php';
?>