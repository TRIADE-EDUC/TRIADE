<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesNotices.class.php,v 1.37.2.1.2.1 2025/04/30 11:58:52 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
global $opac_url_base;
global $gestion_acces_active, $gestion_acces_user_notice;
global $pmb_confirm_delete_from_caddie;

require_once $class_path."/external_services.class.php";
require_once $class_path."/serials.class.php";
require_once $class_path."/notice.class.php";
require_once $class_path."/notice_relations_collection.class.php";
require_once $class_path."/acces.class.php";

class pmbesNotices extends external_services_api_class {

	public function fetchNoticeList($noticelist, $recordFormat, $recordCharset, $includeLinks, $includeItems, $cleanHTML=false, $nbResa=false) {
		//Je filtre les notices en fonction des droits
		$noticelist=$this->filter_tabl_notices($noticelist);
		if(!count($noticelist)){
			return array();
		}

		$converter = new external_services_converter_notices(1, 600);
		$converter->set_params(array("include_links" => $includeLinks, "include_items" => $includeItems, "clean_html" => $cleanHTML, "include_authorite_ids" => true));
		$notices = $converter->convert_batch($noticelist, $recordFormat, $recordCharset);
		$results = array();
		foreach($notices as $notice_id => $notice_content) {
			$result = array(
				'noticeId' => $notice_id,
				'noticeContent' => $notice_content
			);
			if($nbResa === true){
				$nbResaQuery = "SELECT COUNT(*) FROM resa WHERE resa_idnotice = '$notice_id' UNION SELECT COUNT(*) FROM resa_planning WHERE resa_idnotice = '$notice_id'";
				$res = pmb_mysql_query($nbResaQuery);
				$nbResa = pmb_mysql_result($res, 0);
				$nbResaPlanning = pmb_mysql_result($res, 1);
				$result['nbResa'] = intval($nbResa) + intval($nbResaPlanning);
			}
			$results[] = $result;
		}
		return $results;
	}

	public function fetchExternalNoticeList($noticelist, $recordFormat, $recordCharset) {
		$converter = new external_services_converter_external_notices(4, 600);
		$converter->set_params(array());
		$notices = $converter->convert_batch($noticelist, $recordFormat, $recordCharset);
		$results = array();
		foreach($notices as $notice_id => $notice_content) {
			$results[] = array(
				'noticeId' => $notice_id,
				'noticeContent' => $notice_content
			);
		}
		return $results;
	}


	public function fetchNoticeListArray($noticelist, $recordCharset, $includeLinks, $includeItems, $cleanHTML=false) {
		//Je filtre les notices en fonction des droits
		$noticelist=$this->filter_tabl_notices($noticelist);
		if(!count($noticelist)){
			return array();
		}

		$converter = new external_services_converter_notices(1, 600);
		$converter->set_params(array("include_links" => $includeLinks, "include_items" => $includeItems, "clean_html" => $cleanHTML, "include_authorite_ids" => true));
		$keyed_results = $converter->convert_batch($noticelist, "raw_array", $recordCharset);
		$array_results = array_values($keyed_results);
		return $array_results;
	}


	public function listNoticeExplNums($noticeId, $OPACUserId=-1) {

		global $opac_url_base;
		$noticeId = intval($noticeId);

		//Je filtre les notices en fonction des droits
		$notice_ids=array($noticeId);
		$notice_ids=$this->filter_tabl_notices($notice_ids,"docnum");
		if(count($notice_ids) && $notice_ids[0]){
			$noticeId=$notice_ids[0];
		}else{
			return array();
		}

		//Je filtre les documents numériques en fonction des droits
		$restrict_join = $this->filter_tabl_explnum();

		$sql = "SELECT explnum_id, explnum_nom, explnum_mimetype, explnum_url, explnum_extfichier, explnum_nomfichier,explnum_repertoire, explnum_path FROM explnum $restrict_join WHERE explnum_notice = ".$noticeId;
		$res = pmb_mysql_query($sql);

		$results = array();
		while($row = pmb_mysql_fetch_assoc($res)) {
			if($row['explnum_repertoire']){
				$rqt="select repertoire_path from upload_repertoire where repertoire_id = ".$row['explnum_repertoire'];
				$r= pmb_mysql_query($rqt);
				$path = pmb_mysql_result($r,0,0);
				$filesize = filesize(str_replace("//","/",$path.$row['explnum_path']."/".$row['explnum_nomfichier']));
			}else if ($row['explnum_url'] == ""){
				$rqt = "select bit_length(explnum_data) from explnum where explnum_id = ".$row["explnum_id"];
				$r= pmb_mysql_query($rqt);
				$filesize = pmb_mysql_result($r,0,0);
			}
			$aresult = array(
				"id" => $row["explnum_id"],
				"noticeId" => $noticeId,
				"bulletinId" => 0,
				"name" =>encoding_normalize::utf8_normalize( $row["explnum_nom"]),
				"mimetype" => encoding_normalize::utf8_normalize($row["explnum_mimetype"]),
				"filename" => encoding_normalize::utf8_normalize($row["explnum_nomfichier"]),
				"extention" => encoding_normalize::utf8_normalize($row["explnum_extfichier"]),
				"filesize" => encoding_normalize::utf8_normalize($filesize),
				"url" => encoding_normalize::utf8_normalize($row["explnum_url"]),
				"downloadUrl" => encoding_normalize::utf8_normalize($opac_url_base."/doc_num.php?explnum_id=".$row["explnum_id"])
			);
			$results[] = $aresult;
		}

		return $results;
	}

	public function listNoticesExplNums($notice_ids, $OPACUserId=-1) {

		global $opac_url_base;

		if (!$notice_ids) {
			throw new Exception("Missing parameter: notice_ids");
		}
		array_walk($notice_ids, function(&$a) {$a = intval($a);});	//Virons ce qui n'est pas entier
		$notice_ids = array_unique($notice_ids);

		//Je filtre les notices en fonction des droits
		$notice_ids=$this->filter_tabl_notices($notice_ids,"docnum");
		if(!count($notice_ids)){
			return array();
		}

		//Je filtre les documents numériques en fonction des droits
		$restrict_join = $this->filter_tabl_explnum();

		$sql = "SELECT explnum_id, explnum_nom, explnum_mimetype, explnum_url, explnum_notice, explnum_extfichier, explnum_nomfichier, explnum_repertoire, explnum_path FROM explnum $restrict_join WHERE explnum_notice IN (".(implode(',', $notice_ids)).") order by explnum_notice";
		$res = pmb_mysql_query($sql);

		$results = array();
		$current_noticeid = 0;
		$current_explnums = array();
		while($row = pmb_mysql_fetch_assoc($res)) {
			if (!$current_noticeid)
				$current_noticeid = $row['explnum_notice'];

			if ($current_noticeid != $row['explnum_notice']){
				$results[] = array(
					'noticeid' => $current_noticeid,
					'explnums' => $current_explnums,
				);
				$current_explnums = array();
				$current_noticeid = $row['explnum_notice'];
			}
			if($row['explnum_repertoire']){
				$rqt="select repertoire_path from upload_repertoire where repertoire_id = ".$row['explnum_repertoire'];
				$r= pmb_mysql_query($rqt);
				$path = pmb_mysql_result($r,0,0);
				$filesize = filesize(str_replace("//","/",$path.$row['explnum_path']."/".$row['explnum_nomfichier']));
			}else if ($row['explnum_url'] == ""){
				$rqt = "select bit_length(explnum_data) from explnum where explnum_id = ".$row["explnum_id"];
				$r= pmb_mysql_query($rqt);
				$filesize = pmb_mysql_result($r,0,0);
			}
			$aresult = array(
				"id" => $row["explnum_id"],
				"noticeId" => $row['explnum_notice'],
				"bulletinId" => 0,
				"name" =>encoding_normalize::utf8_normalize( $row["explnum_nom"]),
				"mimetype" => encoding_normalize::utf8_normalize($row["explnum_mimetype"]),
				"filename" => encoding_normalize::utf8_normalize($row["explnum_nomfichier"]),
				"extention" => encoding_normalize::utf8_normalize($row["explnum_extfichier"]),
				"filesize" => encoding_normalize::utf8_normalize($filesize),
				"url" => encoding_normalize::utf8_normalize($row["explnum_url"]),
				"downloadUrl" => encoding_normalize::utf8_normalize($opac_url_base."doc_num.php?explnum_id=".$row["explnum_id"]),
				"vignUrl" => encoding_normalize::utf8_normalize($opac_url_base."vig_num.php?explnum_id=".$row["explnum_id"])
			);
			$current_explnums[] = $aresult;
		}
		$results[] = array(
			'noticeid' => $current_noticeid,
			'explnums' => $current_explnums,
		);

		return $results;
	}

	public function listBulletinExplNums($bulletinId, $OPACUserId=-1) {

		global $opac_url_base;

		$bulletinId = intval($bulletinId);
		$results = array();

		//Je filtre les bulletins en fonction des droits de visibilité
		$bulletin_ids=$this->filter_tabl_bulletins(array($bulletinId),"docnum");
		if(!count($bulletin_ids) || !$bulletin_ids[0]){
			return array();
		}

		//Je filtre les documents numériques en fonction des droits
		$restrict_join = $this->filter_tabl_explnum();

		$sql = "SELECT explnum_id, explnum_nom, explnum_mimetype, explnum_url, explnum_extfichier, explnum_nomfichier,explnum_repertoire, explnum_path FROM explnum $restrict_join WHERE explnum_bulletin = ".$bulletinId;
		$res = pmb_mysql_query($sql);

		while($row = pmb_mysql_fetch_assoc($res)) {
			if($row['explnum_repertoire']){
				$rqt="select repertoire_path from upload_repertoire where repertoire_id = ".$row['explnum_repertoire'];
				$r= pmb_mysql_query($rqt);
				$path = pmb_mysql_result($r,0,0);
				$filesize = filesize(str_replace("//","/",$path.$row['explnum_path']."/".$row['explnum_nomfichier']));
			}else if ($row['explnum_url'] == ""){
				$rqt = "select bit_length(explnum_data) from explnum where explnum_id = ".$row["explnum_id"];
				$r= pmb_mysql_query($rqt);
				$filesize = pmb_mysql_result($r,0,0);
			}
			$aresult = array(
				"id" => $row["explnum_id"],
				"bulletinId" => $bulletinId,
				"noticeId" => 0,
				"name" => encoding_normalize::utf8_normalize($row["explnum_nom"]),
				"mimetype" => encoding_normalize::utf8_normalize($row["explnum_mimetype"]),
				"filename" => encoding_normalize::utf8_normalize($row["explnum_nomfichier"]),
				"extention" => encoding_normalize::utf8_normalize($row["explnum_extfichier"]),
				"filesize" => filesize($opac_url_base."/doc_num.php?explnum_id=".$row["explnum_id"]),
				"url" => encoding_normalize::utf8_normalize($row["explnum_url"]),
				"downloadUrl" => encoding_normalize::utf8_normalize($opac_url_base."/doc_num.php?explnum_id=".$row["explnum_id"])
			);
			$results[] = $aresult;
		}

		return $results;
	}

	public function listBulletinsExplNums($bulletin_ids, $OPACUserId=-1) {

		global $opac_url_base;

		if (!$bulletin_ids) {
			throw new Exception("Missing parameter: bulletin_ids");
		}

		array_walk($bulletin_ids, function(&$a) {$a = intval($a);});	//Virons ce qui n'est pas entier
		$bulletin_ids = array_unique($bulletin_ids);
		if (!$bulletin_ids) {
			return array();
		}

		//Je filtre les bulletins en fonction des droits de visibilité
		$bulletin_ids=$this->filter_tabl_bulletins($bulletin_ids,"docnum");
		if(!count($bulletin_ids)){
			return array();
		}

		//Je filtre les documents numériques en fonction des droits
		$restrict_join = $this->filter_tabl_explnum();

		$sql = "SELECT explnum_id, explnum_nom, explnum_mimetype, explnum_url, explnum_bulletin, explnum_extfichier, explnum_nomfichier,explnum_repertoire, explnum_path FROM explnum $restrict_join WHERE explnum_bulletin IN (".implode(',', $bulletin_ids).') ORDER BY explnum_bulletin';
		$res = pmb_mysql_query($sql);

		$results = array();
		$current_id = 0;
		$current_explnums = array();
		while($row = pmb_mysql_fetch_assoc($res)) {
			if (!$current_id) {
				$current_id = $row["explnum_bulletin"];
			}
			if ($current_id != $row["explnum_bulletin"]) {
				$results[] = array(
					'bulletin_id' => $current_id,
					'bulletin_explnums' => $current_explnums,
				);
				$current_explnums = array();
				$current_id = $row["explnum_bulletin"];
			}
			if($row['explnum_repertoire']){
				$rqt="select repertoire_path from upload_repertoire where repertoire_id = ".$row['explnum_repertoire'];
				$r= pmb_mysql_query($rqt);
				$path = pmb_mysql_result($r,0,0);
				$filesize = filesize(str_replace("//","/",$path.$row['explnum_path']."/".$row['explnum_nomfichier']));
			}else if ($row['explnum_url'] == ""){
				$rqt = "select bit_length(explnum_data) from explnum where explnum_id = ".$row["explnum_id"];
				$r= pmb_mysql_query($rqt);
				$filesize = pmb_mysql_result($r,0,0);
			}
			$aresult = array(
				"id" => $row["explnum_id"],
				"noticeId" => 0,
				"bulletinId" => $current_id,
				"name" => encoding_normalize::utf8_normalize($row["explnum_nom"]),
				"mimetype" => encoding_normalize::utf8_normalize($row["explnum_mimetype"]),
				"filename" => encoding_normalize::utf8_normalize($row["explnum_nomfichier"]),
				"extention" => encoding_normalize::utf8_normalize($row["explnum_extfichier"]),
				"filesize" => filesize($opac_url_base."/doc_num.php?explnum_id=".$row["explnum_id"]),
				"url" => encoding_normalize::utf8_normalize($row["explnum_url"]),
				"downloadUrl" => encoding_normalize::utf8_normalize($opac_url_base."/doc_num.php?explnum_id=".$row["explnum_id"])
			);
			$current_explnums[] = $aresult;
		}
		$results[] = array(
			'bulletin_id' => $current_id,
			'bulletin_explnums' => $current_explnums,
		);

		return $results;
	}

	public function fetchNoticeByExplCb($emprId,$explCb, $recordFormat, $recordCharset, $includeLinks, $includeItems) {

		$sql = "SELECT expl_notice FROM exemplaires WHERE expl_cb LIKE '$explCb'";
		$res = pmb_mysql_query($sql);
		$noticelist = array();
		if(pmb_mysql_num_rows($res)){
			$noticelist[0]= pmb_mysql_result($res,0,0);
		}
		return $this->proxy_parent->pmbesNotices_fetchNoticeList($noticelist, $recordFormat, $recordCharset, $includeLinks);
	}

	public function fetchNoticeListFull($noticelist, $recordFormat, $recordCharset, $includeLinks, $nbResa = false) {

		$results = array();
		$notices_ = $this->proxy_parent->pmbesNotices_fetchNoticeList($noticelist, $recordFormat, $recordCharset, $includeLinks, false, false, $nbResa);
		$notices = array();
		foreach($notices_ as $anotice) {
			$notices[$anotice['noticeId']] = $anotice['noticeContent'];
		}
		$items = $this->proxy_parent->pmbesItems_fetch_notices_items($noticelist, -1);
		$expl_nums = $this->proxy_parent->pmbesNotices_listNoticesExplNums($noticelist, -1);
		$bulletins = $this->proxy_parent->pmbesNotices_fetch_notices_bulletins($noticelist, -1);
		$collstates = $this->proxy_parent->pmbesNotices_fetchNoticesCollstates($noticelist,-1);
		$administrative = $this->proxy_parent->pmbesNotices_fetchNoticesAdministrative($noticelist,-1);
		foreach($notices as $notice_id => $notice_content) {

			$aresult = array();
			$aresult['noticeId'] = $notice_id;
			$aresult['noticeContent'] = $notice_content;
			$aresult['noticeItems'] = array();
			$aresult['noticeExplNums'] = array();
			$aresult['noticeBulletins'] = array();
			$aresult['noticeCollstates'] = array();
			$aresult['noticeAdministrative'] = array();
			foreach($items as $key => $aitem) {
				if ($aitem['noticeid'] == $notice_id) {
					$aresult['noticeItems'] = $aitem['items'];
					unset($items[$key]);
					break;
				}
			}
			foreach($expl_nums as $key => $aexplnum) {
				if ($aexplnum['noticeid'] == $notice_id) {
					$aresult['noticeExplNums'] = $aexplnum['explnums'];
					unset($expl_nums[$key]);
					break;
				}
			}
			foreach($bulletins as $key => $abulletin) {
				if ($abulletin['noticeid'] == $notice_id) {
					$aresult['noticeBulletins'] = $abulletin['bulletins'];
					unset($bulletins[$key]);
					break;
				}
			}
			foreach($collstates as $key => $acollstate) {
				if ($acollstate['noticeid'] == $notice_id) {
					$aresult['noticeCollstates'] = $acollstate['collstates'];
					unset($collstates[$key]);
					break;
				}
			}
			foreach($administrative as $key => $aadministrative) {
				if ($aadministrative['noticeid'] == $notice_id) {
					$aresult['noticeAdministrative'] = $aadministrative['administrative'];
					unset($administrative[$key]);
					break;
				}
			}
			$results[] = $aresult;
		}
		return $results;
	}

	public function fetch_bulletin_list($bulletin_ids) {

		$result = array();

		if (!$bulletin_ids) {
			throw new Exception("Missing parameter: bulletin_ids");
		}
		array_walk($bulletin_ids, function(&$a) {$a = intval($a);});	//Virons ce qui n'est pas entier
		$bulletin_ids = array_unique($bulletin_ids);
		if (!$bulletin_ids) {
			return array();
		}
		//Je filtre les bulletins en fonction des droits de visibilité
		$bulletin_ids=$this->filter_tabl_bulletins($bulletin_ids,"notices");
		if(!count($bulletin_ids)){
			return array();
		}


		$sql = "SELECT bulletins.*,notices.tit1 FROM bulletins LEFT JOIN notices ON bulletin_notice = notice_id WHERE bulletin_id IN (".implode(',', $bulletin_ids).") ORDER BY bulletin_notice, date_date DESC";
		$res = pmb_mysql_query($sql);

		while($row=pmb_mysql_fetch_assoc($res)) {
			$abulletin = array(
				'bulletin_id' => $row['bulletin_id'],
				'serial_id' => $row['bulletin_notice'],
				'serial_title' => encoding_normalize::utf8_normalize($row['tit1']),
				'notice_id' => $row['num_notice'],
				'bulletin_numero' => encoding_normalize::utf8_normalize($row['bulletin_numero']),
				'bulletin_date_caption' => encoding_normalize::utf8_normalize($row['mention_date']),
				'bulletin_date' => encoding_normalize::utf8_normalize($row['date_date']),
				'bulletin_title' => encoding_normalize::utf8_normalize($row['bulletin_titre']),
				'bulletin_barcode' => encoding_normalize::utf8_normalize($row['bulletin_cb']),
			);
			$result[] = $abulletin;
		}

		return $result;
	}

	public function fetchBulletinListFull($bulletinlist, $recordFormat, $recordCharset, $nbResa = false) {
		$results = array();
		$bulletins = $this->proxy_parent->pmbesNotices_fetch_bulletin_list($bulletinlist);
		$items = $this->proxy_parent->pmbesItems_fetch_bulletins_items($bulletinlist, -1);
		$expl_nums = $this->proxy_parent->pmbesNotices_listBulletinsExplNums($bulletinlist, -1);

		$notice_ids = array();
		foreach($bulletins as $bulletin_content) {
			if ($bulletin_content['notice_id'])
				$notice_ids[] = $bulletin_content['notice_id'];
		}
		$notices_res=array();
		if(count($notice_ids)) {
			$notices = $this->proxy_parent->pmbesNotices_fetchNoticeList($notice_ids, $recordFormat, $recordCharset, true, false);
			if(count($notices)){
				foreach ( $notices as $value ) {
					   $notices_res[$value["noticeId"]]=$value["noticeContent"];
				}
			}
		}

		foreach($bulletins as $bulletin_content) {
			$aresult = array();
			$aresult['bulletin_id'] = $bulletin_content['bulletin_id'];
			$aresult['bulletin_notice'] = isset($notices_res[$bulletin_content['notice_id']]) ? $notices_res[$bulletin_content['notice_id']] : '';
			$aresult['bulletin_bulletin'] = $bulletin_content;
			$aresult['bulletin_items'] = array();
			$aresult['bulletin_doc_nums'] = array();
			foreach($items as $key => $aitem) {
				if ($aitem['bulletinid'] == $bulletin_content['bulletin_id']) {
					$aresult['bulletin_items'] = $aitem['items'];
					unset($items[$key]);
					break;
				}
			}
			foreach($expl_nums as $key => $aexplnum) {
				if ($aexplnum['bulletin_id'] == $bulletin_content['bulletin_id']) {
					$aresult['bulletin_doc_nums'] = $aexplnum['bulletin_explnums'];
					unset($expl_nums[$key]);
					break;
				}
			}
			$sql = 'SELECT analysis_notice FROM analysis WHERE analysis_bulletin = '.$bulletin_content['bulletin_id'];
			$res = pmb_mysql_query($sql);
			$aresult['bulletin_analysis_notice_ids'] = array();
			while($row = pmb_mysql_fetch_row($res)) {
				$aresult['bulletin_analysis_notice_ids'][] = $row[0];
			}

			if($nbResa === true) {
				$nbResaQuery = "SELECT COUNT(*) FROM resa WHERE resa_idbulletin = '{$bulletin_content['bulletin_id']}' UNION SELECT COUNT(*) FROM resa_planning WHERE resa_idbulletin = '{$bulletin_content['bulletin_id']}'";
				$res = pmb_mysql_query($nbResaQuery);
				$nbResa = pmb_mysql_result($res,0);
				$nbResaPlanning = pmb_mysql_result($res,1);
				$aresult['nbResa'] = intval($nbResa) + intval($nbResaPlanning);
			}

			$results[] = $aresult;
		}
		return $results;
	}

	public function fetch_notices_bulletins($notice_ids, $OPACUserId=-1) {

		$result = [];

		if (!$notice_ids) {
			throw new Exception("Missing parameter: notice_ids");
		}
		array_walk($notice_ids, function(&$a) {$a = intval($a);});	//Virons ce qui n'est pas entier
		$notice_ids = array_unique($notice_ids);
		if (!$notice_ids) {
			return [];
		}
		//Je filtre les notices en fonction des droits
		$notice_ids=$this->filter_tabl_notices($notice_ids);
		if(!count($notice_ids)){
			return array();
		}

		$sql = "SELECT * FROM bulletins WHERE bulletin_notice IN (".implode(',', $notice_ids).") ORDER BY bulletin_notice, date_date DESC";
		$res = pmb_mysql_query($sql);

		$current_noticeid = 0;
		$current_bulletins = [];
		while($row=pmb_mysql_fetch_assoc($res)) {
			if (!$current_noticeid) {
				$current_noticeid = $row['bulletin_notice'];
			}
			if ($current_noticeid != $row['bulletin_notice']){
				$result[] = array(
					'noticeid' => $current_noticeid,
					'bulletins' => $current_bulletins,
				);
				$current_noticeid = $row['expl_notice'];
				$current_bulletins = [];
			}

			$abulletin = array(
				'bulletin_id' => $row['bulletin_id'],
				'serial_id' => $row['bulletin_notice'],
				'bulletin_numero' => encoding_normalize::utf8_normalize($row['bulletin_numero']),
				'bulletin_date_caption' => encoding_normalize::utf8_normalize($row['mention_date']),
				'bulletin_date' => encoding_normalize::utf8_normalize($row['date_date']),
				'bulletin_title' => encoding_normalize::utf8_normalize($row['bulletin_titre']),
				'bulletin_barcode' => encoding_normalize::utf8_normalize($row['bulletin_cb']),
			);

			$current_bulletins[] = $abulletin;
		}

		$result[] = array(
			'noticeid' => $current_noticeid,
			'bulletins' => $current_bulletins,
		);

		return $result;
	}

	public function findNoticeBulletinId($noticeId) {

		$noticeId = intval($noticeId);
		if (!$noticeId)
			return 0;

		//Je filtre les notices en fonction des droits
		$notice_ids=$this->filter_tabl_notices(array($noticeId));
		if(!count($notice_ids) || !$notice_ids[0]){
			return 0;
		}

		$sql = 'SELECT bulletin_id FROM bulletins WHERE num_notice = '.$noticeId.' LIMIT 1';
		$res = pmb_mysql_query($sql);
		if ($row = pmb_mysql_fetch_row($res))
			return $row[0];
		return 0;
	}

	public function fetchNoticesCollstates($serial_ids, $OPACUserId=-1) {

		if (!$serial_ids)
			throw new Exception("Missing parameter: serial_ids");

		array_walk($serial_ids, function(&$a) {$a = intval($a);});	//Virons ce qui n'est pas entier
		$serial_ids = array_unique($serial_ids);
		if (!$serial_ids)
			return array();

		//Je filtre les notices en fonction des droits
		$serial_ids=$this->filter_tabl_notices($serial_ids);
		if(!count($serial_ids)){
			return array();
		}

		$req="SELECT id_serial, collstate_id FROM arch_statut, collections_state LEFT JOIN docs_location ON location_id=idlocation LEFT JOIN arch_emplacement ON collstate_emplacement=archempla_id WHERE
		id_serial IN (".implode(',', $serial_ids).") and archstatut_id=collstate_statut and ((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".( $OPACUserId? " or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)" : "").")
		 ORDER BY archempla_libelle, collstate_cote";
		$myQuery = pmb_mysql_query($req);

		$current_noticeid = 0;
		$current_collstates = array();
		if((pmb_mysql_num_rows($myQuery))) {
			while(($row = pmb_mysql_fetch_object($myQuery))) {
				if (!$current_noticeid)
					$current_noticeid = $row->id_serial;

				if ($current_noticeid != $row->id_serial){
					$result[] = array(
						'noticeid' => $current_noticeid,
						'collstates' => $current_collstates,
					);
				}

				$collstate=new collstate($row->collstate_id);
				$acollstate = array(
				'collstate_id' => $collstate->collstate_id,
				'collstate_location_libelle' => encoding_normalize::utf8_normalize($collstate->location_libelle),
				'collstate_cote' => encoding_normalize::utf8_normalize($collstate->cote),
				'collstate_type_libelle' => encoding_normalize::utf8_normalize($collstate->type_libelle),
				'collstate_emplacement_libelle' => encoding_normalize::utf8_normalize($collstate->emplacement_libelle),
				'collstate_statut_opac_libelle' => encoding_normalize::utf8_normalize($collstate->statut_opac_libelle),
				'collstate_origine' => encoding_normalize::utf8_normalize($collstate->origine),
				'collstate_state_collections' => encoding_normalize::utf8_normalize($collstate->state_collections),
				'collstate_lacune' => encoding_normalize::utf8_normalize($collstate->lacune)
				);

				$current_collstates[] = $acollstate;
			}
		}
		$result[] = array(
			'noticeid' => $current_noticeid,
			'collstates' => $current_collstates,
		);

		return $result;
	}

	public function fetchNoticeListFullWithBullId($noticelist, $recordFormat, $recordCharset, $includeLinks, $nbResa=false) {
		$results = array();
		$notices_ = $this->proxy_parent->pmbesNotices_fetchNoticeList($noticelist, $recordFormat, $recordCharset, $includeLinks, false, false, $nbResa);
		$notices = array();
		foreach($notices_ as $anotice) {
			$notices[$anotice['noticeId']] = $anotice['noticeContent'];
		}
		$items = $this->proxy_parent->pmbesItems_fetch_notices_items($noticelist, -1);
		$expl_nums = $this->proxy_parent->pmbesNotices_listNoticesExplNums($noticelist, -1);
		$bulletins = $this->proxy_parent->pmbesNotices_fetchNoticesBulletinsList($noticelist, -1);
		$collstates = $this->proxy_parent->pmbesNotices_fetchNoticesCollstates($noticelist,-1);
		$administrative = $this->proxy_parent->pmbesNotices_fetchNoticesAdministrative($noticelist,-1);

		foreach($notices as $notice_id => $notice_content) {
			$aresult = array();
			$aresult['noticeId'] = $notice_id;
			$aresult['noticeContent'] = $notice_content;
			$aresult['noticeItems'] = array();
			$aresult['noticeExplNums'] = array();
			$aresult['noticeBulletinIds'] = array();
			$aresult['noticeCollstates'] = array();
			foreach($items as $key => $aitem) {
				if ($aitem['noticeid'] == $notice_id) {
					$aresult['noticeItems'] = $aitem['items'];
					unset($items[$key]);
					break;
				}
			}
			foreach($expl_nums as $key => $aexplnum) {
				if ($aexplnum['noticeid'] == $notice_id) {
					$aresult['noticeExplNums'] = $aexplnum['explnums'];
					unset($expl_nums[$key]);
					break;
				}
			}
			foreach($bulletins as $key => $abulletin) {
				if ($abulletin['noticeid'] == $notice_id) {
					$aresult['noticeBulletinIds'] = $abulletin['bulletinids'];
					unset($bulletins[$key]);
					break;
				}
			}
			foreach($collstates as $key => $acollstate) {
				if ($acollstate['noticeid'] == $notice_id) {
					$aresult['noticeCollstates'] = $acollstate['collstates'];
					unset($collstates[$key]);
					break;
				}
			}
			foreach($administrative as $key => $aadministrative) {
				if ($aadministrative['noticeid'] == $notice_id) {
					$aresult['noticeAdministrative'] = $aadministrative['administrative'];
					unset($administrative[$key]);
					break;
				}
			}
			$results[] = $aresult;
		}
		return $results;
	}

	public function fetchNoticesBulletinsList($notice_ids,$OPACUserId=-1) {

		$result = array();

		if (!$notice_ids)
			throw new Exception("Missing parameter: notice_ids");

		array_walk($notice_ids, function(&$a) {$a = intval($a);});	//Virons ce qui n'est pas entier
		$notice_ids = array_unique($notice_ids);
		if (!$notice_ids)
			return array();

		//Je filtre les notices en fonction des droits
		$notice_ids=$this->filter_tabl_notices($notice_ids);
		if(!count($notice_ids)){
			return array();
		}

		$sql = "SELECT bulletin_id,bulletin_notice FROM bulletins WHERE bulletin_notice IN (".implode(',', $notice_ids).")  ORDER BY bulletin_notice,date_date DESC";
		$res = pmb_mysql_query($sql);

		$current_noticeid = 0;
		$current_bulletinIds = array();
		while($row=pmb_mysql_fetch_assoc($res)) {
			if (!$current_noticeid)
				$current_noticeid = $row['bulletin_notice'];

			if ($current_noticeid != $row['bulletin_notice']){
				$result[] = array(
					'noticeid' => $current_noticeid,
					'bulletinids' => $current_bulletinIds,
				);
			}

			$current_bulletinIds[] = $row['bulletin_id'];
		}

		$result[] = array(
			'noticeid' => $current_noticeid,
			'bulletinids' => $current_bulletinIds,
		);

		return $result;
	}

	public function fetchSerialList($OPACUserId=-1) {

		$sql = "
SELECT n1.notice_id,
       n1.tit1,
       (SELECT COUNT(1)
        FROM   notices n2
               RIGHT JOIN bulletins b2
                 ON ( n2.notice_id = b2.bulletin_notice )
        WHERE  n2.notice_id = n1.notice_id) AS issue_count,
       (SELECT COUNT(1)
        FROM   notices n3
               RIGHT JOIN bulletins b3
                 ON ( n3.notice_id = b3.bulletin_notice )
               RIGHT JOIN analysis a3
                 ON ( b3.bulletin_id = a3.analysis_bulletin )
        WHERE  n3.notice_id = n1.notice_id) AS analysis_count,
       (SELECT COUNT(1)
        FROM   notices n4
               RIGHT JOIN bulletins b4
                 ON ( n4.notice_id = b4.bulletin_notice )
               RIGHT JOIN exemplaires e4
                 ON ( b4.bulletin_id = e4.expl_bulletin )
        WHERE  n4.notice_id = n1.notice_id) AS item_count
FROM   notices n1
WHERE  n1.niveau_biblio = 's'
       AND n1.niveau_hierar = 1
ORDER  BY tit1  ";
		$res = pmb_mysql_query($sql);
		$results = array();
		while($row = pmb_mysql_fetch_assoc($res)) {
			//Je filtre les notices en fonction des droits
			$notice_ids=$this->filter_tabl_notices(array($row['notice_id']));
			if(count($notice_ids)){
				$aresult = array(
					'serial_id' => $row['notice_id'],
					'serial_title' => encoding_normalize::utf8_normalize($row['tit1']),
					'serial_issues_count' => $row['issue_count'],
					'serial_items_count' => $row['item_count'],
					'serial_analysis_count' => $row['analysis_count'],
				);
				$results[] = $aresult;
			}
		}
		return $results;
	}

	public function fetchNoticesAdministrative($notice_ids,$OPACUserId=-1) {

		$result = array();

		if (!$notice_ids) {
			throw new Exception("Missing parameter: notice_ids");
		}
		array_walk($notice_ids, function(&$a) {$a = intval($a);});	//Virons ce qui n'est pas entier
		$notice_ids = array_unique($notice_ids);
		if (!$notice_ids)
			return array();

		//Je filtre les notices en fonction des droits
		$notice_ids=$this->filter_tabl_notices($notice_ids);
		if(!count($notice_ids)){
			return array();
		}

		$sql = "SELECT notice_id, statut, opac_libelle, commentaire_gestion, thumbnail_url FROM notices JOIN notice_statut ON statut=id_notice_statut WHERE notice_id IN (".implode(',', $notice_ids).")  ";
		$res = pmb_mysql_query($sql);

		while($row=pmb_mysql_fetch_object($res)) {
				$result[] = array(
					'noticeid' => $row->notice_id,
					'administrative' => array('statut_id' => $row->statut,
					'statut_lib' => encoding_normalize::utf8_normalize($row->opac_libelle),
					'comment_admin' => encoding_normalize::utf8_normalize($row->commentaire_gestion),
					'thumbnail_url' => encoding_normalize::utf8_normalize($row->thumbnail_url),
				),
			);
		}

		return $result;
	}

	public function deleteNotices($tab_notice, $forcage = false) {
		global $gestion_acces_active;
		global $gestion_acces_user_notice;
		global $PMBuserid;
		$result = array();

		if (!$tab_notice)
			throw new Exception("Missing parameter: tab_notice");
		// on boucle sur le tableau de notice
		foreach($tab_notice as $notice_id) {
			// on vérifie les droits d'accès
			$acces_m = 1;
			if ($notice_id != 0 && $gestion_acces_active == 1 && $gestion_acces_user_notice == 1) {
				$ac= new acces();
				$dom_1= $ac->setDomain(1);
				$acces_m = $dom_1->getRights($PMBuserid,$notice_id,8);
			}

			if ($acces_m == 0) {
				$result[] = array(
						'noticeId'	=> $notice_id,
						'state' 	=> 1
				);
			} else {
				// on vérifie si on peux supprimer la notice ou si on force la suppression
				if ($forcage or $this->checkdelnotice($notice_id)) {
					$requete = "SELECT niveau_biblio, niveau_hierar FROM notices WHERE notice_id = '".$notice_id."'";
					$res = pmb_mysql_query($requete);
					if(pmb_mysql_num_rows($res)){
						// on vérifie si c'est un périodique
						if((pmb_mysql_result($res,0,'niveau_biblio') == "s") && (pmb_mysql_result($res,0,'niveau_hierar') == "1")){
							// on supprime le périodique
							$myPerio = new serial($notice_id);
							$myPerio->serial_delete();
						}else{
							// on supprime la notice
							notice::del_notice($notice_id);
						}
						$result[] = array(
							'noticeId'	=> $notice_id,
							'state' 	=> 0
						);
					}else{
						$result[] = array(
							'noticeId'	=> $notice_id,
							'state' 	=> 2
						);
					}
				}else{
					$result[] = array(
						'noticeId'	=> $notice_id,
						'state' 	=> 2
					);
				}
			}
		}
		return $result;
	}

	public function checkdelnotice($notice_id) {

		global $pmb_confirm_delete_from_caddie;

		if ($notice_id) {
			// TODO concept
			// on vérifie s'il y a des bulletins
			$query = "select count(1) from bulletins where bulletin_notice=".$notice_id." limit 1 ";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_result($result, 0, 0)) return false ;
			// on vérifie s'il y a des relations
			$notice_relations = notice_relations_collection::get_object_instance($notice_id);
			if ($notice_relations->get_nb_links()) return false ;
			// on vérifie s'il y a des exemplaires
			$query = "select count(1) from exemplaires where expl_notice=".$notice_id." limit 1 ";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_result($result, 0, 0)) return false ;
			// on vérifie s'il y a des réservations
			$query = "select count(1) from resa where resa_idnotice=".$notice_id." limit 1 ";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_result($result, 0, 0)) return false ;
			// on vérifie s'il y a des demandes
			$query = "select count(1) from demandes where num_notice=$notice_id";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_result($result, 0, 0)) return false ;
			// on vérifie s'il y a des exemplaires numériques
			$query = "select count(1) from explnum where explnum_notice=".$notice_id." limit 1 ";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_result($result, 0, 0)) return false ;
			// on vérifie s'il sont dans un panier et rajoute un controle sur le parametre
			if ($pmb_confirm_delete_from_caddie != '1') {
				$query = "select count(1) as qte, name from caddie_content, caddie where type='NOTI' and object_id='$notice_id' and caddie_id=idcaddie group by name";
				$result = pmb_mysql_query($query);
				if (pmb_mysql_result($result, 0, 0)) return false ;
			}
			// on vérifie s'il ont des état de collection
			$query = "select count(1) from collections_state where id_serial=".$notice_id." limit 1 ";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_result($result, 0, 0)) return false ;
			// on vérifie s'il y a des abonnements
			$query = "select count(1) from abts_abts where num_notice=".$notice_id." limit 1 ";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_result($result, 0, 0)) return false ;
			// on vérifie s'il y a des modèle d'abonnement
			$query = "select count(1) from abts_modeles where num_notice=".$notice_id." limit 1 ";
			$result = pmb_mysql_query($query);
			if (pmb_mysql_result($result, 0, 0)) return false ;

			return true ;
		}
		return false ;
	}
}
