<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$debug = 1;
$x = 21.3;
$y = 115.4;

if($debug==1){print "Input $x N $y E<hr>";}


$cp = array(); 
  
$cp['cp1'] = array( 
    "name" => "香港",  
    "lat" => 22.3,  
    "long" => 114.2, 
    "dis" => 0,
	"dir" => "",
	"area" => "華南沿岸"
	); 
	
$cp['cp2'] = array( 
    "name" => "那霸",  
    "lat" => 26.1,  
    "long" => 127.6, 
    "dis" => 0,
	"dir" => "",
	"area" => "琉球群島"
	); 
 
// Loop via the mutidemension associate array 
foreach ($cp as $key => $value) { 
    if($debug==1){echo "<br>". $key . "<br>";} 
    foreach ($value as $sub_key => $sub_val) {                  
        if($debug==1){
			echo $sub_key . " = " . $sub_val . "<br>"; 
		}
    }

	// Process
	$cp[$key]['dis'] = round(distance($cp[$key]['lat'], $cp[$key]['long'], $x, $y, "k"));	
	$cp[$key]['dir'] = getDirection($cp[$key]['lat'], $cp[$key]['long'], $x, $y);		
} 

if($debug==1){
	print "<hr>";
	foreach ($cp as $key => $value) { 
		echo "<br>". $key . "<br>"; 
		foreach ($value as $sub_key => $sub_val) {                  
			echo $sub_key . " = " . $sub_val . "<br>"; 
		}
	} 
}
    
   
   
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
