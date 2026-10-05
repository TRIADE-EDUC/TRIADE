<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_date_publication.inc.php,v 1.5 2023/08/17 09:47:52 dbellamy Exp $

function search_other_function_filters() {
	global $sig_choix,$sig_date_debut,$sig_date_fin,$charset,$msg;

	$r="<br/><br/><div style='float:left;margin-right:10px;'><label class='etiquette' for='sig_choix'>".htmlentities($msg["search_date_pub_date"],ENT_QUOTES,$charset).": </label><select name='sig_choix' id='sig_choix'>\n" .
			"<option value='vide' !!vide!! >&nbsp;".htmlentities($msg["search_date_pub_choix"],ENT_QUOTES,$charset)."&nbsp;&nbsp;</option>\n" .
			"<option value='exacte' !!exacte!! >&nbsp;".htmlentities($msg["search_date_pub_exacte"],ENT_QUOTES,$charset)."</option>\n" .
			"<option value='entre' !!entre!! >&nbsp;".htmlentities($msg["search_date_pub_periode"],ENT_QUOTES,$charset)."</option>\n" .
			"<option value='debut' !!debut!! >&nbsp;".htmlentities($msg["search_date_pub_apres"],ENT_QUOTES,$charset)."</option>\n" .
			"<option value='fin' !!fin!! >&nbsp;".htmlentities($msg["search_date_pub_avant"],ENT_QUOTES,$charset)."</option>\n" .
			"</select></div><div style='float:left;' id=\"sig_contenu\"></div><div class='row'></div>\n";
	switch ($sig_choix) {
		case "exacte":
			$r=str_replace("!!exacte!!","selected=\"selected\"",$r);
			break;
		case "entre":
			$r=str_replace("!!entre!!","selected=\"selected\"",$r);
			break;
		case "debut":
			$r=str_replace("!!debut!!","selected=\"selected\"",$r);
			break;
		case "fin":
			$r=str_replace("!!fin!!","selected=\"selected\"",$r);
			break;
		default:
			$r=str_replace("!!vide!!","selected=\"selected\"",$r);
			break;
	}
	$r=preg_replace("/!![a-z]*!!/","",$r);
	
	$sig_js="<script>\n
	document.search_input.sig_choix.onchange=affiche_choix_date;
	affiche_choix_date();
	function affiche_choix_date(){
		var index_select = document.search_input.sig_choix.options[document.search_input.sig_choix.selectedIndex].value;
		div=document.getElementById('sig_contenu');
		a_supp=document.getElementById('div_contenu');
		if(a_supp){
			div.removeChild(a_supp);
		}
		switch(index_select){
			case 'debut':
				div_contenu=document.createElement('div');
				div_contenu.setAttribute('id','div_contenu');
				
				date_debut=document.createElement('input');
				date_debut.setAttribute('type','text');
				date_debut.setAttribute('name','sig_date_debut');
				date_debut.setAttribute('id','sig_date_debut');
				date_debut.setAttribute('placeholder','AAAA ou MM/AAAA ou JJ/MM/AAAA');
				date_debut.setAttribute('size','27');
				date_debut.setAttribute('maxlength','10');
				date_debut.value='".$sig_date_debut."';
				div_contenu.appendChild(date_debut);

				div.appendChild(div_contenu);
				break;
			case 'fin':
				div_contenu=document.createElement('div');
				div_contenu.setAttribute('id','div_contenu');
				
				date_fin=document.createElement('input');
				date_fin.setAttribute('type','text');
				date_fin.setAttribute('name','sig_date_fin');
				date_fin.setAttribute('id','sig_date_fin');
				date_fin.setAttribute('placeholder','AAAA ou MM/AAAA ou JJ/MM/AAAA');
				date_fin.setAttribute('size','27');
				date_fin.setAttribute('maxlength','10');
				date_fin.value='".$sig_date_fin."';
				div_contenu.appendChild(date_fin);
				
				div.appendChild(div_contenu);
				break;
			case 'exacte':
				div_contenu=document.createElement('div');
				div_contenu.setAttribute('id','div_contenu');
				
				date_debut=document.createElement('input');
				date_debut.setAttribute('type','text');
				date_debut.setAttribute('name','sig_date_debut');
				date_debut.setAttribute('id','sig_date_debut');
				date_debut.setAttribute('placeholder','AAAA ou MM/AAAA ou JJ/MM/AAAA');
				date_debut.setAttribute('size','27');
				date_debut.setAttribute('maxlength','10');
				date_debut.value='".$sig_date_debut."';
				div_contenu.appendChild(date_debut);
				
				div.appendChild(div_contenu);
				break;
			case 'entre':
				div_contenu=document.createElement('div');
				div_contenu.setAttribute('id','div_contenu');
				
				label_debut=document.createElement('label');
				label_debut.setAttribute('class','etiquette');
				label_debut.setAttribute('for','sig_date_debut');
				label_debut.innerHTML='Du: ';
				div_contenu.appendChild(label_debut);
				
				date_debut=document.createElement('input');
				date_debut.setAttribute('type','text');
				date_debut.setAttribute('name','sig_date_debut');
				date_debut.setAttribute('id','sig_date_debut');
				date_debut.setAttribute('placeholder','AAAA ou MM/AAAA ou JJ/MM/AAAA');
				date_debut.setAttribute('size','27');
				date_debut.setAttribute('maxlength','10');
				date_debut.value='".$sig_date_debut."';
				div_contenu.appendChild(date_debut);

				label_fin=document.createElement('label');
				label_fin.setAttribute('class','etiquette');
				label_fin.setAttribute('for','sig_date_fin');
				label_fin.innerHTML='&nbsp;&nbsp;&nbsp;&nbsp;Au: ';
				div_contenu.appendChild(label_fin);
				
				date_fin=document.createElement('input');
				date_fin.setAttribute('type','text');
				date_fin.setAttribute('name','sig_date_fin');
				date_fin.setAttribute('id','sig_date_fin');
				date_fin.setAttribute('placeholder','AAAA ou MM/AAAA ou JJ/MM/AAAA');
				date_fin.setAttribute('size','27');
				date_fin.setAttribute('maxlength','10');
				date_fin.value='".$sig_date_fin."';
				div_contenu.appendChild(date_fin);
				
				div.appendChild(div_contenu);
				break;
			default :
				break;
		}
	}	
	</script>";		
	return $r.$sig_js;
}

function search_other_function_clause() {

	//doit retourner une requete de selection d'identifiants de notices
	global $sig_choix,$sig_date_debut,$sig_date_fin;
	$r='';
	//Récupération de la date
	$sig_date_debut_formate=detectFormatDate($sig_date_debut);
	
	$sig_date_fin_formate=detectFormatDate($sig_date_fin);
	
	if($sig_choix == "exacte" && $sig_date_debut ){
		$r="SELECT notice_id FROM notices WHERE date_parution='".addslashes($sig_date_debut_formate)."'";
	}elseif($sig_date_debut && $sig_date_fin){
		$r="SELECT notice_id FROM notices WHERE date_parution >= '".addslashes($sig_date_debut_formate)."' AND date_parution <= '".addslashes($sig_date_fin_formate)."'";
	}elseif($sig_date_debut){
		$r="SELECT notice_id FROM notices WHERE date_parution >= '".addslashes($sig_date_debut_formate)."'";
	}elseif($sig_date_fin){
		$r="SELECT notice_id FROM notices WHERE date_parution <= '".addslashes($sig_date_fin_formate)."'";
	}
	
	return $r;
}

function search_other_function_has_values() {
	global $sig_date_debut,$sig_date_fin;
	if ($sig_date_debut || $sig_date_fin) return true; 
	else return false;
}

function search_other_function_get_values(){
	global $sig_choix,$sig_date_debut,$sig_date_fin;
	return $sig_choix."---".$sig_date_debut."---".$sig_date_fin;
}

function search_other_function_rec_history($n) {
	global  $sig_choix,$sig_date_debut,$sig_date_fin;
	$_SESSION["sig_choix".$n]=$sig_choix;
	$_SESSION["sig_date_debut".$n]=$sig_date_debut;
	$_SESSION["sig_date_fin".$n]=$sig_date_fin;
}

function search_other_function_get_history($n) {
	global  $sig_choix,$sig_date_debut,$sig_date_fin;
	$sig_choix=$_SESSION["sig_choix".$n];
	$sig_date_debut=$_SESSION["sig_date_debut".$n];
	$sig_date_fin=$_SESSION["sig_date_fin".$n];
}

function search_other_function_human_query($n) {
	global  $sig_choix,$sig_date_debut,$sig_date_fin,$charset,$msg;
	$r1="";
	
	$sig_choix=$_SESSION["sig_choix".$n];
	$sig_date_debut=$_SESSION["sig_date_debut".$n];
	$sig_date_fin=$_SESSION["sig_date_fin".$n];
	
	if($sig_choix == "exacte" && $sig_date_debut ){
		$r1=htmlentities($msg["search_date_pub_res_egal"],ENT_QUOTES,$charset)." ".$sig_date_debut;
	}elseif($sig_date_debut && $sig_date_fin){
		//$r1="Date de publication entre le ".$sig_date_debut." et le ".$sig_date_fin;
		$r1=htmlentities(sprintf($msg["search_date_pub_res_entre"],$sig_date_debut,$sig_date_fin),ENT_QUOTES,$charset);
	}elseif($sig_date_debut){
		$r1=htmlentities($msg["search_date_pub_res_sup"],ENT_QUOTES,$charset)." ".$sig_date_debut;
	}elseif($sig_date_fin){
		$r1=htmlentities($msg["search_date_pub_res_inf"],ENT_QUOTES,$charset)." ".$sig_date_fin;
	}
	
	return $r1;
}


function search_other_function_post_values() {
	global $sig_choix,$sig_date_debut,$sig_date_fin, $charset;
	return "<input type=\"hidden\" name=\"sig_choix\" value=\"".htmlentities($sig_choix, ENT_QUOTES, $charset)."\">\n<input type=\"hidden\" name=\"sig_date_debut\" value=\"".htmlentities($sig_date_debut, ENT_QUOTES, $charset)."\">\n<input type=\"hidden\" name=\"sig_date_fin\" value=\"".htmlentities($sig_date_fin, ENT_QUOTES, $charset)."\">\n";
}


?>