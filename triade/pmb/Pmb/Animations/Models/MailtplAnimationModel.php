<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailtplAnimationModel.php,v 1.1 2021/03/08 16:48:52 gneveu Exp $

namespace Pmb\Animations\Models;

use Pmb\Common\Models\MailtplModel;

class MailtplAnimationModel extends MailtplModel
{
    public static function getSelVars() {
        $selvars = parent::getSelVars();
        $selvars['animation_group'] = [
            'animation_name',
            'animation_start_date',
            'animation_start_hour',
            'animation_end_date',
            'animation_end_hour',
            'animation_registered_list',
            'animation_location',
            'animation_empr_name',
            'animation_empr_firstname'
        ];
        
        return $selvars;
    }
    
}
