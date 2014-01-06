<?php
/**
  Table Name : fieldoptions
  Author : Prashant Yadav & Sakshi Agarwal
 * */
//Comment Over
//Adding the require lines
require_once 'functions.php';
require_once 'Classes/FIELDOPTIONS.php';
require_once 'Classes/FIELDS.php';

if (!isset($_GET['field'])) {
  header('Location: index.php');
}
if (!$db) {
  $db = new MySQLDataBase("root", "Prashant#9", "localhost", "acadmis");
}
$field = $_GET['field'];
$objfield = new FIELDS($field, $db);
//Generating the postback script
if (isset($_POST['button1'])) {
  $id1 = "";
  if ($_POST['lblPrimary1'] != '') {
    $id1 = $_POST['lblPrimary1'];
  }
  $objtable = new FIELDOPTIONS($id1, $db);

  $objtable->setField($field);
  $objtable->setOption_name($_POST['textbox2']);
  $objtable->setOption_value($_POST['textbox3']);

  $objtable->saveChanges();
}
if (isset($_POST['button2'])) {
  $query = "DELETE FROM `fieldoptions` WHERE 1  AND `id` = '" . $_POST['lblPrimary1'] . "' ";
  $db->query($query);
}

//Importing the page header
require_once 'header.php';
?>
<script type="text/javascript">
  $(function(){
    document.getElementById('textbox2').focus();
  });
  
  function editRecord(  var1 ) {
    document.getElementById('lblPrimary1').value = var1 ;
	
    dest = document.getElementById('textbox2');
    source = document.getElementById('label_'+  var1 + '_' + '2');
    dest.value = source.innerHTML;
	
    dest = document.getElementById('textbox3');
    source = document.getElementById('label_'+  var1 + '_' + '3');
    dest.value = source.innerHTML;
	
    dest = document.getElementById('button1');
    dest.value = 'Update';
    return false;
  }

  function validateAdd() {
    var status = true;
    var reqd_elmnts = ['textbox3','textbox2'];
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

  $(function(){
    $('#textbox2').keyup(function(){
      _val = $(this).val();
      $('#textbox3').val(_val);
    });
  });

</script>
<div class="">
  <table class="Datatable">
    <thead>
      <tr>
        <th>#</th>
        <th>option_name</th>
        <th>option_value</th>
        <th>EDIT</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $query = "SELECT * FROM `fieldoptions` WHERE `field` = '$field'";
      $db->query($query);
      $results = $db->last_result;
      $sno = 1;
      foreach ($results as $row) {
        ?>
        <tr>
          <td><?= $sno++ ?></td>

          <td title="<?= $row->option_name ?>">
            <label id="label_<?= $row->id ?>_2"><?= $row->option_name ?></label>
  <?php echo showLimitedText($row->option_name) ?></label>
          </td>

          <td title="<?= $row->option_value ?>">
            <label id="label_<?= $row->id ?>_3"><?= $row->option_value ?></label>
  <?php echo showLimitedText($row->option_value) ?></label>
          </td>
          <td><a href="#" onclick="return editRecord('<?= $row->id ?>' );">Edit</a></td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
</div>
<div class="inputform">
  <input type="hidden" name="lblPrimary1" id="lblPrimary1" />
  <fieldset>
    <legend>Add/Edit</legend>

    <label>option_name<span style="color: red;">*</span></label>
    <input type="text" name="textbox2" id="textbox2" value="" />


    <label>option_value<span style="color: red;">*</span></label>
    <input type="text" name="textbox3" id="textbox3" value="" />

  </fieldset>
  <br/>
  <input type="submit" name="button1" id="button1" value="Add" style="text-align: center;" onclick="return validateAdd();">
  <a href="">Cancel</a>
  <input type="submit" name="button2" id="button2" value="Remove" onclick="if(!confirm('Are you sure you want to remove this field')){return false;}">
</div>
<div class="navigation_plain">
  <a href="fields.php?page=<?= $objfield->getPage() ?>" class="previous">Back</a>
</div>
<?php
require_once 'footer.php';
?>
