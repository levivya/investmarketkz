<?php

require_once("dbConnect.php");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
<HEAD>
	<title>Pool</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/ajax-poller.css" type="text/css">
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/ajax-poller.js">	</script>

</HEAD>
<BODY>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return false" method="post">

		<?php

        if (!isset($pollerId))
        {
		$id_res = mysql_query("select max(id) mid from poller");

			if($ids = mysql_fetch_array($id_res)){
				$pollerId = $ids["mid"];
				                              }
        }
		?>
		<!-- START OF POLLER -->

			<div  class="voting" id="poller_question<?php echo $pollerId; ?>">

			<?php


			// Retreving poll from database
			$res = mysql_query("select * from poller where ID='$pollerId'");
			if($inf = mysql_fetch_array($res)){
				echo "<div class=\"question\">".$inf["pollerTitle"]."</div>";	// Output poller title

				$resOptions = mysql_query("select * from poller_option where pollerID='$pollerId' order by pollerOrder") or die(mysql_error());	// Find poll options, i.e. radio buttons
				echo "<ul>";
				while($infOptions = mysql_fetch_array($resOptions)){
					if($infOptions["defaultChecked"])$checked=" checked"; else $checked = "";
					echo "<li><input$checked type=\"radio\" value=\"".$infOptions["ID"]."\" name=\"vote[".$inf["ID"]."]\" id=\"pollerOption".$infOptions["ID"]."\">&nbsp;&nbsp;<label for=\"pollerOption".$infOptions["ID"]."\" id=\"optionLabel".$infOptions["ID"]."\">".$infOptions["optionText"]."</label></li>";

				}
			}
			?>
			<!--<a href="#" onclick="castMyVote(<?php echo $pollerId; ?>,document.forms[0])"><img src="images/vote_button.gif"></a>-->
			<li class="button"><input value="Голосовать" type="button" onclick="castMyVote(<?php echo $pollerId; ?>,document.forms[0])"/></li>
			<!--<a href="#" onclick="displayResultsWithoutVoting(<?php echo $pollerId; ?>)"><img src="images/vote_button.gif"></a>-->
			<ul>
			</div>

			<div class="poller_waitMessage" id="poller_waitMessage<?php echo $pollerId; ?>">
				 Получение результатов голосования. Ждите...
			</div>
			<div class="voting" id="poller_results<?php echo $pollerId; ?>">
			<!-- This div will be filled from Ajax, so leave it empty --></div>

		<!-- END OF POLLER -->
		<script type="text/javascript">
		if(useCookiesToRememberCastedVotes){
			var cookieValue = Poller_Get_Cookie('dhtmlgoodies_poller_<?php echo $pollerId; ?>');
			if(cookieValue && cookieValue.length>0)displayResultsWithoutVoting(<?php echo $pollerId; ?>); // This is the code you can use to prevent someone from casting a vote. You should check on cookie or ip address

		}
		</script>
</form>

</BODY>
</HTML>