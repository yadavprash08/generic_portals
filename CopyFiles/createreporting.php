<?php
require_once '../functions.php';
if (!isset($db)) {
  $db = new MySQLDataBase();
}
$repttable = "/*ReportingTable*/";
if(isset($_GET['delete'])){
  $query = "DELETE FROM `$repttable` WHERE `id` = '".$_GET['delete']."'";
  $db->query($query);
  header('Location: createreporting.php');
}

  
  
/*$table = array();
$query = "SHOW FULL TABLES;";
$db->query($query);
foreach ($db->last_result as $row) {
  $trow = (array) $row;
  $keys = array_keys($trow);
  $table[] = $trow[$keys[0]];
}
*/
if (isset($_POST['add'])) {
  $ins_query = "SELECT ";
  $fields = $_POST['fields'];
  $no_of_cols = count($fields);
  foreach ($fields as $col) {
    $ins_query.="`" . $col . "` , ";
  }
  $ins_query = substr($ins_query, 0, strlen($ins_query) - 2);
  $ins_query.=" FROM " . $_POST['tblname'] . " WHERE 1 ";
  $query = "SHOW FULL COLUMNS FROM " . $_POST['tblname'];
  $db->query($query);
  foreach ($db->last_result as $col) {
    $tempddl_name = "ddl_cond_" . $col->Field;
    $tempinp_name = "inp_" . $col->Field;
    if (isset($_POST[$tempddl_name]) && ($_POST[$tempinp_name]) != "") {
      $ins_query.=" AND `$col->Field` " . $_POST[$tempddl_name] . " '" . $_POST[$tempinp_name] . "'";
    }
  }
  $ins_query = str_replace("'", "''", $ins_query);
  $query = "INSERT INTO `$repttable`(`query`, `no_of_cols`) VALUES ('$ins_query',$no_of_cols)";
  if ($no_of_cols > 0) {
    $db->query($query);
  }
}
?>


<?php require_once 'header.php'; ?>

<fieldset>
  <legend>Reporting</legend>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Query</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $query = "SELECT * FROM `$repttable`";
      $db->query($query);
      $qsno = 0;
      foreach ($db->last_result as $row) {
        $qsno++;
      ?>
      <tr>
        <td><?= $qsno ?> .</td>
        <td><?= $row->query ?></td>
        <td>&nbsp;</td>
        <td><a href="createreporting.php?delete=<?= $row->id ?>">Remove</a></td>
      </tr>
      <?php
      }
      ?>
    </tbody>
  </table>


</fieldset>

<fieldset>
  <legend>Table</legend>
  <p>Select the table from which you wan't to extract the data.</p>
  <select name="tablename" onchange="var frm = document.getElementById('frmForm1');frm.submit();">
    <option value="">Choose one</option>
    <!-- Table Names -->
  </select>
</fieldset>

<?php
if (isset($_POST['tablename'])):
  $tblename = $_POST['tablename'];
  ?>
  <input type="hidden" name="tblname" id="tblname" value="<?= $tblename ?>"/>
  <fieldset>
    <legend>COLUMNS</legend>
    <table style="width: 100%;">
      <thead>
        <tr>
          <th>#</th>
          <th>Column Name</th>
          <th>Display</th>
          <th>Condition</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sno = 0;
        $query = "SHOW FULL COLUMNS FROM $tblename";
        $db->query($query);
        $cols = $db->last_result;

        foreach ($cols as $row) {
          if ($row->Field <> "id" && $row->Field <> "applicant"):
            $sno++;
            ?>
            <tr>
              <td><?= $sno ?> .</td>
              <td><?= ucwords(str_replace("  ", " ", str_replace("_", " ", $row->Field))) ?></td>
              <td><input type="checkbox" name="fields[]" value="<?= $row->Field ?>"/></td>
              <td>
                <select name="ddl_cond_<?= $row->Field ?>">
                  <option value="=">=</option>
                  <option value="<">&lt;</option>
                  <option value="<=">&lt;=</option>
                  <option value=">">&gt;</option>
                  <option value=">=">&gt;=</option>
                  <option value="LIKE">LIKE</option>
                </select>
              </td>
              <td>
                <?php
                $query = "SELECT DISTINCT `$row->Field` as 'field' FROM `$tblename`";
                $db->query($query);
                if ($db->num_rows < 50) {
                  ?>
                  <select name="inp_<?= $row->Field ?>">
                    <option value="">Choose One</option>
                    <?php
                    foreach ($db->last_result as $orow) {
                      echo "<option value=\"$orow->field\">$orow->field</option>";
                    }
                    ?>
                  </select>
                  <?php
                } else {
                  ?>
                  <input type="text" name="inp_<?= $row->Field ?>"/>
                  <?php
                }
                ?>
              </td>
            </tr>
            <?php
          endif;
        }
        ?>
      </tbody>
      </tbody>
    </table>
    <p style="float: right;"><input type="submit" name="add" value="Add"/></p>
  </fieldset>
  <?php
endif;
?>

<?php require_once 'footer.php'; ?>