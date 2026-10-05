<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SemanticSearchView.php,v 1.1 2024/01/23 15:39:43 gneveu Exp $

namespace Pmb\AI\Views;

use Pmb\Common\Views\VueJsView;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class SemanticSearchView extends VueJsView
{

}
