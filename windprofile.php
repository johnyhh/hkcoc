<form method="post" action="windprofile.php">
          <textarea name="profile"  rows="10" cols="50"></textarea>
          <br><input type="submit">
</form>
		
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
header('Content-Type: text/html; charset=utf8');

if ($_POST["profile"] == "")
{
	$profile = "
	JTWC Example:
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
} else 
{
	$profile = $_POST["profile"];
}

print "<pre>$profile</pre>";

# JTWC analysis
$lines = explode("\n",$profile);
$cnt = 0;

for ($i=0;$i<=count($lines);$i++){
	# RADIUS OF 050 KT WINDS
	
	
	if (preg_match('/RADIUS OF ([0-9]+) KT WINDS/' , $lines[$i], $arr))
	{
		if ($cnt > 0)
		{
			$radius = round(($ne + $se + $sw + $nw) / 4,-1);
			print "西北象限 $nw 公里，東北象限 $ne 公里，<br>";
			print "西南象限 $sw 公里，東南象限 $se 公里<br>";			
			print "平均半徑 $radius 公里";
		}
		
		$windrad = floor($arr[1] * 1.852);
		print "<br><br>".windgrade($windrad)."圈<br>";
		$windrad = $ne = $se = $sw = $nw = 0;
		$cnt ++;
	}

	if (preg_match('/([0-9]+) NM NORTHEAST QUADRANT/' , $lines[$i], $arr))
	{ 
		$ne = round($arr[1] * 1.852,-1);
		
	}
	if (preg_match('/([0-9]+) NM SOUTHEAST QUADRANT/' , $lines[$i], $arr))
	{ 
		$se = round($arr[1] * 1.852,-1);
	}	
	if (preg_match('/([0-9]+) NM SOUTHWEST QUADRANT/' , $lines[$i], $arr))
	{ 
		$sw = round($arr[1] * 1.852,-1);
	}	
	if (preg_match('/([0-9]+) NM NORTHWEST QUADRANT/' , $lines[$i], $arr))
	{ 
		$nw = round($arr[1] * 1.852,-1);
	}		
}

	$radius = round(($ne + $se + $sw + $nw) / 4,-1);
	print "西北象限 $nw 公里，東北象限 $ne 公里，<br>";
	print "西南象限 $sw 公里，東南象限 $se 公里<br>";		
	print "平均半徑 $radius 公里";
	
	$radius = $radius * 1.5;
	
	print "<br><br>強風圈<br>";
	print "半徑 $radius 公里";
	
	$radius = $radius * 2;	
	$scale = tc_scale($radius);
	
	print "<br><br>的風暴直徑估計約為 $radius 公里，風暴尺度屬".$scale."熱帶氣旋。<br>";
	print "的風圈半徑如下：";

	
function windgrade($pw)
{
		if (($pw>=41)and($pw<62)){$windgrade="強風";}
		elseif (($pw>=62)and($pw<92)){$windgrade="烈風";}
		elseif (($pw>=92)and($pw<118)){$windgrade="暴風";}
		elseif (($pw>=118)and($pw<180)){$windgrade="颶風";}
		elseif ($pw>=180){$windgrade="十六級風";}
	return $windgrade;	
}	

function tc_scale($r)
{
		if ($r<221){$windgrade="迷你";}
		elseif (($r>=221)and($r<331)){$windgrade="小型";}
		elseif (($r>=331)and($r<661)){$windgrade="中型";}
		elseif (($r>=661)and($r<881)){$windgrade="大型";}
		elseif ($r>=881){$windgrade="特大";}
	return $windgrade;	
}

?>
