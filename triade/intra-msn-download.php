<?php
session_start();
if (empty($_SESSION["nom"]))  {
	header("Location: ./acces_refuse.php");
	exit;
}
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

include_once('./common/config.inc.php');
include_once('./librairie_php/db_triade.php');
include_once("./common/config2.inc.php");
include_once('./librairie_php/pclzip.lib.php');

@unlink("./messenger/IntraMessengerClient/options.ini");
$fd=fopen("./messenger/IntraMessengerClient/options.ini","w+");
$text="[General]\n";
$lien=$_SERVER['SERVER_NAME']."/".ECOLE."/messenger/";
$lien=preg_replace('/\/\//','/',$lien);
$text.="url=http://$lien\r\n";
$text.="Lang=FR\r\n";
$text.="[ThreadMode]\r\n";
$text.="Hyperthreading=0\r\n";
$text.="[Options]\r\n";
$text.="Startup_Status=1\r\n";
$text.="open_msg=1\r\n";
$text.="DisplayBulle=1\r\n";
$text.="Display_online_only=1\r\n";
$text.="Display_online_order_state=1\r\n";
$text.="Away_on_screensaver=1\r\n";
$text.="Display_name_col=0\r\n";
$text.="Display_state_reason=1\r\n";
$text.="OnTop=0\r\n";
$text.="EnterSend=1\r\n";
$text.="History=0\r\n";
$text.="DateFormat_EN=0\r\n";
$text.="TimeFormat_AMPM=0\r\n";
$text.="Display_IM_onStartup=1\r\n";
$text.="Display_all_groups_open_if_a_few_users=15\r\n";
$text.="username_uppercase=0\r\n";
$text.="AutoDisplayAvatar=0\r\n";
$text.="DisplayAvatar=1\r\n";
$text.="[Connect]\r\n";
$text.="Agent=-\r\n";
$text.="[Proxy]\r\n";
$text.="Port=0\r\n";
$text.="IP=?\r\n";
$text.="[Windows_Top]\r\n";
$text.="fe_start=1031\r\n";
$text.="[Windows_Left]\r\n";
$text.="fe_start=204\r\n";
$text.="[ShoutBox]\r\n";
$text.="NotifyBulle=1\r\n";
$text.="[Phenix]\r\n";
$text.="Remind=0\r\n";
$text.="[Update]\r\n";
$text.="no_lan_net=1\r\n";
$text.="[Server]\r\n"; 
$text.="ExternAuth=Triade\r\n";
fwrite($fd,$text);
fclose($fd);

$fichier='./messenger/public/intra-msn-triade.zip';
@unlink($fichier);
$archive = new PclZip($fichier);
$archive->create('./messenger/IntraMessengerClient',PCLZIP_OPT_REMOVE_PATH, 'messenger');

$fic=$fichier;
$filename = stripslashes(basename($fic));
switch(strrchr(basename($filename), ".")) {
	case ".zip": $type = "application/zip"; break;
	default: $type = "application/octet-stream"; break;

}
header("Content-disposition: attachment; filename=$filename");
header("Content-Type: application/force-download");
header("Content-Transfer-Encoding: $type\n"); // Surtout ne pas enlever le \n
header("Content-Length: ".filesize($fic));
if (HTTPS == "oui") {
	header("Cache-Control: public"); 
	header("Pragma:"); 
	header("Expires: 0");
}else{
	header("Pragma: no-cache");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
	header("Expires: 0");
}
readfile($fic);

?>
