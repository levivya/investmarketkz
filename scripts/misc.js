// misc js scripts
$(function() {
		$( "#tabs" ).tabs();
		$( "#tabs2" ).tabs();
});
//clear input text  
function clear_field(obj,txt)
{
 if (obj.value==txt) {obj.value="";}
}
// check if mumber
function IsNumeric(obj)
{
   var ValidChars = "0123456789.";
   var IsNumber=true;
   var Char;
   var sText=obj.value;

   for (i = 0; i < sText.length && IsNumber == true; i++)
      {
       Char = sText.charAt(i);
       if (ValidChars.indexOf(Char) == -1)
          {
          alert("Значение должно быть числовым!");
          IsNumber = false;
          obj.focus();
          }
     }
   return IsNumber;
}
