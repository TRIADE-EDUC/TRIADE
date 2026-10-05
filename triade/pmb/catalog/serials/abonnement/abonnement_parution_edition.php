<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abonnement_parution_edition.php,v 1.13 2022/01/03 10:20:17 dgoron Exp $

// définition du minimum nécéssaire
$base_path="./../../..";
$base_auth = "CATALOGAGE_AUTH";
$base_title = "\$msg[6]";
require_once ("$base_path/includes/init.inc.php");

global $current_module, $msg, $act, $abonnement_id, $nom, $date_parution;

$abonnement_id = intval($abonnement_id);

$templates = <<<ENDOFFILE
			<script type='text/javascript'>
				function Fermer(obj,type_doc) {
					var id_obj=parent.document.getElementById(obj);
					if(type_doc==1)  id_obj.className='lien_date';
					else if(type_doc==2) id_obj.className= 'lien_date_hs';
					else if(type_doc==3) id_obj.className= 'lien_date_hs_p';
					else id_obj.className= '';
				 	parent.kill_frame_periodique();
				}				
			</script>
<div style='width: 90%;'>
	<div id="bouton_fermer_notice_preview" class="right"><a href='#' class='panel-close' onClick='parent.kill_frame_periodique();return false;'><i class='fa fa-times' aria-hidden='true'></i></a></div>
	!!form!!
</div>						
ENDOFFILE;

function gen_serie($id,$nom,$nombre) {
	$serie="
		<div class='row'>
		<input type='text' size='4' name='serie[$id]' id='$id' value='$nombre'/>
		$nom
		</div>";
	return $serie;	
}

function gen_hors_serie($id,$modele_name,$checked,$nom,$numero) {
	global $msg;
	$serie="
		<div class='row'>
		<input type='checkbox' value='2' $checked name='check_hors_serie[$id]'/>$modele_name
		</div>
		<div class='row'>
		".$msg["abonnements_attribuer_un_numero"]."
		</div>
		<div class='row'>
		<input type='text' size='15' name='numero[$id]' id='numero' value='$numero'/>
		</div>";
	return $serie;	
}


$form="<form class='form-$current_module' id='form_modele' name='form_modele' method='post' action='./abonnement_parution_edition.php?abonnement_id=$abonnement_id&date_parution=$date_parution'>
	<div class='row center'>!!date_parution!!</div>			
	<div class='row'>
	".$msg["abonnements_edition_serie"]." : 
	</div>
	!!series!!	
	<hr />
	".$msg["abonnements_hors_serie"]." :
	!!hors_series!!	
	&nbsp;
	<div class='row'>
		<input type='hidden' id='act' name='act' value='' />		
		<div class='left'>
			<input class='bouton_small' value='".$msg["77"]."' onclick=\"document.getElementById('act').value='update';this.form.submit();\" type='submit'>
		</div>
	</div>
	</form>";

$type_doc=0;
switch ($act) {
	case 'update':	
		$requete = "delete FROM abts_grille_abt WHERE num_abt='$abonnement_id' and date_parution ='$date_parution'  ";
		pmb_mysql_query($requete);	
		$requete = "SELECT  distinct (modele_name) ,abts_grille_abt.modele_id from abts_modeles, abts_grille_abt where num_abt='$abonnement_id' and abts_grille_abt.modele_id = abts_modeles.modele_id ";
		$resultat=pmb_mysql_query($requete);
		while($r=pmb_mysql_fetch_object($resultat)){
			$modele_name=$r->modele_name;
			$modele_id=$r->modele_id;
			$nombre= $serie[$modele_id];
			/*			if($nombre){
				$requete = "INSERT INTO abts_grille_abt SET num_abt='$abonnement_id', date_parution ='$date_parution', modele_id = '$modele_id', type = '1', nombre='$nombre'";
				pmb_mysql_query($requete);
				$type_serie=1;
			}*/
			for($i=1;$i<=$nombre;$i++){
				$requete = "INSERT INTO abts_grille_abt SET num_abt='$abonnement_id', 
							date_parution ='$date_parution', 
							modele_id='$modele_id', 
							type = '1',
							nombre='1', 
							ordre='$i' ";
				pmb_mysql_query($requete);
				$type_serie=1;			
			}

			if (isset($check_hors_serie[$modele_id])){	
				$numero= $numero[$modele_id];
				$requete = "INSERT INTO abts_grille_abt SET num_abt='$abonnement_id', date_parution ='$date_parution', modele_id = '$modele_id', type = '2', numero='$numero', nombre='1', ordre='1' ";
				pmb_mysql_query($requete);
				$type_horsserie=2;					
			}				
		}		
		$type_doc=	$type_horsserie+$type_serie;
		$form="<script type='text/javascript'>Fermer('$date_parution','$type_doc');</script>";
	break;
	case 'change':
		$form="<script type='text/javascript'>Fermer('$date_parution','$type_doc');</script>";		
	break;
	default:			
		$series="";
		$hors_series="";
		$requete = "SELECT  distinct (modele_name) ,abts_grille_abt.modele_id from abts_modeles, abts_grille_abt where num_abt='$abonnement_id' and abts_grille_abt.modele_id = abts_modeles.modele_id ";
		$resultat=pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){
				$modele_name=$r->modele_name;
				$modele_id=$r->modele_id;
				$nombre=0;
				$requete = "SELECT COUNT(*) as nombre from abts_grille_abt where num_abt='$abonnement_id' and date_parution ='$date_parution' and modele_id = '$modele_id' and type='1' ";
				$resultat_nb=pmb_mysql_query($requete);
				if($r_nb=pmb_mysql_fetch_object($resultat_nb)){
					$nombre=$r_nb->nombre;					
				}
				$series.=gen_serie($modele_id,$modele_name,$nombre);
				$checked="";
				$numero="";
				$requete = "SELECT numero from abts_grille_abt where num_abt='$abonnement_id' and date_parution ='$date_parution' and modele_id = '$modele_id' and type='2' ";
				$resultat_nb=pmb_mysql_query($requete);
				if($r_nb=pmb_mysql_fetch_object($resultat_nb)){
					$numero=$r_nb->numero;
					$checked= "checked";
				}		
				$hors_series.=gen_hors_serie($modele_id,$modele_name,$checked,$nom,$numero);
			}
		}
		$form=str_replace("!!series!!",$series,$form);
		$form=str_replace("!!hors_series!!",$hors_series,$form);
		$form=str_replace("!!date_parution!!",formatdate($date_parution),$form);
		break;
	}
	
print str_replace("!!form!!",$form,$templates);
print "</body></html>"
?>