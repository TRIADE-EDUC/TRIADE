<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_expl.inc.php,v 1.10 2021/07/13 10:39:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub;

print "
<script>
  function resizeIframe(obj) {
	if(obj.contentWindow && obj.contentWindow.document.documentElement.scrollHeight) {
		var iframeHeight = obj.contentWindow.document.documentElement.scrollHeight + 100;
	} else {
		var iframeHeight = 700;
	}
    obj.style.height = iframeHeight + 'px';
  }
</script>
<iframe name='iimport_expl' frameborder='0' scrolling='yes' width='100%' onload='resizeIframe(this)' src='./admin/import/iimport_expl.php?categ=import&sub=$sub'>
<noframes>
</noframes>" ;
