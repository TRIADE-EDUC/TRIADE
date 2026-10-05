<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shortcuts.php,v 1.14 2021/04/28 07:48:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "shortcuts.php")) die("no access");

if ( ! defined( 'SHORTCUTS' ) ) {
  define( 'SHORTCUTS', 1 );

global $raclavier, $msg;
  
$escape = 27;

print "
<script type='text/javascript' src='./javascript/select.js'></script>
<script type='text/javascript'>
<!--
// affichage des raccourcis

function clean_raccourci() {
	setTimeout(\"top.document.getElementById('keystatus').firstChild.nodeValue=' '\",1000);
}

function touche(e) {
	if (!e) var e = window.event;
	if (e.keyCode) key = e.keyCode;
		else if (e.which) key = e.which;
		window.clearTimeout(timer);
		kill_frame('frame_shortcuts');
	
	top.document.getElementById('keystatus').firstChild.nodeValue='$msg[97] - '+String.fromCharCode(key);
	top.document.getElementById('keystatus').style.color='#FF0000';
	key = String.fromCharCode(key);
	key = key.toLowerCase();
	key = key.charCodeAt(0);

	//Traitement des actions
	switch(key) {
		//case ".ord("s").":
		//	if (document.getElementById('btsubmit')) document.getElementById('btsubmit').focus();
		//	e.cancelBubble = true;
		//	if (e.stopPropagation) { e.stopPropagation(); }
		//	clean_raccourci();
		//	break;
		default:	
			switch(key) {
";
if(isset($raclavier) && $raclavier) {
    foreach ($raclavier as $cle => $key) {
    	print "				case ".ord(pmb_strtolower($key[0]))." : document.location='$key[1]'; break;\n";
    }
}
print "				default : clean_raccourci(); break;\n";

print "			}
	}
	document.onkeydown=backhome;
}

function backhome(e){
	if (!e) var e = window.event;
	if (e.keyCode) key = e.keyCode;
		else if (e.which) key = e.which;

	if(key == $escape) {
		propagate=true;
		//Récupération de l'objet d'origine
		if (e.target) origine=e.target; else origine=e.srcElement;
	    if (origine.getAttribute('completion')) {
			id=origine.getAttribute('id');
			if (document.getElementById('d'+id).style.display=='block') {
				propagate=false;
			}
		}		
		if (propagate) {
			timer=setTimeout('ShowShortcuts()',2000);
			top.document.getElementById('keystatus').firstChild.nodeValue='$msg[97]';
			top.document.getElementById('keystatus').style.color='#FF0000';
			window.focus();
			document.onkeydown=touche;
		}
	}	
}

document.onkeydown=backhome;

function ShowShortcuts(){
	frame_shortcuts('./includes/shortcuts/frame_shortcuts.php')
}

//-->
</script>

";
} # fin déclaration

?>