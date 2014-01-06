<?php
require_once 'functions.php';
require_once 'Classes/EVENTS.php';

if(!isset($_GET['event'])){ header("Location: index.php"); }
if(!isset($db)){ $db=new MySQLDataBase(); }

$event = $_GET['event'];

if(isset($_POST['button1'])){
  
  $code = "";
  $code.="<?php
            define(\"APP_START_TIME\",\"".$_POST['textbox1']."\");
            define(\"APP_END_TIME\",\"".$_POST['textbox2']."\");

            define(\"DB_NAME\", \"".$_POST['textbox3']."\");
            define(\"DB_USER\", \"".$_POST['textbox4']."\");
            define(\"DB_PASSWORD\", \"".$_POST['textbox5']."\");
            define(\"DB_HOST\", \"".$_POST['textbox6']."\");
            
            define(\"ADMIN_PASSWD\",\"".$_POST['textbox7']."\");

            define(\"APP_REF_PREFIX\",\"".$_POST['textbox8']."\");
            define(\"APP_REF_SUBFIX\",\"".$_POST['textbox9']."\");

            define(\"PDF_HEADER\",\"".$_POST['textbox10']."\");
            define(\"PDF_SUB_HEADER\",\"".$_POST['textbox11']."\");

            define(\"SMTP_HOST\",\"".$_POST['textbox12']."\");
            define(\"SMTP_SECURE\",\"".$_POST['textbox13']."\");
            define(\"SMTP_AUTH\",".$_POST['textbox14'].");
            define(\"SMTP_PORT\",".$_POST['textbox15'].");
            define(\"SMTP_USERNAME\",\"".$_POST['textbox16']."\");
            define(\"SMTP_PASSSWORD\",\"".$_POST['textbox17']."\");
            define(\"SMTP_SENDFROM\",\"".$_POST['textbox18']."\");
    ?>";
  $basedir = "event_$event";
  if(!is_dir($basedir)){
    mkdir("$basedir",0777);
  }
  
  if(!is_dir("$basedir/Classes")){
    mkdir("$basedir/Classes",0777);
  }
  
  $fp = fopen("event_$event/Classes/DEFINE_PARAM.php","w");
  fwrite($fp,$code);
  fclose($fp);
  
  header("Location: createGenericPortal.php?event=$event");
}


$query = "SELECT id, date_format(`start`,'%Y-%m-%d') as 'start', date_format(`end`, '%Y-%m-%d') as 'end'  FROM `events` WHERE `id` = '$event'";
$db->query($query);
$event_detail = $db->last_result[0];
require_once 'header.php';
?>
<div class="inputform">
  <fieldset>
    <legend>Portal Options</legend>
    
    <label>Start Date</label>
    <input type="text" readonly="" id="textbox1" name="textbox1" value="<?= $event_detail->start ?>"/>
    
    <label>End Date</label>
    <input type="text" readonly="" id="textbox2" name="textbox2" value="<?= $event_detail->end ?>"/>
    
    <label>Database Name</label>
    <input type="text" id="textbox3" name="textbox3" value="<?= DB_NAME ?>"/>
    
    <label>Database User</label>
    <input type="text" id="textbox4" name="textbox4" value="<?= DB_USER ?>"/>
    
    <label>Database Password</label>
    <input type="password" id="textbox5" name="textbox5" value="<?= DB_PASSWORD ?>"/>
    
    <label>Database Host</label>
    <input type="text" id="textbox6" name="textbox6" value="<?= DB_HOST ?>"/>
    
    <label>Administrator Password</label>
    <input type="text" id="textbox7" name="textbox7" value="Pass@Word"/>
    
    <label>Application Reference Prefix</label>
    <input type="text" id="textbox8" name="textbox8" value=""/>
    
    <label>Application Reference Subfix</label>
    <input type="text" id="textbox9" name="textbox9" value=""/>
    
    <label>PDF Header</label>
    <input type="text" id="textbox10" name="textbox10" value="Malaviya National Institute of Technology"/>
    
    <label>PDF Sub Header</label>
    <input type="text" id="textbox11" name="textbox11" value="JLN Road - Jaipur"/>
    
    <label>SMTP HOST</label>
    <input type="text" id="textbox12" name="textbox12" value="smtp.gmail.com"/>
    
    <label>SMTP SECURE</label>
    <select id="textbox13" name="textbox13" >
      <option value="tls">tls</option>
      <option value="ssl">ssl</option>
    </select>
    
    <label>SMTP Authorization</label>
    <select id="textbox14" name="textbox14" >
      <option value="true">True</option>
      <option value="false">False</option>
    </select>
    
    <label>SMTP PORT</label>
    <input type="text" id="textbox15" name="textbox15" value="587"/>
    
    <label>SMTP Username</label>
    <input type="text" id="textbox16" name="textbox16" value="portal.prashant@gmail.com"/>
    
    <label>SMTP Password</label>
    <input type="text" id="textbox17" name="textbox17" value="Pass@Word"/>
    
    <label>SMTP Send From Name</label>
    <input type="text" id="textbox18" name="textbox18" value="Generic Portal"/>
    
    <br/>
    <br/>
    <input type="submit" name="button1" id="button1" value="Create"/>
  </fieldset>
  <div style="clear: left;"></div>
</div>

<?php
require_once 'footer.php';
?>