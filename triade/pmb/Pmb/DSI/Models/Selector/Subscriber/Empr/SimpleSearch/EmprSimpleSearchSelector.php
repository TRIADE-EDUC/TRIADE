<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EmprSimpleSearchSelector.php,v 1.5 2023/04/03 14:56:06 rtigero Exp $
namespace Pmb\DSI\Models\Selector\Subscriber\Empr\SimpleSearch;

use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Models\Selector\SubSelector;
use Pmb\DSI\Models\SubscriberList\Subscribers\Subscriber;
use Pmb\DSI\Models\SubscriberList\Subscribers\SubscriberEmpr;

class EmprSimpleSearchSelector extends SubSelector
{

	public const TYPE_SELECTOR = 2;

	protected $data = null;

	public function __construct(?string $data = "")
	{
		$this->data = $data;
	}

	public function getData()
	{
        if (empty($this->data)) {
            return array();
        }
        $userInput = str_replace("*", "%", addslashes($this->data));
        $where = "empr_nom like '{$userInput}%' or empr_prenom like '{$userInput}%' or empr_cb like '{$userInput}%' ";

        $query = "SELECT id_empr, empr_nom, empr_prenom, empr_cb, empr_mail FROM empr";
        if ($userInput) {
            $query .= " WHERE $where ORDER BY empr_nom, empr_prenom";
        }

        $result = @pmb_mysql_query($query);

        $emprs = array();
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $emprs[] = $this->getSubscriberFromQuery($row);
            }
        }

        pmb_mysql_free_result($result);
		return $emprs;
	}

	/**
	 * Cree et remplit les donnees d'un subcriber a partir d'une ligne de requete
	 * @param object $row
	 * @return Subscriber
	 */
	protected function getSubscriberFromQuery($row)
	{
		$empr = new SubscriberEmpr();
		$empr->settings->idEmpr = $row->id_empr;
		$empr->settings->cb = $row->empr_cb;
		$empr->settings->idEmpr = $row->id_empr;
		$empr->settings->email = $row->empr_mail;
		$empr->name = $row->empr_prenom . ' ' . $row->empr_nom;
		$empr->type = self::TYPE_SELECTOR;

		return $empr;
	}

	/**
	 * Retourne la recherche effectuer pour l'affichage.
	 *
	 * @return string
	 */
	public function getSearchInput(): string
	{
	    if (isset($this->searchInput)) {
	        return $this->searchInput;
	    }

	    $messages = $this->getMessages();
	    $this->searchInput = sprintf(
	        $messages['search_input'],
	        htmlentities($this->data, ENT_QUOTES, GlobalContext::charset())
        );
	    return $this->searchInput;
    }
}

