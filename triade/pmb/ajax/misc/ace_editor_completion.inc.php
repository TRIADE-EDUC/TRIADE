<?php
use Pmb\Common\Helper\H2oAutocompletion;

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ace_editor_completion.inc.php,v 1.1 2021/05/04 13:13:36 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php"))
    die("no access");

$h2oAutocomplete = new H2oAutocompletion();
print json_encode($h2oAutocomplete->search($word));