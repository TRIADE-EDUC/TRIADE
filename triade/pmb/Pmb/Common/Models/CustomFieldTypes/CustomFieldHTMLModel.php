<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CustomFieldHTMLModel.php,v 1.1 2020/09/29 09:32:33 btafforeau Exp $

namespace Pmb\Common\Models\CustomFieldTypes;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;

class CustomFieldHTMLModel extends Model
{
    public static function findHTMLValues($customField)
    {
        $htmlValues = [];
        if (empty($customField['VALUES'])) {
            $htmlValues[] = [
                'value' => ''
            ];
        } else {
            foreach ($customField['VALUES'] as $customValue) {
                $htmlValues[] = [
                    'value' => $customValue
                ];
            }
        }
        
        return $htmlValues;
    }
    
    public static function getHTMLGlobalValue($customValues)
    {
        $globalValue = [];
        foreach ($customValues as $customValue) {
            $globalValue[] = $customValue->value;
        }
        
        return $globalValue;
    }
    
    public static function getHTMLInformations($customField)
    {
        $customField['OPTIONS'][0]['DATA_PROPS'][0]['value'] = "
					plugins:[
							'undo', 'redo', '|',
							'cut','copy','paste','|',
							'bold','italic','underline','strikethrough','subscript','superscript','|',
							'indent', 'outdent', 'justifyLeft', 'justifyCenter', 'justifyRight','|',
							'insertOrderedList', 'insertUnorderedList', 'insertHorizontalRule', '|',
							'createLink'
					],
					extraPlugins:[
						'foreColor','hiliteColor',
						{name:'dijit/_editor/plugins/FontChoice', command:'fontSize', generic:true},
						{name:'dijit/_editor/plugins/FontChoice', command:'fontName', generic:true},
						{name:'dijit/_editor/plugins/FontChoice', command:'formatBlock', generic:true},
						{name:'dijit/_editor/plugins/LinkDialog', command:'createLink', generic:true},
						{name:'dijit/_editor/plugins/LinkDialog', command:'unlink', generic:true},
						{name:'dijit/_editor/plugins/LinkDialog', command:'insertImage', generic:true},
						{name:'dijit/_editor/plugins/ViewSource', command:'viewsource', generic:true},
					],
				";
        return Helper::array_camelize_key_recursive(Helper::array_change_key_case_recursive($customField));
    }
}