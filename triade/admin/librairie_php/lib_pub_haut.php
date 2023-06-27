<?php
include_once("../../common/config2.inc.php");
include_once("../../common/productId.php");
if ((LAN != "oui") && (PUBHAUT != "oui")) {
	if (HTTPS == "oui") {
	      print "<IFRAME NAME=pubtri SRC='https://www.triade-educ.org/sponsor/pub-online-h.php?inc=".GRAPH."&productid=".PRODUCTID."' width=468 height=60 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>";
	 }else{
	      print "<IFRAME NAME=pubtri SRC='http://www.triade-educ.org/sponsor/pub-online-h.php?inc=".GRAPH."&productid=".PRODUCTID."' width=468 height=60 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>";

	 }
}else {
      print "";
}
?>

