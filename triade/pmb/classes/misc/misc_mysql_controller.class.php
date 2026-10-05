<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: misc_mysql_controller.class.php,v 1.3.4.1 2025/05/21 12:02:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/misc/misc_controller.class.php");

class misc_mysql_controller extends misc_controller {
	
	protected static $model_class_name = '';
	
	protected static $list_ui_class_name = 'list_misc_mysql_ui';
	
	protected static function get_line_mysql_info_from_query($label, $query) {
		return "
			<div class='row'>
				<label class='etiquette'>".$label."</label>
			</div>
		  	<div class='row'>
				".pmb_sql_value($query)."
			</div>";
	}
	
	protected static function get_line_mysql_subtab($label, $url_extra) {
		global $base_path;
		return "
			<div class='row'>
				<a href='".$base_path."/admin.php?categ=misc&sub=mysql".$url_extra."'>".$label."</a>
			</div>";
	}
	
	public static function proceed_info() {
		global $info, $msg, $database_window_title;
		
		switch ($info) {
			case 'phpinfo':
				echo window_title($database_window_title."Php - Info");
				echo phpinfo();
				break;
			case 'table_index':
				echo window_title($database_window_title."Tables - indexes");
				$list_misc_table_index_ui = list_misc_table_index_ui::get_instance();
				if (count($list_misc_table_index_ui->get_objects())) {
				    echo "<b>".$msg['admin_info_table_index_pb']."</b><br />";
				    echo $list_misc_table_index_ui->get_display_list();
				} else {
				    echo $msg['admin_info_table_index_ok'];
				}
				break;
				/*case 'verif_base':
				 echo window_title($database_window_title.$msg[verification_verif_base]);
				 require_once("$base_path/admin/misc/verifications/verif_base.inc.php");
				 break;*/
			case 'mysqlinfo':
				echo window_title($database_window_title."MySQL - Info");
				
				echo static::get_line_mysql_info_from_query($msg['sql_info_notices'], "select count(*) as nb from notices");
				echo static::get_line_mysql_info_from_query($msg['sql_info_exemplaires'], "select count(*) as nb from exemplaires");
				echo static::get_line_mysql_info_from_query($msg['sql_info_bulletins'], "select count(*) as nb from bulletins");
				echo static::get_line_mysql_info_from_query($msg['sql_info_authors'], "select count(*) as nb from authors");
				echo static::get_line_mysql_info_from_query($msg['sql_info_publishers'], "select count(*) as nb from publishers");
				echo static::get_line_mysql_info_from_query($msg['sql_info_empr'], "select count(*) as nb from empr");
				echo static::get_line_mysql_info_from_query($msg['sql_info_pret'], "select count(*) as nb from pret");
				echo static::get_line_mysql_info_from_query($msg['sql_info_pret_archive'], "select count(*) as nb from pret_archive");
				
				echo "<hr />" ;
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Database name, host and user</label>
				</div>
			  <div class='row'>
					".DATA_BASE." on ".SQL_SERVER.", user=".USER_NAME."
					</div>
			  <div class='row'>
				<label class='etiquette' >MySQL Server Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_server_info()."
					</div><hr />" ;
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Client Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_client_info()."
					</div><hr />" ;
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Host Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_host_info()."
					</div><hr />" ;
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Protocol Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_proto_info()."
					</div><hr />" ;
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Stat. Information</label>
				</div>
			  <div class='row'>
					".str_replace('  ','<br />',pmb_mysql_stat())."</div><hr />";
				
				echo "<div class='row'>
				<label class='etiquette' >MySQL Variables</label>
				</div>
			  <div class='row'>";
				echo list_misc_mysqlinfo_ui::get_instance()->get_display_list();
                echo "</div>" ;
				break;
			case '':
			default:
				print static::get_line_mysql_subtab($msg['719'], '&action=CHECK');
				print static::get_line_mysql_subtab($msg['720'], '&action=ANALYZE');
				print static::get_line_mysql_subtab($msg['721'], '&action=REPAIR');
				print static::get_line_mysql_subtab($msg['722'], '&action=OPTIMIZE');
				print static::get_line_mysql_subtab($msg['admin_info_mysql'], '&info=mysqlinfo');
				print static::get_line_mysql_subtab($msg['admin_info_php'], '&info=phpinfo');
				print static::get_line_mysql_subtab($msg['admin_info_table_index'], '&info=table_index');
// 				print static::get_line_mysql_subtab($msg['admin_info_verif_base'], '&info=verif_base');
				break;
		}
	}
	
	public static function proceed($id=0) {
		global $action;
		
		$id = intval($id);
		if($action) {
			$list_ui_class_name = static::$list_ui_class_name;
			if($list_ui_class_name::is_authorized_action($action)) {
				$list_ui_class_name::set_table_action($action);
			
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
			}
		}
	}
}