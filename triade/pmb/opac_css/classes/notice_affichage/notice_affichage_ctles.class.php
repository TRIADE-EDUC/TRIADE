<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_affichage_ctles.class.php,v 1.41.2.1.2.1 2025/03/27 09:48:48 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/notice_affichage.class.php");

class notice_affichage_ctles extends notice_affichage {

	protected $display_childs = false; //Filles affichées dans la méthode do_public

	// génération de l'affichage public----------------------------------------
	public function do_public($short=0,$ex=1) {
		global $msg;
		global $charset;

		$this->notice_public= $this->genere_in_perio ();
		if(!$this->notice_id) return;

		/* début modif */
		// Notices parentes
		//$this->notice_public.=$this->parents;
		/* fin modif */

		$this->notice_public .= "<table>";
	//Titre
		// constitution de la mention de titre
		if ($this->notice->serie_name) {
			$this->notice_public.= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['tparent_start']."</span></td><td>".inslink($this->notice->serie_name,  str_replace("!!id!!", $this->notice->tparent_id, $this->lien_rech_serie));;
			if ($this->notice->tnvol) $this->notice_public .= ',&nbsp;'.$this->notice->tnvol;
			$this->notice_public .="</td></tr>";
		}

		$this->notice_public .= "<tr><td class='align_leftbg-grey'><span class='etiq_champ'>".$msg['title']." :</span></td>";
		$this->notice_public .= "<td><span class='public_title'>".$this->notice->tit1 ;
		//if($tdoc->table[$this->notice->typdoc]) $this->notice_public .= "&nbsp;[".$tdoc->table[$this->notice->typdoc]."]";
		if ($this->notice->tit4) $this->notice_public .= "&nbsp;: ".$this->notice->tit4 ;
		if ($this->notice->tit3) $this->notice_public .= "&nbsp;= ".$this->notice->tit3 ;
		if ($this->notice->mention_edition)  $this->notice_public .= "&nbsp;-&nbsp;".$this->notice->mention_edition ;
		$this->notice_public.="</span></td></tr>";

	//Préparation des champs personnalisés
	if (!$this->p_perso->no_special_fields) {
		if(!isset($this->memo_perso_) || !$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);
	}

	if(!$this->memo_perso_){
		$this->memo_perso_["FIELDS"]=array();
	}

	//PPN
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "ppn001")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".str_replace("Sudoc : ","",$p["AFF"])."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	//ISSN
		// ISBN ou NO. commercial
		$issn=$this->notice->code;
		$mes_pp= new parametres_perso("notices");
		$mes_pp->get_values($this->notice_id);
		$values = $mes_pp->values;
		foreach ( $values as $field_id => $vals ) {
			if($mes_pp->t_fields[$field_id]["NAME"] == "cp_issn_autres") {
				foreach ( $vals as $value ) {
					if($issn)$issn.=". ";
					$issn.=$mes_pp->get_formatted_output(array($value),$field_id);//Val
				}
			}
		}
		if ($issn) $this->notice_public .= "<tr><td' class='align_left bg-grey'><span class='etiq_champ'>".$msg['code_start']."</span></td><td>".htmlentities($issn,ENT_QUOTES, $charset)."</td></tr>";

	//Auteurs
		if ($this->auteurs_tous) $this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['auteur_start']."</span></td><td>".$this->auteurs_tous."</td></tr>";
		if ($this->congres_tous) $this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['congres_aff_public_libelle']."</span></td><td>".$this->congres_tous."</td></tr>";

	//Editeurs
		/*foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "cp_editeurs")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}*/
		//if ($this->notice->tit2) $this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['other_title_t2']." :</span></td><td>".$this->notice->tit2."</td></tr>" ;
		//if ($this->notice->tit3) $this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['other_title_t3']." :</span></td><td>".$this->notice->tit3."</td></tr>" ;

		//if ($tdoc->table[$this->notice->typdoc]) $this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['typdocdisplay_start']."</span></td><td>".$tdoc->table[$this->notice->typdoc]."</td></tr>";

		// mention d'édition
		//if ($this->notice->mention_edition) $this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['mention_edition_start']."</span></td><td>".$this->notice->mention_edition."</td></tr>";

	// Années de publication
		if ($this->notice->year)
			$this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".($charset != "utf-8"?"Années de publication":encoding_normalize::utf8_normalize("Années de publication"))." :</span></td><td>".$this->notice->year."</td></tr>" ;

	// $annee est vide si ajoutée avec l'éditeur, donc si pas éditeur, on l'affiche ici
		/*$this->notice_public .= $annee ;
		if ($this->notice->ed1_id) {
			$editeur = new publisher($this->notice->ed1_id);
			$this->publishers[]=$editeur;
			$this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['editeur_start']."</span></td><td>".inslink($editeur->display,  str_replace("!!id!!", $this->notice->ed1_id, $this->lien_rech_editeur))."</td></tr>" ;
			if ($annee) {
				$this->notice_public .= $annee ;
				$annee = "" ;
			}
		}*/
		// Autre editeur
		/*if ($this->notice->ed2_id) {
			$editeur_2 = new publisher($this->notice->ed2_id);
			$this->publishers[]=$editeur;
			$this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['other_editor']."</span></td><td>".inslink($editeur_2->display,  str_replace("!!id!!", $this->notice->ed2_id, $this->lien_rech_editeur))."</td></tr>" ;
		}*/
	// Numérotation
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "numerotation207")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Pays de publication
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "paysdepublication102")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}


	// Langues
		if (count($this->langues)) {
			$this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['537']." :</span></td><td>".$this->construit_liste_langues($this->langues);
			if (count($this->languesorg)) $this->notice_public .= " <span class='etiq_champ'>".$msg['711']." :</span> ".$this->construit_liste_langues($this->languesorg);
			$this->notice_public.="</td></tr>";
		} elseif (count($this->languesorg)) {
			$this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['711']." :</span></td><td>".$this->construit_liste_langues($this->languesorg)."</td></tr>";
		}

	// Périodicité
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "periodicite110")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Note générale
		if ($this->notice->n_gen) {
			$zoneNote = nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
			$this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['n_gen_start']."</span></td><td>".$zoneNote."</td></tr>";
		}

	// Périodicité
		if (!$this->p_perso->no_special_fields) {
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);
			foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p["AFF"] && ($p["NAME"] == "annexes320")){
					$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
					unset($this->memo_perso_["FIELDS"][$i]);
					break;
				}
			}
		}

	// Catégories
		if ($this->categories_toutes) $this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>Sujets :</span></td><td>".$this->categories_toutes."</td></tr>";

	// Titre clé
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titrecle530")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Titre abrégé
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titreabrege531")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Titre(s) parallèle(s)
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titreparallele510")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Titre(s) de couverture
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titredecouverture512")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Titre(s) courant(s)
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titrecourant515")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}
	// Titre(s) historique(s)
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titrehistorique520")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Autres titres
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titreautres517")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	// Titre développé
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "titredeveloppe532")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

	//Lien notices
		if($this->parents){
			$this->notice_public.=$this->parents;
		}
		$this->genere_notice_childs();
		if($this->notice_childs){
			$this->notice_public.=$this->notice_childs;
		}

	// Origine de la notice
		foreach ( $this->memo_perso_["FIELDS"] as $i => $value ) {
			$p=$this->memo_perso_["FIELDS"][$i];
			if ($p["AFF"] && ($p["NAME"] == "originenotice")){
				$this->notice_public.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
				unset($this->memo_perso_["FIELDS"][$i]);
				break;
			}
		}

		// collection
		/*if ($this->notice->nocoll) $affnocoll = " ".str_replace("!!nocoll!!", $this->notice->nocoll, $msg['subcollection_details_nocoll']) ;
		else $affnocoll = "";
		if($this->notice->subcoll_id) {
			$subcollection = new subcollection($this->notice->subcoll_id);
			$collection = new collection($this->notice->coll_id);
			$this->collections[]=$collection;
			$this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->name,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection))." ".$collection->collection_web_link."</td></tr>" ;
			$this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['subcoll_start']."</span></td><td>".inslink($subcollection->name,  str_replace("!!id!!", $this->notice->subcoll_id, $this->lien_rech_subcollection)) ;
			$this->notice_public .=$affnocoll."</td></tr>";
		} elseif ($this->notice->coll_id) {
			$collection = new collection($this->notice->coll_id);
			$this->collections[]=$collection;
			$this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['coll_start']."</span></td><td>".inslink($collection->isbd_entry,  str_replace("!!id!!", $this->notice->coll_id, $this->lien_rech_collection)) ;
			$this->notice_public .=$affnocoll." ".$collection->collection_web_link."</td></tr>";
		}*/

		// Titres uniformes
		/*if($this->notice->tu_print_type_2) {
			$this->notice_public.=
			"<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['titre_uniforme_aff_public']."</span></td>
			<td>".$this->notice->tu_print_type_2."</td></tr>";
		}*/
		// zone de la collation
		/*if($this->notice->npages) {
			if ($this->notice->niveau_biblio<>"a") {
				$this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['npages_start']."</span></td><td>".$this->notice->npages."</td></tr>";
			} else {
				$this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['npages_start_perio']."</span></td><td>".$this->notice->npages."</td></tr>";
			}
		}*/
		//if ($this->notice->ill) $this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['ill_start']."</span></td><td>".$this->notice->ill."</td></tr>";
		//if ($this->notice->size) $this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['size_start']."</span></td><td>".$this->notice->size."</td></tr>";
		//if ($this->notice->accomp) $this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['accomp_start']."</span></td><td>".$this->notice->accomp."</td></tr>";


		//if ($this->notice->prix) $this->notice_public .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['price_start']."</span></td><td>".$this->notice->prix."</td></tr>";



		if (!$short) $this->notice_public .= $this->aff_suite() ;
		else $this->notice_public.=$this->genere_in_perio();


		$this->notice_public.="</table>\n";

		//etat des collections
		if ($this->notice->niveau_biblio=='s' && $this->notice->niveau_hierar==1) $this->notice_public.=$this->affichage_etat_collections();

		// exemplaires, résas et compagnie
		if ($ex) $this->affichage_resa_expl = $this->aff_resa_expl() ;

		return;
	} // fin do_public($short=0,$ex=1)

	protected function get_notice_header($id_tpl=0) {
		global $opac_notice_reduit_format;
		global $msg, $charset;
		global $memo_notice;
	
		$notice_header="";
	
		if(!isset($this->notice_reduit_format)) {
			$this->notice_reduit_format = $opac_notice_reduit_format;
		}
		$type_reduit = substr($this->notice_reduit_format,0,1);
		$notice_tpl_header="";
		if ($type_reduit=="H" || $id_tpl){
			if(!$id_tpl) $id_tpl=substr($this->notice_reduit_format,2);
			if($id_tpl){
				$tpl = notice_tpl_gen::get_instance($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);
				if($notice_tpl_header){
					$notice_header=$notice_tpl_header;
					//coins pour Zotero
					$coins_span=$this->gen_coins_span();
					$notice_header.=$coins_span;
					$memo_notice[$this->notice_id]["header_without_doclink"]=$notice_header;
					$memo_notice[$this->notice_id]["header_doclink"]="";
					$memo_notice[$this->notice_id]["header"]=$notice_header;
					$memo_notice[$this->notice_id]["niveau_biblio"]	= $this->notice->niveau_biblio;
					return $notice_header;
				}
			}
		}
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($this->notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}
	
		if ($type_reduit=="E") {
			// zone de l'éditeur
			if ($this->notice->ed1_id) {
				$editeur = new publisher($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " (".$this->notice->year.")";
			} elseif ($this->notice->year) {
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;
	
		//Champs personalisés à ajouter au réduit
		if (!$this->p_perso->no_special_fields) {
			if (!empty($perso_voulus)) {
				$this->p_perso->get_values($this->notice_id) ;
				for ($i=0; $i<count($perso_voulus); $i++) {
					$perso_voulu_aff .= $this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])." " ;
				}
				$perso_voulu_aff=trim($perso_voulu_aff);
			} else $perso_voulu_aff = "" ;
		} else $perso_voulu_aff = "" ;
	
		//Si c'est un depouillement, ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2 && $this->parent_title)  {
			$aff_perio_title="<span class='header_perio'><i>".$msg['in_serial']." ".$this->parent_title.", ".$this->parent_numero." (".($this->parent_date?$this->parent_date:"[".$this->parent_aff_date_date."]").")</i></span>";
		}
	
		//Si c'est une notice de bulletin ajout du titre et bulletin
		if($this->notice->niveau_biblio == 'b' && $this->notice->niveau_hierar == 2)  {
			$aff_bullperio_title = "<span class='isbulletinof'><i> ".($this->parent_date?sprintf($msg["bul_titre_perio"],$this->parent_title):sprintf($msg["bul_titre_perio"],$this->parent_title.", ".$this->parent_numero." [".$this->parent_aff_date_date."]"))."</i></span>";
		} else $aff_bullperio_title="";
	
		// récupération du titre de série
		// constitution de la mention de titre
		if($this->notice->serie_name) {
			$notice_header = $this->notice->serie_name;
			if($this->notice->tnvol) $notice_header .= ', '.$this->notice->tnvol;
		} elseif ($this->notice->tnvol) $notice_header .= $this->notice->tnvol;
	
		if ($notice_header) $notice_header .= ". ".$this->notice->tit1 ;
		else $notice_header = $this->notice->tit1;
	
		if ($type_reduit=='4') {
			if ($this->notice->tit3 != "") $notice_header .= "&nbsp;=&nbsp;".$this->notice->tit3;
		}
	
		//Titre
		if ($this->notice->tit4) $notice_header .= "&nbsp;: ".$this->notice->tit4 ;
		if ($this->notice->tit3) $notice_header .= "&nbsp;= ".$this->notice->tit3 ;
		if ($this->notice->mention_edition)  $notice_header .= "&nbsp;-&nbsp;".$this->notice->mention_edition ;
		
		$notice_header .= $aff_bullperio_title;
	
		//$notice_header_without_html = $notice_header;
	
		$notice_header = "<span !!zoteroNotice!! class='header_title'>".$notice_header."</span>";
		//on ne propose à Zotero que les monos et les articles...
		if($this->notice->niveau_biblio == "m" ||($this->notice->niveau_biblio == "a" && $this->notice->niveau_hierar == 2)) {
			$notice_header =str_replace("!!zoteroNotice!!"," notice='".$this->notice_id."' ",$notice_header);
		}else $notice_header =str_replace("!!zoteroNotice!!","",$notice_header);
	
		$notice_header = '<span class="statutnot'.$this->notice->statut.'" '.(($this->statut_notice)?'title="'.htmlentities($this->statut_notice,ENT_QUOTES,$charset).'"':'').'></span>'.$notice_header;
	
		$notice_header_suite = "";
		if ($type_reduit=="T" && $this->notice->tit4) $notice_header_suite = " : ".$this->notice->tit4;
		if ($type_reduit!='3' && $this->auteurs_principaux) $notice_header_suite .= "<span class='header_authors'> / ".$this->auteurs_principaux."</span>";
		if (!empty($editeur_reduit)) $notice_header_suite .= " / ".$editeur_reduit ;
		if (!empty($perso_voulu_aff)) $notice_header_suite .= " / ".$perso_voulu_aff ;
		if (!empty($aff_perio_title)) $notice_header_suite .= " ".$aff_perio_title;
		//$notice_header_without_html .= $notice_header_suite ;
		//$notice_header .= $notice_header_suite."</span>";
		//Un  span de trop ?
		$notice_header .= $notice_header_suite;
	
		if ($this->notice->niveau_biblio =='m' || $this->notice->niveau_biblio =='s') {
			switch($type_reduit) {
				case '1':
					if ($this->notice->year != '') $notice_header.=' ('.htmlentities($this->notice->year,ENT_QUOTES,$charset).')';
					break;
				case '2':
					if ($this->notice->year != '' && $this->notice->niveau_biblio!='b') $notice_header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					if ($this->notice->code != '') $notice_header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
					break;
				default:
					break;
			}
		}
		return $notice_header;
	}

	public function aff_suite() {
		global $msg;
		global $opac_categories_affichage_ordre,$lang,$pmb_keyword_sep;
		global $opac_permalink, $opac_url_base;

		// afin d'éviter de recalculer un truc déjà calculé...
		if (isset($this->affichage_suite_flag) && $this->affichage_suite_flag) return $this->affichage_suite ;

		$ret = '';
		
		//Espace
		//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";

		// toutes indexations
		$ret_index = "";

		// Affectation du libellé mots clés ou tags en fonction de la recherche précédente
		//if($opac_allow_tags_search == 1) $libelle_key = $msg['tags'];
		//else $libelle_key = 	$msg['motscle_start'];

		// indexation libre
		//$mots_cles = $this->do_mots_cle() ;
		//if($mots_cles) $ret_index.= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$libelle_key."</span></td><td>".nl2br($mots_cles)."</td></tr>";

		// indexation interne
		/*if($this->notice->indexint) {
			$indexint = new indexint($this->notice->indexint);
			$ret_index.= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['indexint_start']."</span></td><td>".inslink($indexint->name,  str_replace("!!id!!", $this->notice->indexint, $this->lien_rech_indexint))." ".nl2br(htmlentities($indexint->comment,ENT_QUOTES, $charset))."</td></tr>" ;
		}*/
		//if ($ret_index) {
		//	$ret.=$ret_index;
			//$ret.="<tr class='tr_spacer'><td colspan='2' class='td_spacer'>&nbsp;</td></tr>";
		//}

		// résumé
		//if($this->notice->n_resume) $ret .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['n_resume_start']."</span></td><td class='td_resume'>".nl2br($this->notice->n_resume)."</td></tr>";

		// note de contenu
		//if($this->notice->n_contenu) $ret .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg['n_contenu_start']."</span></td><td>".nl2br(htmlentities($this->notice->n_contenu,ENT_QUOTES, $charset))."</td></tr>";

		//Champs personalisés
		$perso_aff = "" ;
		/*if (!$this->p_perso->no_special_fields) {
			// $this->memo_perso_ permet au affichages personalisés dans notice_affichage_ex de gagner du temps
			if(!$this->memo_perso_) $this->memo_perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($this->memo_perso_["FIELDS"]); $i++) {
				$p=$this->memo_perso_["FIELDS"][$i];
				if ($p['OPAC_SHOW'] && $p["AFF"]){
					if($p["NAME"] == "ppn001"){
						$perso_aff .="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".str_replace("Sudoc : ","",$p["AFF"])."</td></tr>";
					}else{
						$perso_aff .="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".strip_tags($p["TITRE"])."</span></td><td>".$p["AFF"]."</td></tr>";
					}
				}
			}
		}*/
		$ret .= $perso_aff ;

		/*if ($this->notice->lien) {
			$ret .= $this->get_line_aff_suite($msg['lien_start'], $this->get_constructed_external_url(), 'lien');
			if ($this->notice->eformat && substr($this->notice->eformat,0,3)!='RSS') $ret.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg["eformat_start"]."</span></td><td>".htmlentities($this->notice->eformat,ENT_QUOTES,$charset)."</td></tr>";
		}*/
		// Permalink avec Id
		if ($opac_permalink) {
			if($this->notice->niveau_biblio != "b"){
				$ret.= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id."'>".substr($opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id,0,80)."</a></td></tr>";
			}else {
				$ret.= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$msg["notice_permalink"]."</span></td><td><a href='".$opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id."'>".substr($opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id,0,80)."</a></td></tr>";
			}
		}

	//PCP
		$ret_index = "";
		$requete = "select * from (
			select libelle_thesaurus, c0.libelle_categorie as categ_libelle, c0.comment_public, n0.id_noeud , n0.num_parent, langue_defaut,id_thesaurus, if(c0.langue = '".$lang."',2, if(c0.langue= thesaurus.langue_defaut ,1,0)) as p, ordre_vedette, ordre_categorie
			FROM noeuds as n0, categories as c0,thesaurus,notices_categories
			where notices_categories.num_noeud=n0.id_noeud and n0.id_noeud = c0.num_noeud and n0.num_thesaurus=id_thesaurus and
			notices_categories.notcateg_notice=".$this->notice_id." AND id_thesaurus='2' order by id_thesaurus, n0.id_noeud, p desc
			) as list_categ group by id_noeud";
		if ($opac_categories_affichage_ordre==1) $requete .= " order by ordre_vedette, ordre_categorie";
		$result_categ=@pmb_mysql_query($requete);
		if ($result_categ && pmb_mysql_num_rows($result_categ)) {
			$ret_index .= "<tr><td class='align_left bg-grey'><span class='etiq_champ'>PCP :</span></td><td>";
			$first=true;
			while(($res_categ = pmb_mysql_fetch_object($result_categ))) {
				$categ_id=$res_categ->id_noeud 	;
				$libelle_categ=$res_categ->categ_libelle ;
				$comment_public=$res_categ->comment_public ;
				// Si il y a présence d'un commentaire affichage du layer
				$result_com = categorie::zoom_categ($categ_id, $comment_public);
				$libelle_aff_complet = inslink($libelle_categ,  str_replace("!!id!!", $categ_id, $this->lien_rech_categ), $result_com['java_com']);
				$libelle_aff_complet .= $result_com['zoom'];
				if(!$first)$ret_index .=" ".$pmb_keyword_sep." ";
				$first=false;
				$ret_index .=$libelle_aff_complet;
			}
			$ret_index .= "</td></tr>";
		}

		if ($ret_index) {
			$ret.=$ret_index;
		}

		$this->affichage_suite = $ret ;
		$this->affichage_suite_flag = 1 ;
		return $ret;
	} // fin aff_suite()

	// Construction des parents-----------------------------------------------------
	public function do_parents() {
		global $memo_notice;
		global $opac_notice_affichage_class;

		$this->parents = "";
		$r_type=array();
		$ul_opened=false;
		
		if($this->notice_relations->get_nb_parents()) {
			$this->parents .= "<div class='notice_parents'>";
			$parents = $this->notice_relations->get_parents();
			foreach ($parents as $relation_type=>$parents_relations) {
				foreach ($parents_relations as $parent) {
					if ($opac_notice_affichage_class) $notice_affichage=$opac_notice_affichage_class; else $notice_affichage="notice_affichage";
					
					if(!$memo_notice[$parent->get_linked_notice()]["header_without_doclink"]) {
						$parent_notice=new $notice_affichage($parent->get_linked_notice(),$this->liens,1,$this->to_print,1);
						$parent_notice->visu_expl = 0 ;
						$parent_notice->visu_explnum = 0 ;
						if ($this->parents_header_without_html) {
							$parent_notice->do_header_without_html();
						} else {
							$parent_notice->do_header();
						}
					}
					//Présentation différente si il y en a un ou plusieurs
					/*if ($this->notice_relations->get_nb_parents()==1) {
						// si une seule, peut-être est-ce une notice de bulletin, aller cherche $this>bulletin_id
						$this->parents.="<br /><b>".notice_relations::$liste_type_relation['up']->table[$relation_type]."</b> ";
						if ($this->lien_rech_notice) $this->parents.="<a href='".str_replace("!!id!!",$parent->get_linked_notice(),$this->lien_rech_notice)."&seule=1'>";
						//$this->parents.=$parent_notice->notice_header;
						if ($this->parents_header_without_html) {
							$this->parents.=$memo_notice[$parent->get_linked_notice()]["header_without_html"];
						} else {
							$this->parents.=$memo_notice[$parent->get_linked_notice()]["header_without_doclink"];
						}
						if ($this->lien_rech_notice) $this->parents.="</a>";
						$this->parents.="<br /><br />";
						// si une seule, peut-être est-ce une notice de bulletin, aller cherche $this->bulletin_id
						$rqbull="select bulletin_id from bulletins where num_notice=".$this->notice_id;
						$rqbullr=pmb_mysql_query($rqbull);
						$rqbulld=@pmb_mysql_fetch_object($rqbullr);
						$this->bulletin_id=$rqbulld->bulletin_id;
					} else {*/
						if (!$r_type[$relation_type]) {
							$r_type[$relation_type]=1;
							if ($ul_opened) $this->parents.="</td></tr>";
							else {
								//$this->parents.="<br />";
								$ul_opened=true;
							}
							$this->parents.="<tr><td class='align_left bg-grey'><span class='etiq_champ'>".notice_relations::$liste_type_relation['up']->table[$relation_type]."</span></td><td>\n";
							//$this->parents.="<ul class='notice_rel'>\n";
						}
						if ($this->lien_rech_notice) $this->parents.="<a href='".str_replace("!!id!!",$parent->get_linked_notice(),$this->lien_rech_notice)."&seule=1'>";
						//$this->parents.=$parent_notice->notice_header;
						if ($this->parents_header_without_html) {
							$this->parents.=$memo_notice[$parent->get_linked_notice()]["header_without_html"];
						} else {
							$this->parents.=$memo_notice[$parent->get_linked_notice()]["header_without_doclink"];
						}
						if ($this->lien_rech_notice) $this->parents.="</a>";
						$this->parents.="<br/>\n";
					/*}*/
				}
				//if($this->notice_relations->get_nb_parents() > 1) {
					$this->parents.="</td></tr>\n";
				//}
			}
			$this->parents .= "</div>\n";
		}
		return;		
	} // fin do_parents()

	protected function genere_childs_relation($relation_type, $child_notices) {
		$notice_childs = "<tr><td class='align_left bg-grey'><span class='etiq_champ'>".$relation_type." :</span></td>";
		if (!$this->seule) {
			$notice_childs .= "<td>";
		}
		foreach ($child_notices as $i=>$child) {
			if(($i<20) || $this->seule) {
				$notice_childs .= $child['display']."<br/>";
			} else {
				break;
			}
		}
		if ((count($child_notices)>20) && (!$this->seule)) {
			$notice_childs .= $this->link_see_more(count($child_notices));
		}
		if (!$this->seule) {
			$notice_childs.="</td></tr>";
		}
		return $notice_childs;
	}
	
	public function genere_notice_childs() {
		/* début modif */
		//Je ne veux que les liens vers les notices liées, pas de notices dépliables.
		$this->seule=0;
		/* fin modif */
		$this->notice_childs = parent::genere_notice_childs();
		$this->notice_childs .= "<br />";
		return $this->notice_childs;
	}

	public function affichage_etat_collections() {
		global $msg;
		global $pmb_etat_collections_localise;
		if ($this->notice->niveau_biblio!='s' && $this->notice->niveau_hierar!=1) return "";
		if($pmb_etat_collections_localise) {
			$this->coll_state_list("",0,0,0,1);
		} else {
			$this->coll_state_list("",0,0,0,0);
		}
		if($this->coll_state_list_nbr) {
			$affichage.= "<h3><span class='titre_exemplaires'>".$msg["perio_etat_coll"]."</span></h3>";
			$affichage.=$this->coll_state_list_liste;
		}

		return $affichage;
	} // fin affichage_etat_collections()

	//Récupérer de l'affichage complet
	public function coll_state_list($base_url,$filtre,$debut=0,$page=0, $type=0) {
		global $msg;
		global $opac_sur_location_activate, $opac_view_filter_class;
		global $opac_collstate_order, $opac_url_base;
		global $empr_location;
		global $script_coll_modif_ctles_is_include;

		$tpl_collstate_liste="
		<table class='exemplaires' style='padding:2px; width:100%'>
		<tbody>
		<tr>
		<th>Biblioth&egrave;que</th>
		<!--<th>Emplacement</th>-->
		<th>P&ocircle de conservation</th>
		<th>Cote</th>
		<th>Etat de collections</th>
		<th>Lacunes</th>
		<th>Fonds sp&eacute;cifique</th>
		<th>Notes</th>
		</tr>
		!!collstate_liste!!
		</tbody>
		</table>
		";
		if(!$script_coll_modif_ctles_is_include){
			$script_coll_modif_ctles_is_include=true;
			$tpl_collstate_liste="
<script>
function coll_modif_update(id,sql_field,texte){
	// récupération du form d'édition de la collection
	var action = new http_request();
	var url = \"./ajax.php?module=ajax&categ=extend&id=\"+id+\"&quoifaire=coll_save&texte=\"+texte+\"&sql_field=\"+sql_field;
	url = encodeURI(url);
	action.request(url);
}
</script>".$tpl_collstate_liste;
		}

		$tpl_collstate_liste_line="
		<tr class='!!pair_impair!!' !!tr_surbrillance!! >
		<td !!tr_javascript!! >!!localisation!!</td>
		<!--<td !!tr_javascript!! >!!emplacement_libelle!!</td>-->
		<td !!tr_javascript!! >!!statut_libelle!!</td>
		<td !!tr_javascript!! >!!cote!!</td>
		<td !!tr_javascript!! >!!state_collections!!</td>
		<td !!tr_javascript!! >!!lacune!!</td>
		<td !!tr_javascript!! >!!cp_stat_fonds!!</td>
		<td !!tr_javascript!! >!!note!!</td>
		</tr>";

		$location=$filtre->location;
		if($opac_view_filter_class){
			$req="SELECT  collstate_id , location_id, num_infopage, surloc_id FROM arch_statut, collections_state
			LEFT JOIN arch_emplacement ON collstate_emplacement=archempla_id, docs_location
			LEFT JOIN sur_location on docs_location.surloc_num=surloc_id
			WHERE ".($location?"(location_id='$location') and ":"")."id_serial='".$this->notice_id."'
			and location_id=idlocation and idlocation in(". implode(",",$opac_view_filter_class->params["nav_collections"]).")
			and archstatut_id=collstate_statut
			and ((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".( $_SESSION["user_code"]? " or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)" : "").")";
			if ($opac_collstate_order) $req .= " ORDER BY ".$opac_collstate_order;
			else $req .= " ORDER BY ".($type?"location_libelle, ":"")."archempla_libelle, collstate_cote";
		} else {
			$req="SELECT collstate_id , location_id, num_infopage, surloc_id FROM arch_statut, collections_state
			LEFT  JOIN docs_location ON location_id = idlocation
			LEFT JOIN sur_location on docs_location.surloc_num=surloc_id
			LEFT JOIN arch_emplacement ON collstate_emplacement=archempla_id
			WHERE ".($location?"(location_id='$location') and ":"")."id_serial='".$this->notice_id."'
			and archstatut_id=collstate_statut
			and ((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".( $_SESSION["user_code"]? " or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)" : "").")";
			if ($opac_collstate_order) $req .= " ORDER BY ".$opac_collstate_order;
			else $req .= " ORDER BY ".($type?"location_libelle, ":"")."archempla_libelle, collstate_cote";
		}
		$myQuery = pmb_mysql_query($req);

		if(($this->coll_state_list_nbr = pmb_mysql_num_rows($myQuery))) {

			$parity=1;
			while(($coll = pmb_mysql_fetch_object($myQuery))) {
				$my_collstate=new collstate($coll->collstate_id);
				if ($parity++ % 2) $pair_impair = "even"; else $pair_impair = "odd";
				$tr_javascript="  ";
				$tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";

				$line = str_replace('!!tr_javascript!!',$tr_javascript , $tpl_collstate_liste_line);
				$line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
				$line = str_replace('!!pair_impair!!',$pair_impair , $line);
				if ($opac_sur_location_activate) {
					$line = str_replace('!!surloc!!', $my_collstate->surloc_libelle, $line);
				}
				if ($my_collstate->num_infopage) {
					if ($my_collstate->surloc_id != "0") $param_surloc="&surloc=".$my_collstate->surloc_id;
					else $param_surloc="";
					$collstate_location = "<a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$my_collstate->num_infopage."&location=".$my_collstate->location_id.$param_surloc."\" title=\"".$msg['location_more_info']."\">".$my_collstate->location_libelle."</a>";
				} else
					$collstate_location = $my_collstate->location_libelle;
				$line = str_replace('!!localisation!!', $collstate_location, $line);
				$line = str_replace('!!cote!!', $my_collstate->cote, $line);
				$line = str_replace('!!type_libelle!!', $my_collstate->type_libelle, $line);
				$line = str_replace('!!emplacement_libelle!!', $my_collstate->emplacement_libelle, $line);
				$line = str_replace('!!origine!!', $my_collstate->origine, $line);
				if($empr_location==$my_collstate->location_id){
					// modif des notes
					$tpl_note_modif="
					<a onclick=\"document.getElementById('note_modif_".$coll->collstate_id."').style.display='block'; return false;\"  href=\"#\">
					<img style='border:0px' alt='Editer' title='Editer' src='".get_url_icon('tag.png')."'>
					</a>
					<div id='note_modif_".$coll->collstate_id."' style='display:none'>
					<textarea id='note_modif_text_".$coll->collstate_id."' class='saisie-80em'' wrap='virtual' rows='4' cols='40' name='note_modif_text_".$coll->collstate_id."'>".$my_collstate->note."</textarea><br />
					<input class='bouton' type='button' onclick=\"document.getElementById('note_modif_".$coll->collstate_id."').style.display='none';\" value='Annuler'>
					<input class='bouton' type='button' onclick=\"coll_modif_update(".$coll->collstate_id.",'collstate_note', document.getElementById('note_modif_text_".$coll->collstate_id."').value);
					document.getElementById('note_modif_".$coll->collstate_id."').style.display='none';
					document.getElementById('note_contens".$coll->collstate_id."').innerHTML=document.getElementById('note_modif_text_".$coll->collstate_id."').value.replace(/\\n/g,'<br />');\" value='Enregistrer'>
					</div>
					";
					// modif du statut de la collection
					//#33553 n'est plus modifiable
					//$on_change="coll_modif_update(".$coll->collstate_id.",'collstate_statut',document.getElementById('collstate_statut_".$coll->collstate_id."').value )";
					//$select =  gen_liste("select archstatut_id, archstatut_gestion_libelle from arch_statut order by 2", "archstatut_id", "archstatut_gestion_libelle", "collstate_statut_".$coll->collstate_id, $on_change, $my_collstate->statut, "", "","","",0) ;
					//$line = str_replace('!!statut_libelle!!',$select, $line);
					$line = str_replace('!!statut_libelle!!', $my_collstate->statut_opac_libelle, $line);
				}else {
					$tpl_coll_modif="";
					$tpl_lacune_modif="";
					$tpl_note_modif="";
					$line = str_replace('!!statut_libelle!!', $my_collstate->statut_opac_libelle, $line);
				}

				$line = str_replace('!!state_collections!!',"<div id='coll_contens_".$coll->collstate_id."'>".str_replace("\n","<br />",$my_collstate->state_collections)."</div>".$tpl_coll_modif, $line);
				$line = str_replace('!!archive!!',$my_collstate->archive, $line);
				$line = str_replace('!!lacune!!', "<div id='lacune_contens_".$coll->collstate_id."'>".str_replace("\n","<br />",$my_collstate->lacune)."</div>".$tpl_lacune_modif, $line);
				//cp_stat_fonds
				$requete="SELECT collstate_custom_small_text FROM collstate_custom_values JOIN collstate_custom ON collstate_custom_champ=idchamp WHERE collstate_custom_origine='".$coll->collstate_id."' AND name='cp_stat_fonds'";
				$rescp=pmb_mysql_query($requete);
				if($rescp && pmb_mysql_num_rows($rescp)){
					$line = str_replace('!!cp_stat_fonds!!', "<div id='cp_stat_fonds".$coll->collstate_id."'>".str_replace("\n","<br />",pmb_mysql_result($rescp,0,0))."</div>", $line);
				}else{
					$line = str_replace('!!cp_stat_fonds!!', "<div id='cp_stat_fonds".$coll->collstate_id."'></div>", $line);
				}
				$line = str_replace('!!note!!', "<div id='note_contens".$coll->collstate_id."'>".str_replace("\n","<br />",$my_collstate->note)."</div>".$tpl_note_modif, $line);
				$liste.=$line;
			}
			$liste = str_replace('!!collstate_liste!!',$liste , $tpl_collstate_liste);
			$liste = str_replace('!!base_url!!', $base_url, $liste);
			$liste = str_replace('!!location!!', $location, $liste);
		} else {
			$liste= $msg["collstate_no_collstate"];
		}
		$this->coll_state_list_liste=$liste;

	}

	// requête de récupération des categories ------------------------------------------------------------------
	public function get_query_categories() {
		global $lang;
		global $opac_categories_affichage_ordre;
	
		$query = "select * from (
			select libelle_thesaurus, c0.libelle_categorie as categ_libelle, c0.comment_public, n0.id_noeud , n0.num_parent, langue_defaut,id_thesaurus, if(c0.langue = '".$lang."',2, if(c0.langue= thesaurus.langue_defaut ,1,0)) as p, ordre_vedette, ordre_categorie
			FROM noeuds as n0, categories as c0,thesaurus,notices_categories
			where notices_categories.num_noeud=n0.id_noeud and n0.id_noeud = c0.num_noeud and n0.num_thesaurus=id_thesaurus and
			notices_categories.notcateg_notice=".$this->notice_id." AND id_thesaurus!='2' order by id_thesaurus, n0.id_noeud, p desc
			) as list_categ group by id_noeud";
		if ($opac_categories_affichage_ordre==1) $query .= " order by ordre_vedette, ordre_categorie";
		return $query;
	}

}// fin class notice_affichage_ctles
?>