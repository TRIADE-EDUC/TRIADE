<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UrlBuilder_34_3301.php,v 1.1 2022/07/19 12:13:02 qvarin Exp $
namespace Pmb\CMS\Library\UrlBuilder;

class UrlBuilder_34_3301 extends SearchUniversesUrlBuilder
{
	public const LVL = "search_universe";
	
	public static $entitiesIds = null;
	
	protected function getQuery(): string
	{
		return "SELECT id_search_universe AS 'entity_id' FROM search_universes";
	}
}