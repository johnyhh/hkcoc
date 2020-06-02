<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
header('Content-Type: text/html; charset=utf8');

$profile = "
   RADIUS OF 064 KT WINDS - 020 NM NORTHEAST QUADRANT
                            015 NM SOUTHEAST QUADRANT
                            000 NM SOUTHWEST QUADRANT
                            000 NM NORTHWEST QUADRANT
   RADIUS OF 050 KT WINDS - 065 NM NORTHEAST QUADRANT
                            020 NM SOUTHEAST QUADRANT
                            025 NM SOUTHWEST QUADRANT
                            035 NM NORTHWEST QUADRANT
   RADIUS OF 034 KT WINDS - 265 NM NORTHEAST QUADRANT
                            095 NM SOUTHEAST QUADRANT
                            120 NM SOUTHWEST QUADRANT
                            185 NM NORTHWEST QUADRANT";

print "JTWC Example:<pre>$profile</pre>";

# JTWC analysis
$lines = explode("\n",$profile);
$cnt = 0;

for ($i=0;$i<=count($lines);$i++){
	# RADIUS OF 050 KT WINDS
	
	
	if (preg_match('/RADIUS OF ([0-9]+) KT WINDS/' , $lines[$i], $arr))
	{
		if ($cnt > 0)
		{
			$radius = floor(($ne + $se + $sw + $nw) / 4);
			print "平均半徑 $radius 公里";
		}
		
		$windrad = floor($arr[1] * 1.852);
		print "<br><br>$windrad 圈<br>";
		$windrad = $ne = $se = $sw = $nw = 0;
		$cnt ++;
	}

	if (preg_match('/([0-9]+) NM NORTHEAST QUADRANT/' , $lines[$i], $arr))
	{ 
		$ne = floor($arr[1] * 1.852);
		print "東北象限 $ne 公里, ";
	}
	if (preg_match('/([0-9]+) NM SOUTHEAST QUADRANT/' , $lines[$i], $arr))
	{ 
		$se = floor($arr[1] * 1.852);
		print "東南象限 $se 公里,<br>";
	}	
	if (preg_match('/([0-9]+) NM SOUTHWEST QUADRANT/' , $lines[$i], $arr))
	{ 
		$sw = floor($arr[1] * 1.852);
		print "西南象限 $sw 公里, ";
	}	
	if (preg_match('/([0-9]+) NM NORTHWEST QUADRANT/' , $lines[$i], $arr))
	{ 
		$nw = floor($arr[1] * 1.852);
		print "西北象限 $nw 公里<br>";
	}		
}

	$radius = floor(($ne + $se + $sw + $nw) / 4);
	print "平均半徑 $radius 公里";
	
	# todo
	/*
	1. 四拾五入
	2. 象限根據位置排列來顯示
	3. 風圈根據風級轉名
	4. 計算強風圈
	5. 風暴尺度
	6. 沒有象限一句過版 -- 報告用
	7. 輸入介面
	*/

?>
