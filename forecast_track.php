<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include 'config_fs.php';
include 'dis.php';

header('Content-Type: text/html; charset=utf8');
  
$tcname = "天鵝";
$engname = "GONI";
$tcno = "1510";

// Display report description [0 no, 1 yes]
$report_mode = 1;

$debug=1;

$j = 0;
$dt = array();
$y = array();
$x = array();
$p = array();

#$url="http://www.typhoon2000.ph/multi/log.php?name=".$engname."_20".$yr;
$url="http://localhost/php/extern.txt";
$page = file_get_contents($url);
$lines = explode("\n",$page);
	
for ($i=0;$i<=count($lines);$i++){

	// time: (2019-11-07 09:31:53 UTC)
	if (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2}) /', $lines[$i], $arr)){
		$yrmn = "$arr[1]$arr[2]$arr[3]"; 
		$cnt = 0; $lat = 0; $long = 0; $pw = 0;
	}	
	
	# parts
	if (preg_match("/HKO/", $lines[$i])){
		$start = "HKO";
	} elseif (preg_match("/JTWC/", $lines[$i])){
		$start = "JTWC";
	} elseif (preg_match("/JMA/", $lines[$i])){
		$start = "JMA";
	} elseif (preg_match("/NMC/", $lines[$i])){
		$start = "NMC";
	} elseif (preg_match("/CWB/", $lines[$i])){
		$start = "CWB";
	} elseif (preg_match("/KMA/", $lines[$i])){
		$start = "KMA";
	} elseif (preg_match("/PAGASA/", $lines[$i])){
		$start = "PAGASA";
	}
	
	if ($start == "HKO")
	{
		// HKO:\n041800Z 18.7N 152.3E 105KT\n => *1.1
		if (preg_match('/..(..)..Z ([0-9]+.[0-9])N ([0-9]+.[0-9])E ([0-9]+)KT/', $lines[$i], $arr)){
			$datetime = $yrmn.$arr[1]; $lat += $arr[2]; $long += $arr[3]; $pw += floor($arr[4] * 1.1); $cnt++;
		if($debug==1){print "HKO: $yrmn.$arr[1] | $arr[2] N | $arr[3] E | $arr[4]<br>";}
		}

		// 24H
		if (preg_match('/\(\+024H\) ([0-9]+.[0-9])N ([0-9]+.[0-9])E ([0-9]+)KT/', $lines[$i], $arr)){
			$lat += $arr[1]; $long += $arr[2]; $pw += floor($arr[3] * 1.1); $cnt++;
		if($debug==1){print "24H: $arr[1] N | $arr[2] E | $arr[3]<br>";}
		}
		
		// todo
		// 1. Save in different list [arr]
		// 2. Add 48, 72 ...
		
		// Calculate avarage position
//		if($debug==1){print "計前: $datetime , 北緯: $lat 東經: $long 強度 $pw kts | cnt: $cnt<br>";}
//		$lat = round($lat/$cnt,1);
//		$long = round($long/$cnt,1);
		
		// Calculate avarage & Max strength
//		$pw = floor($pw*1.852/$cnt);
		
		// convert grade
		$tcgrade = tcgrade($pw);			
		
		$content .= $lines[$i] . "<\n>";
	//	print "<tr><td>$datetime</td><td>$lat N</td><td>$long E</td><td>$pw</td><td>$tcgrade</td></tr>";
		$dt[$j] = $datetime; $y[$j] = $lat; $x[$j] = $long; $p[$j] = $pw; $pg[$j] = $tcgrade; $j++;

	}
}

// print table
print "<h3 id=$tcno>$tcname $engname ($tcno)</h3><br> ";
// print "<img src = http://agora.ex.nii.ac.jp/digital-typhoon/map-s/wnp/20".$tcno.".png>";
print "<table style=\"border:1px solid;padding:5px;\" rules=all cellpadding=5>";
print "<tr><th>香港時間</th><th>北緯</th><th>東經</th><th>強度<br>(km/h)</th><th>等級</th><th>趨勢</th><th>位置</th></tr>";

$tot_spd = $tot_time = $named = 0;

for ($i=0;$i<=$j-1;$i++){
	$dis = $time_int = $speed = 0;
	$trend = "";	
	
	if ($dt[$i] != $dt[$i-1])
	{
		if ($i>=1)
		{
			// Calculate moving speed
			$dis = round(distance($y[$i-1], $x[$i-1], $y[$i], $x[$i], "K"));
			$time_int = differenceInHours($dt[$i-1],$dt[$i]);
			$speed = round($dis/$time_int);
			
			// Move direction
			$direction = getDirection($y[$i-1], $x[$i-1], $y[$i], $x[$i]);
			
			// Output sentences
			if ($speed == 0){
				$trend = "停留不動<br> (共 $time_int 小時)";
			} else {
				$trend = "$direction $speed km/h<br>(共 $time_int 小時)";
			};
			// Summary stat
			$tot_spd = $tot_spd + ($speed * $time_int);
			$tot_time += $time_int;
			
			$cp_gps = findcp($y[$i],$x[$i]);
						
		}
		
		// UTC to HKT
		$datetime = utctohkt($dt[$i]);
			
		print "<tr><td>$datetime</td><td>$y[$i] N</td><td>$x[$i] E</td><td>$p[$i]</td><td>$pg[$i]</td><td>$trend</td><td>".$cp_gps['area']."</td></tr>";
		
		// Report description
		if ($report_mode == 1)
		{
			// Intensity Change
			if ($pg[$i] != $pg[$i-1])
			{
				if ($p[$i] < $p[$i-1])
				{
					print "$tcname 在 $datetime 減弱為 $pg[$i]<br>";
				} else {
					if ($named == 0)
					{
						print "熱帶低氣壓在 $datetime 增強為 $pg[$i]，日本氣象廳把其命名為 $tcname （ $engname ），國際編號 $tcno<br>";
						$named = 1;
					} else {
						print "$tcname 在 $datetime 增強為 $pg[$i]<br>";
					}
				}
			}
		}
	}
}

		// Report description
		if ($report_mode == 1)
		{
			$k = $i-1;
			// TC from CPA
			$cp_gps = findcp($y[$k],$x[$k]);
			// TC from HK
			$hk_gps = findhk($y[$k],$x[$k]);
			// Calculate Pressure
			$pre = tpre($p[$k]);
			// Calculate Gust
			$vg = round((1.58*$p[$k]) + 10);
			// Intensity change
			$pchg = inten_chg_desc($p[$k],$p[$k-1],$time_int);
			
		// Current status
			print "<br><br>在".$datetime."，<br>"
			.$pg[$k].$tcname."集結在".$cp_gps['name']."的".$cp_gps['dir']."約".$cp_gps['dis']."公里，".
			$hk_gps['name']."的".$hk_gps['dir']."約".$hk_gps['dis']."公里，<br>".
			"即在北緯 $y[$k] 度，東經 $x[$k] 度附近。<br>
			估計".$tcname."的中心最高持續風力為時速 $p[$k] 公里，陣風可達時速".$vg."，<br>中心附近最低海平面氣壓約為 ".$pre." hPa。
			<br><br>			
			在過去 $time_int 小時，".$tcname.$pchg."，並以平均以時速 $speed 公里向".$direction."移動，趨向".$cp_gps['area']."。<br><br>";
			
		// Latest trend
		}
	
print "</table>";

// Life time
print "<br>生命週期:";
print "$tot_time 小時 / ";
$tot_day = round($tot_time/24)+1;
print "$tot_day 日<br>";

// Overall average moving speed
print "平均移動:";
$avg_spd = round($tot_spd/$tot_time);
print "時速 $avg_spd 公里<br>";

// Maximum intensity at
print "最大強度:";
$max_spd = max($p);
$tcgrade = tcgrade($max_spd);
print "時速 $max_spd 公里 ($tcgrade)<br>";
print "<a href = $url target=_blank>[Data source]</a>";

// Super typhoon sustain for

if($debug==1){print "<pre>$content</pre>";}
//}


// ------------- functions ====================
function tcgrade($pw)
{
	if ($pw<63){$tcgrade="熱帶低氣壓";}
		elseif (($pw>=63)and($pw<88)){$tcgrade="熱帶風暴";}
		elseif (($pw>=87)and($pw<118)){$tcgrade="強烈熱帶風暴";}
		elseif (($pw>=118)and($pw<180)){$tcgrade="颱風";}
		elseif (($pw>=180)and($pw<230)){$tcgrade="強烈颱風";}
		elseif ($pw>=230){$tcgrade="超級颱風";}
	return $tcgrade;	
}

function utctohkt($time)
{
  // 2019112309  
  // 2019010512 
  preg_match('/(....)(..)(..)(..)/', $time, $d);
  $yr = $d[1]; $mth = $d[2]; $date = $d[3]; $hr = $d[4];
  
  $mth++;$mth--;
  
  $maxdate = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
  $hr += 8;
  if ($hr >= 24) {
    $hr -= 24;
    $date += 1;
  }
  if ($date > $maxdate[$mth]) {
    $date = 1;
    $mth += 1;
  }
  if ($mth > 12) {
    $mth = 1;
  }
  $mth++;$mth--;
  
  //if ($hr<12){$pm="上午"}
  //if ($hr==12){$pm="中午"}
  //if ($hr>12){$pm="下午";$hr-=12;}
  if ($hr < 10){$hr = "0$hr";}

  $wday = date("N", mktime(0, 0, 0, $mth, $date, $yr));
 if ($wday == 1){$cwday = "一";}
 if ($wday == 2){$cwday = "二";}
 if ($wday == 3){$cwday = "三";}
 if ($wday == 4){$cwday = "四";}
 if ($wday == 5){$cwday = "五";}
 if ($wday == 6){$cwday = "六";}
 if ($wday == 7){$cwday = "日";} 
  
  $hkt = "$mth 月 $date 日<br>(週$cwday) $hr 時";  
#  $hkt = "$pm$hr時";
  return $hkt;
}

function differenceInHours($t1,$t2)
{
  preg_match('/(....)(..)(..)(..)/', $t1, $d);
  $yr1 = $d[1]; $mth1 = $d[2]; $date1 = $d[3]; $hr1 = $d[4];

  preg_match('/(....)(..)(..)(..)/', $t2, $d);
  $yr2 = $d[1]; $mth2 = $d[2]; $date2 = $d[3]; $hr2 = $d[4];
  
    // YYYY-MM-DD for strtotime
	$starttimestamp = strtotime("$yr1-$mth1-$date1 $hr1:00");
	$endtimestamp = strtotime(("$yr2-$mth2-$date2 $hr2:00"));
	$difference = abs($endtimestamp - $starttimestamp)/3600;
	return $difference;
}

function tpre($b)
{
if (($b>40)and($b<= 55)) {$dw=1000;}
if (($b> 55)and($b<= 65)) {$dw=997;}
if (($b> 65)and($b<= 85)) {$dw=991;}
if (($b> 85)and($b<= 100)) {$dw=984;}
if (($b> 100)and($b<=120)) {$dw=976;}
if (($b>120)and($b<=140)) {$dw=966;}
if (($b>140)and($b<=165)) {$dw=954;}
if (($b>165)and($b<=185)) {$dw=941;}
if (($b>185)and($b<=210)) {$dw=927;}
if (($b>210)and($b<=235)) {$dw=914;}
if (($b>235)and($b<=260)) {$dw=898;}
if (($b>260)and($b<=290)) {$dw=879;}
if (($b>290)and($b<=315)) {$dw=858;}
return $dw;
}

function inten_chg_desc($p1,$p2,$t)
{
	// p1: current intensity
	// p2: pervious intensity
	// t: time interval
	$change=(($p1-$p2)/$t)*24;
	if ($change >= 55){$desc = "迅速增強";}
	if ($change >= 10 and $change < 55){$desc = "呈增強之勢";}
	if ($change >= 0 and $change < 10){$desc = "稍為增強";}
	if ($change >= -10 and $change < 10){$desc = "稍為減弱";}
	if ($change > -37 and $change < -10){$desc = "呈減弱之勢";}
	if ($change <= -37){$desc = "迅速減弱";}
	 
	return $desc;
}


function query_db($fr,$to,$link)
{
	if (($fr == "" or $to == "")){
		print "Error! input should not be blank";
		exit;
	} elseif (($fr < 0 or $fr > 9999) or ($to < 0 or $to > 9999)) 
	{
		print "Error! input out of range";
		exit;
	} else {
		$dbquery = "select * from tc_list where tcno >= $fr and tcno <= $to;";
		$result = mysqli_query($link,$dbquery);
		$number_of_rows = mysqli_num_rows($result);
		if ($number_of_rows == 0)
		{
			print "Record not found";
			exit;
		}

		for ( $l=0; $l<=$number_of_rows-1; $l++ ) 
		{
			if (mysqli_data_seek($result, $l)) {	
			list($tcno, $chiname, $engname) = mysqli_fetch_row($result);
				
				$tc[$l] = array( 
					"tcno" => $tcno,  
					"chiname" => $chiname,  
					"engname" => $engname
				); 
			}
		}
	}
	return $tc;
}

?>
