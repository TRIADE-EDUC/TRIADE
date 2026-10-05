<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UrlBuilder_35_3401.php,v 1.1 2022/07/19 12:13:02 qvarin Exp $
namespace Pmb\CMS\Library\UrlBuilder;

class UrlBuilder_35_3401 extends SearchUniversesUrlBuilder
{
	public const LVL = "search_segment";
	
	public static $entitiesIds = null;
	
	protected function getQuery(): string
	{
		return "SELECT id_search_segment AS 'entity_id' FROM search_segments";
	}
}