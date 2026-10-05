<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: param_func.inc.php,v 1.36 2024/03/21 15:28:59 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path;

// affichage du form de creation/modification parametres
require_once ($class_path . '/translation.class.php');
require_once ($include_path . '/templates/admin.tpl.php');
require_once ($include_path . "/parser.inc.php");

function param_form($id_param = 0, $type_param = "", $sstype_param = "", $valeur_param = "", $comment_param = "") {
    global $msg;
    global $admin_param_form;
    global $form_ajax;
    global $charset;
    
    $title = $msg[1606]; // modification
    
    $admin_param_form = str_replace('!!form_title!!', $title, $admin_param_form);
    $admin_param_form = str_replace('!!id_param!!', $id_param, $admin_param_form);
    $admin_param_form = str_replace('!!type_param!!', $type_param, $admin_param_form);
    $admin_param_form = str_replace('!!sstype_param!!', $sstype_param, $admin_param_form);
    $admin_param_form = str_replace('!!valeur_param!!', htmlentities($valeur_param, ENT_QUOTES, $charset), $admin_param_form);
    $admin_param_form = str_replace('!!comment_param!!', htmlentities($comment_param, ENT_QUOTES, $charset), $admin_param_form);
    $is_translated = parameter::is_translated($type_param, $sstype_param);
    if($is_translated) {
        $admin_param_form = str_replace('!!data-translation!!', "data-translation-fieldname='valeur_param'", $admin_param_form);
    } else {
        $admin_param_form = str_replace('!!data-translation!!', "", $admin_param_form);
    }
    if ($form_ajax) {
        $admin_param_form = encoding_normalize::utf8_normalize($admin_param_form);
    }
    print $admin_param_form;
}

function _section_($param) {
    global $section_table;
    
    $section_table[$param["NAME"]]["LIB"] = $param["value"];
    if (isset($param["ORDER"])) {
        $section_table[$param["NAME"]]["ORDER"] = $param["ORDER"];
    } else {
        $section_table[$param["NAME"]]["ORDER"] = '';
    }
}

function show_param() {
    global $msg;
    global $begin_result_liste;
    global $form_type_param, $form_sstype_param; // si modif , ces valeurs sont connues, on va faire une ancre avec
    global $lang;
    global $include_path;
    global $section_table, $charset;
    
    $allow_section = 0;
    
    if (file_exists($include_path . "/section_param/$lang.xml")) {
        _parser_($include_path . "/section_param/$lang.xml", array(
            "SECTION" => "_section_"
        ), "PMBSECTIONS");
        $allow_section = 1;
    }
    print $begin_result_liste;
    
    $requete = "select * from parametres where gestion=0 order by type_param, section_param, sstype_param ";
    $res = pmb_mysql_query($requete);
    $i = 0;
    $type_param = '';
    $section_param = '';
    while ($param = pmb_mysql_fetch_object($res)) {
        if (! $type_param) {
            $type_param = $param->type_param;
            $creer = 1;
            $fincreer = 0;
            $odd_even = 0;
        } elseif ($type_param != $param->type_param) {
            $type_param = $param->type_param;
            $creer = 1;
            $fincreer = 1;
            $odd_even = 0;
        } else {
            $creer = 0;
            $fincreer = 0;
        }
        if (($section_param != $param->section_param) && ($allow_section)) {
            $section_param = $param->section_param;
            $creer_section = 1;
        } else
            $creer_section = 0;
        
        if ($fincreer) {
            print "\n</table></div>";
        }
        if ($creer) {
            $lab_param = $msg["param_" . $type_param];
            if ($lab_param == "")
                $lab_param = $type_param;
            
            print "\n<div id=\"el" . $type_param . "Parent\" class='parent' width=\"100%\">
                    ".get_expandBase_button('el' . $type_param)."
					<span class='heada'>" . $lab_param . "</span>
					<br />
					</div>\n
					<div id=\"el" . $type_param . "Child\" class=\"child\" style=\"margin-bottom:6px;display:none;\"";
            if ($form_type_param == $type_param) {
                print " startOpen='Yes' ";
            }
            print ">";
            print "\n<table><tr>";
            print "
				<th>" . $msg[1603] . "</th>
				<th>" . $msg[1604] . "</th>
				<th>" . $msg['param_explication'] . "</th></tr>";
        }
        if ($odd_even == 0) {
            $class_liste = "odd";
            $odd_even = 1;
        } else if ($odd_even == 1) {
            $class_liste = "even";
            $odd_even = 0;
        }
        $surbrillance = "surbrillance";
        if ($param->type_param == $form_type_param && $param->sstype_param == $form_sstype_param) {
            $class_liste .= " justmodified";
            $surbrillance .= " justmodified";
        }
        $tr_javascript = " onmouseover=\"this.className='$surbrillance'\" onmouseout=\"this.className='$class_liste'\" ";
        if ($section_param && $creer_section) {
            print "\n<tr><th colspan='3'><b>" . $section_table[$section_param]["LIB"] . "</b></th></tr>";
        }
        if ($param->type_param == $form_type_param && $param->sstype_param == $form_sstype_param) {
            print "\n<tr data-param-id='" . $param->id_param . "' data-search='" . strtolower(encoding_normalize::json_encode(array(
                'search_value' => $type_param . ' ' . $param->sstype_param . ' ' . $param->comment_param . ' ' . $param->valeur_param
            ),JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP)) . "'   class='$class_liste' $tr_javascript style='cursor: pointer;'>
				<td style='vertical-align:top'><a name='justmodified'></a>$param->sstype_param</td>";
        } else {
            print "\n<tr data-param-id='" . $param->id_param . "' data-search='" . strtolower(encoding_normalize::json_encode(array(
                'search_value' => $type_param . ' ' . $param->sstype_param . ' ' . $param->comment_param . ' ' . $param->valeur_param
            ),JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP)) . "' class='$class_liste' $tr_javascript style='cursor: pointer'>
					<td style='vertical-align:top'>$param->sstype_param</td>";
        }
        
        $valeur_param = $param->valeur_param;
        // Si $param->valeur_param contient un balise html... on formate la valeur pour l'affichage
        if (preg_match("/<.+>/", $param->valeur_param)) {
            $valeur_param = "<pre class='params_pre'>"
                                .htmlentities($param->valeur_param, ENT_QUOTES, $charset).
                            "</pre>";
        }
        print " <td class='ligne_data'>
                    $valeur_param
                </td>
                <td style='vertical-align:top'>$param->comment_param</td>\n</tr>";
    } // fin while
    print "</table></div>";
    print "<script type='text/javascript'>
                require(['dojo/ready', 'apps/pmb/ParametersRefactor'], function(ready, ParametersRefactor){
                    ready(function(){
                        new ParametersRefactor();
                    });
                });
           </script>";
}
