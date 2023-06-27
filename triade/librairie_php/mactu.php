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
if (file_exists("./common/config2.inc.php")) { include_once("./common/config2.inc.php"); }
if (file_exists("../common/config2.inc.php")) { include_once("../common/config2.inc.php"); }
if (file_exists("../../common/config2.inc.php")) { include_once("../../common/config2.inc.php"); }
if (file_exists("../../../common/config2.inc.php")) { include_once("../../../common/config2.inc.php"); }
//----//
function top_h(){
	if ((LAN == "oui") && (PUBHAUT != "oui")) {
		if (HTTPS == "oui") {
			print "";
		}else{
			$idpers=$_SESSION["id_pers"];
			$url="http://www.triade-educ.org/sponsor/mactu-h.php?inc=".GRAPH."&https=".HTTPS."&productid=".PRODUCTID."&id=$idpers";
			print "<script src='http://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
			print "<script>";
			print "if (ok2) {";
			print "document.write(\"<IFRAME NAME=ptri SRC='$url' width=468 height=60 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")";
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
			$url="http://www.triade-educ.org/sponsor/mactu-d.php?inc=".GRAPH."&https=".HTTPS."&productid=".PRODUCTID;
			print "<script src='http://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
			print "<script>\n";
                        print "if (ok2) {\n";
                        print "var xhr = new XMLHttpRequest( );\n";
                        print "xhr.onload = (res)=>{\n";
                        print "if(res.target.status == 200){\n";
                        print "document.getElementById('ptd1').innerHTML += \n";
                        print "(res.target.responseText);\n";
                        print "}\n";
                        print "}\n";
                        print "xhr.open(\"GET\",\"$url\",true);\n";
                        print "xhr.send();\n";
//			print "document.write(\"<IFRAME NAME=ptri SRC='$url' width=120 height=600 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")";
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
			print "<script src='http://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
			print "<script>";
			print "if (ok2) {";
		        print "var xhr = new XMLHttpRequest( );";
		        print "xhr.onload = (res)=>{";
		           print "if(res.target.status == 200){";
		             print "document.body.innerHTML += ";
			             print "(res.target.responseText);";
		           print "}";
		        print "}";
		        print "xhr.open(\"GET\",\"$url\",true);";
		        print "xhr.send();";

	//	print "document.write(\"<IFRAME NAME=ptri2 SRC='$url'  width=468 height=60 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")";

			print "}";
			print "</script>";
		}
        } else {
                print "";
        }
}
?>
