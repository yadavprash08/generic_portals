<?php
	/**
		Table Name : fields
		Author : Prashant Yadav & Sakshi Agarwal
	**/

//Comment Over

//Adding the require lines
require_once 'functions.php';
require_once 'Classes/FIELDS.php';
require_once 'Classes/PAGES.php';

if (!isset($_GET["page"])) { header("Location: index.php"); }
if(!$db)	{	$db = new MySQLDataBase("root", "Prashant#9", "localhost", "acadmis");}
$page = $_GET['page'];
$objpage = new PAGES($page, $db);

//Generating the postback script
if(isset($_POST['button1'])){
	$id1 = "";
	if($_POST['lblPrimary1']!=''){
		$id1 = $_POST['lblPrimary1'];
	}
	$objtable = new FIELDS( $id1, $db);
	
	$objtable->setPage($page);
	$objtable->setName($_POST['textbox2']);
	$objtable->setDisplay_name($_POST['textbox3']);
	$objtable->setMax_length($_POST['textbox4']);
	$objtable->setType($_POST['textbox5']);
	$objtable->setOrder($_POST['textbox6']);
	$objtable->setRemarks($_POST['textbox7']);
	$objtable->setMandatory($_POST['textbox8']);
	
	$objtable->saveChanges();
	
}

if(isset($_POST['button2'])){
  $id1 = "";
	if($_POST['lblPrimary1']!=''){
		$id1 = $_POST['lblPrimary1'];
	}
  
  $query = "DELETE FROM `fields` WHERE id='$id1'";
  $db->query($query);
}

//Importing the page header
require_once 'header.php';
?>
<script type="text/javascript">
  $(function(){
    document.getElementById('textbox3').focus();
  });
    
  function editRecord(  var1 ) {
    document.getElementById('lblPrimary1').value = var1 ;

    dest = document.getElementById('textbox2');
    source = document.getElementById('label_'+  var1 + '_' + '2');
    dest.value = source.innerHTML;

    dest = document.getElementById('textbox3');
    source = document.getElementById('label_'+  var1 + '_' + '3');
    dest.value = source.innerHTML;

    dest = document.getElementById('textbox4');
    source = document.getElementById('label_'+  var1 + '_' + '4');
    dest.value = source.innerHTML;

    dest = document.getElementById('textbox5');
    source = document.getElementById('label_'+  var1 + '_' + '5');
    dest.value = source.innerHTML;

    dest = document.getElementById('textbox6');
    source = document.getElementById('label_'+  var1 + '_' + '6');
    dest.value = source.innerHTML;

    dest = document.getElementById('textbox7');
    source = document.getElementById('label_'+  var1 + '_' + '7');
    dest.value = source.innerHTML;
    //tinyMCE.get('textbox7').setContent(source.innerHTML);

    dest = document.getElementById('textbox8');
    source = document.getElementById('label_'+  var1 + '_' + '8');
    dest.value = source.innerHTML;

    dest = document.getElementById('button1');
    dest.value = 'Update';
    return false;
  }

  function chooseOptions( var1 )  {
    window.location = 'fieldoptions.php?field='+var1;
  }

  function setFieldsName(){
    var txt3 = document.getElementById('textbox3');
    var txt2 = document.getElementById('textbox2');
    var fdname = txt3.value.toLowerCase();
    var i = 0;
    var fname="";
    var re = /^[a-z0-9]$/;
    var match;
    for(i=0;i<fdname.length;i++){
      match = fdname[i].match(re);
      if(match!=null){
        fname = fname + match;
      }else{
        fname = fname + "_";
      }
    }
    txt2.value = fname;
  }

  function validateAdd() {
    var txtdata = tinyMCE.get('textbox7').getContent();
    document.getElementById('textbox7').value = txtdata;
    
      var status = true;
      var reqd_elmnts = ['textbox3','textbox4'];
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

  function validate_max(obj){
    _val = obj.value;
    if(($('#textbox5').val()=="Integer") && _val > 15 ){
      alert('Too large value for an integer');
      obj.value = 15;
    }
  }

  $(function(){
    $('#textbox5').change(function(){
      if($(this).val()=="Date"){
        $('#textbox4').val('10');
        $('#divtextbox').slideUp();
        $('#textbox6').focus();
      }else{
        $('#textbox4').val('');
        $('#divtextbox').slideDown();
        $('#textbox4').focus();
      }
    });
  });
</script>

<div class="">
	<table class="Datatable">
		<thead>
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Display Text</th>
				<th>Max Length</th>
				<th>Type</th>
				<th>Order</th>
                                <th>Mandatory</th>
				<th>Remarks</th>
				<th>EDIT</th>
				<th>Options</th>
			</tr>
		</thead>
		<tbody>
		<?php
    $max_order = 0;
		$query = "SELECT * FROM `fields` WHERE `page` = '$page' ORDER BY `order`"; 
		$db->query($query); 
		$results = $db->last_result; 
		$sno = 1; 
		foreach($results as $row){
			?>
			<tr>
				<td><?= $sno++ ?></td>
				
				<td title="<?= $row->name ?>">
					<label id="label_<?= $row->id ?>_2"><?= $row->name ?></label>
					<?php echo showLimitedText($row->name) ?></label>
				</td>
				
				<td title="<?= $row->display_name ?>">
					<label id="label_<?= $row->id ?>_3"><?= $row->display_name ?></label>
					<?php echo showLimitedText($row->display_name) ?></label>
				</td>
				
				<td title="<?= $row->max_length ?>">
					<label id="label_<?= $row->id ?>_4"><?= $row->max_length ?></label>
					<?php echo showLimitedText($row->max_length) ?></label>
				</td>
				
				<td title="<?= $row->type ?>">
					<label id="label_<?= $row->id ?>_5"><?= $row->type ?></label>
					<?php echo showLimitedText($row->type) ?></label>
				</td>
				
				<td title="<?= $row->order ?>">
					<label id="label_<?= $row->id ?>_6"><?= $row->order ?></label>
					<?php echo showLimitedText($row->order) ?></label>
          <?php if($row->order > $max_order) { $max_order = $row->order; } ?>
				</td>
				
        <td title="<?= $row->mandatory ?>">
					<label id="label_<?= $row->id ?>_8"><?= $row->mandatory ?></label>
					<?php echo showLimitedText($row->mandatory) ?></label>
				</td>
        
				<td title="<?= $row->remarks ?>">
					<label id="label_<?= $row->id ?>_7"><?= str_replace("\"","&quot;",$row->remarks) ?></label>
					<?php echo str_replace("<","&lt;",showLimitedText($row->remarks)) ?></label>
				</td>
				
				<td><a href="#" onclick="return editRecord('<?= $row->id ?>' );">Edit</a></td>
				<td><a href="#" onclick="return chooseOptions('<?= $row->id ?>' );">Choose Options</a></td>
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
		
		<label>Name</label>
    <input type="text" name="textbox2" id="textbox2" value="" readonly="" />
		
		
		<label>Display Name<span style="color: red;">*</span></label>
    <input type="text" name="textbox3" id="textbox3" value="" onkeyup="setFieldsName();" />
		
		
		<label>Type<span style="color: red;">*</span></label>
    <select name="textbox5" id="textbox5">
      <option value="String">String</option>
      <option value="Integer">Integer</option>
      <option value="Date">Date</option>
      <option value="Decimal">Decimal</option>
    </select>
    
    <div id="divtextbox">
      <label>Max Length<span style="color: red;">*</span></label>
      <input type="text" name="textbox4" id="textbox4" value="" onkeyup="validateNumeric(this);" onblur="validate_max(this);" />
		</div>
		
		<label>Order<span style="color: red;">*</span></label>
		<select name="textbox6" id="textbox6">
      <?php
      for($i=1;$i<50;$i++):
        ?>
      <option value="<?= $i ?>"><?= $i ?></option>
      <?php
      endfor;
      ?>
    </select>
    <script type="text/javascript">
      $('#textbox6').val('<?= $max_order+1 ?>');
    </script>
		
		<label>Mandatory</label>
    <select name="textbox8" id="textbox8">
      <option value="No">No</option>
      <option value="Yes">Yes</option>
    </select>
    
		<label>Remarks</label>
    <input type="text" name="textbox7" id="textbox7" />

  </fieldset>
	<br/>
  <input type="submit" name="button1" id="button1" value="Add" style="text-align: center;" onclick="return validateAdd();">
	<a href="">Cancel</a>
  <input type="submit" name="button2" id="button2" value="Remove" onclick="if(!confirm('Are you sure you want to delete the record')){return false;}">
</div>
<div class="navigation_plain">
  <a href="pages.php?event=<?= $objpage->getEvent() ?>" class="previous">Back</a>
  <div class="clear"></div>
</div>
<?php
require_once 'footer.php';
?>
