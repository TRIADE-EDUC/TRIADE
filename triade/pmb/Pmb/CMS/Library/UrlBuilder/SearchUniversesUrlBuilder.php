<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchUniversesUrlBuilder.php,v 1.1 2022/07/19 12:13:02 qvarin Exp $
namespace Pmb\CMS\Library\UrlBuilder;

class SearchUniversesUrlBuilder extends EntityUrlBuilder
{
	protected function active_module(): bool
	{
		global $opac_search_universes_activate;
		return $opac_search_universes_activate == 1;
	}
}

