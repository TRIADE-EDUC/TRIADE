<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldsGroup.php,v 1.1 2023/07/10 14:45:15 qvarin Exp $

namespace Pmb\DSI\Models\Group\CustomFields;

use Pmb\DSI\Models\Group\RootGroup;

class CustomFieldsGroup extends RootGroup
{
    public const COMPONENT = "";

    /**
     * Permet de fournir des donnees pour le formulaire
     *
     * @return array
     */
    public function getFormData()
    {
        $messages = static::getMessages();
        return array_merge(parent::getFormData(), [
            "customFieldList" => $this->getCustomFields($messages['select_empty_Label']),
        ]);
    }
}