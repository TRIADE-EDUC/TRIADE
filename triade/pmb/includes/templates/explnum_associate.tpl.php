<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_associate.tpl.php,v 1.5 2019/05/27 14:05:40 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $explnum_associate_tpl, $msg;
global $explnum_associate_background_svg, $explnum_associate_background_svg_posX, $explnum_associate_background_svg_posY, $explnum_associate_background_svg_width, $explnum_associate_background_svg_height;
global $explnum_associate_timescale_svg, $explnum_associate_timescale_svg_posX, $explnum_associate_timescale_svg_posY, $explnum_associate_timescale_svg_width, $explnum_associate_timescale_svg_height;
global $explnum_associate_speakers_svg, $explnum_associate_speakers_svg_posX, $explnum_associate_speakers_svg_posY, $explnum_associate_speakers_svg_width, $explnum_associate_speakers_svg_height;
global $explnum_associate_segments_svg, $explnum_associate_segments_svg_posX, $explnum_associate_segments_svg_posY, $explnum_associate_segments_svg_width, $explnum_associate_segments_svg_height;
global $explnum_associate_cursor_svg, $explnum_associate_cursor_svg_posX, $explnum_associate_cursor_svg_posY, $explnum_associate_cursor_svg_width, $explnum_associate_cursor_svg_height;
global $explnum_associate_left_cursor_svg, $explnum_associate_left_cursor_svg_posX, $explnum_associate_left_cursor_svg_posY, $explnum_associate_left_cursor_svg_width, $explnum_associate_left_cursor_svg_height;
global $explnum_associate_right_cursor_svg, $explnum_associate_right_cursor_svg_posX, $explnum_associate_right_cursor_svg_posY, $explnum_associate_right_cursor_svg_width, $explnum_associate_right_cursor_svg_height;

$explnum_associate_tpl = "
	<script type='text/javascript' src='./javascript/ajax.js'></script>
	<h1>".$msg['explnum_associate_speakers']."</h1>
	<form>
	<div id='player'>
		<h3>".$msg['explnum_associate_docnum']."</h3>
		!!player!!
	</div>
	!!ajaxCall!!
	<div id='speech_timeline'>
	</div>
	<div id='speech_timeline_cursors_pos'>
		<label>Debut : </label>
		<input id='left_cursor_svg_pos' type='text' value='0:00' size='4'/>
		<img id='left_cursor_svg_to_cursor' src='".get_url_icon('cursor.png')."' height='20px' style='cursor:pointer'/>
		<label>Fin : </label>
		<input id='right_cursor_svg_pos' type='text' value='0:00' size='4'/>
		<img id='right_cursor_svg_to_cursor' src='".get_url_icon('cursor.png')."' height='20px' style='cursor:pointer'/>
	</div>
	<div id='speech_timeline_js'>
	</div>
	<input id='explnum_associate_add_speaker' type='button' class='bouton' value='+' title='".$msg['explnum_associate_add_speaker']."' />
	<input type='button' class='bouton' value='".$msg['explnum_associate_return']."' title='".$msg['explnum_associate_return']."' onClick='window.location = \"!!return_link!!\"' />
	</form>
";

// Code svg pour le fond
$explnum_associate_background_svg = '<linearGradient id="SVGID_1_" gradientUnits="userSpaceOnUse" x1="14.3228" y1="13.313" x2="179.5859" y2="82.085">
	<stop  offset="0" style="stop-color:#24397E"/>
	<stop  offset="1" style="stop-color:#32539D"/>
</linearGradient>
<rect x="13.123" y="12.123" fill="url(#SVGID_1_)" width="179.649" height="76.14"/>';

$explnum_associate_background_svg_posX = 13.123;
$explnum_associate_background_svg_posY = 12.123;
$explnum_associate_background_svg_width = 179.649;
$explnum_associate_background_svg_height = 76.14;

// Code svg pour les graduations temporelles
$explnum_associate_timescale_svg = '<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="56.188" y1="110.271" x2="142.25" y2="110.271"/>
<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="56.688" y1="110.432" x2="56.688" y2="72.598"/>
<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="141.729" y1="110.368" x2="141.729" y2="72.535"/>
<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="99.219" y1="110.271" x2="99.219" y2="72.438"/>
<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="65.194" y1="110" x2="65.194" y2="91"/>
<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="73.700" y1="110" x2="73.700" y2="91"/>
<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="82.207" y1="110" x2="82.207" y2="91"/>
<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="90.713" y1="110" x2="90.713" y2="91"/>
<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="107.721" y1="110" x2="107.721" y2="91"/>
<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="116.223" y1="110" x2="116.223" y2="91"/>
<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="124.725" y1="110" x2="124.725" y2="91"/>
<line fill="none" stroke="#000000" stroke-miterlimit="10" x1="133.227" y1="110" x2="133.227" y2="91"/>';

$explnum_associate_timescale_svg_posX = 56.188;
$explnum_associate_timescale_svg_posY = 72.438;
$explnum_associate_timescale_svg_width = 85.041;
$explnum_associate_timescale_svg_height = 39;

// Code svg pour les cadres des locuteurs
$explnum_associate_speakers_svg = '<rect x="42.52" y="28.346" fill="#EAF6FD" width="201.98" height="56.693"/>';

$explnum_associate_speakers_svg_posX = 42.52;
$explnum_associate_speakers_svg_posY = 28.346;
$explnum_associate_speakers_svg_width = 201.98;
$explnum_associate_speakers_svg_height = 56.693;

// Code svg pour les segments
$explnum_associate_segments_svg = '<rect x="42.52" y="28.346" fill="#C7C9E4" width="85.04" height="56.693"/>';

$explnum_associate_segments_svg_posX = 42.52;
$explnum_associate_segments_svg_posY = 28.346;
$explnum_associate_segments_svg_width = 85.04;
$explnum_associate_segments_svg_height = 56.693;

// Code svg pour le curseur
$explnum_associate_cursor_svg = '<rect x="56.688" y="90.972" opacity="0.8" fill="#E11D0A" enable-background="new    " width="14.166" height="19.008"/>
<line fill="none" stroke="#E11D28" stroke-miterlimit="10" x1="63.771" y1="109.98" x2="63.771" y2="245.874"/>';

$explnum_associate_cursor_svg_posX = 56.688;
$explnum_associate_cursor_svg_posY = 90.972;
$explnum_associate_cursor_svg_width = 14.166;
$explnum_associate_cursor_svg_height = 154.902;

// Code svg pour le taquet gauche
$explnum_associate_left_cursor_svg = '<path id="path3766" opacity="0.8" style="fill:#008000;stroke:#008000;stroke-width:1.32232666;stroke-miterlimit:4;stroke-opacity:1" d="M 7.222094,18.144129 3.9416281,14.084412 0.66116333,10.024698 3.9393295,5.956432 7.2174959,1.8881693 l 0.0023,8.1279797 z m 0.1254735,0.64544 -3e-7,135.893981" inkscape:connector-curvature="0" />';

$explnum_associate_left_cursor_svg_posX = 0;
$explnum_associate_left_cursor_svg_posY = 0;
$explnum_associate_left_cursor_svg_width = 8.009;
$explnum_associate_left_cursor_svg_height = 154.118;

// Code svg pour le taquet droit
$explnum_associate_right_cursor_svg = '<path id="path3766" opacity="0.8" style="fill:#008000;stroke:#008000;stroke-width:1.32235146;stroke-miterlimit:4;stroke-opacity:1" d="m 0.78665377,18.230252 3.28058583,-4.05958 3.2805845,-4.0597 -3.2782866,-4.0681803 -3.27828543,-4.06848 -0.002307,8.1279003 z m -0.12547804,0.64541 2.8e-7,135.894298" inkscape:connector-curvature="0" />';

$explnum_associate_right_cursor_svg_posX = 0;
$explnum_associate_right_cursor_svg_posY = 0;
$explnum_associate_right_cursor_svg_width = 8.009;
$explnum_associate_right_cursor_svg_height = 154.118;

?>