<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// mutiple records
$j = 0;
$dt = array();
$y = array();
$x = array();
$p = array();

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
				$cnt = 0; $lat = 0; $long = 0; $pw = 0;
			}			
			// HKO:\n041800Z 18.7N 152.3E 105KT\n => *1.1
		    if (preg_match('/HKO\:....(..)..Z ([0-9]+.[0-9])N ([0-9]+.[0-9])E ([0-9]+)KT/', $lines[$i], $arr)){
				$datetime = $yrmn.$arr[1]; $lat += $arr[2]; $long += $arr[3]; $pw += floor($arr[4] * 1.1); $cnt++;
			if($debug==1){print "HKO: $yrmn.$arr[1] | $arr[2] N | $arr[3] E | $arr[4]<br>";}
			}
			// JTWC:\n041800Z 18.7N 152.3E 105KT\n
			if (preg_match('/JTWC\:....(..)..Z ([0-9]+.[0-9])N ([0-9]+.[0-9])E ([0-9]+)KT/', $lines[$i], $arr)){
				$datetime = $yrmn.$arr[1]; $lat += $arr[2]; $long += $arr[3]; $pw += $arr[4] ; $cnt++;
			if($debug==1){print "JTWC: $yrmn.$arr[1] | $arr[2] N | $arr[3] E | $arr[4]<br>";}
			}
			// JMA:\n041800Z 18.8N 152.3E 80KT\n => *1.1
		    if (preg_match('/JMA\:....(..)..Z ([0-9]+.[0-9])N ([0-9]+.[0-9])E ([0-9]+)KT/', $lines[$i], $arr)){
				$datetime = $yrmn.$arr[1]; $lat += $arr[2]; $long += $arr[3]; $pw += floor($arr[4] * 1.1); $cnt++;
			if($debug==1){print "JMA: $yrmn.$arr[1] | $arr[2] N | $arr[3] E | $arr[4]<br>";}
			}			
			// NMC:\n041800Z 18.9N 152.3E 87KT\n
			if (preg_match('/NMC\:....(..)..Z ([0-9]+.[0-9])N ([0-9]+.[0-9])E ([0-9]+)KT/', $lines[$i], $arr)){
				$datetime = $yrmn.$arr[1]; $lat += $arr[2]; $long += $arr[3]; $pw += $arr[4] ; $cnt++;
			if($debug==1){print "NMC: $yrmn.$arr[1] | $arr[2] N | $arr[3] E | $arr[4]<br>";}
			}			
			// CWB:\n041800Z 18.8N 152.3E 78KT\n => *1.1
		    if (preg_match('/CWB\:....(..)..Z ([0-9]+.[0-9])N ([0-9]+.[0-9])E ([0-9]+)KT/', $lines[$i], $arr)){
				$datetime = $yrmn.$arr[1]; $lat += $arr[2]; $long += $arr[3]; $pw += floor($arr[4] * 1.1); $cnt++;
			if($debug==1){print "CWB: $yrmn.$arr[1] | $arr[2] N | $arr[3] E | $arr[4]<br>";}
			}				
			// KMA:\n041800Z 18.7N 152.3E 76KT\n => *1.1
		    if (preg_match('/KMA\:....(..)..Z ([0-9]+.[0-9])N ([0-9]+.[0-9])E ([0-9]+)KT/', $lines[$i], $arr)){
				$datetime = $yrmn.$arr[1]; $lat += $arr[2]; $long += $arr[3]; $pw += floor($arr[4] * 1.1); $cnt++;
			if($debug==1){print "KMA: $yrmn.$arr[1] | $arr[2] N | $arr[3] E | $arr[4]<br>";}
			}				
			// PAGASA:\n041800Z 18.7N 152.3E 76KT\n => *1.1
		    if (preg_match('/PAGASA\:....(..)..Z ([0-9]+.[0-9])N ([0-9]+.[0-9])E ([0-9]+)KT/', $lines[$i], $arr)){
				$datetime = $yrmn.$arr[1]; $lat += $arr[2]; $long += $arr[3]; $pw += floor($arr[4] * 1.1); $cnt++;
			if($debug==1){print "PAGASA: $yrmn.$arr[1] | $arr[2] N | $arr[3] E | $arr[4]<br>";}
			}				
			// Calculate avarage position
			if($debug==1){print "計前: $datetime , 北緯: $lat 東經: $long 強度 $pw kts | cnt: $cnt<br>";}
			$lat = round($lat/$cnt,1);
			$long = round($long/$cnt,1);
			
			// Calculate avarage & Max strength
			$pw = floor($pw*1.852/$cnt);
			
			// convert grade
			$tcgrade = tcgrade($pw);			
			
			$content .= $lines[$i] . "<\n>";
		//	print "<tr><td>$datetime</td><td>$lat N</td><td>$long E</td><td>$pw</td><td>$tcgrade</td></tr>";
			$dt[$j] = $datetime; $y[$j] = $lat; $x[$j] = $long; $p[$j] = $pw; $pg[$j] = $tcgrade; $j++;
		}
	}
}

// print table

print "<table style=\"border:1px solid;padding:5px;\" rules=all cellpadding=5>";
print "<tr><th>香港時間</th><th>北緯</th><th>東經</th><th>強度<br>(km/h)</th><th>等級</th><th>趨勢</th></tr>";

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
				$trend = "停留不動 (共 $time_int 小時)";
			} else {
				$trend = "時速 $speed 公里向 $direction 移動(共 $time_int 小時)";
			};
						
		}
		
		// UTC to HKT
		$datetime = utctohkt($dt[$i]);
			
		print "<tr><td>$datetime</td><td>$y[$i] N</td><td>$x[$i] E</td><td>$p[$i]</td><td>$pg[$i]</td><td>$trend</td></tr>";
	}
}
	
print "</table>";

// Life time

// Overall average moving speed

// Maximum intensity at

// Super typhoon sustain for

// -- Report use --//
// Grade change description
// Current status
// Latest trend

print "<pre>$content</pre>";


// ------------- functions ====================
function tcgrade($pw)
{
	if ($pw<65){$tcgrade="熱帶低氣壓";}
		elseif (($pw>=65)and($pw<90)){$tcgrade="熱帶風暴";}
		elseif (($pw>=90)and($pw<118)){$tcgrade="強烈熱帶風暴";}
		elseif (($pw>=118)and($pw<180)){$tcgrade="颱風";}
		elseif (($pw>=180)and($pw<230)){$tcgrade="強烈颱風";}
		elseif ($pw>=230){$tcgrade="超級颱風";}
	return $tcgrade;	
}

function utctohkt($time)
{
  // 2019112309
  preg_match('/(....)(..)(..)(..)/', $time, $d);
  $yr = $d[1]; $mth = $d[2]; $date = $d[3]; $hr = $d[4];
  
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
  
  $hkt = "$mth 月 $date 日($cwday) $hr 時";  
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

/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::  This routine calculates the distance between two points (given the     :*/
/*::  latitude/longitude of those points). It is being used to calculate     :*/
/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
/*::                                                                         :*/
/*::  Definitions:                                                           :*/
/*::    South latitudes are negative, east longitudes are positive           :*/
/*::                                                                         :*/
/*::  Passed to function:                                                    :*/
/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
/*::    unit = the unit you desire for results                               :*/
/*::           where: 'M' is statute miles (default)                         :*/
/*::                  'K' is kilometers                                      :*/
/*::                  'N' is nautical miles                                  :*/
/*::  Worldwide cities and other features databases with latitude longitude  :*/
/*::  are available at https://www.geodatasource.com                          :*/
/*::                                                                         :*/
/*::  For enquiries, please contact sales@geodatasource.com                  :*/
/*::                                                                         :*/
/*::  Official Web site: https://www.geodatasource.com                        :*/
/*::                                                                         :*/
/*::         GeoDataSource.com (C) All Rights Reserved 2018                  :*/
/*::                                                                         :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
function distance($lat1, $lon1, $lat2, $lon2, $unit) {
  if (($lat1 == $lat2) && ($lon1 == $lon2)) {
    return 0;
  }
  else {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
      return ($miles * 1.609344);
    } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
      return $miles;
    }
  }
}


function getDirection($lat1, $lon1, $lat2, $lon2) {
   //difference in longitudinal coordinates
   $dLon = deg2rad($lon2) - deg2rad($lon1);
 
   //difference in the phi of latitudinal coordinates
   $dPhi = log(tan(deg2rad($lat2) / 2 + pi() / 4) / tan(deg2rad($lat1) / 2 + pi() / 4));
 
   //we need to recalculate $dLon if it is greater than pi
   if(abs($dLon) > pi()) {
      if($dLon > 0) {
         $dLon = (2 * pi() - $dLon) * -1;
      }
      else {
         $dLon = 2 * pi() + $dLon;
      }
   }
   //return the angle, normalized
   $bearing = (rad2deg(atan2($dLon, $dPhi)) + 360) % 360;
   
   $tmp = round($bearing / 22.5);
   switch($tmp) {
      case 1:
         $direction = "東北偏北";
         break;
      case 2:
         $direction = "東北";
         break;
      case 3:
         $direction = "東北偏東";
         break;
      case 4:
         $direction = "東";
         break;
      case 5:
         $direction = "東南偏東";
         break;
      case 6:
         $direction = "東南";
         break;
      case 7:
         $direction = "東南偏南";
         break;
      case 8:
         $direction = "南";
         break;
      case 9:
         $direction = "西南偏南";
         break;
      case 10:
         $direction = "西南";
         break;
      case 11:
         $direction = "西南偏西";
         break;
      case 12:
         $direction = "西";
         break;
      case 13:
         $direction = "西北偏西";
         break;
      case 14:
         $direction = "西北";
         break;
      case 15:
         $direction = "西北偏北";
         break;
      default:
         $direction = "北";
   }
   return $direction;   
}

?>
