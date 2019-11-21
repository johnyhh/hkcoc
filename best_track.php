<?php
print "test";

$url="http://www.typhoon2000.ph/multi/log.php?name=FUNG-WONG_2019";
$page = file_get_contents($url);
$lines = explode("\n",$page);

$start = 0;
$content = "";

for ($i=0;$i<=count($lines);$i++){
	if (preg_match("/function preload/", $lines[$i])){
		$start = 1;
	} elseif (preg_match("/body onLoad/", $lines[$i])){
		$start = 0;
	}
	if ($start == 1)
	{
		if (preg_match("/text/", $lines[$i])){
			// each line[$i] here is data
			// time: (2019-11-07 09:31:53 UTC)
			// HKO:\n041800Z 18.7N 152.3E 105KT\n => *1.1
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
		}
	}
}

print "<pre>$content</pre>";

?>
