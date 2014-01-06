<?php
require_once 'functions.php';
require_once 'Classes/PAGES.php';
if(!$db){ $db = new MySQLDataBase(); }
if(!isset($_GET['event'])){ header("Location: index.php"); }
$event = $_GET['event'];
if(isset($_GET['remove'])){ $remid = $_GET['remove']; $db->query("DELETE FROM `pages` WHERE `id` = '$remid' ");  header('Location: pages.php?event='.$event);}

if(isset($_POST['button1'])){
  $id="";
  if($_POST['lblPageId']!=''){
   $id = $_POST['lblPageId'];
  }
  $objpage = new PAGES($id, $db);
  
  $objpage->setEvent($event);
  $objpage->setName($_POST['textbox1']);
  $objpage->setOrder($_POST['textbox2']);
  $objpage->setMin_records($_POST['textbox3']);
  $objpage->setMax_records($_POST['textbox4']);
  $objpage->setRemarks($_POST['textbox5']);
  
  $objpage->saveChanges();
}

$max_page_order = 0;
require_once 'header.php';
?>
<script type="text/javascript">
  $(function(){
    document.getElementById('textbox1').focus();
  });
  
  function editRecord(recid){
    document.getElementById('lblPageId').value = recid;
    document.getElementById('button1').value = 'Edit';
    
    for(i=0;i<5;i++){
      var dest = document.getElementById('textbox'+(i+1));
      var source = document.getElementById('label'+recid+'_'+i);
      dest.value = source.innerHTML;
      if(i==4){
        tinyMCE.get('textbox5').setContent(source.innerHTML);
      }
    }
  }
  
  function validateAdd() {
    var txtdata = tinyMCE.get('textbox5').getContent();
    document.getElementById('textbox5').value = txtdata;
    
    var status = true;
    var reqd_elmnts = ['textbox1','textbox2','textbox3','textbox4','textbox5'];
    var i = 0;
    for(i=0;i<reqd_elmnts.length;i++){
      var elmnt = document.getElementById(reqd_elmnts[i]);
      if(elmnt.value==""){
        status = false;
      }
    }
    
    if(!status){
      alert('File fill all the required field. \nAll the required fields are marked *');
    }
    
    return status;
  }
</script>
<table class="Datatable">
  <thead>
    <tr>
      <th>#</th>
      <th>Name</th>
      <th>Order</th>
      <th>Min Entry</th>
      <th>Max Entry</th>
      <th>Records</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $query = "SELECT `p`.`id`, p.name, `p`.`order`, `p`.`min_records`, `p`.`max_records`, `p`.`remarks`
              FROM `pages` as `p` 
              WHERE `p`.`event` = '$event' ORDER BY `p`.`order`";
    $db->query($query);
    $results = $db->last_result;
    $sno =1;
    foreach($results as $row){
      $row = (array)$row;
    ?>
    <tr>
      <td><?= $sno++ ?></td>
      <?php
      $col_indx = 0;
      foreach($row as $key => $value){
        if($key!="id"){
          echo "<td title=\"".str_replace("\"","&quot;",$value)."\">";
          echo "<label id=\"label".$row['id']."_".$col_indx++."\">$value</label>";
          echo str_replace("<","&lt;",showLimitedText($value));
          echo "</td>\n";
        }
        $max_page_order = $row['order'];
      }
      ?>
      <td>
        <a href="#" onclick="editRecord('<?= $row['id'] ?>'); return false;">Edit Page</a>
        <a href="fields.php?page=<?= $row['id'] ?>">Edit Columns</a>
	<a href="pages.php?event=<?= $event ?>&remove=<?= $row['id'] ?>">Remove</a>
      </td>
    </tr>
    <?php
    }
    ?>
  </tbody>
</table>
<div class="inputform">
  <input type="hidden" name="lblPageId" id="lblPageId" value=""/>
  <fieldset>
    <legend>Event Page</legend>
    <label for="textbox1">Page Name<span style="color: red;">*</span></label>
    <input type="text" id="textbox1" name="textbox1" value=""/>
    
    <label for="textbox2">Page Order<span style="color: red;">*</span></label>
    <select id="textbox2" name="textbox2">
      <?php
      for($i=1;$i<50;$i++):
        ?>
      <option value="<?= $i ?>"><?= $i ?></option>
      <?php
      endfor;
      ?>
    </select>
    <script type="text/javascript">
      $('#textbox2').val('<?= ($max_page_order+1) ?>');
    </script>
    
    <label for="textbox3">Minimum Records<span style="color: red;">*</span></label>
    <select id="textbox3" name="textbox3">
      <?php
      for($i=0;$i<50;$i++):
        ?>
      <option value="<?= $i ?>"><?= $i ?></option>
      <?php
      endfor;
      ?>
    </select>
    
    <label for="textbox4">Maximum Records<span style="color: red;">*</span></label>
    <select id="textbox4" name="textbox4" value="">
      <?php
      for($i=0;$i<50;$i++):
        ?>
      <option value="<?= $i ?>"><?= $i ?></option>
      <?php
      endfor;
      ?>
    </select>
    
    <label for="textbox5">Remarks<span style="color: red;">*</span></label>
    <div class="yui-skin-sam">
      <textarea id="textbox5" name="textbox5" style="width: 800px; height: 300px;"></textarea>
    </div>
    
    <br/>
    <input class="submitButton" type="submit" value="Add" id="button1" name="button1" style="text-align:center; " onclick="return validateAdd();"/>
    <a href="">Cancel</a>
  </fieldset>
</div>
<div class="navigation_plain">
  <a href="index.php" class="previous" style="margin-top: 20px;">Previous</a>
  
  <?php
    $query = "SELECT COUNT(p.id) as page_count FROM pages as p WHERE p.event = '$event'";
    $db->query($query);
    $page_count = $db->last_result[0]->page_count;
    
    $query = "SELECT p.`id`, count(f.id) as field_count
              FROM pages as p left outer join `fields` as f on (p.id = f.page)
              WHERE p.`event` = '$event'
              GROUP BY p.id
              HAVING field_count < 1;";
    $db->query($query);
    if(($db->num_rows == 0) && $page_count > 0 ):
  ?>
  <a href="PortalOptions.php?event=<?= $event ?>" class="createApplication">Create Application</a>
  <?php
    endif;
  ?>
    
</div>
<?php require_once 'footer.php'; ?>
