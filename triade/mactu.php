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
		$idpers=$_SESSION["id_pers"];
		$url="https://www.triade-educ.org/sponsor/mactu-h.php?inc=".GRAPH."&https=".HTTPS."&productid=".PRODUCTID."&id=$idpers";
		print "<script src='https://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
		print "<script>";
		print "if (ok2) {";
		print "document.write(\"<IFRAME NAME=ptri SRC='$url' width=468 height=60 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")";
		print "}";
		print "</script>";
        } else {
                print "";
        }
}

function top_d() {
	if (LAN == "oui") {
		$url="https://www.triade-educ.org/sponsor/mactu-d.php?inc=".GRAPH."&https=".HTTPS."&productid=".PRODUCTID;
		print "<script src='https://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>\n";
		print "<script async src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5077438242993464' crossorigin='anonymous'></script>\n";
		print "<script>\n";
                print "if (ok2) {\n";
		print "document.write(\"<ins class='adsbygoogle' style='display:inline-block;width:120px;height:600px' data-ad-client='ca-pub-5077438242993464' data-ad-slot='3810437468'></ins>\");\n";
       		print "(adsbygoogle = window.adsbygoogle || []).push({});\n";

            /*  print "\tvar xhr = new XMLHttpRequest();\n";
		print "\t\txhr.onreadystatechange = function() {\n";
	        print "\t\txhr.onload = (res)=>{\n";
                print "\t\t\tif(res.target.status == 200){\n";
		print "\t\t\t\tconsole.log(xhr.responseText);\n";
                print "\t\t\t\tdocument.getElementById('ptd1').innerHTML = (res.target.responseText);\n";
                print "\t\t\t} }\n";
                print "\t\t};\n";
                print "\txhr.open(\"GET\",\"$url\",true);\n";
                print "\txhr.send();\n";
	    */	
//		print "document.write(\"<IFRAME  NAME=ptri SRC='$url' width=120 height=600 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")\n";

		print "\n}\n";
		print "</script>";
        } else {
                print "";
        }
}

function top_p(){
	if (LAN == "oui") {
		$url="https://www.triade-educ.org/sponsor/mactu-h.php?inc=".GRAPH."&https=".HTTPS."&productid=".PRODUCTID;
		print "<script src='https://www.triade-educ.org/sponsor/mactu0.php?productid=".PRODUCTID."'></script>";
		print "\n<script>\n";
		print "document.write(\"<div id='ptd11' width='100%' height='100%'></div>\");\n";
		print "if (ok2) {\n";
                print "\tvar xhr = new XMLHttpRequest();\n";
                print "\t\txhr.onreadystatechange = function() {\n";
                print "\t\txhr.onload = (res)=>{\n";
                print "\t\t\tif(res.target.status == 200){\n";
                print "\t\t\t\tconsole.log(xhr.responseText);\n";
                print "\t\t\t\tdocument.getElementById('ptd11').innerHTML = (res.target.responseText);\n";
                print "\t\t\t} }\n";
                print "\t\t};\n";
                print "\txhr.open(\"GET\",\"$url\",true);\n";
                print "\txhr.send();\n";
	//	print "document.write(\"<IFRAME NAME=ptri2 SRC='$url'  width=468 height=60 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>\")";
		print "}";
		print "</script>";
        } else {
                print "";
        }
}
?>
