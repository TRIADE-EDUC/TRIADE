<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesItems.class.php,v 1.28.2.1.2.1 2025/04/30 09:18:52 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once $class_path."/external_services.class.php";
require_once $class_path."/mono_display.class.php";


class pmbesItems extends external_services_api_class {
	
    public function fetch_notice_items($notice_id, $OPACUserId = - 1)
    {
        global $msg;
        
        $notice_id = intval($notice_id);
        if (! $notice_id) {
            throw new Exception("Missing parameter: notice_id");
        }
        
        // Filtre les notices en fonction des droits
        $notice_ids = $this->filter_tabl_notices(array($notice_id), "exemplaire");
        if (! count($notice_ids) || ! $notice_ids[0]) {
            return [];
        }
        
        $requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, tdoc_libelle, codestat_libelle, ";
        $requete .= " date_format(pret_date, '" . $msg["format_date"] . "') as aff_pret_date, ";
        $requete .= " date_format(pret_retour, '" . $msg["format_date"] . "') as aff_pret_retour, ";
        $requete .= " IF(pret_retour>sysdate(),0,1) as retard ";
        $requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
        $requete .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
        $requete .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
        $requete .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
        $requete .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
        $requete .= " left join docs_codestat on (expl_codestat = idcode)  ";
        $requete .= " WHERE expl_notice=$notice_id ";
        $requete .= " order by location_libelle, section_libelle, expl_cote, expl_cb ";
        $res = pmb_mysql_query($requete);
        
        $result = [];
        
        while ($row = pmb_mysql_fetch_assoc($res)) {
            
            $expl_return = [];
            
            if ($this->expl_visible_opac($row["expl_id"])) {
                
                $pret_date = '';
                $pret_retour = '';
                $situation = '';
                if ($row['pret_date']) {
                    $pret_date = $row['pret_date'];
                }
                if ($row['pret_retour']) {
                    // exemplaire sorti
                    $pret_retour = $row['pret_retour'];
                    $situation = "${msg[358]} " . $row['aff_pret_retour'];
                } else {
                    // tester si réservé
                    $result_resa = pmb_mysql_query("select 1 from resa where resa_cb='" . addslashes($row["expl_cb"]) . "' ");
                    $reserve = pmb_mysql_num_rows($result_resa);
                    
                    if ($reserve) {
                        $situation = $msg['expl_reserve']; // exemplaire réservé
                    } elseif ($row["pret_flag"]) {
                        $situation = $msg[359]; // exemplaire disponible
                    } else {
                        $situation = $msg[114]; // exemplaire exclu du pret
                    }
                }
                
                $expl_return["id"] = $row['expl_id'];
                $expl_return["cb"] = encoding_normalize::utf8_normalize($row['expl_cb']);
                $expl_return["cote"] = encoding_normalize::utf8_normalize($row['expl_cote']);
                $expl_return["location_id"] = $row['expl_location'];
                $expl_return["location"] = encoding_normalize::utf8_normalize($row["location_libelle"]);
                $expl_return["section_id"] = $row['expl_section'];
                $expl_return["section"] = encoding_normalize::utf8_normalize($row["section_libelle"]);
                $expl_return["support"] = encoding_normalize::utf8_normalize($row["tdoc_libelle"]);
                $expl_return["statut"] = encoding_normalize::utf8_normalize($row["statut_libelle"]);
                $expl_return["codestat"] = encoding_normalize::utf8_normalize($row["codestat_libelle"]);
                $expl_return["pret_flag"] = $row['pret_flag'];
                $expl_return['pret_date_raw'] = $pret_date;
                $expl_return["pret_date"] = '';
                if ($pret_date) {
                    $expl_return["pret_date"] = $row['aff_pret_date'];
                }
                $expl_return['pret_retour_raw'] = $pret_retour;
                $expl_return["pret_retour"] = '';
                if ($pret_retour) {
                    $expl_return["pret_retour"] = $row['aff_pret_retour'];
                }
                $expl_return["situation"] = encoding_normalize::utf8_normalize(strip_tags($situation));
                $expl_return["pnb_flag"] = $row["expl_pnb_flag"];
                $result[] = $expl_return;
            }
        }
        return $result;
    }
    
    
    public function fetch_notices_items($notice_ids, $OPACUserId=-1) {

		global $msg;

		if (!$notice_ids) {
			throw new Exception("Missing parameter: notice_ids");
		}
		array_walk($notice_ids, function(&$a) {$a = intval($a);});	//Virons ce qui n'est pas entier
		$notice_ids = array_unique($notice_ids);

		// Filtre les notices en fonction des droits
		$notice_ids=$this->filter_tabl_notices($notice_ids,"exemplaire");
		if(!count($notice_ids)){
			return [];
		}

		$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, tdoc_libelle, codestat_libelle, ";
		$requete .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
		$requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
		$requete .= " IF(pret_retour>sysdate(),0,1) as retard " ;
		$requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
		$requete .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
		$requete .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
		$requete .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
		$requete .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
		$requete .= " left join docs_codestat on (expl_codestat = idcode)  ";
		$requete .= " WHERE expl_notice IN (".(implode(',', $notice_ids)).") ";
		$requete .= " order by expl_notice, location_libelle, section_libelle, expl_cote, expl_cb ";
		$res = pmb_mysql_query($requete);

		$result = [];
		$current_noticeid = 0;
		$current_items = [];
		
		while($row=pmb_mysql_fetch_assoc($res)) {
			if (!$current_noticeid) {
				$current_noticeid = $row['expl_notice'];
			}
			if ($current_noticeid != $row['expl_notice']){
				$result[] = array(
					'noticeid' => $current_noticeid,
					'items' => $current_items,
				);
				$current_items = array();
				$current_noticeid = $row['expl_notice'];
			}
			
			$expl_return = [];
			
			if($this->expl_visible_opac($row['expl_id'])){
				
				$pret_date = '';
				$pret_retour = '';
				$situation = '';
				if($row['pret_date']) {
					$pret_date = $row['pret_date'];
				}
				if($row['pret_retour']) {
					// exemplaire sorti
					$pret_retour = $row['pret_retour'];
					$situation = "{$msg[358]} ".$row['aff_pret_retour'];
				} else {
					// tester si réservé 						
					$result_resa = pmb_mysql_query("select 1 from resa where resa_cb='".addslashes($row['expl_cb'])."' ");
					$reserve = pmb_mysql_num_rows($result_resa);
					if ($reserve) {
						$situation = $msg['expl_reserve']; // exemplaire réservé
					} elseif ($row['pret_flag']) {
						$situation = $msg[359]; // exemplaire disponible
					} else {
						$situation = $msg[114];	//exemplaire exclu du pret
					}
				}
				
				$expl_return["id"] = $row['expl_id'];
				$expl_return["cb"] = encoding_normalize::utf8_normalize($row['expl_cb']);
				$expl_return["cote"] = encoding_normalize::utf8_normalize($row['expl_cote']);
				$expl_return["location_id"] = $row['expl_location'];
				$expl_return["location"] = encoding_normalize::utf8_normalize($row['location_libelle']);
				$expl_return["section_id"] = $row['expl_section'];
				$expl_return["section"] = encoding_normalize::utf8_normalize($row['section_libelle']);
				$expl_return["support"] = encoding_normalize::utf8_normalize($row['tdoc_libelle']);
				$expl_return["statut"] = encoding_normalize::utf8_normalize($row['statut_libelle']);
				$expl_return["codestat"] = encoding_normalize::utf8_normalize($row['codestat_libelle']);
				$expl_return["pret_flag"] = $row['pret_flag'];
				$expl_return["situation"] = encoding_normalize::utf8_normalize(strip_tags($situation));
				$expl_return['pret_date_raw'] = $pret_date;
				$expl_return["pret_date"] = '';
				if($pret_date) {
					$expl_return["pret_date"] = $row['aff_pret_date'];
				}
				$expl_return['pret_retour_raw'] = $pret_retour;
				$expl_return["pret_retour"] = '';
				if($pret_retour) {
					$expl_return["pret_retour"] = $row['aff_pret_retour'];
				}
				$expl_return["pnb_flag"] = $row['expl_pnb_flag'];		

				$current_items[] = $expl_return;
			}
			
		}

		$result[] = array(
			'noticeid' => $current_noticeid,
			'items' => $current_items,
		);
		unset($current_items);
		$current_noticeid = $row['expl_notice'];		
		
		return $result;
	}
	
	
	
	public function fetch_bulletins_items($bulletin_ids, $OPACUserId=-1) {

		global $msg;

		if (!$bulletin_ids) {
			throw new Exception("Missing parameter: bulletin_ids");
		}
		array_walk($bulletin_ids, function(&$a) {$a = intval($a);});	//Virons ce qui n'est pas entier
		$bulletin_ids = array_unique($bulletin_ids);

		//Je filtre les bulletins en fonction des droits de visibilité
		$bulletin_ids=$this->filter_tabl_bulletins($bulletin_ids,"exemplaire");
		if(!count($bulletin_ids)){
			return [];
		}

		$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, tdoc_libelle, codestat_libelle, ";
		$requete .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
		$requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
		$requete .= " IF(pret_retour>sysdate(),0,1) as retard " ;
		$requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
		$requete .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
		$requete .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
		$requete .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
		$requete .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
		$requete .= " left join docs_codestat on (expl_codestat = idcode)  ";
		$requete .= " WHERE expl_bulletin IN (".(implode(',', $bulletin_ids)).") ";
		$requete .= " order by expl_bulletin, location_libelle, section_libelle, expl_cote, expl_cb ";
		$res = pmb_mysql_query($requete);
		
		$result = [];
		$current_bulletinid = 0;
		$current_items = [];
		
		while($row=pmb_mysql_fetch_assoc($res)) {
			if (!$current_bulletinid) {
				$current_bulletinid = $row['expl_bulletin'];
			}
			if ($current_bulletinid != $row['expl_bulletin']) {
				$result[] = array(
					'bulletinid' => $current_bulletinid,
					'items' => $current_items,
				);
				$current_items = array();
				$current_bulletinid = $row['expl_bulletin'];
			}
			
			$expl_return = [];
			
			if($this->expl_visible_opac($row['expl_id'])){
				
				$pret_date = '';
				$pret_retour = '';
				$situation = '';
				if($row['pret_date']) {
					$pret_date = $row['pret_date'];
				}
				if($row['pret_retour']) {
					// exemplaire sorti
					$pret_retour = $row['pret_retour'];
					$situation = "{$msg[358]} ".$row['aff_pret_retour'];
				} else {
					// tester si réservé 						
					$result_resa = pmb_mysql_query("select 1 from resa where resa_cb='".addslashes($row["expl_cb"])."' ");
					$reserve = pmb_mysql_num_rows($result_resa);
					if ($reserve) {
						$situation = $msg['expl_reserve']; // exemplaire réservé
					} elseif ($row["pret_flag"]) {
						$situation = $msg[359]; // exemplaire disponible
					} else {
						$situation = $msg[114];	//exemplaire exclu du pret
					}
				}
				
				$expl_return["id"] = $row['expl_id'];
				$expl_return["cb"] = encoding_normalize::utf8_normalize($row['expl_cb']);
				$expl_return["cote"] = encoding_normalize::utf8_normalize($row['expl_cote']);
				$expl_return["location_id"] = $row['expl_location'];
				$expl_return["location"] = encoding_normalize::utf8_normalize($row['location_libelle']);
				$expl_return["section_id"] = $row['expl_section'];
				$expl_return["section"] = encoding_normalize::utf8_normalize($row['section_libelle']);
				$expl_return["support"] = encoding_normalize::utf8_normalize($row['tdoc_libelle']);
				$expl_return["statut"] = encoding_normalize::utf8_normalize($row['statut_libelle']);
				$expl_return["codestat"] = encoding_normalize::utf8_normalize($row['codestat_libelle']);
				$expl_return["pret_flag"] = $row['pret_flag'];
				$expl_return['pret_date_raw'] = $pret_date;
				$expl_return["pret_date"] = '';
				if($pret_date) {
					$expl_return["pret_date"] = $row['aff_pret_date'];
				}
				$expl_return['pret_retour_raw'] = $pret_retour;
				$expl_return["pret_retour"] = '';
				if($pret_retour) {
					$expl_return["pret_retour"] = $row['aff_pret_retour'];
				}
				$expl_return["situation"] = encoding_normalize::utf8_normalize(strip_tags($situation));
				$expl_return["pnb_flag"] = $row['expl_pnb_flag'];		
	
				$current_items[] = $expl_return;
			}
			
		}

		$result[] = array(
			'bulletinid' => $current_bulletinid,
			'items' => $current_items,
		);
		unset($current_items);
		$current_bulletinid = $row['expl_bulletin'];		
		
		return $result;
	}	
	
	public function fetch_item($item_cb='', $item_id='', $OPACUserId=-1) {

		global $msg;
		global $charset;

		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$item_cb = encoding_normalize::utf8_normalize($item_cb);
		} else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$item_cb = encoding_normalize::utf8_decode($item_cb);	
		}
		
		$empty_result = array(
			'id' => 0,
			'cb' => '',
			'title' => '',
			'cote' => '',
			'location' => '',
			'section' => '',
			'support' => '',
			'statut' => '',
			'situation' => ''
		);
		
		$item_id = intval($item_id);
		$wheres = [];
		if ($item_cb) {
			$wheres[] = "expl_cb = '".addslashes($item_cb)."'";
		}
		if ($item_id) {
			$wheres[] = "expl_id = ".$item_id."";
		}
		
		$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, tdoc_libelle, codestat_libelle, ";
		$requete .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
		$requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
		$requete .= " IF(pret_retour>sysdate(),0,1) as retard " ;
		$requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
		$requete .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
		$requete .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
		$requete .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
		$requete .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
		$requete .= " left join docs_codestat on (expl_codestat = idcode)  ";
		$requete .= " WHERE 1 and ".implode(' and ', $wheres);
		$res = pmb_mysql_query($requete);
		
		if (!pmb_mysql_num_rows($res)) {
			return $empty_result;
		}
		$row=pmb_mysql_fetch_assoc($res);
		
		//Je regarde si j'ai les bons droits d'accès
		if($row['expl_notice']) {
			//Si j'ai un exemplaire de monographie
			$notice_ids=$this->filter_tabl_notices(array($row['expl_notice']),'exemplaire');
			if(!count($notice_ids) || $notice_ids[0]) {
				return $empty_result;
			}
		} elseif($row['expl_bulletin']) {
			$bulletin_ids=$this->filter_tabl_bulletins(array($row['expl_bulletin']),'exemplaire');
			if(!count($bulletin_ids) || $bulletin_ids[0]) {
				return $empty_result;
			}
		}
		if ($row['expl_bulletin']) {
			$isbd = new bulletinage_display($row['expl_bulletin']);
			$titre=$isbd->display;
		} else {
			$isbd= new mono_display($row['expl_notice'], 1);
			$titre= $isbd->header_texte;
		}
		
		$expl_return = [];
		
		if($this->expl_visible_opac($row["expl_id"])) {
	
			$pret_date = '';
			$pret_retour = '';
			$situation = '';
			if($row['pret_date']) {
				$pret_date = $row['pret_date'];
			}
			if($row['pret_retour']) {
				// exemplaire sorti
				$pret_retour = $row['pret_retour'];
				$situation = "{$msg[358]} ".$row['aff_pret_retour'];
			} else {
				// tester si réservé 						
				$result_resa = pmb_mysql_query("select 1 from resa where resa_cb='".addslashes($row["expl_cb"])."' ");
				$reserve = pmb_mysql_num_rows($result_resa);
	
				if ($reserve) {
					$situation = $msg['expl_reserve']; // exemplaire réservé
				} elseif ($row['pret_flag']) {
					$situation = $msg[359]; // exemplaire disponible
				} else {
					$situation = $msg[114];
				}
			}
			
			$expl_return["id"] = $row['expl_id'];
			$expl_return["cb"] = encoding_normalize::utf8_normalize($row['expl_cb']);
			$expl_return["title"] = encoding_normalize::utf8_normalize($titre);
			$expl_return["cote"] = encoding_normalize::utf8_normalize($row['expl_cote']);
			$expl_return["location_id"] = $row['expl_location'];
			$expl_return["location"] = encoding_normalize::utf8_normalize($row['location_libelle']);
			$expl_return["section_id"] = $row['expl_section'];
			$expl_return["section"] = encoding_normalize::utf8_normalize($row['section_libelle']);
			$expl_return["support"] = encoding_normalize::utf8_normalize($row['tdoc_libelle']);
			$expl_return["statut"] = encoding_normalize::utf8_normalize($row['statut_libelle']);
			$expl_return["codestat"] = encoding_normalize::utf8_normalize($row['codestat_libelle']);
			$expl_return["pret_flag"] = $row['pret_flag'];
			$expl_return['pret_date_raw'] = $pret_date;
			$expl_return["pret_date"] = '';
			if($pret_date) {
				$expl_return["pret_date"] = $row['aff_pret_date'];
			}
			$expl_return['pret_retour_raw'] = $pret_retour;
			$expl_return["pret_retour"] = '';
			if($pret_retour) {
				$expl_return["pret_retour"] = $row['aff_pret_retour'];
			}
			$expl_return["situation"] = encoding_normalize::utf8_normalize($situation);
			$expl_return["pnb_flag"] = $row['expl_pnb_flag'];
		}
		
		return $expl_return;
	}

	
	public function fetch_item_info($item_cb='', $item_id='', $OPACUserId=-1) {

		global $msg;
	
		global $charset;
		if ($this->proxy_parent->input_charset!='utf-8' && $charset == 'utf-8') {
			$item_cb = encoding_normalize::utf8_normalize($item_cb);
		}else if ($this->proxy_parent->input_charset=='utf-8' && $charset != 'utf-8') {
			$item_cb = encoding_normalize::utf8_decode($item_cb);
		}
	
		$empty_result = array(
				'id' => 0,
				'cb' => '',
				'title' => '',
				'cote' => '',
				'location' => '',
				'section' => '',
				'support' => '',
				'statut' => '',
				'situation' => ''
		);
	
		$item_id = intval($item_id);
		$wheres = [];
		if ($item_cb) {
			$wheres[] = "expl_cb = '".addslashes($item_cb)."'";
		}
		if ($item_id) {
			$wheres[] = "expl_id = ".$item_id."";
		}
	
		$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, tdoc_libelle, codestat_libelle, ";
		$requete .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
		$requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
		$requete .= " IF(pret_retour>sysdate(),0,1) as retard " ;
		$requete .= " FROM exemplaires  LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
		$requete .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
		$requete .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
		$requete .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
		$requete .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
		$requete .= " left join docs_codestat on (expl_codestat = idcode)  ";
		$requete .= " WHERE 1 and ".implode(' and ', $wheres);
		$res = pmb_mysql_query($requete);

		if (!pmb_mysql_num_rows($res)) {
			return $empty_result;
		}
	
		$row=pmb_mysql_fetch_assoc($res);

		if ($row['expl_bulletin']) {
			$isbd = new bulletinage_display($row['expl_bulletin']);
			$titre=$isbd->display;
		} else {
			$isbd= new mono_display($row['expl_notice'], 1);
			$titre= $isbd->header_texte;
		}
		$expl_return = [];		
		$expl_return['icondoc'] = $this->get_icondoc($isbd->notice->niveau_biblio, $isbd->notice->typdoc);

		if($this->expl_visible_opac($row['expl_id'])){
	
			$pret_date = '';
			$pret_retour = '';
			$situation = '';
			if($row['pret_date']) {
				$pret_date = $row['pret_date'];
			}
			if($row['pret_retour']) {
				// exemplaire sorti
				$pret_retour = $row['pret_retour'];
				$situation = "{$msg[358]} ".$row['aff_pret_retour'];
			} else {
				// tester si réservé
				$result_resa = pmb_mysql_query("select 1 from resa where resa_cb='".addslashes($row["expl_cb"])."' ");
				$reserve = pmb_mysql_num_rows($result_resa);
		
				if ($reserve) {
					$situation = $msg['expl_reserve']; // exemplaire réservé
				} elseif ($row["pret_flag"]) {
					$situation = "{$msg[359]}"; // exemplaire disponible
				} else {
					$situation = $msg[114];	//exemplaire exclu du pret
				}
			}
				
			$expl_return["id"] = $row['expl_id'];
			$expl_return["cb"] = encoding_normalize::utf8_normalize($row['expl_cb']);
			$expl_return["title"] = encoding_normalize::utf8_normalize($titre);
			$expl_return["cote"] = encoding_normalize::utf8_normalize($row['expl_cote']);
			$expl_return["location_id"] = $row['expl_location'];
			$expl_return["location"] = encoding_normalize::utf8_normalize($row['location_libelle']);
			$expl_return["section_id"] = $row['expl_section'];
			$expl_return["section"] = encoding_normalize::utf8_normalize($row['section_libelle']);
			$expl_return["support"] = encoding_normalize::utf8_normalize($row['tdoc_libelle']);
			$expl_return["statut"] = encoding_normalize::utf8_normalize($row['statut_libelle']);
			$expl_return["codestat"] = encoding_normalize::utf8_normalize($row['codestat_libelle']);
			$expl_return["pret_flag"] = $row['pret_flag'];
			$expl_return['pret_date_raw'] = $pret_date;
			$expl_return["pret_date"] = '';
			if($pret_date) {
				$expl_return["pret_date"] = $row['aff_pret_date'];
			}
			$expl_return['pret_retour_raw'] = $pret_retour;
			$expl_return["pret_retour"] = '';
			if($pret_retour) {
				$expl_return["pret_retour"] = $row['aff_pret_retour'];
			}
			$expl_return["situation"] = encoding_normalize::utf8_normalize($situation);
			$expl_return["pnb_flag"] = $row['expl_pnb_flag'];			
		}	
		return $expl_return;
	}	
	
	
	public function get_icondoc($niveau_biblio, $typdoc) {
	    global $opac_url_base;
	    
	    //Icone type de Document
	    $icon_doc = marc_list_collection::get_instance('icondoc');
	    $icon = (!empty($icon_doc->table[$niveau_biblio.$typdoc]) ? $icon_doc->table[$niveau_biblio.$typdoc] : '');
	    if ($icon) {
	        return "<img class='align_top' src='" . $opac_url_base . "images/$icon '>";
	    }
	    return '';
	}
}
