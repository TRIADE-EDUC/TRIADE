<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DsiController.php,v 1.4.4.1 2025/05/21 14:15:06 rtigero Exp $

namespace Pmb\DSI\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Item\Entities\Record\RecordListItem\RecordListItem;

class DsiController extends Controller
{

	public function proceed()
	{
		$classname = $this->foundController();
		if (!empty($classname) && class_exists($classname)) {
			$controller = new $classname($this->data);
			return $controller->proceed();
		}
		$this->defaultAction();
	}

	private function foundController()
	{
		if (empty($this->data->categ)) {
			return "";
		}

		$explode = explode("\\", static::class);
		array_pop($explode);
		$explode[] = Helper::pascalize("{$this->data->categ}_controller");
		return implode("\\", $explode);
	}

	protected function defaultAction()
	{
		global $include_path, $lang;
		switch ($this->data->categ) {
			case 'docwatch':
				include_once("./dsi/docwatch/main.inc.php");
				break;
			case 'fluxrss':
				include_once('./dsi/rss/main.inc.php');
				break;
			case 'equations':
				//On vient du bouton Transformer en equation DSI
				global $requete, $base_path;
				if (!empty($requete)) {
					$newItem = RecordListItem::transformEquationToItem($requete);
					if ($newItem->id) {
						//On renvoie vers l'édition du nouvel item
						$redirect = "{$base_path}/dsi.php?categ=items&action=edit&id={$newItem->id}";
						header("Location: $redirect");
						exit;
					}
				}
				break;
			default:
				$filepath = "$include_path/messages/help/$lang/dsi2.txt";
				if (file_exists($filepath)) {
					include($filepath);
				}
				break;
		}
	}
}
