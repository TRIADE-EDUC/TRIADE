<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visionneuse.tpl.php,v 1.20.2.1.2.1 2025/02/27 14:05:54 dgoron Exp $

global $opac_rgaa_active;


if($opac_rgaa_active){
	$visionneuse = "
		<div id='visio_container'>
			<div id='visio-header'>
				<div class='close'>
					<button type='button' onclick='close_visonneuse();return false;'>!!close!!</button>
				</div>
				<div class='linkFullscreen'>
					<button type='button' id='linkFullscreen' onclick='open_fullscreen();if(typeof(resizeDivConteneur) != \"undefined\"){resizeDivConteneur();}return false;'>!!fullscreen!!</button>
				</div>
			</div>
			<div id='visio_current_object' style='overflow-y:auto;'>
				<div id='visio_current_titre'><h1 id='visio_main_title'>!!titre!!</h1></div>
				!!download!!
				!!explnum_licence_picto!!
				<div id='visio_current_doc'>!!doc!!</div>
				<h2>!!record_section_title!!</h2>
				<div id='visio_current_description'>!!desc!!</div>
			</div>
			<div id='visio_navigator'>
				!!visio_navigator!!
			</div>
		</div>";
}else{
	$visionneuse ="
		<div style='overflow:hidden;position:absolute;top:0%;left:0%;text-align:center;height:100%;width:100%'>
			<div id='visio_current_object' style='overflow-y:auto;'>
				<div id='visio_current_titre'><h1>!!titre!!</h1></div>
				!!download!!
				!!explnum_licence_picto!!
				<div id='visio_current_doc'>!!doc!!</div>
				<div id='visio_current_description'>!!desc!!</div>
			</div>
			<div id='visio_navigator' >
				!!visio_navigator!!
			</div>
		</div>
		<div class='close'><a href='#' onclick='close_visonneuse();return false;'>!!close!!</a></div>
		<div class='linkFullscreen'><a href='#' id='linkFullscreen' onclick='open_fullscreen();if(typeof(resizeDivConteneur) != \"undefined\"){resizeDivConteneur();}return false;'>!!fullscreen!!</a></div>
	";
}

$visionneuse .="
	<script type='text/javascript' src='./includes/javascript/select.js'></script>
	<script>
		function visionneuseNav(where){
			switch(where){
				case 'first' :
					document.forms['docnumForm'].position.value= 0;
					break;
				case 'last' :
					document.forms['docnumForm'].position.value= !!max_pos!!;
					break;
				case 'next' :
					if ((document.forms['docnumForm'].position.value*1+1)> !!max_pos!!){
						document.forms['docnumForm'].position.value= !!max_pos!!;
					}else{
						document.forms['docnumForm'].position.value++;
					}
					break;
				case 'previous' :
					if((document.forms['docnumForm'].position.value*1-1)< 0) {
						document.forms['docnumForm'].position.value= 0;
					}else{
						document.forms['docnumForm'].position.value--;
					}
					break;
				case 'custom' :
					if ((document.forms['docnumForm'].go_page.value*1) > !!max_pos!!){
						document.forms['docnumForm'].position.value= !!max_pos!!;
					} else if ((document.forms['docnumForm'].go_page.value*1) > 0){
						document.forms['docnumForm'].position.value= document.forms['docnumForm'].go_page.value*1-1;
					}
					break;
			}
			document.forms['docnumForm'].submit();	
		}

		document.getElementById('visio_current_object').style.height=getFrameHeight()-80+'px';	
	
		window.onresize = function(){
			document.getElementById('visio_current_object').style.height=getFrameHeight()-80+'px';	
			if (typeof(checkSize) != 'undefined') checkSize();
		}

		function close_visonneuse(){
			window.parent.window.close_visionneuse();
		}

		function open_fullscreen(){
			var visionneuseIframe =window.parent.document.getElementById('visionneuseIframe');
			var linkFullscreen =document.getElementById('linkFullscreen');
			if (linkFullscreen.innerHTML == \"!!fullscreen!!\"){
				visionneuseIframe.style.width = getWindowWidth()+'px';
				visionneuseIframe.style.height = getWindowHeight()+'px';
				visionneuseIframe.style.left = '0px';
				visionneuseIframe.style.top = '0px';
				linkFullscreen.innerHTML=\"!!normal!!\";
			}else{
				visionneuseIframe.style.width = '96%';
				visionneuseIframe.style.height = '96%';
				visionneuseIframe.style.left = '2%';
				visionneuseIframe.style.top = '2%';
				linkFullscreen.innerHTML=\"!!fullscreen!!\";
			}
		}

		function getFrameHeight(){
			if (document.all) {
				var doc = window.parent.document.getElementById('visionneuseIframe');
				return doc.clientHeight;
			}else {
				return window.innerHeight;
			}
		}

		function getFrameWidth(){
			if (document.all) {
				var doc = window.parent.document.getElementById('visionneuseIframe');
				return doc.clientWidth;
			}else {
				return window.innerWidth;
			}
		}
		
		function getWindowHeight(){
			if (document.all) {
				var doc = window.parent.document.getElementById('visionneuse');
				return doc.clientHeight;
			}else {
				return window.parent.innerHeight;
			}
		}

		function getWindowWidth(){
			if (document.all) {
				var doc = window.parent.document.getElementById('visionneuse');
				return doc.clientWidth;
			}else {
				return window.parent.innerWidth;
			}
		}

		".(!empty($opac_rgaa_active) ? "
            if (typeof focus_trap == 'function') {
                focus_trap(document.getElementById('visio_container'));
            }
        
        " : "")."
	</script>
";
?>