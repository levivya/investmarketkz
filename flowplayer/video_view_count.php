<?php
	require_once('../lib/misc.inc');

	if(!empty($id))
	{
		$query = "UPDATE
				  	ism_video
				  SET
				  	viewed = viewed + 1
				  WHERE
				  	id = $id";
		// Connecting, selecting database
		$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
		$result = exec_query($query);
		disconn($conn);
	}
?>