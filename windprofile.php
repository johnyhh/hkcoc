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
								185 NM NORTHWEST QUADRANT
								
	HKO Example
		Radius of over 33 knot winds 120 nautical miles.
		Radius of over 47 knot winds 60 nautical miles.
		Radius of over 2 metre waves 300 nautical miles.
	
	JMA Example
		30-kt wind area	
		NE440km(240NM)
		NW220km(120NM)	

		50-kt wind area	
		SE240km(120NM)
		SW220km(110NM)							
								";
} else 
{
	$profile = $_POST["profile"];
}

print "<pre>$profile</pre>";

$lines = explode("\n",$profile);
$cnt = 0;

$radius = array();

## start loop each line##
for ($i=0;$i<=count($lines);$i++){
# JTWC analysis
	# RADIUS OF 050 KT WINDS
	if (preg_match('/RADIUS OF ([0-9]+) KT WINDS/' , $lines[$i], $arr))
	{
		$flag = "JTWC";
		if ($cnt > 0)
		{
			$radius[$cnt-1] = round(($ne + $se + $sw + $nw) / 4,-1);
			print "西北象限 $nw 公里，東北象限 $ne 公里，<br>";
			print "西南象限 $sw 公里，東南象限 $se 公里<br>";			
			print "平均半徑".$radius[$cnt-1]."公里";
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
	
# HKO analysis		
	if (preg_match('/Radius of over ([0-9]+) knot winds ([0-9]+) nautical miles/' , $lines[$i], $arr))
	{
		$cnt++;
		$windrad = floor($arr[1] * 2);
		$radius[0+$cnt]=round($arr[2] * 1.852,-1);
		# print "line: $lines[$i] arr1: $arr[1] arr2: $arr[2]";
		print windgrade($windrad)."圈半徑 ".$radius[0+$cnt]." 公里，";		
	}		
	if (preg_match('/Radius of over 2 metre waves ([0-9]+) nautical miles/' , $lines[$i], $arr))
	{
		$flag = "HKO";
		$radius[0] = round($arr[1] * 1.852,-1);
		# print "line: $lines[$i] arr1: $arr[1] arr2: $arr[2]";
		print "強風圈半徑 ".$radius[0]." 公里，";
	}		

# JMA analysis		
#30-kt wind area	
#NE440km(240NM)
	if (preg_match('/([0-9]+)-kt wind area/' , $lines[$i], $arr))
	{
		$flag = "JMA";
		# for pervious round
		if ($jmacount>0){
			$radius[$jmacount-1]=round($radius[$jmacount-1]/$jmaqua,-1);
			print "<br>平均半徑".$radius[$jmacount-1]."公里";
		}
		# for this round
		$jmacount++;
		$jmaqua=0;
		#$radius=0;
		$windrad = floor($arr[1] * 2);
		#print "line: $lines[$i] arr1: $arr[1] arr2: $arr[2]";
		print "<br><br>".windgrade($windrad)."圈<br>";
	}
	if ($flag == "JMA"){
		if (preg_match('/([A-Z]+)([0-9]+)km/' , $lines[$i], $arr))
		{
			$eng = array("NE","NW","SW","SE","N","E","W","S");
			$chi = array("東北","西北","西南","東南","北","東","西","南");
			$arr[1] = str_replace($eng,$chi,$arr[1]);
			$radius[$jmacount-1] += $arr[2];
			if($jmaqua>0){print"，";}
			print "$arr[1]象限 $arr[2] 公里";
			$jmaqua++;
		}
	}
	
}
## end loop each line##

# Last circle handling
	if ($flag == "JTWC"){
		$radius[$cnt-1] = round(($ne + $se + $sw + $nw) / 4,-1);
		print "西北象限 $nw 公里，東北象限 $ne 公里，<br>";
		print "西南象限 $sw 公里，東南象限 $se 公里<br>";		
		print "平均半徑 ".$radius[$cnt-1]." 公里";
		
		$radius[$cnt] = max($radius) * 1.5;
		
		print "<br><br>強風圈<br>";
		print "半徑 $radius[$cnt] 公里";	
	}
	if ($flag = "JMA"){
		if ($jmacount>0){
			$radius[$jmacount-1]=round($radius[$jmacount-1]/$jmaqua,-1);
			print "<br>平均半徑".$radius[$jmacount-1]."公里";
		}	
	}
	

	$diameter = max($radius) * 2;		
	$scale = tc_scale($diameter);
	
	print "<br><br>，風暴直徑估計約為 $diameter 公里，風暴尺度屬".$scale."熱帶氣旋。<br>";
	
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