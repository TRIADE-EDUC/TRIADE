<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EmprEmptySourceSubscribersSelector.php,v 1.1 2023/10/10 13:23:05 rtigero Exp $
namespace Pmb\DSI\Models\Selector\Subscriber\Empr\EmptySourceSubscribers;

use Pmb\DSI\Models\Selector\SubSelector;

class EmprEmptySourceSubscribersSelector extends SubSelector
{

	public const TYPE_SELECTOR = 2;

	protected $data = null;

	public function __construct(?string $data = "")
	{
		$this->data = $data;
	}

	public function getData()
	{
        return array();
	}

	/**
	 * Retourne la recherche effectuer pour l'affichage.
	 *
	 * @return string
	 */
	public function getSearchInput(): string
	{
	    return "";
    }
}

