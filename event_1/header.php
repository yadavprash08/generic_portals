<?php
if (!isset($_SESSION)) {
  session_start();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
  <head>
    <title>Sample Event 1</title>
    <link href="site.css" media="all" type="text/css" rel="stylesheet"/>
    <script type="text/javascript" src="script.js"></script>
    
    <link type="text/css" href="css/redmond/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript">
      /* English/UK initialisation for the jQuery UI date picker plugin. */
      /* Written by Stuart. */
      jQuery(function($){
        $.datepicker.regional['en-GB'] = {
          closeText: 'Done',
          prevText: 'Prev',
          nextText: 'Next',
          currentText: 'Today',
          monthNames: ['January','February','March','April','May','June',
            'July','August','September','October','November','December'],
          monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
          dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
          dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],
          weekHeader: 'Wk',
          dateFormat: 'dd/mm/yy',
          firstDay: 1,
          isRTL: false,
          showMonthAfterYear: false,
          yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['en-GB']);
      });

    </script>
    
  </head>
  <body>
    <form method="post" action="" id="frmForm1" enctype="multipart/form-data">
      <div class="page">
        <div class="header">
          <div class="title">
            <h1>Sample Event 1</h1>
          </div>
          <div class="clear hideSkiplink">
            <div class="menu" id="navigationMenu1">
              <ul class="level1">
                <li style="float: left;"><a href="index.php" tabindex="-1">HOME</a></li>
                <?php
                if (isset($_SESSION['applicant_login'])) {
                  ?>
                  <li style="float: left;"><a href="password.php" tabindex="-1">CHANGE PASSWORD</a></li>
                  <li style="float: left;"><a href="logout.php" tabindex="-1">LOGOUT</a></li>
                  <?php
                }else{
                  ?>
                  <li style="float: left;"><a href="applicant_login.php" tabindex="-1">LOGIN</a></li>
                  <li style="float: left;"><a href="password.php" tabindex="-1">RECOVER PASSWORD</a></li>
                  <?php
                }
                ?>
                <li style="float: left;"><a href="about.php" tabindex="-1">ABOUT</a></li>
                <li style="float: left;"><a href="contact.php" tabindex="-1">CONTACT</a></li>
              </ul>
            </div>
            <div style="clear: left;"></div>
          </div>
        </div>
        <div class="main">
          
