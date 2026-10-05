<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CounterModel.php,v 1.1 2024/02/08 16:53:29 jparis Exp $

namespace Pmb\Dashboard\Models\Widget\Counter;
use Pmb\Dashboard\Models\WidgetModel;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class CounterModel extends WidgetModel
{}

