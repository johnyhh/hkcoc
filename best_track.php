<?php
$start = 0;
$content = "";
$lines = array();
$arr   = array();
$datetime = "";
$lat = 0;
$long = 0;
$pw = 0;
$yrmn = "";

$url="http://www.typhoon2000.ph/multi/log.php?name=FUNG-WONG_2019";
$page = file_get_contents($url);
$lines = explode("\n",$page);

for ($i=0;$i<=count($lines);$i++){
	if (preg_match("/function preload/", $lines[$i])){
		$start = 1;
	} elseif (preg_match("/body onLoad/", $lines[$i])){
		$start = 0;
	}
	
	if ($start == 1)
	{
		if (preg_match("/text\[/", $lines[$i])){
			// each line[$i] here is data
			// time: (2019-11-07 09:31:53 UTC)
		    if (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2}) /', $lines[$i], $arr)){
				$yrmn = "$arr[1]$arr[2]$arr[3]";
			}			
			// HKO:\n041800Z 18.7N 152.3E 105KT\n => *1.1
		    if (preg_match('/HKO\:....(..)..Z ([0-9]+.[0-9])N ([0-9]+.[0-9])E ([0-9]+)KT/', $lines[$i], $arr)){
				$datetime = $yrmn.$arr[1]; $lat = $arr[2]; $long = $arr[3]; $pw = floor($arr[4] * 1.1);
			}
			// JTWC:\n041800Z 18.7N 152.3E 105KT\n
			// JMA:\n041800Z 18.8N 152.3E 80KT\n => *1.1
			// NMC:\n041800Z 18.9N 152.3E 87KT\n
			// CWB:\n041800Z 18.8N 152.3E 78KT\n => *1.1
			// KMA:\n041800Z 18.7N 152.3E 76KT\n => *1.1
			// PAGASA:\n041800Z 18.7N 152.3E 76KT\n => *1.1
			// Calculate avarage position
			// Calculate avarage & Max strength, convert grade
			// Trend (compare with pervious 6 hours)
			// Overall average moving speed
			// Output text file
			
			$content .= $lines[$i] . "<\n>";
			print "時間: $datetime , 北緯: $lat 東經: $long 強度 $pw kts <br>";
		}
	}
}

print "<pre>$content</pre>";

?>
