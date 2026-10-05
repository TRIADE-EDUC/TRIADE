<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publisher_browser.php,v 1.13 2019/06/07 08:05:39 btafforeau Exp $

global $base_path, $base_auth, $base_title, $ancre, $coll_parent, $ed_parent, $j_offset, $browser_url, $open_folder, $closed_folder, $up_folder;
global $document, $requete, $image, $limite_affichage, $restriction, $msg;

// page d'affichage du browser de collections

// définition du minimum nécéssaire
$base_path="../../../..";
$base_auth = "CATALOGAGE_AUTH";
$base_title = "\$msg[6]";
require_once ("$base_path/includes/init.inc.php");

if(!isset($ancre)) $ancre = '';
if(!isset($coll_parent)) $coll_parent = 0;
if(!isset($ed_parent)) $ed_parent = 0;

// javascript pour retrouver l'offset dans la liste éditeurs
$j_offset = "
<script type='text/javascript'>
<!--
function jump_anchor(anc) {
	// récupération de l'index de l'ancre
	for ( i = 0; i <= document.anchors.length; i++) {
		if(document.anchors[i].name == anc) {
			anc_index = i;
			break;
		}
	}
	if (document.all) {
		// code pour IE
		document.anchors[anc_index].scrollIntoView();
	} else {
		// mettre ici le code pour Mozilla et Netscape quand on aura trouvé
	}
}
// -->
jump_anchor('$ancre');
</script>
";

// url du présent browser
$browser_url = "./publisher_browser.php";

print "<div id='contenu-frame'>";

// définition de variables
$open_folder = "<img src='".get_url_icon('folderopen.gif')."' border=\"0\" class='align_top' hspace='3'>";
$closed_folder = "<img src='".get_url_icon('folderclosed.gif')."' border=\"0\" class='align_top' hspace='3'>";
$up_folder = "<img src='".get_url_icon('folderup.gif')."' border=\"0\" class='align_top' hspace='3'>";
$document = "<img src='".get_url_icon('doc.gif')."' border=\"0\" class='align_top' hspace='3'>";

function select($ref, $id) {
	global $id_empr;
	// retourne le code javascript changeant l'adresse de la page pour affichage des notices
	// $ref -> type de donnée (editeur, collection)
	// $id -> id de l'objet recherché
	return "window.parent.document.location='../../../../catalog.php?categ=search&mode=2&etat=aut_search&aut_type=$ref&aut_id=$id&no_rec_history=1'; return(false);";
	}

if($coll_parent) {
	// affichage des enfants de la collection $coll_parent
	$requete = "SELECT * FROM publishers, collections, sub_collections";
	$requete .= " WHERE publishers.ed_id=collections.collection_parent";
	$requete .= " AND collections.collection_id=sub_collections.sub_coll_parent";
	$requete .= " AND sub_collections.sub_coll_parent=$coll_parent";
	$requete .= " ORDER BY sub_collections.sub_coll_name";
	$result = pmb_mysql_query($requete);
	$item = pmb_mysql_fetch_object($result);
	print "<a href='$browser_url?ed_parent=".$item->ed_id."'>$up_folder</a>...<br />";
	print $open_folder."<a href='#' onClick=\"".select('publisher', $item->ed_id)."\">".$item->ed_name."</a><br />";
	print "<div style='margin-left:18px'>$open_folder";
	print "<a href=\"\" onClick=\"".select('collection', $item->collection_id)."\">".$item->collection_name."</a></div>";
	print "<div style='margin-left:36px'>$document";
	print "<a href=\"\" onClick=\"".select('subcoll', $item->sub_coll_id)."\">".$item->sub_coll_name."</a></div>";
	while($item=pmb_mysql_fetch_object($result)) {
		print "<div style='margin-left:36px'>$document";
		print "<a href=\"\" onClick=\"".select('subcoll', $item->sub_coll_id)."\">".$item->sub_coll_name."</a></div>";
	}
} else {
	if($ed_parent) {
		// affichage des enfants de l'éditeur $ed_parent
		print "<a href='./publisher_browser.php?ancre=a$ed_parent'>".$up_folder.'</a>...<br />';

		// c'est Eric qui m'a dit ça. Merci Eric ;-) (de toute façon, j'ai jamais aimé les dimanches).
		$requete = "SELECT * FROM publishers, collections";
		$requete .= " LEFT JOIN sub_collections ON collections.collection_id=sub_collections.sub_coll_parent";
		$requete .= " WHERE publishers.ed_id=collections.collection_parent";
		$requete .= " AND publishers.ed_id=$ed_parent";
		$requete .= " ORDER BY collections.collection_name";

		$result = pmb_mysql_query($requete);
		$item = pmb_mysql_fetch_object($result);
		print $open_folder."<a href='#' onClick=\"".select('publisher', $item->ed_id)."\">".$item->ed_name.'</a><br />';
		if($item->sub_coll_id && $item->sub_coll_parent==$item->collection_id)
			$image = "<a href='$browser_url?coll_parent=".$item->collection_id."'>$closed_folder</a>";
		else
			$image = $document;
		print "<div style='margin-left:18px'>".$image."<a href='#' onClick=\"".select('collection', $item->collection_id)."\">".$item->collection_name.'</a></div>';

		while($item=pmb_mysql_fetch_object($result)) {
			if($item->sub_coll_id && $item->sub_coll_parent==$item->collection_id)
				$image = "<a href='$browser_url?coll_parent=".$item->collection_id."'>$closed_folder</a>";
			else
				$image = $document;
			print "<div style='margin-left:18px'>".$image."<a href='#' onClick=\"".select('collection', $item->collection_id)."\">".$item->collection_name.'</a></div>';
		}
	} else {
		if (!isset($limite_affichage)) 
			$restriction = " limit 0,30 ";
		else $restriction = "";
		
		print "<a href='$browser_url?limite_affichage=ALL'>$msg[tout_afficher]</a><br />";
		// affichage de la liste des éditeurs (1er niveau)
		$requete = "SELECT * FROM publishers LEFT JOIN collections";
		$requete .= " ON publishers.ed_id=collections.collection_parent";
		$requete .= " GROUP BY ed_name ORDER BY ed_name $restriction ";

		$result = pmb_mysql_query($requete);

		while($editeur=pmb_mysql_fetch_object($result)) {
			if($editeur->collection_id)
				$image = "<a name='a".$editeur->ed_id."' href='$browser_url?ed_parent=".$editeur->ed_id."'>$closed_folder</a>";
			else
				$image = $document;
			print $image."<a name='a".$editeur->ed_id."' href='#' onClick=\"".select('publisher', $editeur->ed_id)."\">".$editeur->ed_name."</a><br />\n";
		}
		if($ancre)
			print $j_offset;

	} // fin clause ed_parent
} // fin clause coll_parent

pmb_mysql_close();

// affichage du footer
print "</div></body></html>";
