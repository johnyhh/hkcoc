<?php
	// Flow
	// 1. Call by wi cgi / cronjob
	// 2. check if there are n row, delete first row (keep last 7 days record)
	// 3. get and format wi.txt (single row)   [OK]
	// 4. check if they are same with last row, skip
	// 5. else, append to last line  [OK]
	
	// Todo order
	// 4,2,1

	// Loading google api library
	require_once 'google-api-php-client-v2.7.0-PHP5.4/vendor/autoload.php';

	//Setup API
	$client = new \Google_Client();
	$client->setApplicationName('mytest');
	$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
	$client->setAccessType('offline');
	
	// Load API Key
	$client->setAuthConfig('json_auth.json');
	
	//Load Google sheet
	$sheets = new \Google_Service_Sheets($client);
	$data = [];
	$spreadsheetId = '1k9AjYccJ8asjXXOq4oOy7IT1PP8UW-U6Y_IXzhOVuyA';
	
	// Load data from the sheet
	$range = '工作表1!A1:B';
	$rows = $sheets->spreadsheets_values->get($spreadsheetId, $range, ['majorDimension' => 'ROWS']);

	if (isset($rows['values'])) 
	{		
		// Find last row having data
		$lastrow = sizeof($rows);
		print "Last row : " . $rows[$lastrow-1][0] . " | " . $rows[$lastrow-1][1];		

		// print_r($data);	

		list($datetime, $wi) = getwi();
		print "to be write: ".$datetime." ".$wi."<br>";
		
		// Append values to last row of range
		$range = "A1:B";
		$valueRange= new \Google_Service_Sheets_ValueRange();
		$valueRange->setValues(["values" => [$datetime, $wi]]); 
		$conf = ["valueInputOption" => "RAW"];
		$ins = ["insertDataOption" => "INSERT_ROWS"];
		$sheets->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $conf, $ins);
	}
	
function getwi()
{
	// get and format last record at wi.txt
	$url = "http://www.hkcoc.com/wind/test/newwi.txt";

	// Get TC data from internet
	$page = file_get_contents($url);
	$lines = explode("\n",$page);
	$data = explode(" ",$lines[sizeof($lines)-2]);
	
	if (preg_match('/([0-9]{4})([0-9]{2})([0-9]{2})/' , $data[0], $yyyymmdd))
	{
		if (preg_match('/([0-9]{2})([0-9]{2})/' , $data[1], $hhmm))
		{
			$wi = $data[2];
			$datetime = $yyyymmdd[1]."-".$yyyymmdd[2]."-".$yyyymmdd[3]." ".$hhmm[1].":".$hhmm[2].":00";
		}
	}
	return array ($datetime, $wi);
}	
?>	
