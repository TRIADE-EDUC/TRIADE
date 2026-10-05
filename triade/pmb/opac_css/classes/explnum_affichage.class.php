<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_affichage.class.php,v 1.20 2023/08/17 09:47:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once("$class_path/notice_affichage.class.php");
require_once("$include_path/explnum.inc.php");


define("DOCNUM_NOTI",0);
define("DOCNUM_DMDE",1);
define("DOCNUM_SUGG",2);

/**
 * Classe qui permet d'afficher les documents numériques après une recherche OPAC
 */
class explnum_affichage{
	
	public $tableau_id = array();
	public $display = "";
	public $type_elt = "";
	public $termes_recherche=""; //mots recherchés pour le pdf
	
	/**
	 * Constructeur
	 */
	public function __construct($liste_id=array(),$type='',$searchterms=array()){		
		$this->tableau_id = $liste_id;
		$this->type_elt = $type;
		if($searchterms)
			$this->termes_recherche = trim(str_replace('*',' ',implode(' ',$searchterms)));
		$this->construire_tableau();
	}
	
	/**
	 * Affichage sous forme de tableau des exemplaires
	 */
	public function construire_tableau(){
		
		global $_mimetypes_bymimetype_, $_mimetypes_byext_, $charset, $opac_url_base;
		global $opac_visionneuse_allow;
		
		$ligne_finale = "";
		
		if(!$this->tableau_id)
			$this->display = "";
		else {
		
			create_tableau_mimetype() ;
			$url_docnum="";
			switch($this->type_elt){				
				case DOCNUM_DMDE:
					$url_docnum="/explnum_doc.php?explnumdoc_id=";
					$requete = "SELECT id_explnum_doc as explnum_id, explnum_doc_nomfichier as explnum_nom,  explnum_doc_mimetype as explnum_mimetype,
						 explnum_doc_url as explnum_url, explnum_doc_data as explnum_data, explnum_doc_extfichier as explnum_extfichier
						  FROM explnum_doc join explnum_doc_actions on id_explnum_doc=num_explnum_doc WHERE prive=0 and num_action in (".implode(',',$this->tableau_id).")";
					break;
				case DOCNUM_SUGG:
					$url_docnum="/explnum_doc.php?explnumdoc_id=";
					$requete = "SELECT id_explnum_doc as explnum_id, explnum_doc_nomfichier as explnum_nom,  explnum_doc_mimetype as explnum_mimetype,
						 explnum_doc_url as explnum_url, explnum_doc_data as explnum_data, explnum_doc_extfichier as explnum_extfichier
						  FROM explnum_doc join explnum_doc_sugg on id_explnum_doc=num_explnum_doc WHERE num_suggestion in (".implode(',',$this->tableau_id).")";
					break;
				case DOCNUM_NOTI:
				default:	
					// récupération des infos des explnum
					$requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype,
						 explnum_url, explnum_data, explnum_vignette, explnum_nomfichier, explnum_extfichier
						  FROM explnum WHERE explnum_id in (".implode(',',$this->tableau_id).")";
					$url_docnum="/doc_num.php?explnum_id=";
					break;
			}
			$res = pmb_mysql_query($requete);
			
			$i=1;
			$allowed_mimetype=array();
			if ($opac_visionneuse_allow){
				global $opac_photo_filtre_mimetype;
				$allowed_mimetype = explode(",",str_replace("'","",$opac_photo_filtre_mimetype));
			}
			while(($expl = pmb_mysql_fetch_object($res))){
				if($expl->explnum_notice) {
					$notice_aff = new notice_affichage($expl->explnum_notice,'');
					$notice_aff->do_header_without_html();
					$titre = $notice_aff->notice_header_without_html;
				} elseif($expl->explnum_bulletin) {
					$titre = $this->get_header_bulletin($expl->explnum_bulletin);					
				}
				
				if ($i==1) $ligne="<div class='row'><div class='colonne3' >!!1!!</div><div class='colonne3' width='33%'>!!2!!</div><div class='colonne3' >!!3!!</div></div>" ;
				
				$alt = htmlentities($expl->explnum_nom." - ".$expl->explnum_mimetype,ENT_QUOTES, $charset) ;
				
				if ($expl->explnum_vignette) $obj="<img src='".$opac_url_base."/vig_num.php?explnum_id=$expl->explnum_id' alt='$alt' title='$alt' style='border:0px'>";
				else // trouver l'icone correspondant au mime_type
					$obj="<img src='".get_url_icon('mimetype/'.icone_mimetype($expl->explnum_mimetype, $expl->explnum_extfichier), 1)."' alt='$alt' title='$alt' style='border:0px'>";		
				
				$lien="";
				if($expl->explnum_notice)
					$lien = "index.php?lvl=notice_display&id=".$expl->explnum_notice;
				elseif($expl->explnum_bulletin) {	
					$lien = "index.php?lvl=bulletin_display&id=".$expl->explnum_bulletin;	
				}
				
				$words_to_find=""; 
				if(($expl->explnum_mimetype=='application/pdf') ||($expl->explnum_mimetype=='URL' && (strpos($expl->explnum_nom,'.pdf')!==false))){
					$words_to_find = "#search=\"".$this->termes_recherche."\"";
				}
				$expl_liste_obj = "<div class='explnum-titre' style=\"margin-top:20px;margin-bottom:10px;text-align:center;font-weight:bold;\" ><a href='$lien'>$titre</a></div>";
				$expl_liste_obj .= "<div style=\"text-align:center; word-wrap: break-word\">";
				if ($allowed_mimetype && in_array($expl->explnum_mimetype,$allowed_mimetype)){
					$link="
						<script>
							if(typeof(sendToVisionneuse) == 'undefined'){
								var sendToVisionneuse = function (explnum_id){
									document.getElementById('visionneuseIframe').src = 'visionneuse.php?'+(typeof(explnum_id) != 'undefined' ? 'explnum_id='+explnum_id+\"\" : '\'');
								}
							}
						</script>
						<a href='#' onclick=\"open_visionneuse(sendToVisionneuse,".$expl->explnum_id.");return false;\" title='$alt'>".$obj."</a><br />";
					$expl_liste_obj .=$link;
				} else {
					$expl_liste_obj .= "<a href='".$opac_url_base.$url_docnum.$expl->explnum_id.$words_to_find."' title='$alt' target='_blank'>".$obj."</a><br />" ;
				}
				
				if ($_mimetypes_byext_[$expl->explnum_extfichier]["label"]) $explmime_nom = $_mimetypes_byext_[$expl->explnum_extfichier]["label"] ;
				elseif ($_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"]) $explmime_nom = $_mimetypes_bymimetype_[$expl->explnum_mimetype]["label"] ;
				else $explmime_nom = $expl->explnum_mimetype ;
				
				$expl_liste_obj .= htmlentities($expl->explnum_nom,ENT_QUOTES, $charset)."<div class='explnum_type'>".htmlentities($explmime_nom,ENT_QUOTES, $charset)."</div>";
				
				$expl_liste_obj .= "</div>";
				
				$ligne = str_replace("!!$i!!", $expl_liste_obj, $ligne);
				$i++;
				if ($i==4) {
					$ligne_finale .= $ligne ;
					$i=1;
				}
			}
			if (!$ligne_finale) $ligne_finale = $ligne ;
				elseif ($i!=1) $ligne_finale .= $ligne ;
			$ligne_finale = str_replace('!!2!!', "&nbsp;", $ligne_finale);
			$ligne_finale = str_replace('!!3!!', "&nbsp;", $ligne_finale);
 	
		   
		   $this->display = $ligne_finale;
		}
		
		
	}
	
	/**
	 * Affichage des exemplaires numériques
	 */
	public function show_explnum(){
		print $this->display;
	}
	
	/**
	 *  Récupération des infos des bulletins
	 */	
	public function get_header_bulletin($id){
		$id = intval($id);
		$req = "select bulletin_notice, bulletin_numero, date_date, mention_date, bulletin_titre from bulletins where bulletin_id='".$id."'";
		$res = pmb_mysql_query($req);
		$header ='';
		while(($bull = pmb_mysql_fetch_object($res))){
			$notice_mere = $bull->bulletin_notice;
			$titre_bull = $bull->bulletin_titre;
			$date = $bull->date_date;
			$mention = $bull->mention_date;
			$num_bull = $bull->bulletin_numero;
		}
		
		$notice_aff =  new notice_affichage($notice_mere,'');
		
		$header = $notice_aff->notice->tit1.", ".$num_bull." (".($mention?$mention:"[$date]").")".($titre_bull?" - $titre_bull":'');
		
		return $header;
	}
	
	
}

?>