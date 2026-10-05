<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionsController.php,v 1.14.4.2 2025/05/21 10:24:03 rtigero Exp $
namespace Pmb\DSI\Opac\Controller;

use encoding_normalize;
use Pmb\Common\Opac\Controller\Controller;
use Pmb\DSI\Models\Diffusion;
use Pmb\DSI\Models\DiffusionHistory;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;
use Pmb\DSI\Models\SubscriberList\Subscribers\Subscriber;
use Pmb\DSI\Models\SubscriberList\Subscribers\SubscriberEmpr;
use Pmb\DSI\Opac\Views\AlertsView;
use Pmb\DSI\Opac\Views\UnsubscribeView;
use Pmb\DSI\Orm\DiffusionOrm;
use Pmb\DSI\Orm\SubscribersDiffusionOrm;

class DiffusionsController extends Controller
{

	protected $action;

	protected $empr;

	public function __construct(object $data = null)
	{
		parent::__construct($data);
		if ($this->data->id) {
			$this->empr = new \emprunteur(intval($this->data->id));
		}
	}

	public function proceed($action = "")
	{
		switch ($action) {
			case "unsubscribe":
				$viewData = [
					"diffusion" => null
				];
				if (! DiffusionOrm::exist($this->data->idDiffusion)) {
					$view = new UnsubscribeView("dsi/unsubscribe", $viewData);
					print $view->render();
					exit();
				}
				$diffusion = new Diffusion($this->data->idDiffusion);
				$id = $this->data->idEmpr ?? $this->data->id;
				$viewData["diffusion"] = $diffusion;

				if ($id) {
					$view = new UnsubscribeView("dsi/unsubscribe", $viewData);

					//Cas d'une DSI privée, on supprime la diffusion à la désinscription
					if ($diffusion->settings->idEmpr == $id) {
						$diffusion->delete();
						print $view->render();
						exit();
					}
					$diffusion->fetchSubscriberList();
					RootSubscriberList::unsubscribe($diffusion, $id, $this->data->emprType);
					print $view->render();
				}
				break;
			case "bannette":
				$opacDiffusions = $this->getOpacDiffusions();
				$opacDiffusionsPrivate = $this->getOpacDiffusionsPrivate();

				$subscriber = $this->getSubscriberFromType();

				$viewData = [
					"list" => $opacDiffusions,
					"listPrivate" => $opacDiffusionsPrivate,
					"subscriber" => $subscriber,
					"emprType" => $this->data->emprType
				];
				$view = new AlertsView("dsi/alerts", $viewData);
				print $view->render();
				break;
			default:
				return;
		}
	}

	public function getSubscriberFromType()
	{
		$subscriber = null;
		switch ($this->data->emprType) {
			case "pmb":
				//On récupère les infos de l'emprunteur
				$subscriber = new SubscriberEmpr();
				$query = "select empr_cb, empr_nom, empr_prenom, empr_mail from empr WHERE id_empr = '" . $this->data->id . "'";
				$result = pmb_mysql_query($query);
				if (pmb_mysql_num_rows($result) == 1) {
					$row = pmb_mysql_fetch_assoc($result);
					$subscriber->settings->idEmpr = intval($this->data->id);
					$subscriber->settings->cb = $row['empr_cb'];
					$subscriber->settings->email = $row['empr_mail'];
					$subscriber->name = $row['empr_prenom'] . ' ' . $row['empr_nom'];
					//En passant par ici on sera sur un emprunteur de la source
					$subscriber->type = RootSubscriberList::SUBSCRIBER_TYPE_SOURCE;
				}
				break;
			default:
				$subscriber = Subscriber::getInstance("diffusions", $this->data->id);
				break;
		}

		return $subscriber;
	}

	private function getOpacDiffusions()
	{
		$result = array();
		$diffusions = DiffusionOrm::finds([
			"settings" => [
				"value" => "%\"opacVisibility\":true%",
				"operator" => "LIKE",
				"inter" => "AND"
			]
		]);
		foreach ($diffusions as $diffusion) {
			if (! $this->checkDiffusionOpacFilters($diffusion)) {
				continue;
			}

			$result[] = $this->formatDiffusion($diffusion->id_diffusion);
		}
		return $result;
	}

	private function getOpacDiffusionsPrivate()
	{
		$result = array();
		switch ($this->data->emprType) {
			case "pmb":
				//Dans le cas d'un emprunteur on met l'idempr directement dans les settings
				//de la diffusion pour faciliter les choses
				$params = [
					"settings" => [
						"value" => "%\"idEmpr\":" . $this->data->id . "%",
						"operator" => "LIKE",
						"inter" => "AND"
					]
				];
				break;
			default:
				$params = array();
				break;
		}
		$diffusions = DiffusionOrm::finds($params);
		foreach ($diffusions as $diffusion) {
			$result[] = $this->formatDiffusion($diffusion->id_diffusion);
		}
		return $result;
	}

	private function formatDiffusion($idDiffusion)
	{
		$result = array();
		$diffusionModel = new Diffusion($idDiffusion);
		$result['settings'] = $diffusionModel->settings;
		$result['id'] = $diffusionModel->idDiffusion;
		$diffusionModel->fetchLastDiffusion();
		$diffusionModel->fetchDiffusionHistory();
		$result['lastDiffusion'] = $diffusionModel->lastDiffusion;
		$result['diffusionHistory'] = $this->formatHistory($diffusionModel->diffusionHistory);
		$diffusionModel->fetchItem();
		$result['nbResults'] = $diffusionModel->item->getNbResults();
		$result['searchInput'] = $diffusionModel->item->getSearchInput();
		$result['isSubscribed'] = $diffusionModel->isSubscribed($this->data->id);
		$result['tags'] = $diffusionModel->tags;
		$result['subscriber'] = $this->getSubscriberFromEntity("diffusions", $diffusionModel->idDiffusion);

		return $result;
	}

	/**
	 * Vérifie si un emprunteur est est base pour une entitée donnée
	 *
	 * @param $entityType int
	 * @param $entityId int
	 * @return Subscriber | null
	 */
	public function getSubscriberFromEntity($entityType, $entityId)
	{
		$searchSubscriber = array();
		switch ($entityType) {
			case "diffusions":
				$searchSubscriber = SubscribersDiffusionOrm::finds([
					"num_diffusion" => $entityId,
					'settings' => [
						"value" => '%"idEmpr":' . $this->data->id . '%',
						"operator" => "LIKE",
						"inter" => "AND"
					]
				]);
				break;
			default:
				break;
		}
		if (count($searchSubscriber) == 1) {
			$idSubscriber = $searchSubscriber[0]->id_subscriber_diffusion;
			return Subscriber::getInstance($entityType, $idSubscriber);
		}
		return null;
	}

	private function checkDiffusionOpacFilters($diffusion)
	{
		$settings = $diffusion->settings;
		if (is_string($settings)) {
			$settings = encoding_normalize::json_decode($diffusion->settings);
		}

		//Filtrage des diffusions publiques
		if (! empty($settings->opacVisibilityCateg) && array_search($this->empr->categ, $settings->opacVisibilityCateg) === false) {
			return false;
		}
		if(! empty($settings->opacVisibilityGroups)) {
			if(! $this->checkOpacVisibilityGroups($settings->opacVisibilityGroups)) {
				return false;
			}
		}

		return true;
	}

	private function formatHistory($diffusionHistory)
	{
		$result = array();
		foreach ($diffusionHistory as $history) {
			if ($history->state != DiffusionHistory::SENT) {
				continue;
			}
			$result[] = [
				"date" => $history->formatedDate,
				"id" => $history->idDiffusionHistory,
				"render" => $history->previewView()
			];
		}
		return $result;
	}

	/**
	 * Vérifie la visibilité des groupes Opac.
	 *
	 * @param array $groups Les groupes à vérifier.
	 * @return bool Renvoie true si au moins un groupe est visible, false sinon.
	 */
	private function checkOpacVisibilityGroups($groups)
	{
		$groups = implode(",", $groups);
		$query = "SELECT * FROM empr_groupe WHERE empr_id = " . $this->empr->id . " AND groupe_id IN (" . $groups . ")";
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)) {
			return true;
		}
		return false;
	}
}