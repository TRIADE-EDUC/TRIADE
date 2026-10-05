<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dynamic_search_date_flot.class.php,v 1.9 2021/08/17 10:11:27 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($include_path."/search_queries/dynamics/dynamic_search.class.php");

class dynamic_search_date_flot extends dynamic_search {
	  
	public function make_human_query($field = array(), $field1 = array()) {
    	$op = "op_" . $this->n_ligne . "_" . $this->xml_prefix . "_" . $this->id;
		global ${$op};
		
		$field_aff = ${$op} . "|||" . $field . "|||" . $field1;
		return $this->search->pp[$this->xml_prefix]->get_formatted_output(array(0 => $field_aff), $this->id);
	}
	
	public function get_query($field = '', $field1 = '', $operator = "") {
		// Recuperation de l'operateur
    	$op = "op_" . $this->n_ligne . "_" . $this->xml_prefix . "_" . $this->id;
		global ${$op};
		
		if ($operator) {
		    ${$op} = $operator;
		}
		
		switch ($this->prefix) {
			case 'authors' :
				$this->prefix = 'author';
				break;
			case 'publishers' :
			    $this->prefix = 'publisher';
			    break;
			case 'categories' :
			    $this->prefix = 'categ';
			    break;
			case 'series' :
				$this->prefix = 'serie';
				break;
			case 'collections' :
				$this->prefix = 'collection';
				break;
			case 'subcollections' :
				$this->prefix = 'subcollection';
				break;
			default :
				break;
		}
		
		$main = "select distinct " . $this->search->keyName . " from " . $this->search->tableName." ";
		$where_notice = '';
		if ($this->prefix == 'notices') {
			$main.= ", " . $this->prefix . "_custom_dates ";
			$where_notice = " " . $this->prefix . "_custom_origine=" . $this->search->keyName . " and ";
		}		
		$main.= $this->get_join_query();
		$interval_value = $this->search->pp[$this->xml_prefix]->t_fields[$this->id]['OPTIONS'][0]['DURATION'][0]['value'];
		$interval_echelle = $this->search->pp[$this->xml_prefix]->t_fields[$this->id]['OPTIONS'][0]['DURATION_D_M_Y'][0]['value'];
		if (!$interval_value) $interval_value = 1;
		if ($interval_echelle == 2) {
			$interval = $interval_value . ' YEAR ';
		} elseif ($interval_echelle == 1) {
			$interval = $interval_value . ' MONTH ';
		} else {
			$interval = $interval_value . ' DAY ';
		}
		$date_start_signe = 1;
		$date_end_signe = 1;
		if(substr($field, 0, 1) == '-') {
			// date avant JC
			$date_start_signe = -1;
			$field = substr($field, 1);
		}
		if(substr($field1, 0, 1) == '-') {
			// date avant JC
			$date_end_signe = -1;
			$field1 = substr($field1, 0, 1);
		}	
		// années saisie inférieures à 4 digit	
		if(strlen($field) < 4)	$field = str_pad($field, 4, '0', STR_PAD_LEFT);
		if($field1 && strlen($field1) < 4)	$field1 = str_pad($field1, 4, '0', STR_PAD_LEFT);	
		$restricts = array();	
		switch (${$op}) {
			case 'NEAR':
				$date_start = detectFormatDate($field, 'min');
				$date_end = detectFormatDate($field, 'max'); // $field, la meme date de saisie
				$date_start = pmb_sql_value("select DATE_ADD('" . $date_start . "', INTERVAL - " . $interval.")");
				$date_end = pmb_sql_value("select DATE_ADD('" . $date_end . "', INTERVAL + " . $interval.")");
				// format en integer
				$date_start = intval(str_replace('-', '', $date_start)) * $date_start_signe;
				$date_end = intval(str_replace('-', '', $date_end)) * $date_start_signe;
				if($date_end < $date_start) {
					$date = $date_start;
					$date_start = $date_end;
					$date_end = $date;
				}			
				/*
				if ($date_start == $date_end) {
					$restricts[] = 	"(
							'" . addslashes($date_start) . "' BETWEEN 
							if( " . $this->prefix . "_custom_date_type != 2, DATE_ADD(" . $this->prefix . "_custom_date_start, INTERVAL -" . $interval."), " . $this->prefix . "_custom_date_start) AND
							if( " . $this->prefix . "_custom_date_type != 1, DATE_ADD(" . $this->prefix . "_custom_date_end, INTERVAL " . $interval . "), " . $this->prefix . "_custom_date_end)
						) ";
				} else {
					$restricts[] = 	"(
							'" . addslashes($date_start) . "' BETWEEN 
							if( " . $this->prefix . "_custom_date_type != 2, DATE_ADD(" . $this->prefix . "_custom_date_start, INTERVAL -" . $interval."), " . $this->prefix . "_custom_date_start) AND
							if( " . $this->prefix . "_custom_date_type != 1, DATE_ADD(" . $this->prefix . "_custom_date_end, INTERVAL " . $interval . "), " . $this->prefix . "_custom_date_end) 
							or						
							'" . addslashes($date_end) . "' BETWEEN 
							if( " . $this->prefix . "_custom_date_type != 2, DATE_ADD(" . $this->prefix . "_custom_date_start, INTERVAL -" . $interval . "), " . $this->prefix . "_custom_date_start) AND
							if( " . $this->prefix . "_custom_date_type != 1, DATE_ADD(" . $this->prefix . "_custom_date_end, INTERVAL " . $interval . "), " . $this->prefix . "_custom_date_end)
						
							or ( " . $this->prefix . "_custom_date_type = 1 and " . $this->prefix . "_custom_date_start >= '" . addslashes($date_start) . "')	
							or ( " . $this->prefix . "_custom_date_type = 2 and " . $this->prefix . "_custom_date_start <= '" . addslashes($date_start) . "')	
									
						) ";
				}
				*/
				$restricts[] = "(
						(" . $this->prefix . "_custom_date_start >= " . ($date_start) . " 
							and " . $this->prefix . "_custom_date_start <= " . ($date_end) . " )
						or
						(" . $this->prefix . "_custom_date_end  >=  " . ($date_start) . "
							and " . $this->prefix . "_custom_date_end <= " . ($date_end) . " )
						or
						(" . $this->prefix . "_custom_date_end  >=  " . ($date_end) . "
							and " . $this->prefix . "_custom_date_start <= " . ($date_start) . " )
					)";	
				break;
			case 'BETWEEN': 
				$date_start = detectFormatDate($field, 'min');
				$date_end = detectFormatDate($field1, 'max');
				$date_start = str_replace('-', '', $date_start) * $date_start_signe;
				$date_end = str_replace('-', '', $date_end) * $date_end_signe;	
				if($date_end < $date_start) {
					$date = $date_start;
					$date_start = $date_end;
					$date_end = $date;
				}								
				/*
				$restricts[] = 	"(
						( " . $this->prefix . "_custom_date_start <= '" . addslashes($date_start) . "'  AND  " . $this->prefix . "_custom_date_end >=  '" . addslashes($date_end) . "')
						or ( " . $this->prefix . "_custom_date_end BETWEEN  '" . addslashes($date_start) . "' and '" . addslashes($date_end) . "')
						or ( " . $this->prefix . "_custom_date_start BETWEEN  '" . addslashes($date_start) . "' and '" . addslashes($date_end) . "')
						or ( " . $this->prefix . "_custom_date_type = 1 and " . $this->prefix . "_custom_date_start >= '" . addslashes($date_start) . "')	
						or ( " . $this->prefix . "_custom_date_type = 2 and " . $this->prefix . "_custom_date_start <= '" . addslashes($date_start) . "')				
					) ";
				*/
				$restricts[] = "(
						(" . $this->prefix . "_custom_date_start >= '" . ($date_start) . "' and " . $this->prefix . "_custom_date_start <= '" . ($date_end) . "')
						or
						(" . $this->prefix . "_custom_date_end  >=  '" . ($date_start) . "' and " . $this->prefix . "_custom_date_end <= '" . ($date_end) . "')
						or
						(" . $this->prefix . "_custom_date_end  >=  '" . ($date_end) . "' and " . $this->prefix . "_custom_date_start <= '" . ($date_start) . "' )
					)";
				break;
				case 'LTEQ': // <=
				$date_start = detectFormatDate($field, 'max');
				$date_start = str_replace('-', '', $date_start) * $date_start_signe;
				//$restricts[] = $this->prefix . "_custom_date_start <= if( " . $this->prefix . "_custom_date_type != 2, DATE_ADD('" . addslashes($date_start) . "', INTERVAL ".$interval."), '" . addslashes($date_start) . "') ";
				
				$restricts[] = "(
						". $this->prefix . "_custom_date_end <= '" . ($date_start) . "'
						or
						(" . $this->prefix . "_custom_date_end  >=  '" . ($date_start) . "' and " . $this->prefix . "_custom_date_start <= '" . ($date_start) . "' )
					)";
				break;
				case 'GTEQ': // >=
				$date_start = detectFormatDate($field, 'min');
				$date_start = str_replace('-', '', $date_start) * $date_start_signe;
				//$restricts[] = $this->prefix . "_custom_date_start >= if( " . $this->prefix . "_custom_date_type != 1,	DATE_ADD('" . addslashes($date_start) . "', INTERVAL -".$interval."), '" . addslashes($date_start) . "') ";

				$restricts[] = "(
						" . $this->prefix . "_custom_date_start >= '" . ($date_start) . "'
						or
						(" . $this->prefix . "_custom_date_end  >=  '" . ($date_start) . "' and " . $this->prefix . "_custom_date_start <= '" . ($date_start) . "' )
					)";
				break;
				case 'EQ':
				$date_start = detectFormatDate($field, 'min');
				$date_end = detectFormatDate($field, 'max'); // la meme date
				$date_start = str_replace('-', '', $date_start) * $date_start_signe;
				$date_end = str_replace('-', '', $date_end) * $date_end_signe;
				if($date_end < $date_start) {
					$date = $date_start;
					$date_start = $date_end;
					$date_end = $date;
				}		
				//$restricts[] = $this->prefix . "_custom_date_start >= '" . addslashes($date_start) . "'	and " . $this->prefix . "_custom_date_end <= '" . addslashes($date_end) . "' ";

				$restricts[] = "(
						(" . $this->prefix . "_custom_date_start >= '" . ($date_start) . "' and " . $this->prefix . "_custom_date_start <= '" . ($date_end) . "')
						or
						(" . $this->prefix . "_custom_date_end  >= '" . ($date_start) . "' and " . $this->prefix . "_custom_date_end <= '" . ($date_end) . "')
						or
						(" . $this->prefix . "_custom_date_end  >=  '" . ($date_end) . "' and " . $this->prefix . "_custom_date_start <= '" . ($date_start) . "' )
					)";
				break;				
				case 'ISEMPTY':
				case 'ISNOTEMPTY':				    
			        $main = "select distinct " . $this->search->keyName . " from " . $this->search->tableName . $this->get_join_query(${$op});				
				    break;
			    default:
				    break;
		}

		if ($this->search->fichier_xml == 'search_fields' &&  $this->prefix == 'expl') {
		    if (${$op} == 'ISNOTEMPTY') {
		        $restricts[] = '1';
		    }
		    $main = "SELECT ifnull(notices_m.notice_id,notices_s.notice_id) as notice_id FROM ((((exemplaires JOIN expl_custom_dates ON expl_custom_champ=" . $this->id . "
					 AND (" . implode(') and (', $restricts).") AND expl_custom_origine=expl_id) LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id) GROUP BY notice_id order by expl_cb, notices_m.index_serie, notices_m.tnvol, notices_m.index_sew, notices_s.index_serie, notices_s.tnvol, notices_s.index_sew";
		    if (${$op} == 'ISEMPTY') {
		        $main = "SELECT notice_id FROM notices WHERE notice_id NOT IN (".$main.")";
		    }
		    return $main;
		} elseif ($this->search->fichier_xml == 'search_fields' &&  $this->prefix == 'explnum') {
		    if (${$op} == 'ISNOTEMPTY') {
		        $restricts[] = '1';
		    }
		    $main = "
            SELECT ifnull(notices_m.notice_id,notices_s.notice_id) as notice_id 
            FROM explnum 
            JOIN explnum_custom_dates ON explnum_custom_champ=" . $this->id . " AND (" . implode(') and (', $restricts)." AND explnum_custom_origine=explnum_id)
            LEFT JOIN notices AS notices_m ON explnum_notice = notices_m.notice_id 
            LEFT JOIN bulletins ON explnum_bulletin = bulletins.bulletin_id 
            LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id 
            GROUP BY notice_id 
            ORDER BY explnum_id, notices_m.index_serie, notices_m.tnvol, notices_m.index_sew, notices_s.index_serie, notices_s.tnvol, notices_s.index_sew";
		    if (${$op} == 'ISEMPTY') {
		        $main = "SELECT notice_id FROM notices WHERE notice_id NOT IN (".$main.")";
		    }
		    return $main;
		} elseif ($this->search->fichier_xml == 'search_fields_expl') {			
			switch ($this->prefix) {
				case 'notices' :
					$main = "SELECT DISTINCT expl_id FROM exemplaires JOIN notices_custom_dates ON expl_notice=notices_custom_origine AND notices_custom_champ=" . $this->id . " WHERE (" . implode(') and (', $restricts).")
					UNION SELECT DISTINCT expl_id FROM exemplaires JOIN bulletins ON bulletin_id=expl_bulletin JOIN notices_custom_dates ON bulletin_notice=notices_custom_origine AND notices_custom_champ=" . $this->id . " WHERE (" . implode(') and (', $restricts).")
					UNION SELECT DISTINCT expl_id FROM exemplaires JOIN bulletins ON bulletin_id=expl_bulletin JOIN notices_custom_dates ON num_notice=notices_custom_origine AND notices_custom_champ=" . $this->id . " WHERE (" . implode(') and (', $restricts).") ";
				break;	
				case 'expl' :	
					$main = "SELECT DISTINCT expl_custom_origine AS expl_id FROM expl_custom_dates WHERE expl_custom_champ=" . $this->id . " AND (" . implode(') and (', $restricts).") order by ". $this->prefix . "_custom_date_start";
				break;
			}
			return $main;	
		} elseif ($this->search->fichier_xml == 'search_fields_empr') {
			$main = "SELECT DISTINCT empr_custom_origine AS id_empr FROM empr_custom_dates WHERE empr_custom_champ=" . $this->id . " AND (" . implode(') and (', $restricts).") order by ". $this->prefix . "_custom_date_start";
			return $main;				
		}
		if (count($restricts)) {
			$main.= " where " . $where_notice . $this->prefix . "_custom_champ=" . $this->id . " and (" . implode(') and (', $restricts).") order by ". $this->prefix . "_custom_date_start";
		}
		return $main;		
	}
	
	protected function get_join_query($join_type =  '') {
		$join_query = "";
		switch ($this->search->tableName) {
			case 'authorities':
				switch ($this->prefix) {
					case 'author':
					    $join_query.= " join authors on authors.author_id=authorities.num_object and authorities.type_object = 1 ";
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join author_custom_dates on author_custom_dates.author_custom_origine=author_id and author_custom_dates.author_custom_champ = " . $this->id;    
					    } elseif ($join_type == 'ISEMPTY') {
					        $join_query.= " where authors.author_id NOT IN (SELECT author_custom_origine from author_custom_values WHERE author_custom_champ = " . $this->id . ")";
					    } else{
						    $join_query.= " join author_custom_dates on author_custom_origine=author_id ";
					    }
						break;
					case 'categ':
					    $join_query.= " join noeuds on noeuds.id_noeud=authorities.num_object and authorities.type_object = 2  ";
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join categ_custom_dates on categ_custom_dates.categ_custom_origine=id_noeud and categ_custom_dates.categ_custom_champ = " . $this->id;
					    } elseif ($join_type == 'ISEMPTY') {
					        $join_query.= " where noeuds.id_noeud NOT IN (SELECT categ_custom_origine from categ_custom_values WHERE categ_custom_champ = " . $this->id . ")";
					    } else{
					        $join_query.= " join categ_custom_dates on categ_custom_origine=id_noeud ";
					    }
						break;
					case 'publisher':
					    $join_query.= " join publishers on publishers.ed_id=authorities.num_object and authorities.type_object = 3 ";
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join publisher_custom_dates on publisher_custom_dates.publisher_custom_origine=ed_id and publisher_custom_dates.publisher_custom_champ = " . $this->id;
					    } elseif ($join_type == 'ISEMPTY') {
					        $join_query.= " where publishers.ed_id NOT IN (SELECT publisher_custom_origine from publisher_custom_values WHERE publisher_custom_champ = " . $this->id . ")";
					    } else{
					        $join_query.= " join publisher_custom_dates on publisher_custom_origine=ed_id ";
					    }
						break;
					case 'collection':
					    $join_query.= " join collections on collections.collection_id=authorities.num_object and authorities.type_object = 4 ";
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join collection_custom_dates on collection_custom_dates.collection_custom_origine=collection_id and collection_custom_dates.collection_custom_champ = " . $this->id;
					    } elseif ($join_type == 'ISEMPTY') {
					        $join_query.= " where collections.collection_id NOT IN (SELECT collection_custom_origine from collection_custom_values WHERE collection_custom_champ = " . $this->id . ")";
					    } else{
					        $join_query.= " join collection_custom_dates on collection_custom_origine=collection_id ";
					    }
						break;
					case 'subcollection':
					    $join_query.= " join sub_collections on sub_collections.sub_coll_id=authorities.num_object and authorities.type_object = 5 ";
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join subcollection_custom_dates on subcollection_custom_dates.subcollection_custom_origine=sub_coll_id and subcollection_custom_dates.subcollection_custom_champ = " . $this->id;
					    } elseif ($join_type == 'ISEMPTY') {
					        $join_query.= " where sub_collections.sub_coll_id NOT IN (SELECT subcollection_custom_origine from subcollection_custom_values WHERE subcollection_custom_champ = " . $this->id . ")";
					    } else{
					        $join_query.= " join subcollection_custom_dates on subcollection_custom_origine=sub_coll_id ";
					    }					    
						break;
					case 'serie':
					    $join_query.= " join series on series.serie_id=authorities.num_object and authorities.type_object = 6 ";
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join serie_custom_dates on serie_custom_dates.serie_custom_origine=serie_id and serie_custom_dates.serie_custom_champ = " . $this->id;
					    } elseif ($join_type == 'ISEMPTY') {
					        $join_query.= " where series.serie_id NOT IN (SELECT serie_custom_origine from serie_custom_values WHERE serie_custom_champ = " . $this->id . ")";
					    } else{
					        $join_query.= " join serie_custom_dates on serie_custom_origine=serie_id ";
					    }
						break;
					case 'tu':
					    $join_query.= " join titres_uniformes on titres_uniformes.tu_id=authorities.num_object and authorities.type_object = 7 ";
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join tu_custom_dates on tu_custom_dates.tu_custom_origine=tu_id and tu_custom_dates.tu_custom_champ = " . $this->id;
					    } elseif ($join_type == 'ISEMPTY') {
					         $join_query.= " where titres_uniformes.tu_id NOT IN (SELECT tu_custom_origine from tu_custom_values WHERE tu_custom_champ = " . $this->id . ")";
					    } else{
						    $join_query.= " join tu_custom_dates on tu_custom_dates.tu_custom_origine=tu_id ";
					    }
						break;
					case 'indexint':
					    $join_query.= " join indexint on indexint.indexint_id=authorities.num_object and authorities.type_object = 8 ";
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join indexint_custom_dates on indexint_custom_dates.indexint_custom_origine=indexint_id and indexint_custom_dates.indexint_custom_champ = " . $this->id;
					    } elseif ($join_type == 'ISEMPTY') {
					        $join_query.= " where indexint.indexint_id NOT IN (SELECT indexint_custom_origine from indexint_custom_values WHERE indexint_custom_champ = " . $this->id . ")";
					    } else{
					        $join_query.= " join indexint_custom_dates on indexint_custom_origine=indexint_id ";
					    }
						break;
					case 'authperso':
					    $join_query.= " join authperso_authorities on id_authperso_authority=authorities.num_object and authorities.type_object = 9 ";
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join authperso_custom_dates on authperso_custom_dates.authperso_custom_origine=id_authperso_authority and authperso_custom_dates.authperso_custom_champ = " . $this->id;
					    } elseif ($join_type == 'ISEMPTY') {
					        $join_query.= " where authperso_authorities.id_authperso_authority NOT IN (SELECT authperso_custom_origine from authperso_custom_values WHERE authperso_custom_champ = " . $this->id . ")";
					    } else{
					        $join_query.= " join authperso_custom_dates on authperso_custom_origine=id_authperso_authority ";
					    }
						break;
				}
				break;
			case 'notices':
				switch ($this->prefix) {
					case 'author':
					    $join_query.= " join responsability on responsability.responsability_notice = notices.notice_id ";
					    
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join author_custom_dates on author_custom_dates.author_custom_origine= responsability.responsability_author and author_custom_dates.author_custom_champ = " . $this->id;
					    } elseif ($join_type == 'ISEMPTY') {
					        $join_query.= " where notices_titres_uniformes.ntu_num_tu NOT IN (SELECT tu_custom_origine from tu_custom_values WHERE tu_custom_champ = " . $this->id . ")";
					    } else{
					        $join_query.= " join author_custom_dates on author_custom_dates.author_custom_origine= responsability.responsability_author ";
					    }					    
						break;
					case 'categ':
						$join_query.= " join notices_categories on notices_categories.notcateg_notice=notices.notice_id join categ_custom_dates on categ_custom_origine=notices_categories.num_noeud ";
						break;
					case 'publisher':
						$join_query.= " join publisher_custom_dates on publisher_custom_origine=notices.ed1_id ";
						break;
					case 'collection':
						$join_query.= " join collection_custom_dates on collection_custom_origine=notices.coll_id ";
						break;
					case 'subcollection':
						$join_query.= " join subcollection_custom_dates on subcollection_custom_origine=notices.subcoll_id ";
						break;
					case 'serie':
						$join_query.= " join serie_custom_dates on serie_custom_origine=notices.tparent_id ";
						break;
					case 'tu':
					    $join_query.= " join notices_titres_uniformes on notices_titres_uniformes.ntu_num_notice = notices.notice_id ";
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join tu_custom_dates on tu_custom_dates.tu_custom_origine= notices_titres_uniformes.ntu_num_tu and tu_custom_dates.tu_custom_champ = " . $this->id;
					    } elseif ($join_type == 'ISEMPTY') {
					        $join_query.= " where notices_titres_uniformes.ntu_num_tu NOT IN (SELECT tu_custom_origine from tu_custom_values WHERE tu_custom_champ = " . $this->id . ")";
					    } else{
					        $join_query.= " join tu_custom_dates on tu_custom_dates.tu_custom_origine= notices_titres_uniformes.ntu_num_tu ";
					    }
						break;
					case 'indexint':
						$join_query.= " join indexint_custom_dates on indexint_custom_origine=notices.indexint ";
						break;
					case 'authperso':
						$join_query.= " join notices_authperso on notices_authperso.notice_authperso_notice_num=notices.notice_id join authperso_custom_dates on authperso_custom_dates.authperso_custom_origine=notices_authperso.notice_authperso_authority_num ";
						break;
					case 'notices':
					    if ($join_type == 'ISNOTEMPTY') {
					        $join_query.= " join notices_custom_dates on notices_custom_dates.notices_custom_origine= notices.notice_id and notices_custom_dates.notices_custom_champ = " . $this->id;
					    } elseif ($join_type == 'ISEMPTY') {
					        $join_query.= " WHERE notices.notice_id NOT IN (SELECT notices_custom_origine FROM notices_custom_values WHERE notices_custom_champ= " . $this->id . ") ";
					    }
					    break;
					case 'expl':
					    $join_query.= "test";
					    break;
				}
				break;
		}
		return $join_query;
	}	
	
}