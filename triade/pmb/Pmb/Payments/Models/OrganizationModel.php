<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: OrganizationModel.php,v 1.2 2024/01/03 11:24:13 gneveu Exp $

namespace Pmb\Payments\Models;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

use Pmb\Common\Models\Model;
use Pmb\Payments\Models\OrganizationParserManifest;
use Pmb\Payments\Orm\PaymentOrganizationOrm;

class OrganizationModel extends Model
{
    protected $pathManifest = "../Organization/manifest.xml";

    /**
     * The function returns a list of payment organizations.
     *
     * @return a list of payment organizations.
     */

    public static function getOrganizationList()
    {
        return PaymentOrganizationOrm::findAll();
    }

    /**
     * The function `getOrganizationListAvaible()` returns a list of organizations that are not already
     * present in the database.
     *
     * @return the list of organizations that are available for payment, based on the comparison
     * between the organizations in the database and the organizations listed in the manifest.xml file.
     */

    public static function getOrganizationListAvaible()
    {
        $organizations = PaymentOrganizationOrm::findAll();
        $manifest = new OrganizationParserManifest("./Pmb/Payments/Organization/manifest.xml");

        foreach($organizations as $organization) {
            foreach($manifest->organization as $key => $manifestOrganization) {
                if ($organization->name == $manifestOrganization) {
                    unset($manifest->organization[$key]);
                }
            }
        }
        return $manifest->organization;
    }
}
