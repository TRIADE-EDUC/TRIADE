<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir
// www.sigb.net)
// +-------------------------------------------------+
// $Id: titre_uniforme_hooks.class.php,v 1.1 2016-04-06 08:30:28 vtouchard Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once ($class_path.'/event/hook.class.php');
class titre_uniforme_hooks implements hook_interface {
	public static function get_subcriptions() {
		return array (
				'titre_uniforme' => array (
						'update' => array (
								array (
										"titre_uniforme_hooks",
										"titre_uniforme_updated" 
								) 
						) 
				) 
		);
	}
	
	public static function requires(){
		global $class_path;
		return  array(
			$class_path.'/tu_notice.class.php',
			$class_path.'/notice.class.php',
		);
	} 
	
	public static function titre_uniforme_updated($event) {
		global $dbh;
		/**
		 * La mise à jour du titre uniforme a été réalisée après un mappage depuis une notice
		 */
		if(($event->get_source_type() == 'notice') && $event->get_source_id() && $event->get_titre_uniforme_id()){
			
			$query = 'INSERT INTO notices_titres_uniformes SET
			ntu_num_notice='.$event->get_source_id().',
			ntu_num_tu="'.$event->get_titre_uniforme_id().'"';
			pmb_mysql_query($query, $dbh);
			
			tu_notice::update_index($event->get_titre_uniforme_id());
		}
	}
}