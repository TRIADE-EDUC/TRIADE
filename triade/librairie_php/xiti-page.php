<?php
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

function xitipage($infoxiti) { 

	print "<div align='right' style='visibility:hidden' >\n";
	print "<a href='http://www.xiti.com/xiti.asp?s=150072' TARGET='_top'>\n";
	print "<script language='JavaScript1.1' type='text/javascript'>\n";

	print "var version='$infoxiti';\n";
	print "Xt_param = 's=150072&p='+version;\n";
	print "Xt_r = document.referrer;\n";
	print "Xt_h = new Date();\n";
	print "Xt_i = '<img width=\"39\" height=\"25\" border=\"0\" id=\"mailing\" class=\"blk_nav\" onmouseover=\"resetit(this.id)\" onmouseout=\"unfadeimg(this.id)\"  style=\"filter:alpha(opacity=25);-moz-opacity:0.25\" ';\n";
	print "Xt_i += 'src=\"http://logv24.xiti.com/hit.xiti?'+Xt_param;\n";
	print "Xt_i += '&hl='+Xt_h.getHours()+'x'+Xt_h.getMinutes()+'x'+Xt_h.getSeconds();\n";
	print "if(parseFloat(navigator.appVersion)>=4)\n";
	print "{Xt_s=screen;Xt_i+='&r='+Xt_s.width+'x'+Xt_s.height+'x'+Xt_s.pixelDepth+'x'+Xt_s.colorDepth;}\n";
	print "document.write(Xt_i+'&ref='+Xt_r.replace(/[<>\"]/g, '').replace(/&/g, '$')+'\" title=\"Analyse d\'audience\">');\n";

	print "</script></a></div>\n";
}
?>
