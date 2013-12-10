<?php
	function fp_header()
	{
		echo('<script src="flowplayer/flowplayer-3.1.2.min.js"></script>');
	}

	/*
function fp_link($videoFile,$v_width,$v_height,$videoTitle)
	{
		$img=substr($videoFile,0,strlen($videoFile)-4).'.bmp';

		echo('<a href="'.$videoFile.'"
    		 	 style="display:block;width:'.$v_width.';height:'.$v_height.';"
    		 	 id="'.$FlowPlayerId.'">

            <img src="'.$img.'" alt="'.$videoTitle.'" width='.$v_width.' height='.$v_height.'/>
            </a>');
	}
*/

	function fp_render($id, $videoFile, $width, $height, $splashScreen)
	{
		echo(
			 "<div id = \"player\" style=\"width:$width;height:$height\"></div>
			 <script language=\"JavaScript\">
				".'$'."f(\"player\",
						   \"flowplayer/flowplayer-3.1.2.swf\",
						   {
						   		playlist: [
						   			{
						   				url: '$splashScreen',
						   				scaling: 'fit'
						   			},
							   		{
							   			// Update view count
							   			onStart: function(clip) {
											$.post(\"./flowplayer/video_view_count.php\", {id: ".$id."});
            							},
            							url: '$videoFile',
							   			autoPlay: false,
							   			autoBuffering: true,
							   			scaling: 'fit'
						   			}
								]
						   });
			</script>"
			);
	}
?>