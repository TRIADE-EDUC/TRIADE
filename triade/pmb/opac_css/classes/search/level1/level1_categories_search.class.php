<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level1_categories_search.class.php,v 1.8.4.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/search/level1/level1_authorities_search.class.php");
require_once($class_path."/thesaurus.class.php");

class level1_categories_search extends level1_authorities_search {

	protected $thesaurus_id;

	protected $builded_temporary_table = false;

	public function set_thesaurus_id($thesaurus_id) {
		$this->thesaurus_id = intval($thesaurus_id);
	}

	protected function build_temporary_table() {
		global $opac_stemming_active;
		global $lang, $default_tmp_storage_engine;

		$q = 'drop table if exists catjoin ';
		pmb_mysql_query($q);
		$q = 'create  temporary table catjoin ( ';
		$q.= "num_thesaurus int(3) unsigned not null default '0', ";
		$q.= "num_noeud int(9) unsigned not null default '0', ";
		$q.= 'key (num_noeud,num_thesaurus) ';
		$q.= ") ENGINE={$default_tmp_storage_engine} ";
		pmb_mysql_query($q);

		$aq=new analyse_query($this->user_query,0,0,1,0,$opac_stemming_active);
		$members_catdef = $aq->get_query_members('catdef','catdef.libelle_categorie','catdef.index_categorie','catdef.num_noeud');
		$members_catlg = $aq->get_query_members('catlg','catlg.libelle_categorie','catlg.index_categorie','catlg.num_noeud');

		$list_thes = $this->get_thesaurus();
		foreach ($list_thes as $id_thesaurus=>$libelle_thesaurus) {
			$thes = new thesaurus($id_thesaurus);
			$q="INSERT INTO catjoin SELECT noeuds.num_thesaurus, noeuds.id_noeud FROM ";
			if(($lang==$thes->langue_defaut) || (in_array($lang, thesaurus::getTranslationsList())===false)){
				$q.="noeuds JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND  catdef.langue = '".$thes->langue_defaut."'";
				//$q.=" WHERE noeuds.num_thesaurus='".$id_thesaurus."' AND not_use_in_indexation='0' AND catdef.libelle_categorie not like '~%' and ".$members_catdef["where"];
				$q.=" WHERE noeuds.num_thesaurus='".$id_thesaurus."' AND catdef.libelle_categorie not like '~%' and ".$members_catdef["where"];
			}else{
				$q.="noeuds JOIN categories as catdef on noeuds.id_noeud = catdef.num_noeud AND catdef.langue='".$thes->langue_defaut."' LEFT JOIN categories as catlg on catdef.num_noeud=catlg.num_noeud and catlg.langue = '".$lang."'";
				//$q.=" WHERE noeuds.num_thesaurus='".$id_thesaurus."' AND not_use_in_indexation='0' AND if(catlg.num_noeud is null, ".$members_catdef["where"].", ".$members_catlg["where"].") AND if(catlg.num_noeud is null,catdef.libelle_categorie not like '~%',catlg.libelle_categorie not like '~%')";
				$q.=" WHERE noeuds.num_thesaurus='".$id_thesaurus."' AND if(catlg.num_noeud is null, ".$members_catdef["where"].", ".$members_catlg["where"].") AND if(catlg.num_noeud is null,catdef.libelle_categorie not like '~%',catlg.libelle_categorie not like '~%')";
			}
			pmb_mysql_query($q);
		}
		$this->builded_temporary_table = true;
	}

	public function proceed() {
		if(!$this->builded_temporary_table) {
			$this->build_temporary_table();
		}
		parent::proceed();
	}

	protected function get_clause() {
		global $opac_search_other_function;
		global $typdoc;

		if(!isset($this->clause)) {
			if(!$this->builded_temporary_table) {
				$this->build_temporary_table();
			}
			$this->clause = '';
			$add_notice = '';

			if ($opac_search_other_function) {
				$add_notice=search_other_function_clause();
			}
			if ($typdoc || $add_notice) {
				$this->clause.= ' JOIN notices_categories ON notices_categories.num_noeud=catjoin.num_noeud JOIN notices ON notices_categories.notcateg_notice=notices.notice_id WHERE 1 ';
			} else {
				$this->clause.= ' WHERE 1 ';
			}
			if ($typdoc) {
				$this->clause.=" and typdoc='".$typdoc."' ";
			}
			if ($add_notice) {
				$this->clause.= ' and notice_id in ('.$add_notice.')';
			}
		}
		return $this->clause;
	}

	protected function get_hidden_search_content_form() {
		global $opac_thesaurus, $opac_thesaurus_defaut;

		$content_form = parent::get_hidden_search_content_form();
		if ($opac_thesaurus) {
			//mode multithesaurus
			$id_thes_for_link=-1;
		} else {
			//mode monothesaurus
			$id_thes_for_link=$opac_thesaurus_defaut;
		}
		$content_form .= "<input type=\"hidden\" name=\"id_thes\" value=\"".$id_thes_for_link."\">";
		return $content_form;
	}

	protected function get_thesaurus() {
		global $opac_thesaurus, $opac_thesaurus_defaut;

		if ($opac_thesaurus) {
			//mode multithesaurus
			return thesaurus::getThesaurusList();
		} else {
			//mode monothesaurus
			$thes = new thesaurus($opac_thesaurus_defaut);
			return array($opac_thesaurus_defaut => $thes->libelle_thesaurus);
		}
	}

	protected function get_display_by_thesaurus($tab='catalog') {
		global $msg, $charset;
		global $opac_thesaurus;

		$display = '';
		if ($opac_thesaurus) {	//mode multithesaurus dans l'opac
			$nb_thes=0;
			$list_thes = $this->get_thesaurus();
			foreach ($list_thes as $id_thesaurus=>$libelle_thesaurus) {
				$q = 'select count(distinct catjoin.num_noeud) from catjoin '.$this->get_clause();
				$q.= " and catjoin.num_thesaurus = '".$id_thesaurus."' ";
				$r = pmb_mysql_query($q);
				$nb = pmb_mysql_result($r, 0, 0);
				if ($nb) {
					$nb_thes++;
					if($nb_thes==1)$display .= '<blockquote role="presentation">';
					$display .= htmlentities($msg['thes_libelle'],ENT_QUOTES, $charset).' '.htmlentities($libelle_thesaurus,ENT_QUOTES, $charset).'&nbsp;'.$nb.' '.htmlentities($msg['results'],ENT_QUOTES, $charset).' ';
					if($tab == 'affiliate') {
						$display .= "<a href=\"#\" onclick=\"document.".$this->get_hidden_search_form_name().".action = '".$this->get_form_action()."&tab=catalog';document.forms.".$this->get_hidden_search_form_name().".id_thes.value='".$id_thesaurus."';document.forms.".$this->get_hidden_search_form_name().".count.value='".$nb."';document.".$this->get_hidden_search_form_name().".submit(); return false;\">$msg[suite]&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' /></a>";
					} else {
						$display .= "<a href=\"#\" onclick=\"document.forms.".$this->get_hidden_search_form_name().".count.value='".$nb."';submitSearch_CategorieForm('".$id_thesaurus."','".$nb."'); return false;\">".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' /></a>";
					}

					$display .= '<br />';
				}
			}
			if($nb_thes)$display .= ' </blockquote>';
		}
		return $display;
	}

	protected function get_display_link_affiliate() {
	    global $msg, $opac_rgaa_active;
		global $opac_thesaurus, $opac_thesaurus_defaut;

		if ($this->get_nb_results()) {
		    if ($opac_thesaurus) {
		        //mode multithesaurus
		        $id_thes_for_link=-1;
		    } else {
		        //mode monothesaurus
		        $id_thes_for_link=$opac_thesaurus_defaut;
		    }

		    if ($opac_rgaa_active) {
		        return "
    	            <button type='button' class='search_result' onclick=\"
                        document.".$this->get_hidden_search_form_name().".action = '".$this->get_form_action()."&tab=catalog';
                        document.forms.".$this->get_hidden_search_form_name().".id_thes.value='".$id_thes_for_link."';
                        document.forms.".$this->get_hidden_search_form_name().".count.value='".$this->get_nb_results()."';
                        document.".$this->get_hidden_search_form_name().".submit();
                        return false;\">
                       " . $msg['suite'] . "&nbsp;<img src='" . get_url_icon('search.gif') . "' aria-hidden='true' style='border:0px' />
    	            </button>";
		    }
		    return "<a href='#' onclick=\"
                        document.".$this->get_hidden_search_form_name().".action = '".$this->get_form_action()."&tab=catalog';
                        document.forms.".$this->get_hidden_search_form_name().".id_thes.value='".$id_thes_for_link."';
                        document.forms.".$this->get_hidden_search_form_name().".count.value='".$this->get_nb_results()."';
                        document.".$this->get_hidden_search_form_name().".submit();
                        return false;\">
                        " . $msg['suite'] . "&nbsp;<img src='" . get_url_icon('search.gif') . "' style='border:0px' />
                    </a>";
		}
	    return "";
	}

	protected function get_display_result() {
		global $msg;

		$display = "<div class='search_result' id=\"".$this->type."\" name=\"".$this->type."\">";
		$display .= "<strong>".$msg[$this->type]."</strong> ".$this->get_nb_results()." ".$msg['results']." ";
		$display .= "<a href=\"#\" onclick=\"document.forms['".$this->get_hidden_search_form_name()."'].submit(); return false;\">".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' /></a><br />";
		$display .= $this->get_display_by_thesaurus('catalog');
		$display .= $this->get_hidden_search_form();
		$display .= "</div>";
		return $display;
	}
}
