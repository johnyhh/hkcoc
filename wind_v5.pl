#!/usr/bin/perl

##
##  "C:\Users\johny\Desktop\Temp\xampp_johny\perl\bin\perl.exe"
##

use CGI;
use CGI::Carp (fatalsToBrowser);
use Math::Trig;
# use LWP::Simple;
# alternative get() to support https
use HTTP::Tiny;
sub get { return HTTP::Tiny->new->get($_[0])->{content}; }
$q = new CGI;
#require "wiconfig.pl";

# 最後設計版本  2009-07-22 更新 (V 4.3 實測陣風,Bugfixed & 達到風級列出版)
#              2017-02-26 HKCOC 4.0 用的純文字版本, BIG5 to UTF8
#              2022-07-01 LiAhSheep update: 減少四捨五入次數
#              2022-07-15 更新 (V 5.0 重寫網頁解析方法,動態獲取全氣象站數據)
#              2022-08-19 加入平均風向
#              2023-05-15 修正平均風向 bug
#              2023-09-01 修正讀取風速的規則式問題
#              2024-07-18 LiAhSheep update: 使用360度風向 (latestReadings_AWS1_v2.txt)
#              2024-11-14 取消移除極端值

print "Content-type: text/html;CHARSET=utf8\n\n";

# Config
$output_file = 1; # 要唔要輸出txt/htm檔案 (0 = chat.weather.com.hk, 1 = hkcoc.com)
$v4 = 0; # (0 = 用最新計法, 1 = 用v4計法)
$wind360 = 1; # (0 = 用文字判斷風向, 1 = 用360度風向)

$base  = "/home/freelances/domains/hkcoc.freelancesky.com/public_html/";
#$base = ".";
$path   = "$base/wind/";
$home   = "$base/wind/";
$windcount = "$home/windcount.txt";
$msghtml   = "$home/wi_report.htm";
$file  = "$path/newwi.txt";
$file2 = "$path/newwdir.txt";
$rec   = "$path/windindex.txt";

if ($wind360) {
    $url = "https://www.weather.gov.hk/wxinfo/awsgis/latestReadings_AWS1_v2.txt";
} else {
    $url = "https://www.weather.gov.hk/wxinfo/ts/text_readings_e.htm";
    #$url = "http://localhost:81/wind_sample.htm";
}
$page = get($url);
die "Can't GET $url" if (! defined $page);

$page =~ s/\r//g;

# V5 風站資訊  ename, cname, type, w 風力, g 陣風, d 風向(方位角), wiu 納入風指計算
# LiAhSheep 2024-07-18: 風速有可能係0，所以預設係-1
# https://docs.google.com/spreadsheets/d/1cTDLBf5eu6uUvBasEa4UciGpn7mN_vKT6eobt3xehPA/edit#gid=0
%st0  = ('code' => 'CP1', 'ename' => 'Central Pier',         'cname' => '中環碼頭', 'type' => 'city',  'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>0);  $st[0] = \%st0;
%st1  = ('code' => 'HKA', 'ename' => 'Chek Lap Kok',         'cname' => '　赤鱲角', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[1] = \%st1;
%st2  = ('code' => 'CCH', 'ename' => 'Cheung Chau   ',       'cname' => '　　長洲', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[2] = \%st2;
%st3  = ('code' => 'CCB', 'ename' => 'Cheung Chau Beach',    'cname' => '長洲泳灘', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>0);  $st[3] = \%st3;
%st4  = ('code' => 'GI',  'ename' => 'Green Island',         'cname' => '　　青洲', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[4] = \%st4;
%st5  = ('code' => 'SE',  'ename' => 'Kai Tak',              'cname' => '　　啟德', 'type' => 'city',  'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[5] = \%st5;
%st6  = ('code' => 'KP',  'ename' => 'King\'s Park',         'cname' => '　京士柏', 'type' => 'city',  'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[6] = \%st6;
%st7  = ('code' => 'LAM', 'ename' => 'Lamma Island',         'cname' => '　南丫島', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>0);  $st[7] = \%st7;
%st8  = ('code' => 'LFS', 'ename' => 'Lau Fau Shan',         'cname' => '　流浮山', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[8] = \%st8;
%st9  = ('code' => 'NGP', 'ename' => 'Ngong Ping',           'cname' => '　　昂坪', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>0);  $st[9] = \%st9;
%st10 = ('code' => 'NP',  'ename' => 'North Point',          'cname' => '　　北角', 'type' => 'city',  'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>0);  $st[10] = \%st10;
%st11 = ('code' => 'PEN', 'ename' => 'Peng Chau',            'cname' => '　　坪洲', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[11] = \%st11;
%st12 = ('code' => 'SKG', 'ename' => 'Sai Kung',             'cname' => '　　西貢', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[12] = \%st12;
%st13 = ('code' => 'SC',  'ename' => 'Sha Chau',             'cname' => '　　沙洲', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[13] = \%st13;
%st14 = ('code' => 'SHA', 'ename' => 'Sha Tin',              'cname' => '　　沙田', 'type' => 'city',  'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[14] = \%st14;
%st15 = ('code' => 'SEK', 'ename' => 'Shek Kong',            'cname' => '　　石崗', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>0);  $st[15] = \%st15;
%st16 = ('code' => 'STY', 'ename' => 'Stanley',              'cname' => '　　赤柱', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[16] = \%st16;
%st17 = ('code' => 'SF',  'ename' => 'Star Ferry',           'cname' => '天星碼頭', 'type' => 'city',  'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[17] = \%st17;
%st18 = ('code' => 'TKL', 'ename' => 'Ta Kwu Ling',          'cname' => '　打鼓嶺', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>0);  $st[18] = \%st18;
%st19 = ('code' => 'PLC', 'ename' => 'Tai Mei Tuk',          'cname' => '　大美督', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[19] = \%st19;
%st20 = ('code' => 'TPK', 'ename' => 'Tai Po Kau',           'cname' => '　大埔滘', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[20] = \%st20;
%st21 = ('code' => 'TME', 'ename' => 'Tap Mun',              'cname' => '　　塔門', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[21] = \%st21;
%st22 = ('code' => 'TC',  'ename' => 'Tate\'s Cairn',        'cname' => '　大老山', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[22] = \%st22;
%st23 = ('code' => 'JKB', 'ename' => 'Tseung Kwan O',        'cname' => '　將軍澳', 'type' => 'city',  'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[23] = \%st23;
%st24 = ('code' => 'TY1', 'ename' => 'Tsing Yi',             'cname' => '　　青衣', 'type' => 'city',  'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[24] = \%st24;
%st25 = ('code' => 'TU1', 'ename' => 'Tuen Mun',             'cname' => '　　屯門', 'type' => 'city',  'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[25] = \%st25;
%st26 = ('code' => 'WGL', 'ename' => 'Waglan Island',        'cname' => '　橫瀾島', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>0);  $st[26] = \%st26;
%st27 = ('code' => 'WLP', 'ename' => 'Wetland Park',         'cname' => '濕地公園', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>0);  $st[27] = \%st27;
%st28 = ('code' => 'HKS', 'ename' => 'Wong Chuk Hang',       'cname' => '　黃竹坑', 'type' => 'city',  'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>1);  $st[28] = \%st28;
%st29 = ('code' => 'HSS', 'ename' => 'Hong Kong Sea School', 'cname' => '航海學校', 'type' => 'urban', 'w'=>-1,'g'=>-1,'d'=>0,'wiu'=>0);  $st[29] = \%st29;

## v5 debug
debug_windstation();

## old code start
if ($page =~ /pre>/) {
    ($up, $mid, $low) = split(/pre>/, $page);
    ($header, $temp, $wind, $pres, $notes, $footer) = split(/\n\n/, $mid);
    @lines = split(/\n/, $wind);
} else {
    @lines = split(/\n/, $page);
}
## old code end

print "<h1>==Retreived data==</h2>";
print "Get $url<br>";
print "<pre>";
foreach $line(@lines) {
    print $line;
    getdatafrline($line);
    print"\n";
}
print "</pre>";

print "<h1>==Intermedium data==</h2>";## v5 debug
print "<h3>所有市區站</h3>count=\$totalcity & 風 \@rawcity & 陣風 \@rawcityg <br>";## v5 debug
@rawcity = &getcountbykey("w","city",0);
@cityn   = &getcountbykey("cname","city",0);
$totalcity=$#rawcity;
print "count=$#rawcity<br>";## v5 debug
print "風 \@rawcity= @rawcity <br>";## v5 debug
@rawcityg = &getcountbykey("g","city",0);
print "陣風 \@rawcityg= @rawcityg <br>";## v5 debug

print "<h3>納入風指計算的市區站</h3>count=\$city & 風 \@city & 陣風 \@cityg <br>";## v5 debug
@city = &getcountbykey("w","city",1);
print "count=$#city<br>";## v5 debug
print "風 \@city= @city <br>";## v5 debug
@cityg = &getcountbykey("g","city",1);
print "陣風 \@cityg= @cityg <br>";## v5 debug

print "<h3>所有離岸高地站</h3>count=\$totalurb & 風 \@rawurb & 陣風 \@rawurbg <br>";## v5 debug
@rawurb = &getcountbykey("w","urban",0);
@urbn   = &getcountbykey("cname","urban",0);
$totalurb=$#rawurb;
print "count=$totalurb<br>";## v5 debug
print "風 \@rawurb= @rawurb <br>";## v5 debug
@rawurbg = &getcountbykey("g","urban",0);
print "陣風 \@rawurbg= @rawurbg <br>";## v5 debug

print "<h3>納入風指計算的離岸高地站</h3>count=\$urban & 風 \@urb & 陣風 \@urbg<br>";## v5 debug
@urb = &getcountbykey("w","urban",1);## v5 debug
print "temp=$#urb<br>";## v5 debug
print "風 \@urb= @urb <br>";## v5 debug
@urbg = &getcountbykey("g","urban",1);
print "陣風 \@urbg= @urbg <br>";## v5 debug

print "<h3>風向計算</h3>";## v5 debug
avgdir();

# exit 0;

## legacy code (v4)
if ($v4) {
    @cityn = ("　　沙田","　京士柏","　黃竹坑","　　屯門","　將軍澳","　　啟德","　　青衣","　　天星");

    if ($st =~/ (\d+)/) {$st =~/ (\d+)    +(\d+)/; $city[0]=$1; $cityg[0]=$2; $city++;}else{$city[0]=$cityg[0]=0;}   # 0.沙田
    if ($hko =~/ (\d+)/) {$hko =~/ (\d+)    +(\d+)/; $city[1]=$1; $cityg[1]=$2; $city++;}else{$city[1]=$cityg[1]=0;} # 1.京士柏
    if ($wch =~/ (\d+)/) {$wch =~/ (\d+)    +(\d+)/; $city[2]=$1; $cityg[2]=$2;$city++;}else{$city[2]=$cityg[2]=0;}  # 2.黃竹坑
    if ($tm =~/ (\d+)/) {$tm  =~/ (\d+)    +(\d+)/; $city[3]=$1; $cityg[3]=$2; $city++;}else{$city[3]=$cityg[3]=0;}  # 3.屯門
    if ($two =~/ (\d+)/) {$two =~/ (\d+)    +(\d+)/; $city[4]=$1; $cityg[4]=$2; $city++;}else{$city[4]=$cityg[4]=0;} # 4.將軍澳
    if ($kt =~/ (\d+)/) {$kt =~/ (\d+)    +(\d+)/; $city[5]=$1; $cityg[5]=$2; $city++;}else{$city[5]=$city[5]=0;}    # 5.啟德
    if ($ty =~/ (\d+)/) {$ty =~/ (\d+)    +(\d+)/; $city[6]=$1; $cityg[6]=$2; $city++;}else{$city[6]=$cityg[6]=0;}   # 6.青衣
    if ($sf =~/ (\d+)/) {$sf =~/ (\d+)    +(\d+)/; $city[7]=$1; $cityg[7]=$2; $city++;}else{$city[7]=$cityg[7]=0;}   # 7.天星

    @urbn = ("　打鼓嶺","　　長洲","　赤立角","　　西貢","濕地公園","　　石崗","　　橫欄","　　昂平","　大尾篤","　大老山","　　沙州","　　塔門","　　赤柱","　　青洲","　　坪洲","　大埔岰");

    if ($tkl =~/ (\d+)/) {$tkl =~/ (\d+)    +(\d+)/; $urb[0]=$1; $urbg[0]=$2;}else{$urb[0]=$urbg[0]=0;}              # 0.打鼓嶺 *
    if ($cc =~/ (\d+)/) {$cc  =~/ (\d+)    +(\d+)/; $urb[1]=$1; $urbg[1]=$2; $urban++;}else{$urb[1]=$urbg[1]=0;}     # 1.長州
    if ($clk =~/ (\d+)/) {$clk =~/ (\d+)    +(\d+)/; $urb[2]=$1; $urbg[2]=$2; $urban++;}else{$urb[2]=$urbg[2]=0;}    # 2.赤立角
    if ($lfs =~/ (\d+)/) {$lfs =~/ (\d+)    +(\d+)/; $urb[3]=$1; $urbg[3]=$2; $urban++;}else{$urb[3]=$urbg[3]=0;}    # 3.流浮山
    if ($sk =~/ (\d+)/) {$sk  =~/ (\d+)    +(\d+)/; $urb[4]=$1; $urbg[4]=$2; $urban++;}else{$urb[4]=$urbg[4]=0;}     # 4.西頁
    if ($wp =~/ (\d+)/) {$wp =~/ (\d+)    +(\d+)/; $urb[5]=$1; $urbg[5]=$2;}else{$urb[5]=$urbg[5]=0;}                # 5.濕地公園 *
    if ($sko =~/ (\d+)/){$sko =~/ (\d+)    +(\d+)/; $urb[6]=$1; $urbg[6]=$2;}else{$urb[6]=$urbg[6]=0;}              # 6.石崗 *
    if ($wl =~/ (\d+)/){$wl =~/ (\d+)    +(\d+)/; $urb[7]=$1; $urbg[7]=$2;}else{$urb[7]=$urbg[7]=0;}                # 7.橫欄 *
    if ($np =~/ (\d+)/){$np =~/ (\d+)    +(\d+)/; $urb[8]=$1; $urbg[8]=$2;}else{$urb[8]=$urbg[8]=0;}                # 8.昂平 *
    if ($tmt =~/ (\d+)/){$tmt =~/ (\d+)    +(\d+)/; $urb[9]=$1; $urbg[9]=$2; $urban++;}else{$urb[9]=$urbg[9]=0;}    # 9.大尾篤
    if ($tc =~/ (\d+)/){$tc =~/ (\d+)    +(\d+)/; $urb[10]=$1; $urbg[10]=$2;}else{$urb[10]=$urbg[10]=0;}            # 10. 大老山 *
    if ($sc =~/ (\d+)/){$sc =~/ (\d+)    +(\d+)/; $urb[11]=$1; $urbg[11]=$2; $urban++;}else{$urb[11]=$urbg[11]=0;}  # 11. 沙州
    if ($tmo =~/ (\d+)/){$tmo =~/ (\d+)    +(\d+)/; $urb[12]=$1; $urbg[12]=$2; $urban++;}else{$urb[12]=$urbg[12]=0;}   # 12. 塔門
    if ($stanley =~/ (\d+)/){$stanley =~/ (\d+)    +(\d+)/; $urb[13]=$1; $urbg[13]=$2; $urban++;}else{$urb[13]=$urbg[13]=0;} # 13. 赤柱
    if ($gl =~/ (\d+)/){$gl =~/ (\d+)    +(\d+)/; $urb[14]=$1; $urbg[14]=$2; $urban++;}else{$urb[14]=$urbg[14]=0;}  # 14.青州
    if ($pc =~/ (\d+)/){$pc =~/ (\d+)    +(\d+)/; $urb[15]=$1; $urbg[15]=$2; $urban++;}else{$urb[15]=$urbg[15]=0;}  # 15.坪州
    if ($tpk =~/ (\d+)/){$tpk =~/ (\d+)    +(\d+)/; $urb[16]=$1; $urbg[16]=$2; $urban++;}else{$urb[16]=$urbg[16]=0;}  # 16.大埔坳
}

$page =~ /recorded at (\d\d:\d\d) Hong Kong Time (.*) (.*) (....)/;

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

## legacy code (v4)
if ($v4) {
    @rawcity = @city; @rawurb = @urb;
    @rawcityg = @cityg; @rawurbg = @urbg;

    # @temp = @urb;
    @temp = (0,$urb[1],$urb[2],$urb[3],$urb[4],0,0,0,0,$urb[9],0,$urb[11],$urb[12],$urb[13],$urb[14],$urb[15],0);
    @urb = ();
    @urb = @temp;

    @temp = ();
    # @temp = @urbg;
    @temp = (0,$urbg[1],$urbg[2],$urbg[3],$urbg[4],0,0,0,0,$urbg[9],0,$urbg[11],$urbg[12],$urbg[13],$urbg[14],$urbg[15],0);
    @urbg = ();
    @urbg = @temp;
}

#####################   主計算程序   ###########################

($u,$udv,$usize) = sdev(@city);   #市區平均
($ug,$ugdv,$ugsize) = sdev(@cityg);   #市區陣風
# $ug = (2.28*$u)+10;               #市區陣風
# $ug = int($ug*100+0.5)/100;

$cityavg = $u;
$cityavg_2dp = int($cityavg*100+0.5)/100;
$citygavg = $ug;
$citygavg_2dp = int($citygavg*100+0.5)/100;

# if ($u <= 13) {$ug = 0;}
# if ($u > 13) {$cityg = "估計最高陣風 : $ug";}

($v,$vdv,$vsize) = sdev(@urb);    #空地平均
($vg,$vgdv,$vgsize) = sdev(@urbg);   #空地陣風
# $vg = (1.58*$v) + 10;             #空地陣風
# $vg = int($vg*100+0.5)/100;

$ubavg = $v;
$ubavg_2dp = int($ubavg*100+0.5)/100;
$ubgavg = $vg;
$ubgavg_2dp = int($ubgavg*100+0.5)/100;

# if ($v <= 13) {$vg = 0;}
# if ($v > 13) {$urbg = "估計最高陣風 : $vg";}

## 第一次風力指數計算 ##
$ur = gustfactor($u); $vr = gustfactor($v);
$wi = (0.56*((1-$ur)*$u+($ur*$ug))) + (0.44*((1-$vr)*$v+($vr*$vg)));
$wio = $wi;

print "<br>u=$u, udv=$udv, usize=$usize";
print "<br>ug=$ug, ugdv=$ugdv, ugsize=$ugsize";
print "<br>v=$v, vdv=$vdv, vsize=$vsize";
print "<br>vg=$vg, vgdv=$vgdv, vgsize=$vgsize";
print "<br>wi=$wi";

########################

## 第二次風力指數計算 ##
if ($wi >= 24){     # 如果WI大過24，移除異常
    $cityref = \@city; $urbref = \@urb;
    ($u,$udv,$unewsize,@reudat1) = remove($cityref,$u,$udv);  # 移除異常值
    ($v,$vdv,$vnewsize,@revdat2) = remove($urbref,$v,$vdv);

    $ur = gustfactor($u); $vr = gustfactor($v);
    $wi = (0.56*((1-$ur)*$u+($ur*$ug))) + (0.44*((1-$vr)*$v+($vr*$vg)));
    print "<br>recalc, wi=$wi";
}

########################

print "<h1>==Final output==</h2>";## v5 debug
if ($output_file) {
    writewi($readingdate,$readingtime,$wio);
    writedir($readingdate,$readingtime);
}
$wi = int($wi*10+0.5)/10;
$wio = int($wio*10+0.5)/10;

if (($wio>= 0)and($wio<= 13)) {
    $wd="輕微";$co="#008000";
    $mean="<font color=$co>本港境內風力微弱。</font>";
}
if (($wio> 13)and($wio<= 24)) {
    $wd="清勁";$co="#0000FF";
    $mean="<font color=$co>市區風力和緩，陣風每小時40公里以上，<br>離岸及高地風力普遍達強風程度。</font>";
}
if (($wio> 24)and($wio<= 44)) {
    $wd="強勁";$co="#FF0000";
    $mean="<font color=$co>市區風力清勁，陣風可達每小時60公里以上，<br>離岸及高地風力普遍達強風程度，部份地區更可出現烈風風力。</font>";
}
if (($wio> 44)and($wio<= 74)) {
    $wd="強烈";$co="#FF00FF";
    $mean="<font color=$co>市區普遍吹強風，陣風可達每小時100公里，<br>離岸及高地普遍吹烈風，部份地區更可能受到暴風的吹襲。</font>";
}
if ($wio> 74) {
    $wd="極強";$co="#800040";
    $mean="<font color=$co>市區部份區域受烈風影響，陣風頻密並可能超過每小時118公里。<br>離岸及高地風力可普遍達暴風至颶風水平。</font>";
}

#if ($wio >= 24){
	$report = "<strong><big><font color=$co> $wio km/h (程度屬$wd)</font></big></strong> 平均風向：$avgdirtxt ($avgdir&deg) / 移除極端值之風力指數：$wi";
#} else {
#	$report = "<strong><big><font color=$co> $wio km/h (程度屬$wd)</font></big></strong> 平均風向：$avgdirtxt ($avgdir&deg)";
#)

#######################  寫入檔案   ##########################

$nocityg1 = $nocityg2 = $nocityg3 = $nourbg1 = $nourbg2 = $nourbg3 = 0;

# 市區達標站數
if ($v4) { $totalcity = 8; }

for ($i=0; $i<=$#rawcity; $i++){
    if ($rawcity[$i]<=40){$fontcol = "";}
    elsif (($rawcity[$i]>40) and ($rawcity[$i]<=63)){$fontcol = "FF0000"; $nocityg1++;}
    elsif (($rawcity[$i]>63) and ($rawcity[$i]<=103)){$fontcol = "FF00FF"; $nocityg2++;$nocityg1++;}
    elsif ($rawcity[$i]>103){$fontcol = "660000"; $nocityg3++;$nocityg2++;$nocityg1++;}

    if (($rawcity[$i]<10) and ($rawcity[$i]!=-1)){
        $rawcity[$i] = " $rawcity[$i]";
    } elsif ($rawcity[$i]==-1){
        $rawcity[$i] = " -";
        $rawcityg[$i] = " -";
        $totalcity--;
    }
    $rawcity[$i] = "<font color = $fontcol>$rawcity[$i] km/h</font>";
    $rawcityg[$i] .= " km/h";
    $rawcity[$i] .= $reudat1[$i];
}

# 離岸及高地達標站數
if ($v4) { $totalurb = 17; }

for ($i=0; $i<=$#rawurb; $i++){
    if ($rawurb[$i]<=40){$fontcol = "";}
    elsif (($rawurb[$i]>40) and ($rawurb[$i]<=63)){$fontcol = "FF0000"; $nourbg1++;}
    elsif (($rawurb[$i]>63) and ($rawurb[$i]<=103)){$fontcol = "FF00FF"; $nourbg2++;$nourbg1++;}
    elsif ($rawurb[$i]>103){$fontcol = "660000"; $nourbg3++;$nourbg2++;$nourbg1++;}

    if (($rawurb[$i]<10) and ($rawurb[$i]!=-1)){
        $rawurb[$i] = " $rawurb[$i]";
    } elsif ($rawurb[$i]==-1){
        $rawurb[$i] = " -";
        $rawurbg[$i] = " -";
        $totalurb--;
    }
    $rawurb[$i] = "<font color = $fontcol>$rawurb[$i] km/h</font>";
    $rawurbg[$i] .= " km/h";
    $rawurb[$i] .= $revdat2[$i];
}

if ($nocityg1 > 0) {
    $cityg1per = int(($nocityg1/$totalcity)*100);
    $citystat = "<font color=FF0000 size=-1>$nocityg1 個市區站強風或以上風力 (覆率: $cityg1per %)</font><br>";
}
if ($nocityg2 > 0) {
    $cityg2per = int(($nocityg2/$totalcity)*100);
    $citystat .= "<font color=FF00FF size=-1>$nocityg2 個市區站烈風或以上風力 (覆率: $cityg2per %)</font><br>";
}
if ($nocityg3 > 0) {
    $cityg3per = int(($nocityg3/$totalcity)*100);
    $citystat .= "<font color=660000 size=-1>$nocityg3 個市區站暴風或以上風力 (覆率: $cityg3per %)</font><br>";
}
if ($nourbg1 > 0) {
    $urbg1per = int(($nourbg1/$totalurb)*100);
    $urbstat = "<font color=FF0000 size=-1>$nourbg1 個離岸及高地站強風或以上風力 (覆率: $urbg1per %)</font><br>";
} else {
    $urbstat = "<font size=-1>本港沒有錄得強風或以上風力</font><br>";
}
if ($nourbg2 > 0) {
    $urbg2per = int(($nourbg2/$totalurb)*100);
    $urbstat .= "<font color=FF00FF size=-1>$nourbg2 個離岸及高地站烈風或以上風力 (覆率: $urbg2per %)</font><br>";
}
if ($nourbg3 > 0) {
    $urbg3per = int(($nourbg3/$totalurb)*100);
    $urbstat .= "<font color=660000 size=-1>$nourbg3 個離岸及高地站暴風或以上風力 (覆率: $urbg3per %)</font><br>";
}

# 寫入警告狀態
if ($output_file) {
    open(F, ">$windcount") or die "can't open $windcount";
    print F "$citystat<br>$urbstat";
    close(F) or die "can't close $windcount";
}

# 預備訊息

$ft = "";
$ft .= "<p>更新時間: $time</p>";
$ft .= "<ul class=boxul><li>最新風力指數</li></ul>";
$ft .= "<a href='https://chat.weather.com.hk/wind-index/graph.png' target=_blank><img src='https://chat.weather.com.hk/wind-index/graph.png?v=$rawurbg[1]' width=800></a><br><br>";
$ft .= "<a href='https://chat.weather.com.hk/wind-index/graph_directions.png' target=_blank><img src='https://chat.weather.com.hk/wind-index/graph_directions.png?v=$rawurbg[1]' width=610></a><br><br>";
$ft .= "<img src='https://www.hko.gov.hk/wxinfo/ts/windchk.png'></a><br><br>";


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

$ft .= "<br>平均持續風力: $cityavg_2dp km/h";
if ($citygavg ne "") {$ft .= " (平均陣風: $citygavg_2dp km/h)";}
$ft .= "<br><br>$citystat<br>";

$ft .= "<ul class=boxul><li>離岸及高地風力讀數</li></ul>";

$ft .= "<pre>";
for ($i=0; $i<=$#rawurb; $i++)
{
    #$ft .= "$urbn[$i] $rawurb[$i] $rawurbg[$i]<br>";
    $ft .= sprintf '%10s %14s %10s %s', $urbn[$i], $rawurb[$i], $rawurbg[$i], "<br>" ;
}
$ft .= "</pre>";

$ft .= "<br>平均持續風力: $ubavg_2dp km/h";
if ($ubgavg ne "") {$ft .= " (平均陣風: $ubgavg_2dp km/h)";}
$ft .= "<br><br>$urbstat<br>";

# 寫入訊息
if ($output_file) {
    open(F, ">$msghtml") or die "can't open $msghtml";
    print F $ft;
    print $ft;
    close(F) or die "can't close $msghtml";
} else {
    print $ft;
}

# 	print '<meta http-equiv="refresh" content="0;url=http://hkcoc.weather.com.hk/wind/test/wind.htm">';

##############################################################
sub gustfactor {    # 計算陣風比重 (avg)
    my $avg = $_[0];
    my $gustr;

    $gustr = ((-0.00005)*($avg**2)) + (0.0081 * $avg) - 0.0005;
    if ($avg > 81) {$gustr = 0.327;}
    return $gustr;
}

##############################################################
sub sdev {  # 計算標準差及平均 (data array)
    my @data;
    my ($size,$avg,$dev,$total);
    @data = @_;

    foreach $data(@data) {
        if ($data >= 0) {
            $total += $data;
            $size ++
        }
    }
    if ($size == 0) {$size = 1;}
    $avg = $total/$size;

    foreach $data(@data) {
        if ($data >= 0) {
            $dev += (($data - $avg)**2);
        }
    }

    $size2 = $size;
    if ($size2 == 1) {$size2 = 2;}
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

    for ($i=0;$i<=$#data;$i++) {
        $check = $data[$i] - $avg;
        if ($check < 0) {$check *= -1;}
        if ($check > $dev) {
            $data[$i] = 0;
            $redat[$i] = "*";
        }
    }
    ($newavg,$newdev,$newsize) = sdev(@data);
    return ($newavg,$newdev,$newsize,@redat);
}

##############################################################
sub writewi {
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

sub writedir {
    $readingdate = $_[0];
    $readingtime = $_[1];

    #
    open (WI, "$file2") or die " can open $file2";
    @lines = <WI>;
    close (WI);
    chomp(@lines);

    if (($lines[$#lines] !~ /^$readingdate $readingtime /) and ($lines[$#lines-1] !~ /^$readingdate $readingtime /) and ($readingdate) and ($readingtime) and ($avgdir > 0)) {
      open (WI, ">> $file2");
      print WI "$readingdate $readingtime $avgdir $avgdirtxte\n";
      close (WI);
      }
}

##############################################################
#   V5: new retrieve data approach
##############################################################
sub getwinddata {
    $line = $_[0];
}

sub debug_windstation {
    print "<h1>== Predefined data==</h1>";
    print "code, ename, cname, type, w 風力, g 陣風, d 風向(方位角), wiu 納入風指計算<br>";
    for ($i=0;$i<=$#st;$i++)
    {
        print "$st[$i]{'code'},$st[$i]{'ename'},$st[$i]{'cname'},$st[$i]{'type'},$st[$i]{'w'},$st[$i]{'g'},$st[$i]{'d'},$st[$i]{'wiu'}<br>";
    }
}

sub getdatafrline {
    my $line = $_[0];
    for ($i=0;$i<=$#st;$i++)
    {
        if ($line=~/$st[$i]{'ename'}/)
        {
            $line =~/     (.*)\s+(\d+)\s+(\d+)/;
            $st[$i]{'d'} = trim($1);
            $st[$i]{'w'} = $2;
            $st[$i]{'g'} = $3;
            print "    ---- find ".$st[$i]{'ename'}."-".$st[$i]{'d'}."-".$st[$i]{'w'}."-".$st[$i]{'g'};
        }
        elsif ($line=~/^$st[$i]{'code'},/) {
            if ($line =~/^\w+,(\d+),(\d+),(\d+)/) {
                $st[$i]{'d'} = $1;
                $st[$i]{'w'} = $2;
                $st[$i]{'g'} = $3;
                print "    ---- find ".$st[$i]{'code'}."-".$st[$i]{'d'}."-".$st[$i]{'w'}."-".$st[$i]{'g'};
            }
        }
    }
}


#SEL by windType(w/g/d/cname) #WHERE type(city/urban),wiu(0/1)
sub getcountbykey {
    my $windtype = $_[0];
    my $type = $_[1];
    my $wiu = $_[2];
    my @result;
    my $cnt = 0;
    for ($i=0;$i<=$#st;$i++)
    {
        if (($st[$i]{'type'} eq $type) and ($st[$i]{'wiu'}>=$wiu))
        {
            $result[$cnt]=$st[$i]{$windtype};
            $cnt++;
            print "    ---- get ".$st[$i]{'ename'}."-".$st[$i]{$windtype}."<br>";
        }
    }
    @result
}

sub trim { my $s = shift; $s =~ s/^\s+|\s+$//g; return $s };


# Calculate average wind direction
# https://www.itron.com/blog/forecasting/computing-a-weighted-average-wind-speed-and-wind-direction-across-multiple-weather-stations
sub avgdir {
    my $b,$r;
    my $bw; #bear*wind
    my $totw; #wind speed sum
    my $totbw; #bear*wind sum
    my $weight; #num of validate station
    $weight=0;
    for ($i=0;$i<=$#st;$i++)
    {
        if ($st[$i]{'w'}>0 and $st[$i]{'d'}>=0 and $st[$i]{'d'}<=360)
        {
            $weight++;
            print "    ---- get ".$st[$i]{'ename'}."-".$st[$i]{'d'}."-".$st[$i]{'w'};
            # Step 1.  Convert Wind Direction in Degrees to Wind Direction in Radians.
            $b = dtob($st[$i]{'d'});
            $r = deg2rad($b);

            # Step 2. For each station, compute the East-West and North-South Vector
            $EW_Vector=sin($r)*$st[$i]{'w'}; # East-West Vector
            $totEW_Vector+=$EW_Vector;

            $NS_Vector=cos($r)*$st[$i]{'w'}; # North-South Vector
            $totNS_Vector+=$NS_Vector;

            # Step 3. Compute a Weighted Sum of the East-West and North-South Vectors
            print "Bear-".$b."Radius-".$r."-EW_Vector-".$EW_Vector."-NS_Vector-".$NS_Vector;
            print "<br>";
        }
    }
    print "    ---- number of station=".$weight;
    $EW_Vector=($totEW_Vector/$weight); # Avg East-West Vector
    $NS_Vector=($totNS_Vector/$weight); # Avg North-South Vector
    print "Avg-EW_Vector-".$EW_Vector."-NS_Vector-".$NS_Vector;
    print "*********<br>";
    # Step 5.  Compute the Weighted Average Wind Direction
    $r=atan2($EW_Vector,$NS_Vector);
    print "Avg Radius:".$r."<br>";
    $avgdir=int(rad2deg($r)+0.5);
    if($avgdir<0) {$avgdir=$avgdir+=360;}
    print "Avg bear:".$avgdir;
    $avgdirtxt=dir($avgdir);
    $avgdirtxte=dire($avgdir);
    print " (".$avgdirtxt."/".$avgdirtxte.")";
}

sub dtob {
    my $wd = $_[0];
    $b=int($wd);
    if ($wd eq "North") {$b=0}
    if ($wd eq "Northeast") {$b=45}
    if ($wd eq "East") {$b=90}
    if ($wd eq "Southeast") {$b=135}
    if ($wd eq "South") {$b=180}
    if ($wd eq "Southwest") {$b=225}
    if ($wd eq "West") {$b=270}
    if ($wd eq "Northwest") {$b=315}
    return $b;
}

sub dir {
    $b = $_[0];

    if (($b>=348.75)or ($b< 11.25)) {$wd="北";}
    if (($b>= 11.25)and($b< 33.75)) {$wd="東北偏北";}
    if (($b>= 33.75)and($b< 56.25)) {$wd="東北";}
    if (($b>= 56.25)and($b< 78.75)) {$wd="東北偏東";}
    if (($b>= 78.75)and($b<101.25)) {$wd="東";}
    if (($b>=101.25)and($b<123.75)) {$wd="東南偏東";}
    if (($b>=123.75)and($b<146.25)) {$wd="東南";}
    if (($b>=146.25)and($b<168.75)) {$wd="東南偏南";}
    if (($b>=168.75)and($b<191.25)) {$wd="南";}
    if (($b>=191.25)and($b<213.75)) {$wd="西南偏南";}
    if (($b>=213.75)and($b<236.25)) {$wd="西南";}
    if (($b>=236.25)and($b<258.75)) {$wd="西南偏西";}
    if (($b>=258.75)and($b<281.25)) {$wd="西";}
    if (($b>=281.25)and($b<303.75)) {$wd="西北偏西";}
    if (($b>=303.75)and($b<326.25)) {$wd="西北";}
    if (($b>=326.25)and($b<348.75)) {$wd="西北偏北";}

    return $wd;
}
sub dire {
    $b = $_[0];

    if (($b>=348.75)or ($b< 11.25)) {$wd="N";}
    if (($b>= 11.25)and($b< 33.75)) {$wd="NNE";}
    if (($b>= 33.75)and($b< 56.25)) {$wd="NE";}
    if (($b>= 56.25)and($b< 78.75)) {$wd="ENE";}
    if (($b>= 78.75)and($b<101.25)) {$wd="E";}
    if (($b>=101.25)and($b<123.75)) {$wd="ESE";}
    if (($b>=123.75)and($b<146.25)) {$wd="SE";}
    if (($b>=146.25)and($b<168.75)) {$wd="SSE";}
    if (($b>=168.75)and($b<191.25)) {$wd="S";}
    if (($b>=191.25)and($b<213.75)) {$wd="SSW";}
    if (($b>=213.75)and($b<236.25)) {$wd="SW";}
    if (($b>=236.25)and($b<258.75)) {$wd="SWW";}
    if (($b>=258.75)and($b<281.25)) {$wd="W";}
    if (($b>=281.25)and($b<303.75)) {$wd="WNW";}
    if (($b>=303.75)and($b<326.25)) {$wd="NW";}
    if (($b>=326.25)and($b<348.75)) {$wd="NNW";}

    return $wd;
}
