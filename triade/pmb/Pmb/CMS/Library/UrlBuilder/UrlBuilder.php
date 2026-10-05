<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UrlBuilder.php,v 1.1 2022/03/31 13:10:36 qvarin Exp $

namespace Pmb\CMS\Library\UrlBuilder;

interface UrlBuilder
{
    public function makeUrl(): string;
}

