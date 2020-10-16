#!/usr/bin/perl

use CGI;
use CGI::Carp (fatalsToBrowser);
use LWP::Simple;
$q = new CGI;

print "Content-type: text/html;CHARSET=utf8\n\n";
# 最後設計版本  2009-7-22 更新 (V 4.3 實測陣風,Bugfixed & 達到風級列出版)
#               2017-2-26 HKCOC 4.0 用的純文字版本, BIG5 to UTF8

$num='([0-9])';

$base	= "/home/freelances/domains/hkcoc.freelancesky.com/public_html";
$path   = "$base/wind/"; 
$home   = "$base/wind/";
$windcount = "$home/windcount.txt";
$msghtml   = "$home/wi_report.htm"; 

$page = get("http://www.weather.gov.hk/wxinfo/ts/text_readings_e.htm");
$page =~ s/\r//g;

($up,$mid,$low) = split(/pre>/, $page);
($header,$temp,$wind,$pres,$notes,$footer) = split(/\n\n/, $mid);
($head,$central,$clk,$cc,$ccb,$gl,$kt,$hko,$lmi,$lfs,$np,$northpt,$pc,$sk,$sc,$st,$sko,$stanley,$sf,$tkl,$tmt,$tpk,$tmo,$tc,$two,$ty,$tm,$wl,$wp,$wch) = split(/\n/, $wind);
# Central Pier, Chek Lap Kok, Cheung Chau, Cheung Chau Beach, Green Island, Kai Tak, King's Park, Lamma Island,Lau Fau Shan, Ngong Ping, North Point, Peng Chau, Sai Kung, Sha Chau, Sha Tin, Shek Kong, Sha Chau, Stanley, Star Ferry, Ta Kwu Ling, Tai Mei Tuk, Tai Po Kau, Tap Mun, Tate's Cairn, Tseung Kwan O, Tsing Yi, Tuen Mun, Waglan Island, Wetland Park, Wong Chuk Hang
# 中環碼頭,赤立角,長洲,長洲灘,青洲,啟德,京士柏,南丫島,流浮山,昂平,北角,坪洲,西貢,沙州,沙田,石崗,赤柱,天星碼頭,打鼓嶺,大尾篤,大埔坳,塔門,大老山,將軍澳,青衣,屯門,橫蘭,濕地公園,黃竹坑

@cityn = ("　　沙田","　京士柏","　黃竹坑","　　屯門","　將軍澳","　　啟德","　　青衣","　　天星");

if ($st =~/ (\d+)/){$st =~/ (\d+) +(\d+)/; $city[0]=$1; $cityg[0]=$2; $city++;}else{$city[0]=$cityg[0]=0;}   # 0.沙田
if ($hko =~/ (\d+)/){$hko =~/ (\d+) +(\d+)/; $city[1]=$1; $cityg[1]=$2; $city++;}else{$city[1]=$cityg[1]=0;} # 1.京士柏
if ($wch =~/ (\d+)/){$wch =~/ (\d+) +(\d+)/; $city[2]=$1; $cityg[2]=$2;$city++;}else{$city[2]=$cityg[2]=0;}  # 2.黃竹坑
if ($tm =~/ (\d+)/){$tm  =~/ (\d+) +(\d+)/; $city[3]=$1; $cityg[3]=$2; $city++;}else{$city[3]=$cityg[3]=0;}  # 3.屯門
if ($two =~/ (\d+)/){$two =~/ (\d+) +(\d+)/; $city[4]=$1; $cityg[4]=$2; $city++;}else{$city[4]=$cityg[4]=0;} # 4.將軍澳
if ($kt =~/ (\d+)/){$kt =~/ (\d+) +(\d+)/; $city[5]=$1; $cityg[5]=$2; $city++;}else{$city[5]=$city[5]=0;}    # 5.啟德
if ($ty =~/ (\d+)/){$ty =~/ (\d+) +(\d+)/; $city[6]=$1; $cityg[6]=$2; $city++;}else{$city[6]=$cityg[6]=0;}   # 6.青衣
if ($sf =~/ (\d+)/){$sf =~/ (\d+) +(\d+)/; $city[7]=$1; $cityg[7]=$2; $city++;}else{$city[7]=$cityg[7]=0;}   # 7.天星

@urbn = ("　打鼓嶺","　　長洲","　赤立角","　流浮山","　　西貢","濕地公園","　　石崗","　　橫欄","　　昂平","　大尾篤","　大老山","　　沙州","　　塔門","　　赤柱","　　青洲","　　坪洲","　大埔岰");

if ($tkl =~/ (\d+)/){$tkl =~/ (\d+) +(\d+)/; $urb[0]=$1; $urbg[0]=$2;}else{$urb[0]=$urbg[0]=0;}              # 0.打鼓嶺 *
if ($cc =~/ (\d+)/){$cc  =~/ (\d+) +(\d+)/; $urb[1]=$1; $urbg[1]=$2; $urban++;}else{$urb[1]=$urbg[1]=0;}     # 1.長州
if ($clk =~/ (\d+)/){$clk =~/ (\d+) +(\d+)/; $urb[2]=$1; $urbg[2]=$2; $urban++;}else{$urb[2]=$urbg[2]=0;}    # 2.赤立角
if ($lfs =~/ (\d+)/){$lfs =~/ (\d+) +(\d+)/; $urb[3]=$1; $urbg[3]=$2; $urban++;}else{$urb[3]=$urbg[3]=0;}    # 3.流浮山
if ($sk =~/ (\d+)/){$sk  =~/ (\d+) +(\d+)/; $urb[4]=$1; $urbg[4]=$2; $urban++;}else{$urb[4]=$urbg[4]=0;}     # 4.西頁
if ($wp =~/ (\d+)/){$wp =~/ (\d+) +(\d+)/; $urb[5]=$1; $urbg[5]=$2;}else{$urb[5]=$urbg[5]=0;}                # 5.濕地公園 *
if ($sko =~/ (\d+)/){$sko =~/ (\d+) +(\d+)/; $urb[6]=$1; $urbg[6]=$2;}else{$urb[6]=$urbg[6]=0;}              # 6.石崗 *
if ($wl =~/ (\d+)/){$wl =~/ (\d+) +(\d+)/; $urb[7]=$1; $urbg[7]=$2;}else{$urb[7]=$urbg[7]=0;}                # 7.橫欄 *
if ($np =~/ (\d+)/){$np =~/ (\d+) +(\d+)/; $urb[8]=$1; $urbg[8]=$2;}else{$urb[8]=$urbg[8]=0;}                # 8.昂平 *
if ($tmt =~/ (\d+)/){$tmt =~/ (\d+) +(\d+)/; $urb[9]=$1; $urbg[9]=$2; $urban++;}else{$urb[9]=$urbg[9]=0;}    # 9.大尾篤
if ($tc =~/ (\d+)/){$tc =~/ (\d+) +(\d+)/; $urb[10]=$1; $urbg[10]=$2;}else{$urb[10]=$urbg[10]=0;}            # 10. 大老山 *
if ($sc =~/ (\d+)/){$sc =~/ (\d+) +(\d+)/; $urb[11]=$1; $urbg[11]=$2; $urban++;}else{$urb[11]=$urbg[11]=0;}  # 11. 沙州
if ($tmo =~/ (\d+)/){$tmo =~/ (\d+) +(\d+)/; $urb[12]=$1; $urbg[12]=$2; $urban++;}else{$urb[12]=$urbg[12]=0;}   # 12. 塔門
if ($stanley =~/ (\d+)/){$stanley =~/ (\d+) +(\d+)/; $urb[13]=$1; $urbg[13]=$2; $urban++;}else{$urb[13]=$urbg[13]=0;} # 13. 赤柱
if ($gl =~/ (\d+)/){$gl =~/ (\d+) +(\d+)/; $urb[14]=$1; $urbg[14]=$2; $urban++;}else{$urb[14]=$urbg[14]=0;}  # 14.青州
if ($pc =~/ (\d+)/){$pc =~/ (\d+) +(\d+)/; $urb[15]=$1; $urbg[15]=$2; $urban++;}else{$urb[15]=$urbg[15]=0;}  # 15.坪州
if ($tpk =~/ (\d+)/){$tpk =~/ (\d+) +(\d+)/; $urb[16]=$1; $urbg[16]=$2; $urban++;}else{$urb[16]=$urbg[16]=0;}  # 16.大埔坳

$header =~ /recorded at (\d\d:\d\d) Hong Kong Time (.*) (.*) (....)/;

$readingtime = $1;
$yyy=$4; $mmm=$3; $ddd=$2;

if ($mmm eq "July"){$mmm = "07";}
elsif ($mmm eq "August"){$mmm = "08";}
elsif ($mmm eq "September"){$mmm = "09";}
elsif ($mmm eq "October"){$mmm = "10";}
elsif ($mmm eq "November"){$mmm = "11";}
elsif ($mmm eq "December"){$mmm = "12";}
elsif ($mmm eq "January"){$mmm = "01";}
elsif ($mmm eq "February"){$mmm = "02";}
elsif ($mmm eq "March"){$mmm = "03";}
elsif ($mmm eq "April"){$mmm = "04";}
elsif ($mmm eq "May"){$mmm = "05";}
elsif ($mmm eq "June"){$mmm = "06";}

# if ($mmm<10) {$mmm="0$mmm";}
if ($ddd<10) {$ddd="0$ddd";}
$readingtime =~ s/\D//g;
$readingdate = "$yyy$mmm$ddd";

$time = "$yyy 年 $mmm 月 $ddd 日 HKT $readingtime";

#@city = (25,30,20,22,30);
#@urb = (59,30,38,43,47);

@rawcity = @city; @rawurb = @urb;
@rawcityg = @cityg; @rawurbg = @urbg;

# @temp = @urb;
@temp = ($urb[1],$urb[2],$urb[3],$urb[4],$urb[9],$urb[11],$urb[12],$urb[14],$urb[13],$urb[15]);
@urb = ();
@urb = @temp;

@temp = ();
# @temp = @urbg;
@temp = ($urbg[1],$urbg[2],$urbg[3],$urbg[4],$urbg[9],$urbg[11],$urbg[12],$urbg[14],$urbg[13],$urbg[15]);
@urbg = ();
@urbg = @temp;

#####################   主計算程序   ###########################

($u,$udv,$usize) = sdev(@city);   #市區平均
($ug,$ugdv,$ugsize) = sdev(@cityg);   #市區陣風
# $ug = (2.28*$u)+10;               #市區陣風
# $ug = int($ug*10**2+0.5)/10**2;

$cityavg = $u;
$cityavg = int($cityavg*10**2+0.5)/10**2;
$citygavg = $ug;
$citygavg = int($citygavg*10**2+0.5)/10**2;

# if ($u <= 13) {$ug = 0;}
# if ($u > 13) {$cityg = "估計最高陣風 : $ug";}

($v,$vdv,$vsize) = sdev(@urb);    #空地平均
($vg,$vgdv,$vgsize) = sdev(@urbg);   #空地陣風
# $vg = (1.58*$v) + 10;             #空地陣風
# $vg = int($vg*10**2+0.5)/10**2;

$ubavg = $v;
$ubavg = int($ubavg*10**2+0.5)/10**2;
$ubgavg = $vg;
$ubgavg = int($ubgavg*10**2+0.5)/10**2;

# if ($v <= 13) {$vg = 0;}
# if ($v > 13) {$urbg = "估計最高陣風 : $vg";}


## 第一次風力指數計算 ##
$ur = gustfactor($u); $vr = gustfactor($v);
$wi = (0.56*((1-$ur)*$u+($ur*$ug))) + (0.44*((1-$vr)*$v+($vr*$vg)));

########################

## 第二次風力指數計算 ##
if ($wi >= 24){     # 如果WI大過24，移除異常
	$cityref = \@city; $urbref = \@urb;
	($u,$udv,$unewsize,@reudat1) = remove($cityref,$u,$udv);  # 移除異常值
	($v,$vdv,$vnewsize,@revdat2) = remove($urbref,$v,$vdv);

$ur = gustfactor($u); $vr = gustfactor($v);
$wi = (0.56*((1-$ur)*$u+($ur*$ug))) + (0.44*((1-$vr)*$v+($vr*$vg)));

########################
}

writewi($readingdate,$readingtime,$wi);
$wi= int($wi*10+0.5)/10;

if (($wi>= 0)and($wi< 14)) {
	$wd="輕微";$co="#008000";
	$mean="<font color=$co>本港境內風力微弱。</font>";
	}
if (($wi>= 14)and($wi< 25)) {
	$wd="清勁";$co="#0000FF";
	$mean="<font color=$co>市區風力和緩，陣風每小時40公里以上，<br>離岸及高地風力普遍達強風程度。</font>";
	}
if (($wi>= 25)and($wi< 45)) {
	$wd="強勁";$co="#FF0000";
	$mean="<font color=$co>市區風力清勁，陣風可達每小時60公里以上，<br>離岸及高地風力普遍達強風程度，部份地區更可出現烈風風力。</font>";
	}
if (($wi>= 45)and($wi< 75)) {
	$wd="強烈";$co="#FF00FF";
	$mean="<font color=$co>市區普遍吹強風，陣風可達每小時100公里，<br>離岸及高地普遍吹烈風，部份地區更可能受到暴風的吹襲。</font>";
	}
if ($wi>= 75) {
	$wd="極強";$co="#800040";
	$mean="<font color=$co>市區部份區域受烈風影響，陣風頻密並可能超過每小時118公里。<br>離岸及高地風力可普遍達暴風至颶風水平。</font>"; 
	}

$report = "<strong><big><font color=$co> $wi km/h (程度屬$wd)</font></big></strong>";

#######################  寫入檔案   ##########################

$nocityg1 = $nocityg2 = $nocityg3 = $nourbg1 = $nourbg2 = $nourbg3 = 0;

# 市區達標站數
$totalcity = 8;
for ($i=0; $i<=$#rawcity; $i++){
	if ($rawcity[$i]<=40){$fontcol = "";} 
	elsif (($rawcity[$i]>40) and ($rawcity[$i]<=63)){$fontcol = "FF0000"; $nocityg1++;} 
	elsif (($rawcity[$i]>63) and ($rawcity[$i]<=103)){$fontcol = "FF00FF"; $nocityg2++;$nocityg1++;}
	elsif ($rawcity[$i]>103){$fontcol = "660000"; $nocityg3++;$nocityg2++;$nocityg1++;} 
	
	if (($rawcity[$i]<10) and ($rawcity[$i]!=0)){
		$rawcity[$i] = "&nbsp;$rawcity[$i]";
	} elsif ($rawcity[$i]==0){
		$rawcity[$i] = "&nbsp;-";
		$rawcityg[$i] = "&nbsp;-";
		$totalcity--;
	} 
	$rawcity[$i] = "<font color = $fontcol>$rawcity[$i] km/h</font>";
	$rawcityg[$i] .= " km/h";
	$rawcity[$i] .= $reudat1[$i];
}

# 離岸及高地達標站數
$totalurb = 17;
for ($i=0; $i<=$#rawurb; $i++){
	if ($rawurb[$i]<=40){$fontcol = "";} 
	elsif (($rawurb[$i]>40) and ($rawurb[$i]<=63)){$fontcol = "FF0000"; $nourbg1++;} 
	elsif (($rawurb[$i]>63) and ($rawurb[$i]<=103)){$fontcol = "FF00FF"; $nourbg2++;$nourbg1++;}
	elsif ($rawurb[$i]>103){$fontcol = "660000"; $nourbg3++;$nourbg2++;$nourbg1++;}	
		
	if (($rawurb[$i]<10) and ($rawurb[$i]!=0)){
		$rawurb[$i] = "&nbsp;$rawurb[$i]";
	} elsif ($rawurb[$i]==0){
		$rawurb[$i] = "&nbsp;-";
		$rawurbg[$i] = "&nbsp;-";
		$totalurb--;
	}
	$rawurb[$i] = "<font color = $fontcol>$rawurb[$i] km/h</font>";
	$rawurbg[$i] .= " km/h";
	$rawurb[$i] .= $revdat2[$i];
}

if ($nocityg1 > 0){
	$cityg1per = int(($nocityg1/$totalcity)*100);	
	$citystat = "<font color=FF0000 size=-1>$nocityg1 個市區站強風或以上風力 (覆率: $cityg1per %)</font><br>";}
if ($nocityg2 > 0){
	$cityg2per = int(($nocityg2/$totalcity)*100);
	$citystat .= "<font color=FF00FF size=-1>$nocityg2 個市區站烈風或以上風力 (覆率: $cityg2per %)</font><br>";}
if ($nocityg3 > 0){
	$cityg3per = int(($nocityg3/$totalcity)*100);
	$citystat .= "<font color=660000 size=-1>$nocityg3 個市區站暴風或以上風力 (覆率: $cityg3per %)</font><br>";}

if ($nourbg1 > 0){
	$urbg1per = int(($nourbg1/$totalurb)*100);	
	$urbstat = "<font color=FF0000 size=-1>$nourbg1 個離岸及高地站強風或以上風力 (覆率: $urbg1per %)</font><br>";}
	else {$urbstat = "<font size=-1>本港沒有錄得強風或以上風力</font><br>";}
if ($nourbg2 > 0){
	$urbg2per = int(($nourbg2/$totalurb)*100);
	$urbstat .= "<font color=FF00FF size=-1>$nourbg2 個離岸及高地站烈風或以上風力 (覆率: $urbg2per %)</font><br>";}
if ($nourbg3 > 0){
	$urbg3per = int(($nourbg3/$totalurb)*100);
	$urbstat .= "<font color=660000 size=-1>$nourbg3 個離岸及高地站暴風或以上風力 (覆率: $urbg3per %)</font><br>";}	

# 寫入警告狀態	
open(F, ">$windcount") or die "can't open $windcount";  	
    print F "$citystat<br>$urbstat"; 	
	close(F) or die "can't close $windcount";	

# 預備訊息

	$ft = "";		
	$ft .= "<p>更新時間: $time</p>";
    $ft .= "<ul class=boxul><li>最新風力指數</li></ul>";
	$ft .= "<a href = http://www.hkcoc.com/wind/test/windindex.png target=_blank><img src= http://www.hkcoc.com/wind/test/windindex.png?v=$rawurbg[1]></a><br><br>";
	$ft .= "$report<br>";
	$ft .= "$mean<br><br>"; 

	$ft .= "<ul class=boxul><li>市區及新市鎮風力讀數</li></ul>";
	
	$ft .= "<pre>";
	for ($i=0; $i<=$#rawcity; $i++)
	{
		#$ft .= "$cityn[$i] $rawcity[$i] $rawcityg[$i]<br>";
		$ft .= sprintf '%10s %14s %10s %s', $cityn[$i], $rawcity[$i], $rawcityg[$i], "<br>" ;
	}
	$ft .= "</pre>";
	
	$ft .= "<br>平均持續風力: $cityavg km/h";
	if ($citygavg ne ""){$ft .= " (平均陣風: $citygavg km/h)";}
	$ft .= "<br><br>$citystat<br>";

	$ft .= "<ul class=boxul><li>離岸及高地風力讀數</li></ul>";

	$ft .= "<pre>";	
	for ($i=0; $i<=$#urbn; $i++)
	{
		#$ft .= "$urbn[$i] $rawurb[$i] $rawurbg[$i]<br>";
		$ft .= sprintf '%10s %14s %10s %s', $urbn[$i], $rawurb[$i], $rawurbg[$i], "<br>" ;
	}
	$ft .= "</pre>";	
	
	$ft .= "<br>平均持續風力: $ubavg km/h";
	if ($ubgavg ne ""){$ft .= " (平均陣風: $ubgavg km/h)";}
	$ft .= "<br><br>$urbstat<br>";

# 寫入訊息	
	open(F, ">$msghtml") or die "can't open $msghtml";
	print F $ft;	
	print $ft;
	close(F) or die "can't close $msghtml";

# 	print '<meta http-equiv="refresh" content="0;url=http://hkcoc.weather.com.hk/wind/test/wind.htm">';

##############################################################
sub gustfactor {    # 計算陣風比重 (avg)
	my $avg = $_[0];
	my $gustr;
	
	$gustr = ((-0.00005)*($avg**2)) + (0.0081 * $avg ) - 0.0005;
	if ($avg > 81){$gustr = 0.327;}	
	return $gustr;	
}

##############################################################
sub sdev {  # 計算標準差及平均 (data array)
    my @data; 
    my ($size,$avg,$dev,$total);
    @data = @_;
    
    foreach $data(@data){
	    $total += $data;
	    if ($data > 0 ){
		    $size ++
	    }
    }
    if ($size == 0){$size = 1;}
    $avg = $total/$size;
    
    foreach $data(@data){
	    if ($data > 0 ){
		    $dev += (($data - $avg)**2);
	    }	
    }
    
    $size2 = $size;
    if ($size2 == 1){$size2 = 2;}
    $dev /= ($size2 - 1);
    $dev = ($dev ** 0.5); 
    return ($avg , $dev , $size);
}
##############################################################
sub remove {  # 移除極端值  (array ref,dev,avg)
	#$dataref = \@ddd;
	$dataref = $_[0]; $dev = $_[2]; $avg = $_[1];
	my @data = @{$dataref};
	my @redat;		
	
	for ($i=0;$i<=$#data;$i++){
		$check = $data[$i] - $avg;
		if ($check < 0){$check *= -1;}
		if ($check > $dev){
			$data[$i] = 0;
			$redat[$i] = "*";
		}					
	}
	($newavg,$newdev,$newsize) = sdev(@data);
	return ($newavg,$newdev,$newsize,@redat);
}

##############################################################
sub writewi {
	$file = "$path/newwi.txt";
	$rec  = "$path/windindex.txt";
	
	$readingdate = $_[0];
	$readingtime = $_[1];
	$windindex = $_[2];
	
	#
	open (WI, "$file") or die " can open $file";
    @lines = <WI>;
    close (WI);
    chomp(@lines);    

    if (($lines[$#lines] !~ /^$readingdate $readingtime /) and ($lines[$#lines-1] !~ /^$readingdate $readingtime /) and ($readingdate) and ($readingtime) and ($windindex > 0)) {
      open (WI, ">> $file");
      print WI "$readingdate $readingtime $windindex\n";
      close (WI);
      
      open (W, "> $rec");
      print W "$windindex\n";
      close (W);
      }
}