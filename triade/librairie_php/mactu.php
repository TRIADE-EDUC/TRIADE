<?php
if (file_exists("./common/config.inc.php")) { include_once("./common/config.inc.php"); }
if (file_exists("../common/config.inc.php")) { include_once("../common/config.inc.php"); }
if (file_exists("../../common/config.inc.php")) { include_once("../../common/config.inc.php"); }
if (file_exists("../../../common/config.inc.php")) { include_once("../../../common/config.inc.php"); }
//----//
if (file_exists("./common/productId.php")) { include_once("./common/productId.php"); }
if (file_exists("../common/productId.php")) { include_once("../common/productId.php"); }
if (file_exists("../../common/productId.php")) { include_once("../../common/productId.php"); }
if (file_exists("../../../common/productId.php")) { include_once("../../../common/productId.php"); }
 
//----//

function top_h(){
	if (((defined("LAN")) && (LAN == "oui")) && ((defined("PUBHAUT")) && (PUBHAUT != "oui"))) {
		$idpers=$_SESSION["id_pers"];
		if ((defined("PRODUCTID")) && (defined("HTTPS")) && (defined("GRAPH")) )    { 
			$url="https://www.triade-educ.org/sponsor/mactu-h.php?inc=".GRAPH."&https=".HTTPS."&productid=".PRODUCTID."&id=$idpers";
		}else{
			$url="https://www.triade-educ.org/sponsor/mactu-h.php?inc=&https=&productid=&id=$idpers";

		}
		if (defined("PRODUCTID")) {
			print "<script src='https://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
		}else{
			print "<script src='https://www.triade-educ.org/sponsor/mactu0.php?productid='></script>";
		}
		print "<script>";
		print "if (ok2) {";
 		print "document.write(\"<iframe name='ptri' SRC='$url' width='468' height='60' MARGINWIDTH='0' MARGINHEIGHT='0' HSPACE='0' VSPACE='0' FRAMEBORDER='0' SCROLLING='no'></iframe>\")";
		print "}";
		print "</script>";
        }else{
                print "";
        }
}


function top_d() {
	if ((defined("LAN")) && (LAN == "oui")) {
		if ((defined("PRODUCTID")) && (defined("HTTPS")) && (defined("GRAPH")) )    { 
			$url="https://www.triade-educ.org/sponsor/mactu-d.php?inc=".GRAPH."&https=".HTTPS."&productid=".PRODUCTID;
		}else{
			$url="https://www.triade-educ.org/sponsor/mactu-d.php?inc=&https=&productid=";
		}
		if (defined("PRODUCTID")) {
			print "<script src='https://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
		}else{
			print "<script src='https://www.triade-educ.org/sponsor/mactu0.php?productid='></script>";
		}
		print "<script>";
		print "if (ok2) {";
		print "document.write(\"<IFRAME name=ptri SRC='$url' width=120 height=600 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")";
		print "}";
		print "</script>";
        } else {
                print "";
        }
}

function top_p(){
	if ((defined("LAN")) && (LAN == "oui")) {
		if ((defined("PRODUCTID")) && (defined("HTTPS")) && (defined("GRAPH")) )    { 
			$url="https://www.triade-educ.org/sponsor/mactu-h.php?inc=".GRAPH."&https=".HTTPS."&productid=".PRODUCTID;
		}else#

		}
		if (defined("PRODUCTID")) {
			print "<script src='https://www.triade-educ.org/sponsor/mactu0.php?productid='></script>";
		}
		print "<script>";
		print "if (ok2) {";
		print "document.write(\"<IFRAME name=ptri2 SRC='$url'  width=468 height=60 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")";
		print "}";
		print "</script>";
        } else {
                print "";
        }
}

 
?>
