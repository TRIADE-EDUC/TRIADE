<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: perio_a2z_view.php,v 1.6 2023/08/17 09:47:54 dbellamy Exp $

$base_path=".";
//Affichage d'une notice
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path.'/includes/templates/common.tpl.php');

// classe de gestion des catégories
require_once($base_path.'/classes/categorie.class.php');
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/notice_display.class.php');

// classe indexation interne
require_once($base_path.'/classes/indexint.class.php');

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

// classe de gestion des réservations
require_once($base_path.'/classes/resa.class.php');

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");
require_once($base_path."/includes/explnum.inc.php");
require_once($base_path."/includes/notice_affichage.inc.php");

require_once($base_path."/classes/perio_a2z.class.php");

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

// paramétrage de base
$templates = <<<ENDOFFILE
	<html>
		<head>
			!!styles!!
		</head>
		<body>			
			<script>
				function show_what(quoi, id) {
					var whichISBD = document.getElementById('div_isbd' + id);
					var whichPUBLIC = document.getElementById('div_public' + id);
					var whichongletISBD = document.getElementById('onglet_isbd' + id);
					var whichongletPUBLIC = document.getElementById('onglet_public' + id);
					if (quoi == 'ISBD') {
						whichISBD.style.display  = 'block';
						whichPUBLIC.style.display = 'none';
						whichongletPUBLIC.className = 'isbd_public_inactive';
						whichongletISBD.className = 'isbd_public_active';
					} else {
						whichISBD.style.display = 'none';
						whichPUBLIC.style.display = 'block';
			  			whichongletPUBLIC.className = 'isbd_public_active';
						whichongletISBD.className = 'isbd_public_inactive';
					}
			  	}		  	
			</script>
			
			<table style='width:100%'>
				<tbody>
					<tr>
						<td style='vertical-align:top'>
							!!content!!
						</td>
					</tr>
					</td>
				</tbody>
			</table> 
			
		</body>
	</html>
ENDOFFILE;

$templates=str_replace("!!styles!!",$stylescsscodehtml,$templates);

$onglet_sel= $_GET["onglet_sel"];
$a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);
$perio_a2z=$a2z->get_form($onglet_sel);

print str_replace("!!content!!",$perio_a2z,$templates);

?>