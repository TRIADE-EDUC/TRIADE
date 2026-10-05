<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DashboardParserDirectory.php,v 1.1 2024/01/25 09:10:25 jparis Exp $

namespace Pmb\Dashboard\Models;

use Pmb\Common\Library\Parser\ParserDirectory;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class DashboardParserDirectory extends ParserDirectory
{

    protected $baseDir = __DIR__;

    protected $parserManifest = "\Pmb\Dashboard\Models\DashboardParserManifest";

    protected function parse() 
	{
	    $path = $this->baseDir;
	    $manifests = $this->loadManifests($path);
	    
	    foreach ($manifests as $manifest) {
	        $this->manifest[$manifest->namespace] = $manifest;
	    }

	    $this->parsed = true;
	}
}

