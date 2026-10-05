<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FacetsGroup.php,v 1.1 2023/07/10 14:45:15 qvarin Exp $

namespace Pmb\DSI\Models\Group\Facets;

use Pmb\DSI\Models\Group\RootGroup;

class FacetsGroup extends RootGroup
{
    public const COMPONENT = "";

    /**
     * Ordre croissant
     */
    public const ORDER_ASC = 'asc';

    /**
     * Order decroissant
     */
    public const ORDER_DESC = 'desc';

    /**
     * Tri Alphanumerique
     */
    public const SORT_ALPHA = 1;

    /**
     * Tri Numérique
     */
    public const SORT_INTEGER = 2;

    /**
     * Tri par date
     */
    public const SORT_DATE = 3;

    /**
     * Permet de fournir des donnees pour le formulaire
     *
     * @return array
     */
    public function getFormData()
    {
        $messages = static::getMessages();
        return array_merge(parent::getFormData(), [
            "orderList" => [
                static::ORDER_ASC => $messages['order_asc'],
                static::ORDER_DESC => $messages['order_desc'],
            ],
            "sortList" => [
                static::SORT_ALPHA => $messages['sort_alpha'],
                static::SORT_INTEGER => $messages['sort_integer'],
                static::SORT_DATE => $messages['sort_date'],
            ],
            "fieldList" => $this->getFields(),
        ]);
    }
}
