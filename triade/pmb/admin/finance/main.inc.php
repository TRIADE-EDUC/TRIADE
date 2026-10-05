<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.13 2024/01/03 11:24:14 gneveu Exp $
// Gestion financière


if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

switch($sub) {
    case 'abts':
        include("./admin/finance/abts.inc.php");
        break;
    case 'prets':
        include("./admin/finance/tarif_prets.inc.php");
        break;
    case 'amendes':
        include("./admin/finance/amendes.inc.php");
        break;
    case 'amendes_relance':
        include("./admin/finance/amendes_relances.inc.php");
        break;
    case 'transactype':
        include("./admin/finance/transaction.inc.php");
        break;
    case 'transaction_payment_method':
        include("./admin/finance/transaction_payment_method.inc.php");
        break;
    case 'cashdesk':
        include("./admin/finance/cashdesk.inc.php");
        break;
    case 'blocage':
        include("./admin/finance/blocage.inc.php");
        break;
    case 'organization_account':
        include("./admin/finance/payments.inc.php");
        break;
    default:
        include("$include_path/messages/help/$lang/admin_gestion_financiere.txt");
        break;
}
