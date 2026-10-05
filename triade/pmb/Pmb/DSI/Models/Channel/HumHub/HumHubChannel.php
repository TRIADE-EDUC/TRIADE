<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: HumHubChannel.php,v 1.5 2023/05/11 13:17:39 qvarin Exp $
namespace Pmb\DSI\Models\Channel\HumHub;

use Pmb\DSI\Models\Channel\RootChannel;

class HumHubChannel extends RootChannel
{

	/**
	 * Namespace utilisé par humhub pour les espaces
	 *
	 * @var string
	 */
	private const HUMHUB_SPACE_CLASS = "humhub\modules\space\models\Space";

	/**
	 * Delai maximum pour la connexion
	 *
	 * @var int
	 */
	private const CONNEXION_TIMEOUT = 5;

	public function send($subscriberList, $renderedView, $diffusion = null)
	{
		$contentContainer = $this->getContentContainer();

		if (! isset($contentContainer['id'])) {
			return false;
		}
		$data = [
			"data" => [
				"message" => $renderedView,
				"content" => [
					"metadata" => [
						"visibility" => $this->settings->visibility,
						"pinned" => $this->settings->pinned,
						"locked_comments" => $this->settings->lockedComments
					]
				]
			]
		];
		$data = json_encode($data);
		$this->getCurl("post/container/" . $contentContainer['id'], "POST", $data);
	}

	/**
	 * Recupere le "content container" associe a l'espace parametre
	 *
	 * @return array
	 */
	protected function getContentContainer()
	{
		$contentContainers = $this->getCurl("content/container");
		if (! $contentContainers) {
			return array();
		}
		foreach ($contentContainers['results'] as $contentContainer) {
			if ($contentContainer['objectPk'] == $this->settings->humHubSpace && $contentContainer["objectClass"] == self::HUMHUB_SPACE_CLASS) {
				return $contentContainer;
			}
		}
		return array();
	}

	/**
	 * Retourne la liste formatee des containers humhub
	 *
	 * @return array
	 */
	public function getContainers()
	{
		$result = array();
		$containers = $this->getCurl("space");
		if (! $containers) {
			return $result;
		}
		$containers = $containers['results'];
		foreach ($containers as $container) {
			$result[] = [
				"id" => $container['id'],
				"name" => $container['name']
			];
		}

		return $result;
	}

	/**
	 * Joue la requete curl et retourne le resultat
	 *
	 * @param string $apiMethod
	 * @param string $method
	 * @param string $content
	 * @return boolean|array
	 */
	private function getCurl($apiMethod = "", $method = "", $content = "")
	{
		if (! $this->checkSettings()) {
			return false;
		}

		$url = $this->settings->humHubUrl . $apiMethod;
		$authorization = "Authorization: Bearer " . $this->settings->humHubApiKey;

		$curl = new \Curl();
		$curl->set_option('CURLOPT_HTTPHEADER', array(
			'Content-Type: application/json',
			$authorization
		));
		$curl->set_option('CURLOPT_RETURNTRANSFER', true);
		$curl->set_option('CURLOPT_TIMEOUT', self::CONNEXION_TIMEOUT);

		switch ($method) {
			case "POST":
				$response = $curl->post($url, $content);
				break;
			default:
				$response = $curl->get($url);
				break;
		}
		$successHeaders = [
			200
		];
		if (! in_array($response->headers['Status-Code'], $successHeaders)) {
			return false;
		}
		return json_decode($response->body, true);
	}

	/**
	 * Verifie si les parametres necessaires pour la connexion humhub sont definis
	 *
	 * @return boolean
	 */
	private function checkSettings()
	{
		if (isset($this->settings) && isset($this->settings->humHubUrl) && isset($this->settings->humHubApiKey)) {
			return true;
		}
		return false;
	}
}

