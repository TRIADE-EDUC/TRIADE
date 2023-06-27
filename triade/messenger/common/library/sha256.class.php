<?php












































class hashData
{

var $hash=null;
}



class hash
{







function hash($str,$mode='hex')
{
trigger_error('hash::hash() NOT IMPLEMENTED',E_USER_WARNING);
return false;
}


function hashChunk($str,$length,$mode='hex')
{
trigger_error('hash::hashChunk() NOT IMPLEMENTED',E_USER_WARNING);
return false;
}


function hashFile($filename,$mode='hex')
{
trigger_error('hash::hashFile() NOT IMPLEMENTED',E_USER_WARNING);
return false;
}


function hashChunkFile($filename,$length,$mode='hex')
{
trigger_error('hash::hashChunkFile() NOT IMPLEMENTED',E_USER_WARNING);
return false;
}
}





class SHA256Data extends hashData
{

var $buf=array();


var $chunks=null;

function SHA256Data($str)
{
$M=strlen($str);
$L1=($M>>28)&0x0000000F;
$L2=$M<<3;
$l=pack('N*',$L1,$L2);




$k=$L2+64+1+511;
$k-=$k%512+$L2+64+1;
$k>>=3;

$str.=chr(0x80).str_repeat(chr(0),$k).$l;

assert('strlen($str) % 64 == 0');


preg_match_all('#.{64}#',$str,$this->chunks);
$this->chunks=$this->chunks[0];












$this->hash=array
(
1779033703,-1150833019,
1013904242,-1521486534,
1359893119,-1694144372,
528734635,1541459225,
);
}
}



class SHA256 extends hash
{
function hash($str,$mode='hex')
{
static $modes=array('hex','bin','bit');
$ret=false;

if(!in_array(strtolower($mode),$modes))
{
trigger_error('mode specified is unrecognized: '.$mode,E_USER_WARNING);
}
else
{
$data=&new SHA256Data($str);

SHA256::compute($data);

$func=array('SHA256','hash'.$mode);
if(is_callable($func))
{
$func='hash'.$mode;
$ret=SHA256::$func($data);

}
else
{
trigger_error('SHA256::hash'.$mode.'() NOT IMPLEMENTED.',E_USER_WARNING);
}
}

return $ret;
}





function sum()
{
$T=0;
for($x=0,$y=func_num_args();$x<$y;$x++)
{

$a=func_get_arg($x);


$c=0;

for($i=0;$i<32;$i++)
{

$j=(($T>>$i)&1)+(($a>>$i)&1)+$c;

$c=($j>>1)&1;

$j&=1;

$T&=~(1<<$i);

$T |=$j<<$i;
}
}

return $T;
}



function compute(&$hashData)
{
static $vars='abcdefgh';
static $K=null;

if($K===null)
{




















$K=array(
1116352408,1899447441,-1245643825,-373957723,
961987163,1508970993,-1841331548,-1424204075,
-670586216,310598401,607225278,1426881987,
1925078388,-2132889090,-1680079193,-1046744716,
-459576895,-272742522,264347078,604807628,
770255983,1249150122,1555081692,1996064986,
-1740746414,-1473132947,-1341970488,-1084653625,
-958395405,-710438585,113926993,338241895,
666307205,773529912,1294757372,1396182291,
1695183700,1986661051,-2117940946,-1838011259,
-1564481375,-1474664885,-1035236496,-949202525,
-778901479,-694614492,-200395387,275423344,
430227734,506948616,659060556,883997877,
958139571,1322822218,1537002063,1747873779,
1955562222,2024104815,-2067236844,-1933114872,
-1866530822,-1538233109,-1090935817,-965641998,
);
}

$W=array();
for($i=0,$numChunks=sizeof($hashData->chunks);$i<$numChunks;$i++)
{

for($j=0;$j<8;$j++)
${$vars{$j}}=$hashData->hash[$j];


for($j=0;$j<64;$j++)
{
if($j<16)
{
$T1=ord($hashData->chunks[$i]{$j*4})&0xFF;$T1<<=8;
$T1 |=ord($hashData->chunks[$i]{$j*4+1})&0xFF;$T1<<=8;
$T1 |=ord($hashData->chunks[$i]{$j*4+2})&0xFF;$T1<<=8;
$T1 |=ord($hashData->chunks[$i]{$j*4+3})&0xFF;
$W[$j]=$T1;
}
else
{
$W[$j]=SHA256::sum(((($W[$j-2]>>17)&0x00007FFF)|($W[$j-2]<<15))^((($W[$j-2]>>19)&0x00001FFF)|($W[$j-2]<<13))^(($W[$j-2]>>10)&0x003FFFFF),$W[$j-7],((($W[$j-15]>>7)&0x01FFFFFF)|($W[$j-15]<<25))^((($W[$j-15]>>18)&0x00003FFF)|($W[$j-15]<<14))^(($W[$j-15]>>3)&0x1FFFFFFF),$W[$j-16]);
}

$T1=SHA256::sum($h,((($e>>6)&0x03FFFFFF)|($e<<26))^((($e>>11)&0x001FFFFF)|($e<<21))^((($e>>25)&0x0000007F)|($e<<7)),($e&$f)^(~$e&$g),$K[$j],$W[$j]);
$T2=SHA256::sum(((($a>>2)&0x3FFFFFFF)|($a<<30))^((($a>>13)&0x0007FFFF)|($a<<19))^((($a>>22)&0x000003FF)|($a<<10)),($a&$b)^($a&$c)^($b&$c));
$h=$g;
$g=$f;
$f=$e;
$e=SHA256::sum($d,$T1);
$d=$c;
$c=$b;
$b=$a;
$a=SHA256::sum($T1,$T2);
}


for($j=0;$j<8;$j++)
$hashData->hash[$j]=SHA256::sum(${$vars{$j}},$hashData->hash[$j]);
}
}



function hashHex(&$hashData)
{
$str='';

reset($hashData->hash);
do
{
$str.=sprintf('%08x',current($hashData->hash));
}
while(next($hashData->hash));

return $str;
}



function hashBin(&$hashData)
{
$str='';

reset($hashData->hash);
do
{
$str.=pack('N',current($hashData->hash));
}
while(next($hashData->hash));

return $str;
}
}

?>
