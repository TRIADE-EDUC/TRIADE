<?php
/************************************************/
/*																							*/
/*              PHP4WD 5.0.0.4                 */
/*																							*/
/************************************************/


$clef_one = "àb&Eféme!q58%4qsd8µASD843ddqZ,dsazs;5684sDZ:694sdqdzaDzsa";
//$clef_one = "bEfmeq584qsd8ASD843ddqZdsazs5684sDZ694sdqdzaDzsa";


function f_AjusteTailleCle($clef, $taille)
{
	$indice = 0;
	$retour = $clef;
	if ($taille > 20)
            $taille = 20;
	while(strlen($retour) < $taille){
		$retour = $retour. $clef[$indice];
		if ($indice > strlen($clef)) $indice = 0; 
		$indice++;
	}
	//
	return $retour;
}


function f_get_param($mcr)
{
	GLOBAL $clef_one;
	$clef = _PASSWORD_FOR_PRIVATE_SERVER . $clef_one;
	$i = 0;
	$indiceCle = 0;
	$m ="";
	$cle = f_AjusteTailleCle($clef, strlen($mcr)/3);
  $tailleCrypt = strlen($cle);
	$taillemcr = strlen($mcr);
	while( $i<$taillemcr)
	{
		$m .= chr(substr($mcr,($i) ,3)-ord($cle[($indiceCle)]));
		$indiceCle = ($indiceCle + 1) % $tailleCrypt;
		$i = $i + 3;
	}
	//
	return $m;
}


function f_send_param($m)
{
	GLOBAL $clef_one;
	$clef = _PASSWORD_FOR_PRIVATE_SERVER . $clef_one;
	$indiceCle = 0;
	$mcr = "";
	$cle = f_AjusteTailleCle($clef, strlen($m));
  $tailleCrypt = strlen($cle);
	$taillem = strlen($m);
	for ($i=0; $i<$taillem; $i++)
	{
		$mcr .= substr(('000'.(ord($m[$i])+ord($cle[($indiceCle)]))),-3);
    $indiceCle = ($indiceCle + 1) % $tailleCrypt;
	}
	//
	return $mcr;
}

?>