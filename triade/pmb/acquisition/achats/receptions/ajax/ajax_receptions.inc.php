<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_receptions.inc.php,v 1.2 2019/05/28 15:00:01 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $quoifaire, $class_path, $id, $lgstat, $comment;

switch($quoifaire){
	
	case 'get_comment':
	case 'upd_comment':

		require_once("$class_path/lignes_actes.class.php");
		$pos =	strrpos($id,'_');
		$typ_com = substr($id,0,$pos); 
		$id_lig=substr($id,$pos+1);
		$la = new lignes_actes($id_lig); 
		break;

	case 'upd_lgstat':
		require_once("$class_path/lignes_actes.class.php");
		$pos =	strrpos($id,'_');
		$id_lig=substr($id,$pos+1);
		$la = new lignes_actes($id_lig);
		$la->updateFields(array(0=>$la->id_ligne),array('statut'=>$lgstat));
		break;
	
	default :
		break;
}


switch($quoifaire){
	
	case 'get_comment':

		switch($typ_com){	
			case 'comment_lg':
				ajax_http_send_response($la->commentaires_gestion);
				break;
				
			case 'comment_lo':
				ajax_http_send_response($la->commentaires_opac);
				break;
				
			default :
				break;
		}
		break;
		
	case 'upd_comment':
		
		switch($typ_com){	
			case 'comment_lg':
				$la->commentaires_gestion=$comment;
				$la->updateFields(array(0=>$la->id_ligne), array('commentaires_gestion'=>$comment));
				ajax_http_send_response(nl2br(stripslashes($la->commentaires_gestion)));
				break;
				
			case 'comment_lo':
				$la->commentaires_opac=$comment;
				$la->updateFields(array(0=>$la->id_ligne), array('commentaires_opac'=>$comment));
				ajax_http_send_response(nl2br(stripslashes($la->commentaires_opac)));
				break;
				
			default :
				break;
		}
		
		break;

	default :
		break;
}
