<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms.inc.php,v 1.3 2015/04/03 11:16:29 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

function cms_hash_new($table_name,$id=''){
	$new=false;
	while(!$new){
		$hash_tmp = md5($table_name.$id.time());
		$new=cms_hash_exist($hash_tmp);
		if(!$new){			
			$req = "insert into cms_hash set hash ='$hash_tmp' ";
			pmb_mysql_query($req);
		}
	}
	return $hash_tmp;
}

function cms_hash_exist($hash){	
	$rqt = "select * from cms_hash where hash ='$hash' ";
	$res = pmb_mysql_query($rqt);
	if(pmb_mysql_num_rows($res)){
		return true;
	}	
	return false;
}

function cms_hash_del($hash){	
	$rqt = "delete from cms_hash where hash ='$hash' ";
	$res = pmb_mysql_query($rqt);
}