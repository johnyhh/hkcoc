<?php

// HKCOC all right reserved
// PHP library for calculate TC trend and 
// it's relative position from cities

function findcp($x, $y)
{
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
		"area" => "沖繩附近海域"
		); 
		
	$cp['cp3'] = array( 
		"name" => "馬尼拉",  
		"lat" => 14.5,  
		"long" => 121.1, 
		"dis" => 0,
		"dir" => "",
		"area" => "菲律賓附近海域"
		); 	
	 
	$cp['cp4'] = array( 
		"name" => "東沙",  
		"lat" => 20.7,  
		"long" => 116.7, 
		"dis" => 0,
		"dir" => "",
		"area" => "南海北部"
		);  
		
	$cp['cp5'] = array( 
		"name" => "海口",  
		"lat" => 20.1,  
		"long" => 110.5, 
		"dis" => 0,
		"dir" => "",
		"area" => "海南島附近海域"
		);  	
		
	$cp['cp6'] = array( 
		"name" => "上海",  
		"lat" => 31.2,  
		"long" => 121.5, 
		"dis" => 0,
		"dir" => "",
		"area" => "東海"
		);  	
		
	$cp['cp7'] = array( 
		"name" => "汕頭",  
		"lat" => 23.4,  
		"long" => 119.7, 
		"dis" => 0,
		"dir" => "",
		"area" => "廣東沿岸"
		);  	
		
	$cp['cp8'] = array( 
		"name" => "高雄",  
		"lat" => 22.6,  
		"long" => 120.2, 
		"dis" => 0,
		"dir" => "",
		"area" => "台灣附近海域"
		);  	
		
	$cp['cp9'] = array( 
		"name" => "台北",  
		"lat" => 25.1,  
		"long" => 121.6, 
		"dis" => 0,
		"dir" => "",
		"area" => "台灣附近海域"
		);  	

	$cp['cp10'] = array( 
		"name" => "硫磺島",  
		"lat" => 24.8,  
		"long" => 141.3, 
		"dis" => 0,
		"dir" => "",
		"area" => "小笠原群島附近海域"
		);  

	$cp['cp11'] = array( 
		"name" => "鹿兒島",  
		"lat" => 31.7,  
		"long" => 130.7, 
		"dis" => 0,
		"dir" => "",
		"area" => "日本附近海域"
		); 	
		
	$cp['cp12'] = array( 
		"name" => "東京",  
		"lat" => 36.7,  
		"long" => 139.8, 
		"dis" => 0,
		"dir" => "",
		"area" => "日本附近海域"
		); 		
	 
	$cp['cp13'] = array( 
		"name" => "漢城",  
		"lat" => 37.5,  
		"long" => 127.0, 
		"dis" => 0,
		"dir" => "",
		"area" => "朝鮮半島附近海域"
		); 	 
		
	$cp['cp14'] = array( 
		"name" => "胡志明市",  
		"lat" => 10.8,  
		"long" => 106.7, 
		"dis" => 0,
		"dir" => "",
		"area" => "越南附近海域"
		); 	

	$cp['cp15'] = array( 
		"name" => "特魯克",  
		"lat" => 7.4,  
		"long" => 151.8, 
		"dis" => 0,
		"dir" => "",
		"area" => "太平洋米克羅尼西亞一帶海域"
		); 	
		
	$cp['cp16'] = array( 
		"name" => "關島",  
		"lat" => 13.4,  
		"long" => 144.9, 
		"dis" => 0,
		"dir" => "",
		"area" => "太平洋米克羅尼西亞一帶海域"
		); 	
		
	$cp['cp17'] = array( 
		"name" => "雅普島",  
		"lat" => 9.5,  
		"long" => 138.2, 
		"dis" => 0,
		"dir" => "",
		"area" => "太平洋米克羅尼西亞一帶海域"
		); 	
		
	$cp['cp18'] = array( 
		"name" => "威克島",  
		"lat" => 19.3,  
		"long" => 166.7, 
		"dis" => 0,
		"dir" => "",
		"area" => "太平洋馬紹爾群島一帶海域"
		); 	
		
		return calcp($x, $y, $cp);		
}		

function findhk($x, $y)
{
	$cp = array();	
	$cp['cp1'] = array( 
		"name" => "香港",  
		"lat" => 22.3,  
		"long" => 114.2, 
		"dis" => 0,
		"dir" => "",
		"area" => "華南沿岸"
		); 
		
		return calcp($x, $y, $cp);		
}	

function calcp($x, $y, $cp)
{
	 
	$mindis = array();	 
	 	 
	// Loop via the mutidemension associate array 
	foreach ($cp as $key => $value) { 
		if($debug==2){echo "<br>". $key . "<br>";} 
		foreach ($value as $sub_key => $sub_val) {                  
			if($debug==2){
				echo $sub_key . " = " . $sub_val . "<br>"; 
			}
		}

		// Process
		$cp[$key]['dis'] = round(distance($cp[$key]['lat'], $cp[$key]['long'], $x, $y, "k"));	
		$cp[$key]['dir'] = getDirection($cp[$key]['lat'], $cp[$key]['long'], $x, $y);		
	} 

	if($debug==2){
		print "<hr>";
		foreach ($cp as $key => $value) {
				echo "<br>". $key . "<br>"; 		
				foreach ($value as $sub_key => $sub_val) {                
					echo $sub_key . " = " . $sub_val . "<br>"; 			
			}
		}
	}

	// find CP
	foreach ($cp as $array){
		if (!isset($minarr)) $minarr = $array; 
		elseif ($array['dis'] < $minarr['dis']) $minarr = $array; 
	}

	if($debug==2){print "<br>最近城市 = ".$minarr['name']."<br>";}
	
	return $minarr;
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
