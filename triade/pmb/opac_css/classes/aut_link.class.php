<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aut_link.class.php,v 1.18 2019-04-19 12:28:25 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
// gestion des liens entre autorités

require_once("$class_path/marc_table.class.php");
require_once("$class_path/author.class.php");
require_once("$class_path/publisher.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once("$class_path/indexint.class.php");
require_once("$class_path/serie.class.php");
require_once("$class_path/category.class.php");
require_once("$class_path/titre_uniforme.class.php");
require_once("$class_path/authperso.class.php");
require_once("$class_path/authorities_collection.class.php");
//require_once("$class_path/concept.class.php");


//require_once($include_path."/templates/aut_link.tpl.php");

define('AUT_TABLE_AUTHORS',1);
define('AUT_TABLE_CATEG',2);
define('AUT_TABLE_PUBLISHERS',3);
define('AUT_TABLE_COLLECTIONS',4);
define('AUT_TABLE_SUB_COLLECTIONS',5);
define('AUT_TABLE_SERIES',6);
define('AUT_TABLE_TITRES_UNIFORMES',7);
define('AUT_TABLE_INDEXINT',8);
define('AUT_TABLE_AUTHPERSO',9);
define('AUT_TABLE_CONCEPT',10);
define('AUT_TABLE_INDEX_CONCEPT',11);
// Pour la classe authorities_collection
define('AUT_TABLE_CATEGORIES',12);
define('AUT_TABLE_AUTHORITY',13);

$aut_table_name_list=array(
	AUT_TABLE_AUTHORS => 'authors',
	AUT_TABLE_CATEG => 'categ',
	AUT_TABLE_PUBLISHERS=> 'publishers',
	AUT_TABLE_COLLECTIONS => 'collection',
	AUT_TABLE_SUB_COLLECTIONS => 'sub_collections',
	AUT_TABLE_SERIES => 'series',
	AUT_TABLE_TITRES_UNIFORMES => 'titres_uniformes',
	AUT_TABLE_INDEXINT => 'indexint',
	AUT_TABLE_CONCEPT => 'concept',
	AUT_TABLE_INDEX_CONCEPT => 'concept',
	AUT_TABLE_AUTHPERSO => 'authperso'
); 

// définition de la classe de gestion des liens entre autorités
class aut_link {

	public $aut_table;
	public $id;
	
	public function __construct($aut_table,$id) {
		$this->aut_table = $aut_table;
		$this->id = $id;
		$this->getdata();
	}	

	public function getdata() {
		global $dbh,$msg;
		global $aut_table_name_list;
		global $pmb_opac_url;
		if($this->aut_table > 1000) {
			$this->aut_table_name = $aut_table_name_list[AUT_TABLE_AUTHPERSO];
		} else {
			$this->aut_table_name = $aut_table_name_list[$this->aut_table];
		}
		$this->aut_list=array();		
			
		$rqt="select * from aut_link where (aut_link_from='".$this->aut_table."' and aut_link_from_num='".$this->id."')
		order by aut_link_rank";
		$aut_res=pmb_mysql_query($rqt, $dbh);
		$i=0;
		while ($row = pmb_mysql_fetch_object($aut_res)) {
			$i++;
			$this->aut_list[$i]['to'] = $row->aut_link_to;
			$this->aut_list[$i]['to_num'] = $row->aut_link_to_num;
			$this->aut_list[$i]['type'] = $row->aut_link_type;
			$this->aut_list[$i]['comment'] = $row->aut_link_comment;
			$this->aut_list[$i]['string_start_date'] = $row->aut_link_string_start_date;
			$this->aut_list[$i]['string_end_date'] = $row->aut_link_string_end_date;
			$this->aut_list[$i]['start_date'] = $row->aut_link_start_date;
			$this->aut_list[$i]['end_date'] = $row->aut_link_end_date;
			$this->aut_list[$i]['rank'] = $row->aut_link_rank;
			$this->aut_list[$i]['direction'] = $row->aut_link_direction;
			$this->aut_list[$i]['reverse_link_num'] = $row->aut_link_reverse_link_num;
						
			if($this->aut_list[$i]['reverse_link_num']) {
				$this->aut_list[$i]['flag_reciproc'] = 1;
			} else {
				$this->aut_list[$i]['flag_reciproc'] = 0;
			}
			
			switch($this->aut_list[$i]['to']){
				case AUT_TABLE_AUTHORS :
					$auteur = authorities_collection::get_authority("author", $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $auteur->get_isbd();
					$this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_author'] ,$auteur->get_isbd());
				break;
				case AUT_TABLE_CATEG :
					$categ = authorities_collection::get_authority("category", $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $categ->libelle;
					$this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_categ'], $categ->libelle);
				break;
				case AUT_TABLE_PUBLISHERS :
					$ed = authorities_collection::get_authority("publisher", $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $ed->get_isbd();
					$this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_publisher'] ,$ed->get_isbd());
				break;
				case AUT_TABLE_COLLECTIONS :
					$collection = authorities_collection::get_authority("collection", $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $collection->get_isbd();
					$this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_coll'], $collection->get_isbd());
				break;
				case AUT_TABLE_SUB_COLLECTIONS :
					$subcollection = authorities_collection::get_authority("subcollection", $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $subcollection->get_isbd();
					$this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_subcoll'], $subcollection->get_isbd());
				break;
				case AUT_TABLE_SERIES :
					$serie = authorities_collection::get_authority("serie", $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $serie->get_isbd();
					$this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_serie'], $serie->get_isbd());
				break;
				case AUT_TABLE_TITRES_UNIFORMES :
					$tu = authorities_collection::get_authority("titre_uniforme", $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $tu->get_isbd();
					$this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_tu'], $tu->get_isbd());
				break;
				case AUT_TABLE_INDEXINT :
					$indexint = authorities_collection::get_authority("indexint", $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $indexint->get_isbd();
					$this->aut_list[$i]['libelle'] = sprintf($msg['aut_link_indexint'], $indexint->get_isbd());
				break;
				case AUT_TABLE_CONCEPT :
					$concept= authorities_collection::get_authority("concept", $this->aut_list[$i]['to_num']);
					$this->aut_list[$i]['isbd_entry'] = $concept->get_display_label();
					$this->aut_list[$i]['libelle'] =  $concept->get_display_label();					
				break;
				default:
					if($this->aut_list[$i]['to']>1000){
						// authperso
						$authperso = new authperso($this->aut_list[$i]['to']-1000);
						$isbd=authperso::get_isbd($this->aut_list[$i]['to_num']);
						$this->aut_list[$i]['isbd_entry']=$isbd;
						$this->aut_list[$i]['libelle']="[".$authperso->info['name']."] ".$isbd;
						$this->aut_list[$i]['url_to_opac']=$pmb_opac_url."index.php?lvl=authperso_see&id=".$this->aut_list[$i]['to_num'];
					}
				break;
			}
			$relation = new marc_select("aut_link","f_aut_link_type$i", $this->aut_list[$i]['type']);
			$this->aut_list[$i]['relation_libelle'] = $relation->libelle;
		}
	}

	public function get_data() {
		return $this->aut_list;
	}
	
	public function get_display($caller="categ_form") {
		global $msg;

		if(!count($this->aut_list)) return"";
		$aut_link_table_select = array();
		$aut_link_table_select[AUT_TABLE_AUTHORS]='./index.php?lvl=author_see&id=!!to_num!!';		
		$aut_link_table_select[AUT_TABLE_CATEG]='./index.php?lvl=categ_see&id=!!to_num!!';
		$aut_link_table_select[AUT_TABLE_PUBLISHERS]='./index.php?lvl=publisher_see&id=!!to_num!!';
		$aut_link_table_select[AUT_TABLE_COLLECTIONS]='./index.php?lvl=coll_see&id=!!to_num!!';
		$aut_link_table_select[AUT_TABLE_SUB_COLLECTIONS]='./index.php?lvl=subcoll_see&id=!!to_num!!';
		$aut_link_table_select[AUT_TABLE_SERIES]='./index.php?lvl=serie_see&id=!!to_num!!';
		$aut_link_table_select[AUT_TABLE_TITRES_UNIFORMES]='./index.php?lvl=titre_uniforme_see&id=!!to_num!!';
		$aut_link_table_select[AUT_TABLE_INDEXINT]='./index.php?lvl=indexint_see&id=!!to_num!!';
		$aut_link_table_select[AUT_TABLE_CONCEPT]='./index.php?lvl=concept_see&id=!!to_num!!';
		$aut_link_table_select[AUT_TABLE_AUTHPERSO]='./index.php?lvl=authperso_see&id=!!to_num!!';
		
		$marc = marc_list_collection::get_instance("aut_link");
		$liste_type_relation = $marc->table;
		
		$aff="<ul>";
		foreach ($this->aut_list as $aut) {
		    $aff.="<li>";
		    if($aut['direction'] == 'up') {
		        $aff.= $liste_type_relation['ascendant'][$aut['type']]." : ";
		    } else	{
		        $aff.= $liste_type_relation['descendant'][$aut['type']]." : ";
		    }
			if($aut['to'] > 1000) {
				$link=str_replace("!!to_num!!",$aut['to_num'],$aut_link_table_select[AUT_TABLE_AUTHPERSO]);
			} else {
				$link=str_replace("!!to_num!!",$aut['to_num'],$aut_link_table_select[$aut['to']]);
			}
			$aff.=" <a href='".$link."'>".$aut['libelle']."</a>";
			$aff_dates = '';
			if ($aut['string_start_date']) {
			    $aff_dates.= $aut['string_start_date'];
			}
			if ($aff_dates && $aut['string_end_date']) {
			    $aff_dates.= ' - ';
			}
			if ($aut['string_end_date']) {
			    $aff_dates.= $aut['string_start_date'];
			}
			if ($aff_dates && !$aut['comment']) {
			    $aff.= " ( " . $aff_dates . " )";
			}
			if($aut['comment']) {
			    $aff.= " ( " . $aff_dates . ' ' . $aut['comment'] . " )";
			}
			$aff.="</li>";
		}
		$aff.="</ul>";
		return $aff;
	}
	
	
// fin class
}