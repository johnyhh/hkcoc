<?php
  include ('config.php');
  $tcroot  = "$path/txt/tcroot2.txt";
  
  // parse url by get method
  parse_str( parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $array );
  extract($array);
  //print_r ($array); // Debug
  
?>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>翔龍 Control Panel</title>
<style type="text/css">
<!--
.style1 {font-size: small}
-->
</style>
</head>

<body>
<div align="center">
  <table width="800" border="0">
    <tr bgcolor="#E7F4FD">
      <td><div align="center">
        <p>
          <?php if ($task == ""){
  		if ($password != ""){
######## Need password ###############
#		if ($password != $setpass){
?>
        </p>
        <form method="get" action="1_tcinfo.php">
          <label>
          <input type="text" name="password">
          </label>
          　
          <input type="submit" value="Login">
        </form>
        <p>
          <?php
		} else { ?>
        </p>
        <h3>翔龍 - Control Panel      </h3>
        <table border="1" cellpadding="5" cellspacing="0">
          <tr>
            <td bgcolor="#FFFF99"><div align="center">JTWC</div></td>
            <td bgcolor="#FFFF99"><div align="center">國際編號</div></td>
            <td bgcolor="#FFFF99"><div align="center">命名</div></td>
            <td bgcolor="#FFFF99">&nbsp;</td>
          </tr>
          <?php 
		      $dbquery = "select * from tc_current order by id asc";
	          $result = mysql_query($dbquery);
		      $number_of_rows = mysql_num_rows($result); 
			  
		   for ( $i=$pointer; $i<$number_of_rows; $i++ ) {
	        if (mysql_data_seek($result, $i)) {	    
	        list($id, $jtwcno, $tcno, $authorize, $weight, $readat) = mysql_fetch_row($result);
			
			  $dbquery2 = "select chiname,engname from tc_list where tcno = $tcno";
	          $result2 = mysql_query($dbquery2);
		      list($chiname, $engname) = mysql_fetch_row($result2);
		  ?>
          <tr>
            <td bgcolor="#FFFFFF"><?php print $jtwcno; ?></td>
            <td bgcolor="#FFFFFF"><?php if ($tcno != 0){ print $tcno; } else { print "-";} ?></td>
            <td bgcolor="#FFFFFF"><?php if ($chiname != ""){ print "$chiname ($engname)";} else {print "未命名";} ?></td>
            <td bgcolor="#FFFFFF"><div align="center"><span class="style1"><a href="1_tcinfo.php?task=edit&amp;id=<?php print $id;?>&password=<?php print $password?>">設定</a> / <a href="1_tcinfo.php?task=del&amp;id=<?php print $id;?>&password=<?php print $password?>&tcno=<?php print $tcno ?>">刪除 </a> / 
			<!--<a href = <?php print "$getc?fileno=$id" ?> target=_blank>瀏覽</a>-->
			<a href = "http://hkcoc.freelancesky.com/php/forecast_track.php?tcno=<?php print $tcno ?>" target=_blank>瀏覽</a>
			/
			<a href="http://hkcoc.freelancesky.com/php/best_track.php?fr=<?php print $tcno;?>&to=<?php print $tcno;?>" target=_blank>歷史</a></span></div></td>
          </tr>
          <?php 		  
		  }
		  $tmp .= "檔號 $id 暫時編號 $jtwcno 國際編號 $tcno 名 $engname 中名 $chiname 權 $authorize 比重 $weight 存取 $readat\n"; // 預備寫入 tcroot2		  
		  }
		  writein ($tcroot, $tmp);
		  ?>
        </table>
        <p><a href="1_tcinfo.php?task=add&password=<?php print $password?>">新增</a> / 
		<!--<a href = <?php print "$updall" ?> target=_blank>全部更新</a></p>-->		<a href="http://www.hkcoc.com/php/tcname.php" target=_blank>更新TC命名</a>
		<!--<hr>
		<p><a href="http://hkcoc.freelancesky.com/php/best_track.php?fr=&to=" target=_blank>Best Track</a> | <a href="http://www.hkcoc.com/php/tcname.php" target=_blank>更新TC命名</a></p>-->
        <p>
          <?php }} elseif ($task == "add"){ ?>
          </p>
        <h3>新增</h3>
        <form action="1_tcinfo.php" method="get" name="form1" id="form1">
          <table border="0" cellpadding="6">
            <tr>
              <td bgcolor="#FFFF99"><div align="center">JTWC</div></td>
              <td bgcolor="#FFFF99"><div align="center">國際編號</div></td>
            </tr>
            <tr>
              <td valign="top" bgcolor="#FFFFFF"><input name="jtwcno" type="text" id="jtwcno" size="15" /></td>
              <td valign="top" bgcolor="#FFFFFF"><input name="tcno" type="text" id="tcno" size="15" /></td>
              </tr>
            <tr>
              <td colspan="3" bgcolor="#FFFFFF"><div align="center">
                <input type="submit" name="button" id="button" value="送出" />
                <input name="task" type="hidden" id="task" value="add2" />
                <input name="password" type="hidden" id="task3" value="<?php print $password ?>" />
              </div></td>
            </tr>
          </table>
          <br>
          <span class="style1">*JTWC no need add &quot;w&quot;</span>
        </form>
        <p>
          <?php } elseif ($task == "add2"){ ?>
          <?php
		  // insert tc current
		  $dbquery = "INSERT INTO `tc_current` ( `id` , `jtwcno` , `tcno`, `authorize`, `weight`, `readat`) 
	  	              VALUES ('', '$jtwcno', '$tcno', '0', '1,0.5,1,0,1,1,1,0.6', '1,1,1,0,1,1,1,0');";	  
	      $result = mysql_query($dbquery);
		  if ($result){ print "新增 $tcno $jtwc 成功<br>";} else {print "新增 $tcno $jtwc 失敗<br>";}
		  
		  /* 讀取最後的 ID
		  $dbquery = "SELECT id FROM `tc_current` order by id desc LIMIT 0 , 1";	  
	      $result = mysql_query($dbquery);
		  list($id) = mysql_fetch_row($result);
		  */
		  
		  /* 自動化 create and copy folder, 但暫時未成功, 請手動進行
		  print "id is $id <hr>";
		  // Copy folder from blank
		  mkdir("$logpath/$id", 0777);
		  chmod("$logpath/$id", 0777);
          chgrp("$logpath/$id", "hkcoccom44");
		  */
		  
		  // update tc status in tc_list
		  $dbquery = "update `tc_list` set
						  active = '1'	  
						  where tcno = $tcno;";  
	      $result = mysql_query($dbquery);
		  if ($result){ print "已設定 $tcno 為活躍風暴<br>";}	  	  		  
		  ?>
        </p>
        <p>
          <?php } elseif ($task == "del"){ ?>
          <?php
          $dbquery = "delete from `tc_current` where id = '$id';";
	      $result = mysql_query($dbquery);
		  if ($result){ print "已刪除 $id";}
		  
		  // update tc status in tc_list
		  $dbquery = "update `tc_list` set
						  active = '2'	  
						  where tcno = $tcno;";  
	      $result = mysql_query($dbquery);
	      ?>
          <?php } elseif ($task == "edit"){ ?>
          <?php 
		 if ($edited == "yes"){
			  $dbquery = "update `tc_current` set
						  jtwcno = '$jtwcno',
						  tcno = '$tcno',
						  authorize = '$authorize',
						  weight = '$weight[0],$weight[1],$weight[2],$weight[3],$weight[4],$weight[5],$weight[6],$weight[7]',						 
						  readat = '$readat[0],$readat[1],$readat[2],$readat[3],$readat[4],$readat[5],$readat[6],$readat[7]'	  
						  where id = '$id' 
	  ";
	  $result = mysql_query($dbquery);
	  
	  // update tc status in tc_list
		  $dbquery = "update `tc_list` set
						  active = '1'	  
						  where tcno = $tcno;";  
	      $result = mysql_query($dbquery);
	}
		 
	$dbquery = "select * from tc_current where id LIKE '$id'";
			 $result = mysql_query($dbquery);
			 list($id, $jtwcno, $tcno, $authorize, $weight_tmp, $readat_tmp) = mysql_fetch_row($result);
			 $weight = split(",",$weight_tmp);
			 $readat = split(",",$readat_tmp); 
		  ?>
        </p>
        <h3>設定</h3>
        <form action="1_tcinfo.php" method="get" name="form1" id="form2">
          <table height="60" border="0" cellpadding="5">
    <tr>
      <td bgcolor="#FFFF66">基本資料</td>
      <td bgcolor="#FFFFFF"><div align="left">
      <p align="center">暫時編號 <input type="text" name="jtwcno" value="<?php print $jtwcno;?>" size="4">
                 &nbsp; 國際編號 <input type="text" name="tcno" value="<?php print $tcno;?>" size="4">
                     　  授權預報 <input name="authorize" type="checkbox" value="1" 
					            <?php if ($authorize == 1){print "checked";}?>> </p>
      </div></td>
    </tr>
    <tr>
      <td bgcolor="#FFFF66">讀出設定</td>
      <td bgcolor="#FFFFFF">美國
        <select name="readat[0]" size="1">
        <option selected value="<?php print $readat[0];?>">預設<?php print $readat[0];?></option>
        <option value="0">暫停</option>
        <option value="1">外援</option>
        <option value="2">原稿</option>
      </select>
      　日本<select name="readat[1]" size="1">
        <option selected value="<?php print $readat[1];?>">預設<?php print $readat[1];?></option>
        <option value="0">暫停</option>
        <option value="1">外援</option>
        <option value="2">原稿</option>
      </select>
      　台灣<select name="readat[2]" size="1">
        <option selected value="<?php print $readat[2];?>">預設<?php print $readat[2];?></option>
        <option value="0">暫停</option>
        <option value="1">外援</option>
        <option value="2">原稿</option>
      </select>　英國<select name="readat[3]" size="1">
        <option selected value="<?php print $readat[3];?>">預設<?php print $readat[3];?></option>
        <option value="0">暫停</option>
        <option value="1">第1預測</option>
        <option value="2">第2預測</option>
        <option value="3">第3預測</option>        
      </select>
        <br>
        香港
        <select name="readat[4]" size="1">
        <option selected value="<?php print $readat[4];?>">預設<?php print $readat[4];?></option>
        <option value="0">暫停</option>
        <option value="1">外援</option>
        <option value="2">原稿</option>
      </select>　韓國
      <select name="readat[5]" size="1">
      　<option selected value="<?php print $readat[5];?>">預設<?php print $readat[5];?></option>
        <option value="0">暫停</option>
        <option value="1">外援</option>
      </select>
      　中國<select name="readat[6]" size="1">
        <option selected value="<?php print $readat[6];?>">預設<?php print $readat[6];?></option>
        <option value="0">暫停</option>
        <option value="1">外援</option>
      </select>
      </select>　氣侯<select name="readat[7]" size="1">
        <option selected value="<?php print $readat[7];?>">預設<?php print $readat[7];?></option>
        <option value="1">開</option>
        <option value="0">關</option>
      </select>      </td>
    </tr><tr>
          <td bgcolor="#FFFF66">預報加權</td>       
          <td height="31" bgcolor="#FFFFFF">
          <div align="center">
          美國<input name="weight[0]" type="text" value="<?php print $weight[0];?>" size="3">
　         日本<input name="weight[1]" type="text" value="<?php print $weight[1];?>" size="3">
         　台灣<input name="weight[2]" type="text" value="<?php print $weight[2];?>" size="3">
      　   英國<input name="weight[3]" type="text" value="<?php print $weight[3];?>" size="3"><br>
          香港<input name="weight[4]" type="text" value="<?php print $weight[4];?>" size="3">　
          韓國
          <input name="weight[5]" type="text" value="<?php print $weight[5];?>" size="3">
         　中國<input name="weight[6]" type="text" value="<?php print $weight[6];?>" size="3">
         　氣侯<input name="weight[7]" type="text" value="<?php print $weight[7];?>" size="3">
          </div></td>
        </tr>
  </table>

          <strong>寫報告</strong> / 
          <input type="submit" name="button2" id="button2" value="修改設定" />
          <input name="task" type="hidden" id="task2" value="edit" />
          <input name="edited" type="hidden" id="edited2" value="yes" />
          <input name="id" type="hidden"  value="<?php print $id?>" />
          <input name="password" type="hidden" id="task4" value="<?php print $password ?>" />
        </form>
        <p>
          <?php }?>
        </p>
        <p><a href="1_tcinfo.php?password=<?php print $password?>">返回編輯首頁</a></p>
</div></td>
    </tr>
  </table>

 
</div>
</body>
</html>
<?php
function writein ($filename, $somecontent){

if (is_writable($filename)) {
   if (!$handle = fopen($filename, 'w')) {
         echo "Cannot open file ($filename)";
         exit;
   }
   if (fwrite($handle, $somecontent) === FALSE) {
       echo "Cannot write to file ($filename)";
       exit;
   }
  // echo "Success, wrote to file ($filename)";
   fclose($handle);
} else {
   echo "The file $filename is not writable";
	}
}?>
