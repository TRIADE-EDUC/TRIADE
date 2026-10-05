<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: HarvestRouterRest.php,v 1.3 2024/03/14 10:32:21 qvarin Exp $
namespace Pmb\REST;

class HarvestRouterRest extends RouterRest
{

	/**
	 *
	 * @const string
	 */
	protected const CONTROLLER = "\\Pmb\\Harvest\\Controller\\HarvestController";

	/**
	 *
	 * {@inheritdoc}
	 * @see \Pmb\REST\RouterRest::generateRoutes()
	 */
	protected function generateRoutes()
	{
		$this->post("/profil/save", "saveProfile");
		$this->post("/profil/delete", "deleteProfile");
		$this->post("/profil/import/save", "saveImportProfile");
		$this->post("/profil/import/delete", "deleteImportProfile");
	}

	/**
	 *
	 * @param RouteRest $route
	 * @return mixed
	 */
	protected function call(RouteRest $route)
	{
		global $data;

		$className = static::CONTROLLER;
		$data = \encoding_normalize::json_decode(stripslashes($data ?? "{}"));
		if (empty($data) || ! is_object($data)) {
			$data = new \stdClass();
		}

		$callback = [
			new $className($data),
			$route->getMethod()
		];

		if (is_callable($callback)) {
			return call_user_func_array($callback, $route->getArguments());
		}
	}
}