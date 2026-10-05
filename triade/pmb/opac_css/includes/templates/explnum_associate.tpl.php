<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_associate.tpl.php,v 1.2 2019/05/29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $explnum_associate_background_svg, $explnum_associate_background_svg_posX, $explnum_associate_background_svg_posY, $explnum_associate_background_svg_width, $explnum_associate_background_svg_height;
global $explnum_associate_timescale_svg, $explnum_associate_timescale_svg_posX, $explnum_associate_timescale_svg_posY, $explnum_associate_timescale_svg_width, $explnum_associate_timescale_svg_height;
global $explnum_associate_speakers_svg, $explnum_associate_speakers_svg_posX, $explnum_associate_speakers_svg_posY, $explnum_associate_speakers_svg_width, $explnum_associate_speakers_svg_height;
global $explnum_associate_segments_svg, $explnum_associate_segments_svg_posX, $explnum_associate_segments_svg_posY, $explnum_associate_segments_svg_width, $explnum_associate_segments_svg_height;
global $explnum_associate_cursor_svg, $explnum_associate_cursor_svg_posX, $explnum_associate_cursor_svg_posY, $explnum_associate_cursor_svg_width, $explnum_associate_cursor_svg_height;

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
?>