<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: campaigns_controller.class.php,v 1.5 2020/11/18 14:42:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/campaigns/campaign.class.php");
require_once($class_path."/campaigns/campaign_stats.class.php");

class campaigns_controller extends lists_controller {
	
	protected static $model_class_name = 'campaign';
	
	protected static $list_ui_class_name = 'list_campaigns_ui';
	
	public static function proceed($id=0) {
		global $action;
	
		switch ($action) {
			case 'view':
				print static::get_model_instance($id)->get_view();
				break;
			case 'consolidate':
				$campaign_stats = new campaign_stats($id);
				$campaign_stats->build_data();
				$campaign_stats->save();
				print $campaign_stats->get_json_data();
				break;
			case 'list_view':
				print list_campaigns_ui::view();
				break;
			default:
				parent::proceed($id);
		}
	}
}