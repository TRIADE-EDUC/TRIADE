<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: StatusDiffusionsController.php,v 1.5.6.1.2.1 2025/05/21 12:31:40 rtigero Exp $

namespace Pmb\DSI\Controller;


use Pmb\DSI\Models\DiffusionStatus;
use Pmb\DSI\Orm\DiffusionStatusOrm;

class StatusDiffusionsController extends CommonController
{

	protected const VUE_NAME = "dsi/statusDiffusions";

	/**
	 *
	 * {@inheritDoc}
	 * @see \Pmb\DSI\Controller\CommonController::getBreadcrumb()
	 */
	protected function getBreadcrumb()
	{
		global $msg, $action, $id;
		$breadcrum = "{$msg['dsi_statutes']} {$msg['menu_separator']} {$msg['dsi_diffusions']}";

		if (isset($action) && $action == "edit" && isset($id)) {
			if (DiffusionStatusOrm::exist($id)) {
				$status = DiffusionStatusOrm::findById($id);
				$breadcrum .= " {$msg['menu_separator']} {$status->name}";
			}
		}

		return $breadcrum;
	}

	/**
	 * Ajout statut diffusion
	 */
	protected function addAction()
	{
		print $this->render([
			"status" => new DiffusionStatus()
		]);
	}

	/**
	 * Edition statut diffusion
	 */
	protected function editAction()
	{
		global $id;

		$id = intval($id);
		if (DiffusionStatusOrm::exist($id)) {
			$this->render([
				"status" => new DiffusionStatus($id)
			]);
		} else {
			global $msg;
			$this->notFound(
				sprintf($msg['status_diffusion_not_found'], strval($id)),
				"./dsi.php?categ=status_diffusions"
			);
		}
	}

	/**
	 * Liste des statuts de diffusion
	 */
	protected function defaultAction()
	{
		$diffusionStatus = new DiffusionStatus();
		print $this->render([
			"list" => $diffusionStatus->getList()
		]);
	}

	public function save()
	{
		$this->data->id = intval($this->data->id);

		$diffusionStatus = new DiffusionStatus($this->data->id);
		$result = $diffusionStatus->check($this->data);
		if ($result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}

		$diffusionStatus->setFromForm($this->data);
		if (0 == $this->data->id) {
			$diffusionStatus->create();
		} else {
			$diffusionStatus->update();
		}

		$this->ajaxJsonResponse($diffusionStatus);
		exit();
	}

	public function delete()
	{
		if (!DiffusionStatusOrm::exist($this->data->id)) {
			http_response_code(404);
			$this->ajaxError("Statut de diffusion introuvable");
			exit();
		}
		$diffusionStatus = new DiffusionStatus($this->data->id);
		$result = $diffusionStatus->delete();

		if ($result['error']) {
			$this->ajaxError($result['errorMessage']);
			exit();
		}
		$this->ajaxJsonResponse([
			'success' => true
		]);
		exit();
	}
}
