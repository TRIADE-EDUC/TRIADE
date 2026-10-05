<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animations.tpl.php,v 1.18 2021/04/07 12:31:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");


global $msg, $categ, $action;
global $animations_menu, $animations_layout, $animations_layout_end;

//	----------------------------------
// $animations_menu : Menu de la page animations

// Menu Recherche
$animations_menu = "
<div id='menu'>
<h3 onclick='menuHide(this,event)'>".$msg['animation_search']."</h3>
<ul>
	<li id='animations_menu_msg_animation_search' ".( empty($categ) ? "class='active'" : "" ).">
        <a href='./animations.php'>".$msg['animation_all_animations']."</a>
    </li>
</ul>";

// Menu Animations
$animations_menu .= "
<h3 onclick='menuHide(this,event)'>".$msg['animation_base_title']."</h3>
<ul>
	<li id='animations_menu_msg_animation_list' ".( $categ == "animations" && ($action == "list" || $action == "view") ? "class='active'" : "" ).">
        <a href='./animations.php?categ=animations&action=list'>".$msg['animation_list']."</a>
    </li>
	<li id='animations_menu_msg_list_animation_addAnimation' ".( $categ == "animations" && $action == "add" ? "class='active'" : "" ).">
		<a href='./animations.php?categ=animations&action=add'>".$msg['list_animation_addAnimation']."</a>
	</li>
	<li id='animations_menu_msg_list_animation_dndAnimation' ".( $categ == "animations" && $action == "dnd" ? "class='active'" : "" ).">
		<a href='./animations.php?categ=animations&action=gestion'>".$msg['list_animation_dndAnimation']."</a>
	</li>
</ul>";

// Menu Inscription
$animations_menu .= "
<h3 onclick='menuHide(this,event)'>".$msg['animation_resaservation']."</h3>
<ul>
	<li id='animations_menu_msg_animation_resaservation_list' ".( $categ == "registration" && $action == "list" ? "class='active'" : "" ).">
        <a href='./animations.php?categ=registration&action=list'>".$msg['animation_resaservation_list']."</a>
    </li>
</ul>";

// Gestion des alertes
$animations_menu .= "<div id='div_alert' class='erreur'></div>";

$animations_menu .= "</div>";
	

//	----------------------------------
// $animations_layout : layout page animations
$animations_layout = "
<div id='conteneur' class='animations'>
    $animations_menu
	<div id='contenu'>
";


//	----------------------------------
// $animations_layout_end : layout page animations (fin)
$animations_layout_end = '
	</div>
</div>
';

