function getAjaxResponse(target,followurl){
  target.innerHTML="We are working ... Please wait...";
  if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  }
  else
  {// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
      
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
      target.innerHTML=xmlhttp.responseText;
    }
  }
  xmlhttp.open("GET",followurl,true);
  xmlhttp.send();
}
  
function validateNumeric(obj){
  var re = /^\d*$/;
  if(obj.value!=obj.value.match(re)){
    obj.value='';
    alert('Please enter only numeric values');
  }
}
  
function validateDate(obj){
  if(obj.value==''){
    return;
  }
  var re = /^\d\d\/\d\d\/\d\d\d\d$/;
  if(obj.value!=obj.value.match(re)){
    obj.value='';
    alert('Please enter date in proper format.(dd/mm/YYYY)');
  }
}

function validateDecimal(obj){
  var re = /^\d*\.?\d*$/;
  if(obj.value!=obj.value.match(re)){
    obj.value='';
    alert('Please enter only numeric values');
  }
}