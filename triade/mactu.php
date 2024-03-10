<?php
session_start();
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
	if ((LAN == "oui") && (PUBHAUT != "oui")) {
		if (HTTPS == "oui") {
			print "";
		}else{
			$idpers=$_SESSION["id_pers"];
			$url="https://www.triade-educ.org/sponsor/mactu-h.php?inc=".GRAPH."&https=".HTTPS."&productid=".PRODUCTID."&id=$idpers";
			print "<script src='https://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
			print "<script>";
			print "if (ok2) {";
 			print "document.write(\"<iframe name='ptri' SRC='$url' width='468' height='60' MARGINWIDTH='0' MARGINHEIGHT='0' HSPACE='0' VSPACE='0' FRAMEBORDER='0' SCROLLING='no'></iframe>\")";
			print "}";
			print "</script>";
		}
        } else {
                print "";
        }
}


function top_d() {

	if (LAN == "oui") {
		if (HTTPS == "oui") {
			print "";
		}else{
			$url="https://www.triade-educ.org/sponsor/mactu-d.php?inc=".GRAPH."&https=".HTTPS."&productid=".PRODUCTID;
			print "<script src='https://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
			print "<script>";
			print "if (ok2) {";
			print "document.write(\"<IFRAME name=ptri SRC='$url' width=120 height=600 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")";
			print "}";
			print "</script>";
		}
        } else {
                print "";
        }
}

function top_p(){
	if (LAN == "oui") {
		if (HTTPS == "oui") {
			print "";
		}else{ 
			$url="http://www.triade-educ.org/sponsor/mactu-h.php?inc=".GRAPH."&https=".HTTPS."&productid=".PRODUCTID;
			print "<script src='https://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
			print "<script>";
			print "if (ok2) {";
			print "document.write(\"<IFRAME name=ptri2 SRC='$url'  width=468 height=60 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")";
			print "}";
			print "</script>";
		}
        } else {
                print "";
        }
}

?>
