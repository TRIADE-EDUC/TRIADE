<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pixel.php,v 1.1 2018/03/01 16:49:42 dgoron Exp $

$base_path=".";
$base_nocheck = 1;
$base_noheader = 1;
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

//Génération d'un pixel transparent
$img = imagecreatetruecolor(1,1);
$col = imagecolorallocate($img,0,0,0);
imagecolortransparent($img, $col);
header('Content-Type: image/png');
imagepng($img);