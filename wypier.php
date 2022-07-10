<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<style type="text/css">
		body, td {
			font-size: 8pt;
			font-family: sans-serif;
		}
		a:link, a:hover, a:active, a:visited {
			font-size: 8pt;
			color: #0000FF;
		}
		</style>
	</head>
	<body>
		<?php

		$dir = ".";
		$directories = array();
		$files = array();

		function recursedir($rootdir){
			$directories = array();
			$files = array();
			$dir = (substr($rootdir, -1) == '/') ? substr($rootdir, 0, -1) : $rootdir;
			if(is_dir($dir)){
				if($handle = opendir($dir)){
					while(false !== ($file = readdir($handle))){
						if($file != "." && $file != ".."){
							$filename = $dir.'/'.$file;
							if(is_dir($filename)){
								$folder = $filename;
								$files = array_merge($files, recursedir($filename));
								//echo $folder."<br />";
							} else {
								$files[$filename] = filemtime($filename);
							}
						}
					}
					closedir($handle);
				} else {
					die('Could not open directory.');
				}
			} else {
				die('Invalid directory.');
			}
			return $files;
		}

		$files = recursedir(".");
		if($_GET['sort'] == 'alpha'){
			if($_GET['mode'] == 'desc'){
				krsort($files);
				$highlight = 'alpha_desc';
			} else {
				ksort($files);
				$highlight = 'alpha_asc';
			}
		} else {
			if($_GET['mode'] == 'asc'){
				asort($files, SORT_NUMERIC);
				$highlight = 'date_asc';
			} else {
				arsort($files, SORT_NUMERIC);
				$highlight = 'date_desc';
			}
		}
		$sort_alpha_asc = ($highlight == 'alpha_asc') ? '<b>Asc</b>' : '<a href="?sort=alpha&mode=asc">Asc</a>';
		$sort_alpha_desc = ($highlight == 'alpha_desc') ? '<b>Desc</b>' : '<a href="?sort=alpha&mode=desc">Desc</a>';
		$sort_date_asc = ($highlight == 'date_asc') ? '<b>Asc</b>' : '<a href="?sort=date&mode=asc">Asc</a>';
		$sort_date_desc = ($highlight == 'date_desc') ? '<b>Desc</b>' : '<a href="?sort=date&mode=desc">Desc</a>';
		echo "Sort by: Date- $sort_date_asc | $sort_date_desc; Name- $sort_alpha_asc | $sort_alpha_desc<br />\n<br />\n";

		echo "<table border=\"0\">\n<tr><td><u>File</u></td><td width=\"25\"></td><td><u>Size</u></td><td width=\"25\"></td><td><u>Last Modified</u></td></tr>\n";
		foreach($files as $file => $timestamp){
			echo "<tr><td><a href=\"$dir/$file\">$file</a></td><td></td><td>";
			$filesize = filesize($file);
			if($filesize >= 1048576){
				echo round($filesize / 1048576, 1).'MB';
			} else {
				echo round($filesize / 1024, 1).'kb';
			}
			echo '</td><td></td><td>'.date('d M Y H:i:s', $timestamp)."</td></tr>\n";
		}
		echo '</table>';

		?>
	</body>
</html>
