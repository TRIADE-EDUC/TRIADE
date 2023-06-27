<?php
include("../../common/config2.inc.php");
include_once("../../common/productId.php");
if (LAN == "oui") {
	if (HTTPS == "oui") {
		print "<IFRAME NAME=pubtri SRC='https://www.triade-educ.org/sponsor/pub-online-d.php?inc=".GRAPH."&productid=".PRODUCTID."' width=120 height=600 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>";

	}else{
		print "<IFRAME NAME=pubtri SRC='http://www.triade-educ.org/sponsor/pub-online-d.php?inc=".GRAPH."&productid=".PRODUCTID."' width=120 height=600 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no></iframe>";
	}
}else {
    print "";
}
?>
					       

