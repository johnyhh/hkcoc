<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include 'dis.php';

header('Content-Type: text/html; charset=utf8');

// Display report description [0 no, 1 yes]
$report_mode = 1;
$debug=0;
$j = 0;
$dt = array(); $y = array(); $x = array(); $p = array(); 

$datetime = 0;
$lat1 = $lat24 = $lat48 = $lat72 = $lat96 = $lat120 = $lat144 = 0;
$long1 = $long24 = $long48 = $long72 = $long96 = $long120 = $long144 = 0;
$pw1 = $pw24 = $pw48 = $pw72 = $pw96 = $pw120 = $pw144 = 0;
$cnt1 = $cnt24 = $cnt48 = $cnt72 = $cnt96 = $cnt120 = $cnt144 = $cntdatetime = 0;

#$url = "http://localhost/php/raw_data/hko-track.htm";
$url = "https://www.hko.gov.hk/textonly/v2/tc/tcp.htm";
	

// Get TC data from internet
$page = file_get_contents($url);
$parts = explode("Past Positions and Intensities",$page);
$lines = explode("\n",$parts[0]);

// Parse content	
for ($i=count($lines);$i--;$i==0){	

		# 08:00 HKT 17 May 2020 19.1 N 119.5 E Tropical Storm              85 km/h
			if (preg_match('/(.*) ([0-9]+.[0-9]) N ([0-9]+.[0-9]) E.* (.*) km/' , $lines[$i], $arr))
			{
				if ($cnt1 == 0)
				{
					$datetime = $arr[1]; 
					$lat24 = $arr[2]; 
					$long24 = $arr[3]; 
					$pw24 = floor($arr[4] * 1.1 / 1.852); 
					$cnt24++; $cntpw24++;
				}
				elseif ($cnt1 == 1)
				{
					$lat48 = $arr[2]; 
					$long48 = $arr[3]; 
					$pw48 = floor($arr[4] * 1.1 / 1.852); 
					$cnt48++; $cntpw48++;
				}				
				elseif ($cnt1 == 2)
				{
					$lat72 = $arr[2]; 
					$long72 = $arr[3]; 
					$pw72 = floor($arr[4] * 1.1 / 1.852); 
					$cnt72++; $cntpw72++;
				}
				elseif ($cnt1 == 3)
				{
					$lat96 = $arr[2]; 
					$long96 = $arr[3]; 
					$pw96 = floor($arr[4] * 1.1 / 1.852); 
					$cnt96++; $cntpw96++;
				}	
				elseif ($cnt1 == 4)
				{
					$lat120 = $arr[2]; 
					$long120 = $arr[3]; 
					$pw120 = floor($arr[4] * 1.1 / 1.852); 
					$cnt120++; $cntpw120++;
				}
				elseif ($cnt1 == 5)
				{
					$lat144 = $arr[2]; 
					$long144 = $arr[3]; 
					$pw144 = floor($arr[4] * 1.1 / 1.852); 
					$cnt144++; $cntpw144++;
				}				
				$cnt1++;
				
			if($debug==1){print "HKO: $arr[1] | $arr[2] N | $arr[3] E | $arr[4] km/h<br>";}
			}				
}


$lines = $i = $cnt1 = 0;
$lines = explode("\n",$parts[1]);

// Parse content	
for ($i=0;$i<=count($lines);$i++){
		# 08:00 HKT 17 May 2020 19.1 N 119.5 E Tropical Storm              85 km/h
			if (preg_match('/(.*) ([0-9]+.[0-9]) N ([0-9]+.[0-9]) E.* (.*) km/' , $lines[$i], $arr))
			{
				if ($cnt1 == 0)
				{
					$datetime = $arr[1]; 
					$lat1 = $arr[2]; 
					$long1 = $arr[3]; 
					$pw1 = floor($arr[4] * 1.1 / 1.852); 
					$cnt1++;
					if($debug==1){print "HKO: $arr[1] | $arr[2] N | $arr[3] E | $arr[4] km/h<br>";}
				}
			}				
}

//----------------------------------------------------
	// Convert HKO datatime -- 08:00 HKT 17 May 2020
	preg_match('/([0-9]+):([0-9]+) HKT ([0-9]+) ([A-Za-z]+) ([0-9]+)/' , $datetime, $arr);
	$datetime = $arr[5].format_month($arr[4]).$arr[3].$arr[1];
	$datetime1 = $datetime;
	
	// Calculate avarage position
	if($debug==1){print "計前: $datetime , 北緯: $lat1 東經: $long1 強度 $pw1 kts | cnt1: $cnt1<br>";}
	//$datetime = round($datetime/$cnt1);
	$lat1 = round($lat1/$cnt1,1);
	$long1 = round($long1/$cnt1,1);
	
	// Calculate avarage & Max strength
	$pw1 = floor($pw1*1.852/$cnt1);
	
	// convert grade
	$tcgrade = tcgrade($pw1);
	if ($datetime < 10){$datetime = "0".$datetime;}
	$dt[$j] = $yrmn.$datetime; $y[$j] = $lat1; $x[$j] = $long1; $p[$j] = $pw1; $pg[$j] = $tcgrade; $j++;

//----------------------------------------------------
	// Calculate avarage position +24
	if ($cnt24 > 0){	
	if($debug==1){print "計前: $datetime , 北緯: $lat24 東經: $long24 強度 $pw24 kts | cnt24: $cnt24 | cntpw24: $cntpw24<br>";}
	$lat24 = round($lat24/$cnt24,1);
	$long24 = round($long24/$cnt24,1);
	
	// Calculate avarage & Max strength +24
	$pw24 = floor($pw24*1.852/$cntpw24);
	
	// convert grade +24
	$tcgrade = tcgrade($pw24);

	$dt[$j] = plusoneday($dt[$j-1]);
	$y[$j] = $lat24; $x[$j] = $long24; $p[$j] = $pw24; $pg[$j] = $tcgrade; $j++;
	}
	
//----------------------------------------------------
	// Calculate avarage position +48
	if ($cnt48 > 0){	
	if($debug==1){print "計前: $datetime , 北緯: $lat48 東經: $long48 強度 $pw48 kts | cnt48: $cnt48 | cntpw48: $cntpw48<br>";}
	$lat48 = round($lat48/$cnt48,1);
	$long48 = round($long48/$cnt48,1);
	
	// Calculate avarage & Max strength +48
	$pw48 = floor($pw48*1.852/$cntpw48);
	
	// convert grade +48
	$tcgrade = tcgrade($pw48);

	$dt[$j] = plusoneday($dt[$j-1]);
	$y[$j] = $lat48; $x[$j] = $long48; $p[$j] = $pw48; $pg[$j] = $tcgrade; $j++;	
	}

//----------------------------------------------------
	// Calculate avarage position +72
	if ($cnt72 > 0){	
	if($debug==1){print "計前: $datetime , 北緯: $lat72 東經: $long72 強度 $pw72 kts | cnt72: $cnt72 | cntpw72: $cntpw72<br>";}
	$lat72 = round($lat72/$cnt72,1);
	$long72 = round($long72/$cnt72,1);
	
	// Calculate avarage & Max strength +72
	$pw72 = floor($pw72*1.852/$cntpw72);
	
	// convert grade +72
	$tcgrade = tcgrade($pw72);

	$dt[$j] = plusoneday($dt[$j-1]);
	$y[$j] = $lat72; $x[$j] = $long72; $p[$j] = $pw72; $pg[$j] = $tcgrade; $j++;
	}
	
//----------------------------------------------------
	// Calculate avarage position +96
	if ($cnt96 > 0){
	if($debug==1){print "計前: $datetime , 北緯: $lat96 東經: $long96 強度 $pw96 kts | cnt96: $cnt96 | cntpw96: $cntpw96<br>";}
	$lat96 = round($lat96/$cnt96,1);
	$long96 = round($long96/$cnt96,1);
	
	// Calculate avarage & Max strength +96
	$pw96 = floor($pw96*1.852/$cntpw96);
	
	// convert grade +96
	$tcgrade = tcgrade($pw96);

	$dt[$j] = plusoneday($dt[$j-1]);
	$y[$j] = $lat96; $x[$j] = $long96; $p[$j] = $pw96; $pg[$j] = $tcgrade; $j++;
	}

//----------------------------------------------------
	// Calculate avarage position +120
	if ($cnt120 > 0){
	if($debug==1){print "計前: $datetime , 北緯: $lat120 東經: $long120 強度 $pw120 kts | cnt120: $cnt120 | cntpw120: $cntpw120<br>";}
	$lat120 = round($lat120/$cnt120,1);
	$long120 = round($long120/$cnt120,1);
	
	// Calculate avarage & Max strength +120
	$pw120 = floor($pw120*1.852/$cntpw120);
	
	// convert grade +120
	$tcgrade = tcgrade($pw120);

	$dt[$j] = plusoneday($dt[$j-1]);
	$y[$j] = $lat120; $x[$j] = $long120; $p[$j] = $pw120; $pg[$j] = $tcgrade; $j++;
	}

//----------------------------------------------------
	// Calculate avarage position +144
	if ($cnt144 > 0){
	if($debug==1){print "計前: $datetime , 北緯: $lat144 東經: $long144 強度 $pw144 kts | cnt144: $cnt144 | cntpw144: $cntpw144<br>";}
	$lat144 = round($lat144/$cnt144,1);
	$long144 = round($long144/$cnt144,1);
	
	// Calculate avarage & Max strength +144
	$pw144 = floor($pw144*1.852/$cntpw144);
	
	// convert grade +144
	$tcgrade = tcgrade($pw144);

	$dt[$j] = plusoneday($dt[$j-1]);
	$y[$j] = $lat144; $x[$j] = $long144; $p[$j] = $pw144; $pg[$j] = $tcgrade; $j++;
	}
	
//----------------------------------------------------
// print table
// print "<img src = http://agora.ex.nii.ac.jp/digital-typhoon/map-s/wnp/20".$tcno.".png>";
print "<table style=\"border:1px solid;padding:5px;\" rules=all cellpadding=5>";
print "<tr><th>香港時間</th><th>北緯</th><th>東經</th><th>強度<br>(km/h)</th><th>等級</th><th>趨勢</th><th>位置</th></tr>";

$tot_spd = $tot_time = $named = 0;

if ($report_mode == 1){print "=== 預測的強度變化重點 ===<br>";}

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
				$trend = "停留不動";
			} else {
				$trend = "$direction $speed km/h";
			};
			// Summary stat
			$tot_spd = $tot_spd + ($speed * $time_int);
			$tot_time += $time_int;
			
			$cp_gps = findcp($y[$i],$x[$i]);
			
			if ($i==1){$direction1 = $direction; $speed1 = $speed; $area1 = $cp_gps['area'];}
						
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
					print "$tcname 將會在 $datetime 減弱為 $pg[$i]<br>";
				} else {
					print "$tcname 將會在 $datetime 增強為 $pg[$i]<br>";
				}
			}
		}
	}
}

		// Report description
		if ($report_mode == 1)
		{
			print "<br>=== 實時狀況重點 ===<br>";
			
			$k = 0;
			// TC from CPA
			$cp_gps = findcp($y[$k],$x[$k]);
			// TC from HK
			$hk_gps = findhk($y[$k],$x[$k]);
			// Calculate Pressure
			$pre = tpre($p[$k]);
			// Calculate Gust
			$vg = round((1.58*$p[$k]) + 10);
			// Intensity change
			$pchg = inten_chg_desc($p[$k+1],$p[$k],$time_int);
			
		// Current status
			print "在".utctohkt($datetime1)."，<br>"
			.$pg[$k].$tcname."集結在".$cp_gps['name']."的".$cp_gps['dir']."約 ".$cp_gps['dis']." 公里，".
			$hk_gps['name']."的".$hk_gps['dir']."約 ".$hk_gps['dis']." 公里，<br>".
			"即在北緯 $y[$k] 度，東經 $x[$k] 度附近。<br>
			估計".$tcname."的中心最高持續風力為時速 $p[$k] 公里，陣風可達時速 ".$vg." 公里，<br>中心附近最低海平面氣壓約為 ".$pre." hPa。
			<br><br>			
			預測在未來 $time_int 小時，".$tcname.$pchg."，並以平均以時速 $speed1 公里向".$direction1."移動，趨向".$area1."。<br><br>";
			
		// Latest trend
		}
	
print "</table>";

// Life time
print "<br>預測時長:";
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
// }



// ------------- functions -------------
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
  //$hr += 8;
  $hr += 0;
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
  
  $hkt = "$mth 月 $date 日(週$cwday) $hr 時";  
#  $hkt = "$pm$hr時";
  return $hkt;
}


function format_month($mon)
{
  if ($mon == "January"){$mon = "01";}
  if ($mon == "February"){$mon = "02";}
  if ($mon == "March"){$mon = "03";}
  if ($mon == "April"){$mon = "04";}
  if ($mon == "May"){$mon = "05";}
  if ($mon == "June"){$mon = "06";}
  if ($mon == "July"){$mon = "07";}
  if ($mon == "August"){$mon = "08";}
  if ($mon == "September"){$mon = "09";}
  if ($mon == "October"){$mon = "10";}
  if ($mon == "November"){$mon = "11";}
  if ($mon == "December"){$mon = "12";}
  return $mon;  
}

function plusoneday($time)
{
  // 2019112309  
  // 2019010512 
  preg_match('/(....)(..)(..)(..)/', $time, $d);
  $yr = $d[1]; $mth = $d[2]; $date = $d[3]; $hr = $d[4];
  
  $mth++;$mth--;
  
  $maxdate = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
  $date += 1;
  
  if ($date > $maxdate[$mth]) {
    $date = 1;
    $mth += 1;
  }
  if ($mth > 12) {
    $mth = 1;
	$yr += 1;
  }
  $mth++;$mth--;

  if ($mth < 10){$mth = "0$mth";}
  if ($date < 10){$date = "0$date";}

	$newdate = $yr.$mth.$date.$hr;
//print "in plusoneday input : $time<br>";
//print "in plusoneday return : $newdate<br>";
  return $newdate;
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


function query_db($tcno,$link)
{
	if (($tcno == "")){
		print "Error! input should not be blank";
		exit;
	} elseif ($tcno < 0 or $tcno > 9999)
	{
		print "Error! input out of range";
		exit;
	} else {
		$dbquery = "select * from tc_list where tcno = $tcno;";
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
