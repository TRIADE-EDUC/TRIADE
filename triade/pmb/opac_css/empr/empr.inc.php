<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr.inc.php,v 1.48.2.1.2.2 2025/02/18 09:12:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset;
global $opac_resa, $opac_resa_dispo;
global $opac_rgaa_active;

$message_null_resa=$msg["empr_resa_empty"];
if ($opac_resa) {
	$message_null_resa .= "<br /><br /><p>".$msg["empr_resa_how_to"]."</p><br />
	<form style='margin-bottom:0px;padding-bottom:0px;' action='empr.php' method='post' name='FormName'>
	<INPUT type='button' class='bouton' 'name='lvlx' value='".$msg["empr_make_resa"]."' onClick=\"document.location='./index.php?search_type_asked=simple_search'\">
	</form>";
	if (!$opac_resa_dispo) {
	    $message_null_resa .= "<br /><p>".$msg["empr_resa_only_loaned_book"]."</p>";
	}
}

// recherche des valeurs dans la table empr suivant id_empr
$query = "SELECT *, date_format(empr_date_adhesion, '".$msg["format_date_sql"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$msg["format_date_sql"]."') as aff_empr_date_expiration, date_format(date_fin_blocage, '".$msg["format_date_sql"]."') as aff_date_fin_blocage FROM empr WHERE empr_login='$login'";
$result = pmb_mysql_query($query) or die("Query failed ".$query);

// récupération des valeurs MySQL du lecteur et injection dans les variables
while (($line = pmb_mysql_fetch_array($result, PMB_MYSQL_ASSOC))) {
	$id_empr=$line["id_empr"];
	$empr_cb = $line["empr_cb"];
	$empr_nom = $line["empr_nom"];
	$empr_prenom = $line["empr_prenom"];
	$empr_adr1 = $line["empr_adr1"];
	$empr_adr2 = $line["empr_adr2"];
	$empr_cp = $line["empr_cp"];
	$empr_ville = $line["empr_ville"];
	$empr_mail = $line["empr_mail"];
	$empr_tel1 = $line["empr_tel1"];
	$empr_tel2 = $line["empr_tel2"];
	$empr_prof = $line["empr_prof"];
	$empr_year = $line["empr_year"];
	$empr_categ = $line["empr_categ"];
	$empr_codestat = $line["empr_codestat"];
	$empr_sexe = $line["empr_sexe"];
	$empr_login = $line["empr_login"];
	$empr_password = $line["empr_password"];
	$empr_date_adhesion = $line["empr_date_adhesion"];
	$empr_date_expiration = $line["empr_date_expiration"];
	$aff_empr_date_adhesion = $line["aff_empr_date_adhesion"];
	$aff_empr_date_expiration = $line["aff_empr_date_expiration"];
	$date_fin_blocage = $line["date_fin_blocage"];
	$aff_date_fin_blocage = $line["aff_date_fin_blocage"];
	$empr_statut = $line["empr_statut"];
}
	
if($opac_rgaa_active){
	$empr_identite = "
	<div id='fiche-empr'>
        <h1>{$msg['onglet_empr_compte']}</h1>
        <h2><span>". htmlentities("{$empr_prenom} {$empr_nom}", ENT_QUOTES, $charset) ."</span></h2>
		<div id='fiche-empr-container'>
			<ul class='fiche-lecteur'>";
}else{
	$empr_identite = "
	<div id='fiche-empr'>
        <h3><span>". htmlentities("{$empr_prenom} {$empr_nom}", ENT_QUOTES, $charset) ."</span></h3>
		<div id='fiche-empr-container'>
			<table class='fiche-lecteur' role='presentation'>";
}


$i=0;
$tab_empr_info=array();
$tab_empr_info[$i]["titre"]=$msg["empr_tpl_cb"];
$tab_empr_info[$i]["class"]="tab_empr_info_cb";
$tab_empr_info[$i++]["val"]=$empr_cb;

if ($empr_adr1 || $empr_adr2 || $empr_cp || $empr_ville) {
    if ($empr_adr1 && $empr_adr2) {
        $empr_adr = $empr_adr1.$msg["empr_adr_separateur"].$empr_adr2;
    } else {
        $empr_adr = $empr_adr1.$empr_adr2;
    }
	
    if ($empr_adr &&($empr_cp || $empr_ville)) {
        $empr_adr .= $msg["empr_ville_separateur"];
    }
	$empr_adr .= "$empr_cp <u>$empr_ville</u>";
	
	$tab_empr_info[$i]["titre"]=$msg["empr_adresse"];
	$tab_empr_info[$i]["class"]="tab_empr_info_adr";
	$tab_empr_info[$i++]["val"]=$empr_adr;
}
if($empr_tel1 || $empr_tel2){
    if($empr_tel1 && $empr_tel2) {
        $tel = $empr_tel1.$msg["empr_tel2_separateur"].$empr_tel2;
    } else {
        $tel = $empr_tel1.$empr_tel2;
    }
	$tab_empr_info[$i]["titre"] = $msg["empr_tel_titre"];
	$tab_empr_info[$i]["class"] = "tab_empr_info_tel";
	$tab_empr_info[$i++]["val"] = $tel;
}
if($empr_mail){
    $empr_mail_aff = str_replace(";", "<br>", $empr_mail);
	$tab_empr_info[$i]["titre"]=$msg["empr_mail"];
	$tab_empr_info[$i]["class"]="tab_empr_info_mail";
	$tab_empr_info[$i++]["val"]="<a href='mailto:$empr_mail'>$empr_mail_aff</a>";	
}
if ($empr_prof){
	$tab_empr_info[$i]["titre"]=$msg["empr_tpl_prof"];
	$tab_empr_info[$i]["class"]="tab_empr_info_prof";
	$tab_empr_info[$i++]["val"]=$empr_prof;	
}
if ($empr_year){
	$tab_empr_info[$i]["titre"]=$msg["empr_tpl_year"];
	$tab_empr_info[$i]["class"]="tab_empr_info_year";
	$tab_empr_info[$i++]["val"]=$empr_year;
}

//Paramètres perso
require_once("$class_path/parametres_perso.class.php");
$p_perso=new parametres_perso("empr");
$perso_=$p_perso->show_fields($id_empr);
if (!empty($perso_["FIELDS"])) {
	for ($ipp=0; $ipp<count($perso_["FIELDS"]); $ipp++) {
		$p=$perso_["FIELDS"][$ipp];
		if(($p['OPAC_SHOW']==1) && $p["AFF"] !== ''){
			$tab_empr_info[$i]["titre"]=$p["TITRE_CLEAN"];
			$tab_empr_info[$i]["class"]="tab_empr_info_".$p["NAME"];
			$tab_empr_info[$i++]["val"]=$p["AFF"];
		}
	}
}

if($opac_rgaa_active){
	$adhesion=str_replace("!!date_adhesion!!","<span class='fw-bold'>".$aff_empr_date_adhesion."</span>",$msg["empr_format_adhesion"]);	
	$adhesion=str_replace("!!date_expiration!!","<span class='fw-bold'>".$aff_empr_date_expiration."</span>",$adhesion);	
}else{
	$adhesion=str_replace("!!date_adhesion!!","<strong>".$aff_empr_date_adhesion."</strong>",$msg["empr_format_adhesion"]);	
	$adhesion=str_replace("!!date_expiration!!","<strong>".$aff_empr_date_expiration."</strong>",$adhesion);	
}
$tab_empr_info[$i]["titre"]=$msg["empr_tpl_adh"];
$tab_empr_info[$i]["class"]="tab_empr_info_adh";
$tab_empr_info[$i++]["val"]=$adhesion;

if ($date_fin_blocage != "0000-00-00"){
	$date_blocage=array();
	$date_blocage=explode("-",$date_fin_blocage);
	if (mktime(0,0,0,intval($date_blocage[1]),intval($date_blocage[2]),intval($date_blocage[0]))>time()) {
		$tab_empr_info[$i]["titre"]=$msg["empr_tpl_date_fin_blocage"];
		$tab_empr_info[$i]["class"]="tab_empr_info_blocage";
		$blocage=str_replace("!!date_fin_blocage!!","<strong>".$aff_date_fin_blocage."</strong>",$msg["empr_tpl_blocage_pret"]);
		$tab_empr_info[$i++]["val"]=$blocage;
	}
}

//Message(s) de groupe(s)
$query = "SELECT distinct id_groupe, libelle_groupe, comment_opac 
    FROM groupe 
    JOIN empr_groupe ON empr_groupe.groupe_id = groupe.id_groupe
    JOIN empr ON empr.id_empr = empr_groupe.empr_id
    WHERE empr_login='".addslashes($login)."' AND groupe.comment_opac <> '' 
    ORDER BY libelle_groupe";
$result = pmb_mysql_query($query);
if (pmb_mysql_num_rows($result)) {
    $comments = array();
    while ($row = pmb_mysql_fetch_object($result)) {
        if($row->comment_opac) {
            $comments[] = $row->libelle_groupe." : ".$row->comment_opac;
        }
    }
    if(count($comments)) {
    	$tab_empr_info[$i]["titre"]=$msg["empr_groups_comments"];
    	$tab_empr_info[$i]["class"]="tab_empr_groups_comments";
    	$tab_empr_info[$i++]["val"]=implode('<br />', $comments);
    }
}


require_once($class_path.'/event/events/event_empr.class.php');
$event = new event_empr('empr', 'get_additionnal_informations');
$evth = events_handler::get_instance();
$event->set_empr_cb($empr_cb);
$evth->send($event);
$additionnal_informations = $event->get_additionnal_informations();
if ($additionnal_informations){
    foreach ($additionnal_informations as $info){
        $tab_empr_info[$i]["titre"] = $info['titre'];
        $tab_empr_info[$i]["class"] = $info['class'];
        $tab_empr_info[$i++]["val"] = $info['val'];
    }
}

if($opac_rgaa_active){
	foreach ($tab_empr_info as $ligne){
		$empr_identite.=
		"<li><div class='row d-flex flex-wrap empr_info ".$ligne["class"]."'>
			<div class='empr_label'><span class='etiq_champ'>".$ligne["titre"]."</span></div>	
			<div class='empr_content'>".$ligne["val"]."</div>
		</div></li>";
	}
}else{
	foreach ($tab_empr_info as $ligne){
		$empr_identite.=
		"<tr class='".$ligne["class"]."'>
			<td class='bg-grey align_right'><span class='etiq_champ'>".$ligne["titre"]."</span></td>	
			<td>".$ligne["val"]."</td>
		</tr>";
	}
}


// Mon Compte
global $pmb_relance_adhesion, $empr_ldap, $allow_pwd, $lvl, $empr_active_opac_renewal, $msg, $charset;
$my_account_item ='<ul class="empr_subtabs">';
$my_account_item .= "<li><a id='logout_profil' href='./index.php?logout=1'>" . htmlentities($msg['empr_logout'], ENT_QUOTES, $charset) . "</a></li>";
if (! $empr_ldap && $allow_pwd) {
    $my_account_item .= "<li " . (($lvl == "change_password") ? "class=\"subTabCurrent\"" : "") . "><a id='change_password' href='./empr.php?lvl=change_password'>" . htmlentities($msg['empr_modify_password'], ENT_QUOTES, $charset) . "</a></li>";
}
if(emprunteur_display::is_renewal_form_set() ){
    $my_account_item .= "<li " . (($lvl == "change_profil" ) ? "class=\"subTabCurrent\"" : "") . "><a id='change_profil' href='./empr.php?lvl=change_profil'>" . htmlentities($msg['empr_change_profil'], ENT_QUOTES, $charset) . "</a></li>";
}
$pmb_relance_adhesion = intval($pmb_relance_adhesion);
//Affichage de la prolongation si on est dans l'intervalle de relance
if ($empr_active_opac_renewal) {
    $datetime_empr_renewal_delay = new DateTime($empr_date_expiration);
    global $opac_empr_renewal_delay;
    $opac_empr_renewal_delay = intval($opac_empr_renewal_delay);
    //Autorise-t-on un delai après expiration
    if($opac_empr_renewal_delay) {
        $datetime_empr_renewal_delay->modify('+'.$opac_empr_renewal_delay.' days');
    }
    $datetime_empr_renewal_delay_format = $datetime_empr_renewal_delay->format('Y-m-d');
    $datetime_today = new DateTime(date('Y-m-d'));
    $datetime_today_format = $datetime_today->format('Y-m-d');
    $datetime_today->modify('+'.$pmb_relance_adhesion.' days');
    $datetime_relance_delay_format = $datetime_today->format('Y-m-d');
    //La date de fin d'adhesion (+ délai) doit etre superieure ou egale a la date du jour
    // && La date de fin d'adhésion doit etre inferieure ou egale a la date du jour incrementee du nombre de jours pour la relance
    if (strtotime($datetime_empr_renewal_delay_format) >= strtotime($datetime_today_format) && strtotime($empr_date_expiration) <= strtotime($datetime_relance_delay_format)) {
        $my_account_item .= "<li " . (($lvl == "renewal" ) ? "class=\"subTabCurrent\"" : "") . "><a id='renewal' href='./empr.php?lvl=renewal'>" . htmlentities($msg['empr_renewal'], ENT_QUOTES, $charset) . "</a></li><ul/>";
    }
}

if($opac_rgaa_active){
	$empr_identite .= "
			</ul>
	";
}else{
	$empr_identite .= "
			</table>
		<br />
	";
}
$empr_identite .= "
        </div>
        " . $my_account_item . "
    </div>
";