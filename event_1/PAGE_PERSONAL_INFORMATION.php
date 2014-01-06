<?php
	/**
		Table Name : tab_1_page_1
		Author : Prashant Yadav & Sakshi Agarwal
	**/

//Comment Over

//Adding the require lines
require_once 'functions.php';
require_once 'Classes/TAB_1_PAGE_1.php';

if(!$db)	{
	$db = new MySQLDataBase();
}
if(!isset($_SESSION)){session_start();}

if($_SESSION['applicant']==""){header('Location: signinerr.php');}
$applicant = $_SESSION['applicant'];


//Generating the postback script
if(isset($_POST['button1'])){
	$id = "";
	$id = $_POST['lblPrimary1'];
	$objtable = new TAB_1_PAGE_1($id, $db);
	
	$objtable->setApplicant($applicant);
	$objtable->setFirst_name($_POST['textbox1']);
	$objtable->setMiddle_name($_POST['textbox2']);
	$objtable->setLast_name($_POST['textbox3']);
	$objtable->setDate_of_birth($_POST['textbox4']);
	
	$objtable->saveChanges();
	
}
if(isset($_POST['button2'])){
	$query = "DELETE FROM `tab_1_page_1` WHERE `id` = '".$_POST['lblPrimary1']."' ";
	$db->query($query);
}

//Importing the page header
require_once 'header.php';
?>
<div>
<p>Please provide with your personal information. This information will be used for the selection procedure.</p></div>
<script type="text/javascript">
function editRecord( var1 )	{
	document.getElementById('lblPrimary1').value = var1 ;
	
	dest = document.getElementById('textbox1');
	source = document.getElementById('label_'+ var1 + '_1');
	dest.value = source.innerHTML;
	
	dest = document.getElementById('textbox2');
	source = document.getElementById('label_'+ var1 + '_2');
	dest.value = source.innerHTML;
	
	dest = document.getElementById('textbox3');
	source = document.getElementById('label_'+ var1 + '_3');
	dest.value = source.innerHTML;
	
	dest = document.getElementById('textbox4');
	source = document.getElementById('label_'+ var1 + '_4');
	dest.value = source.innerHTML;
	
	dest = document.getElementById('button1');
	dest.value = 'Update';
	return false;
}

function validateForm(){
	var reqdelements = ['textbox1' , 'textbox3' , 'textbox4' ];
	var FormStatus = true;
	var errmsg = '';
	for(i=0;i<reqdelements.length;i++) {
		var inp = document.getElementById(reqdelements[i]);
		if(inp && inp.value!=''){
		}else{
			errmsg = 'Required field is left blank';
			FormStatus = false;
		}
		
	}
	if(errmsg!='')
		alert(errmsg);
	return FormStatus;
}
</script>
<?php
	$query = "SELECT `id` FROM `tab_1_page_1` WHERE `applicant` = '$applicant' ";
	$db->query($query);
	$id = "";
	if($db->num_rows>0){
		$id=$db->last_result[0]->id;
	}
	$objtable = new TAB_1_PAGE_1($id , $db);
	
?>
<div class="inputform">
	<input type="hidden" name="lblPrimary1" id="lblPrimary1" value="<?= $objtable->getId() ?>" />
	<fieldset>
		<legend>Add/Edit</legend>
		<label for="textbox1">First Name <span style="color:#f00;">*</span>  </label>
		<input type="text" name="textbox1" id="textbox1" value="<?= $objtable->getFirst_name() ?>"  maxlength="20"  />
		<script type="text/javascript">
		document.getElementById('textbox1').value = '<?= $objtable->getFirst_name() ?>';
		</script>
		<label for="textbox2">middle Name  <span class="remarks">( If application provide with your middle name. )</span> </label>
		<input type="text" name="textbox2" id="textbox2" value="<?= $objtable->getMiddle_name() ?>"  maxlength="20"  />
		<script type="text/javascript">
		document.getElementById('textbox2').value = '<?= $objtable->getMiddle_name() ?>';
		</script>
		<label for="textbox3">Last Name <span style="color:#f00;">*</span>  </label>
		<input type="text" name="textbox3" id="textbox3" value="<?= $objtable->getLast_name() ?>"  maxlength="20"  />
		<script type="text/javascript">
		document.getElementById('textbox3').value = '<?= $objtable->getLast_name() ?>';
		</script>
		<label for="textbox4">Date of Birth <span style="color:#f00;">*</span> <span class="remarks">( Your Date of birth as displayed on documents )</span> </label>
		<input type="text" name="textbox4" id="textbox4" value="<?= $objtable->getDate_of_birth() ?>"  maxlength="10"  onblur="validateDate(this);"  />
		<script type="text/javascript">
		document.getElementById('textbox4').value = '<?= $objtable->getDate_of_birth() ?>';
		$(function(){$('#textbox4').datepicker({changeMonth:true, changeYear:true, yearRange: "c-20:c+20"});});
		</script>
	</fieldset>
	<br/>
	<input type="submit" name="button1" id="button1" value="Save" onclick="return validateForm();" >
	<input type="submit" name="button2" id="button2" value="Reset" onclick="if(!confirm('Are you sure you want to reset all fields.')){return false;}">
</div>
<div class="navigation_plain">
	<a href="submitapplication.php" class="forward" onclick="if(!confirm('You are about to submit the application.\nOnce submitted, no changes to the application would be applowed.\nIt is sujested to check your application before submission. Continue with submission of Application.')){return false;}"> Next </a>
	<a href="index.php" class="previous" > Previous </a>
	<div style="clear:both;"></div>
</div>
<?php
require_once 'footer.php';
?>
