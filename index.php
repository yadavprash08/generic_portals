<?php
require_once 'functions.php';
require_once 'Classes/EVENTS.php';

if (!$db) {
  $db = new MySQLDataBase();
}

if (isset($_POST['lbleventid'])) {
  $id = $_POST['lbleventid'];
  $objevent = new EVENTS($id, $db);

  $objevent->setName($_POST['textbox1']);
  $objevent->setStart($_POST['textbox2']);
  $objevent->setEnd($_POST['textbox3']);
  $objevent->setRemarks($_POST['textbox4']);

  $objevent->saveChanges();
}

require_once 'header.php';
?>
<script type="text/javascript">
  $(function(){
    var dates = $('#textbox2, #textbox3').datepicker({
      changeMonth: true,
      onSelect: function(selectedDate){
        var option = this.id == "textbox2" ? "minDate" : "maxDate",
          instance = $(this).data("datepicker"),
          date = $.datepicker.parseDate(
                  instance.settings.dateFormat ||
                  $.datepicker._defaults.dateFormat,
                  selectedDate, instance.settings );
          dates.not( this ).datepicker( "option", option, date );
      }
    });
  });
  
  function editEvent(event,name,start,end){
    var textbox1, textbox2, textbox3, textbox4, p, labelid;
    textbox1 = document.getElementById('textbox1');
    textbox2 = document.getElementById('textbox2');
    textbox3 = document.getElementById('textbox3');
    textbox4 = document.getElementById('textbox4');
    labelid = document.getElementById('lbleventid');
    p = document.getElementById('remarks'+event);
    button = document.getElementById('button1');
    button2 = document.getElementById('button2');
    button2.style.display = "inline";
    //button3 = document.getElementById('button3');
    //button3.style.display = "inline";
    labelid.value = event;
    textbox1.value = name;
    textbox2.value = start;
    textbox3.value = end;
    textbox4.value = p.innerHTML;

    tinyMCE.get('textbox4').setContent(p.innerHTML);
  
    button.value = "Edit";
  }
  
  function validateAdd() {
    
    var txtdata = tinyMCE.get('textbox4').getContent();
    document.getElementById('textbox4').value = txtdata;
    
    var status = true;
    var reqd_elmnts = ['textbox1','textbox2','textbox3','textbox4'];
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
<h2>Manage Application Events</h2>
<p>This page is to manage the events in the portal</p>
<?php
$query = "SELECT `id`, `name`, DATE_FORMAT(`start`,'%d %M, %Y') as `start_date`, 
          DATE_FORMAT(`end`,'%d %M, %Y') as `end_date`, `remarks`, 
          DATE_FORMAT(`start`,'%d/%m/%Y') as `edit_start`, 
          DATE_FORMAT(`end`,'%d/%m/%Y') as `edit_end`
          FROM `events`
          WHERE inactive = 0;";
$db->query($query);
$events = $db->last_result;
foreach ($events as $event) {
  ?>
  <div class="item" onclick="editEvent('<?= $event->id ?>', '<?= $event->name ?>', '<?= $event->edit_start ?>', '<?= $event->edit_end ?>');">
    <h3><?= $event->name ?></h3>
    <p><b><?= $event->start_date ?></b> to <b><?= $event->end_date ?></b></p>
    <div  style="display: none;"><div id="remarks<?= $event->id ?>"><?= str_replace("","",$event->remarks) ?></div></div>
  </div>
  <?php
}
?>
<div style="clear: left;"></div>
<div class="inputform">
  <input type="hidden" id="lbleventid" name="lbleventid"/>
  <fieldset>
    <legend>Event</legend>
    <label for="textbox1">Event name<span style="color: red;">*</span></label>
    <input type="text" value="" id="textbox1" name="textbox1"/>

    <label for="textbox2">Start Date<span style="color: red;">*</span></label>
    <input type="text" name="textbox2" id="textbox2" value="" onblur="validateDate(this);"/>

    <label for="textbox3">End Date<span style="color: red;">*</span></label>
    <input type="text" name="textbox3" id="textbox3" value="" onblur="validateDate(this);"/>

    <label for="textbox4">Remarks<span style="color: red;">*</span></label>
    <textarea name="textbox4" id="textbox4" style="width: 800px; height: 300px;"></textarea>
    <br/>
  </fieldset>

  <input class="submitButton" type="submit" value="Add" id="button1" style="text-align: center;" onclick="return validateAdd();" />
  <a href="">Cancel</a>
  <span class="navigation_plain">
    <a href="#" id="button2" style="display: none;" onclick="window.location = 'pages.php?event='+document.getElementById('lbleventid').value;return false;" class="forward">Edit Pages</a>
  </span>
</div>
<?php require_once 'footer.php'; ?>