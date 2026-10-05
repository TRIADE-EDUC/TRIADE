<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: import_clcc.inc.php,v 1.7 2022/09/07 15:13:30 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
global $action, $imp_empr;

require_once $class_path."/emprunteur.class.php";

function show_import_choix_fichier() {
	global $msg;
	global $current_module ;

print "
<form class='form-$current_module' name='form1' ENCTYPE=\"multipart/form-data\" method='post' action=\"./admin.php?categ=empr&sub=implec&action=1\">
<h3>Choix du fichier</h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='form_import_lec'>".$msg["import_lec_fichier"]."</label>
        <input name='import_lec' accept='text/plain' type='file' class='saisie-80em' size='40'>
	</div>
	<br />
	<div class='row'>
		<b>Pour plus de sécurité avant de lancer l'import faire une sauvegarde de la base de pmb</b>	
	</div>	
    <br />
	<div class='row'>
		Le fichier doit être au format cvs en ISO-8859-15 avec ; comme serapateur de champ et le texte entre guillemets (ex : \"Nom\")	
	</div>
	<br />
	<div class='row'>
		Le statut de lecteur Interdit ne doit pas avoir été supprimé, la localisation des lecteurs sera 'Clcc'
	</div>
	<br />
	<div class='row'>
		Si les lecteurs n'ont pas de date de sortie :<br />	
	</div>
	<div class='row'>
		<dd>	- Les lecteurs du fichier non présents dans la base seront abonnés pour 6 mois<br />	
	</div>
	<div class='row'>
		<dd>	- L'abonnement des lecteurs déjà présent sera repousé de six mois<br />	
	</div>
	<div class='row'>
		Si les lecteurs ont une date de sortie elle sera prise comme date de fin d'abonnement
	</div>
	<br />
	<div class='row'>
		Pour les lecteurs de la base non présent dans le fichier, ils seront supprimés s'ils n'ont pas de prêt en cours sinon leur statut sera 'Interdit'	
	</div>  
</div>
<div class='row'>
	<input name='imp_empr' type='submit' class='bouton' value='Importer les lecteurs'/>
</div>
</form>";
}

function import_lect_par_lect($tab){
    
	global $lect_cree,$lect_erreur,$lect_modif,$lect_non_traite;
	
	//On regarde si le lecteur existe déja en le recherchant par son badge
	$requete="select id_empr,empr_modif from empr where empr_cb='".addslashes($tab[12])."'";
	$select = pmb_mysql_query($requete);
	$nb_enreg = pmb_mysql_num_rows($select);
	if($nb_enreg == 1){
		if(pmb_mysql_result($select,0,1) != date('Y-m-j') ){
			$requete="update empr set empr_modif='".addslashes(date('Y-m-j'))."'";
			if($tab[4]){
				$data3=array();
				$data3=explode('/',$tab[4]);
				$empr_date_exp = $data3[2]."-".$data3[1]."-".$data3[0];
				$requete = $requete.", empr_date_expiration='".addslashes($empr_date_exp)."'";
			}else{
				$requete=$requete.", empr_date_expiration=DATE_ADD('".addslashes(date('Y-m-j'))."', INTERVAL 6 MONTH)";
			}
			$requete=$requete." where empr_cb='".addslashes($tab[12])."' ";
			if(!pmb_mysql_query($requete)){
				$lect_erreur++;
				echo "Erreur requete : ".$requete."<br />";
			}else{
				$lect_modif++;
			}
		}else{
			$lect_non_traite++;
			echo "Information : Le lecteur  ".$tab[1]." ".$tab[2]." avec le code barres ".$tab[12]." est présent plusieurs fois dans le fichier ou le fichier a déjà été traité<br />";
		}
		return;
	}elseif($nb_enreg > 1){
		$lect_erreur++;
		echo "<b>Erreur : Attention le code barre ".$tab[12]." est en double veuillez le modifier pour l'un des deux lecteurs<b><br />";
		return;
	}
	
	$data=array();
	
	$data['categ_libelle_create']="Indéterminé";
	if($tab[8]){
		$data['categ_libelle_create']=$tab[8];
	}
	
	$data['codestat_libelle_create']="Indéterminé";
	if($tab[6]){
		$data['codestat_libelle_create']=$tab[6];
	}
	
	$data['date_adhesion']=date('Y-m-j');
	$data['date_modif']=date('Y-m-j');
	
	$data['date_creation']=date('Y-m-j');
	if($tab[3]){
		$data3=array();
		$data3=explode('/',$tab[3]);
		if(count($data3) == 3){
			$data['date_creation']=$data3[2]."-".$data3[1]."-".$data3[0];
		}else{
			$data['date_creation']=date('Y-m-j');
		}	
	}

	if($tab[4]){
		$data3=array();
		$data3=explode('/',$tab[4]);
		if(count($data3) == 3){
			$data['date_expiration']=$data3[2]."-".$data3[1]."-".$data3[0];
		}
	}
	
	if(!$data['date_expiration']){
		if (($result = pmb_mysql_query("SELECT DATE_ADD('".addslashes(date('Y-m-j'))."', INTERVAL 6 MONTH)"))) {
			if (($row = pmb_mysql_fetch_row($result))) { 
				$data['date_expiration']= $row[0];		
			}
		}
	}
	
	$empr_cb =addslashes($tab[12]);
	if(!$empr_cb)$empr_cb="ind";
	$pb = 1 ;
	$num_cb=1 ;
	$empr_cb2=$empr_cb;
	while ($pb==1) {
		$q = "SELECT empr_cb FROM empr WHERE empr_cb='".$empr_cb2."' LIMIT 1 ";
		$r = pmb_mysql_query($q);
		$nb = pmb_mysql_num_rows($r);
		if ($nb) {
			$empr_cb2 =$empr_cb."-".$num_cb ;
			$num_cb++;
		} else $pb = 0 ;
	}
	$data['cb']=stripslashes($empr_cb2);
	if($data['cb'] != $tab[12]){
		echo "<b>Information : pour le lecteur ".$tab[1]." ".$tab[2]." le code barres sera ".$data['cb']." car le matricule ".$tab[12]." est déja utilisé comme code barres pour un autre lecteur</b><br />";
	}
	
	$data['nom']=$tab[1];
	
	$data['prenom']=$tab[2];

	$data['sexe']=0;
	if($tab[9] == "F"){
		$data['sexe']=2;
	}elseif($tab[9] == "M"){
		$data['sexe']=1;
	}
	
	$data['adr1']="";
	$data['adr2']="";
	$data['ville']="";
	$data['pays']="";
	$data['cp']="";
	$data['mail']=$tab[13];
	$data['tel1']=$tab[10];
	$data['tel2']="";
	$data['prof']=$tab[7];
	$data['year']="";
	$data['login'] = $tab[11];
	$data['password']=$tab[11];
	$data['location_libelle_create']= "Clcc";
	$data['msg']="";
	$data['lang']='fr_FR';
	$data['statut_libelle_create']="Actif";
	
	$mon_emprunteur= new emprunteur();
	$id_empr=$mon_emprunteur->import($data);
	if(!$id_empr){
		$lect_erreur++;
		echo "Erreur : Lecteur non cree\n";
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}else{
		$lect_cree++;
		if ($tab[0]) {
			$q="select idchamp from empr_custom where name='matricule' limit 1";
			$r = pmb_mysql_query($q);
			if (pmb_mysql_num_rows($r)) {
				$idchamp=pmb_mysql_result($r,0,0);
				$q = "insert into empr_custom_values (empr_custom_champ, empr_custom_origine, empr_custom_small_text) ";
				$q.= "values('".$idchamp."', '".$id_empr."','".addslashes($tab[0])."' ) ";
				$r=pmb_mysql_query($q);
			}
		}
	}
}

function import_empr(){
    
	global $lect_cree,$lect_erreur,$lect_modif,$lect_non_traite;
	$lect_tot=0;
	$lect_supprime=0;
	$lect_cree=0;
	$lect_erreur=0;
	$lect_modif=0;
	$lect_interdit=0;
	$lect_non_traite=0;
	
	//La structure du fichier texte doit être la suivante avec ceci comme première ligne: 
    //MATRICULE;NOM_USAGE;PRENOM_USAGE;DAT_DER_ENTREE;DAT_SORTIE;COD_UF;LIB_UF;POSTE;SEXE;TELEPHONE;USER_NAME;BADGE;MAIL
    
    //Upload du fichier
	if (!($_FILES['import_lec']['tmp_name'])) {
        print "Cliquez sur Pr&eacute;c&eacute;dent et choisissez un fichier";
	} elseif (!(move_uploaded_file($_FILES['import_lec']['tmp_name'], "./temp/".basename($_FILES['import_lec']['tmp_name'])))) {
        print "Le fichier n'a pas pu être téléchargé. Voici plus d'informations :<br />";
        print_r($_FILES)."<p>";
    }
    $fichier = @fopen( "./temp/".basename($_FILES['import_lec']['tmp_name']), "r" );
       
    if ($fichier) {
    	
    	//on change la date de mise a jour pour retrouver les lecteurs
    	$requete="select id_empr from empr where empr_modif='".addslashes(date('Y-m-j'))."'";
    	$select = pmb_mysql_query($requete);
        while (($verif = pmb_mysql_fetch_array($select))) {
        	$requete="update empr set empr_modif=DATE_SUB(empr_modif, INTERVAL 1 DAY) where id_empr='".addslashes($verif["id_empr"])."' ";
        	if(!pmb_mysql_query($requete)){
				echo "Erreur requete : ".$requete."<br />";
			}
        }
    	
    	while (!feof($fichier)) {
            $buffer = fgets($fichier, 4096);
            //$buffer = pmb_mysql_escape_string($buffer);
            $tab = explode(";", $buffer);
            $nb=0;
            $incr=0;
            $empr=array();
            
            $valeur=false;
            for($i=0;$i<count($tab);$i++){
            	
            	$nb=$nb+substr_count($tab[$i],"\"");
				if($nb%2 === 0){
					if($valeur === false){
						$valeur=$tab[$i];
					}else{
						$valeur=$valeur.";".$tab[$i];
					}
					//$notices[$i][$incr]=trim(trim($valeur,"\""));
					$empr[$incr]=preg_replace("/^\"|\"$|\"\r\n$/","",$valeur);
					$valeur=false;
					$incr++;
					$nb=0;
				}else{
					if($valeur === false){
						$valeur=$tab[$i];
					}else{
						$valeur=$valeur.";".$tab[$i];
					}				
				}
            }
            
            if(count($empr) == 1 or $empr[0] == "MATRICULE"){
            	//Passe ici pour l'entête et les lignes vides (la dernière)
            }elseif(count($empr) != 14){
            	$lect_tot++;
            	$lect_erreur++;
            	print("<b>Erreur : Personne non prise en compte car le nombre de champs n'est pas valide : </b><br />");
            	echo "<pre>";
          	 	print_r($empr);
            	echo "</pre>";
            }elseif(trim($empr[1]) == "" or trim($empr[12]) === ""){
            	$lect_tot++;
            	$lect_erreur++;
            	print("<b>Erreur : Personne non prise en compte car elle n'a pas de nom : </b><br />");
            	echo "<pre>";
          	 	print_r($empr);
            	echo "</pre>";
            }else{
            	//Tous les lecteurs à traiter
            	$lect_tot++;
            	import_lect_par_lect($empr);
            }	
    	}
    	
    	 //On supprime tous les lecteurs qui ne sont pas dans le fichier et qui n'ont pas de prets en cours
        $req_select_verif_pret = "SELECT distinct id_empr, pret_idempr FROM empr left join pret on id_empr=pret_idempr WHERE empr_modif != '".addslashes(date('Y-m-j'))."' ";
        $select_verif_pret = pmb_mysql_query($req_select_verif_pret);
        while (($verif_pret = pmb_mysql_fetch_array($select_verif_pret))) {
        	//pour tous les emprunteurs qui n'ont pas de pret en cours
        	if($verif_pret["pret_idempr"]){
        		$requete="update empr set empr_statut='2' where id_empr='".addslashes($verif_pret["id_empr"])."' ";
        		if(!pmb_mysql_query($requete)){
					$lect_erreur++;
					echo " requete echoué : ".$requete."<br />";
				}else{
					$lect_interdit++;
				}
        	}else{
        		emprunteur::del_empr($verif_pret["id_empr"]);
           		$lect_supprime++;
        	}
        }
        
    	print("<br />_____________________<br />");
    	if($lect_erreur)echo "<b> Attention ".$lect_erreur." lecteur(s) n'a(ont) pas été traité(s) : voir erreur(s) ci-dessus </b><br />";
        echo "Nombre de lecteurs créés : ".$lect_cree."<br />";
        echo "Nombre de lecteurs non traités (en double ou déjà traités) : ".$lect_non_traite."<br />";
        echo "Nombre de lecteurs pour lesquels la date d'expiration a été repoussée : ".$lect_modif."<br />";
        echo "Nombre total de lecteurs dans le fichier : ".$lect_tot."<br />";
        echo "Nombre d'anciens lecteurs (non présents dans le fichier) supprimés : ".$lect_supprime."<br />";
        echo "Nombre d'anciens lecteurs (non présents dans le fichier) avec un statut interdit (non supprimés car ils ont au moins un prêt en cours) : ".$lect_interdit."<br />";
  		
        fclose($fichier);
    }
}

switch($action) {
    case 1:
        if ($imp_empr){
            import_empr();
        }
        else {
            show_import_choix_fichier();
        }
        break;
    case 2:
        break;
    default:
        show_import_choix_fichier();
        break;
}
