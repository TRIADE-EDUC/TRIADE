<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_graph_view_spacetree.class.php,v 1.9 2023/10/26 15:26:18 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_graph_view_spacetree extends cms_module_common_view
{

    /**
     * Permet de définir la liste des parametres et leur valeur par defaut
     */
    public const DEFAULT_VALUE = [

        /* Elements graphiques */
        /* Distance entre les points/noeuds */
        "distance" => 100,

        "node_type" => "rectangle",
        "node_width" => "100",
        "node_height" => "50",
        "node_color" => "#000000",
        "node_color_selected" => "#FF7000",

        "label_style" => "",
        "label_size" => "10",
        "label_color" => "#FFFFFF",

        /* Nb de niveaux a afficher */
        "levels_to_show" => 2,
    ];

    /**
     * Liste des types de noeuds
     */
    public const NODE_TYPES = [
        "circle",
        "rectangle",
        "square",
        "ellipse",
    ];

    /**
     * Liste des styles de labels
     */
    public const LABEL_STYLES = [
        "none",
        "italic",
        "bold",
    ];

    /**
     * Retourne le formulaire de parametrage de la vue
     *
     * @return string
     */
    public function get_form()
    {
        $distance = $this->parameters['distance'] ?? static::DEFAULT_VALUE['distance'];
        $levelsToShow = $this->parameters['levels_to_show'] ?? static::DEFAULT_VALUE['levels_to_show'];
        $nodeType = $this->parameters['node_type'] ?? static::DEFAULT_VALUE['node_type'];
        $nodeWidth = $this->parameters['node_width'] ?? static::DEFAULT_VALUE['node_width'];
        $nodeHeight = $this->parameters['node_height'] ?? static::DEFAULT_VALUE['node_height'];
        $nodeColor = $this->parameters['node_color'] ?? static::DEFAULT_VALUE['node_color'];
        $nodeColorSelected = $this->parameters['node_color_selected'] ?? static::DEFAULT_VALUE['node_color_selected'];
        $tooltipTemplateFolder = $this->parameters['tooltip_template_folder'] ?? "common";
        $labelStyle = $this->parameters['label_style'] ?? static::DEFAULT_VALUE['label_style'];
        $labelSize = $this->parameters['label_size'] ?? static::DEFAULT_VALUE['label_size'];
        $labelColor = $this->parameters['label_color'] ?? static::DEFAULT_VALUE['label_color'];

        $nodeTypeOptions = "";
        foreach(static::NODE_TYPES as $nodeTypeOption) {
            $label = $this->msg['cms_module_graph_view_spacetree_node_type_'.$nodeTypeOption] ?? $nodeTypeOption;
            $selected = "";
            if ($nodeTypeOption == $nodeType) {
                $selected = "selected='selected'";
            }
            $nodeTypeOptions .= "<option value='{$nodeTypeOption}' {$selected}>". $this->format_text($label) ."</option>";
        }

        $labelStyleOptions = "";
        foreach(static::LABEL_STYLES as $labelStyleOption) {
            $label = $this->msg['cms_module_graph_view_spacetree_label_style_'.$labelStyleOption] ?? $labelStyleOption;
            $selected = "";
            if ($labelStyleOption == $labelStyle) {
                $selected = "selected='selected'";
            }
            $labelStyleOptions .= "<option value='{$labelStyleOption}' {$selected}>". $this->format_text($label) ."</option>";
        }
        $form =  "<div class='row'><p>{$this->msg['toolkit_required']}</p></div>";
        $form .= "
        <div class='row'>
            <div class='colonne3'>
                <label for='distance'>"
                 . $this->format_text($this->msg['cms_module_graph_view_spacetree_distance']) .
                "</label>
            </div>
            <div class='colonne-suite'>
                <input min='50' required
                    type='number'
                    id='distance'
                    name='". $this->get_form_value_name('distance') ."'
                    class='saisie-20em'
                    value='". intval($distance) ."' />
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='levels_to_show'>"
                 . $this->format_text($this->msg['cms_module_graph_view_spacetree_levels_to_show']) .
                "</label>
            </div>
            <div class='colonne-suite'>
                <input min='2' required
                    type='number'
                    id='levels_to_show'
                    name='". $this->get_form_value_name('levels_to_show') ."'
                    class='saisie-20em'
                    value='". intval($levelsToShow) ."' />
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='node_type'>"
                 . $this->format_text($this->msg['cms_module_graph_view_spacetree_node_type']) .
                "</label>
            </div>
            <div class='colonne-suite'>
                <select
                    id='node_type' required
                    name='". $this->get_form_value_name('node_type') ."'
                    class='saisie-20em'>
                    {$nodeTypeOptions}
                </select>
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='node_width'>"
                 . $this->format_text($this->msg['cms_module_graph_view_spacetree_node_width']) .
                "</label>
            </div>
            <div class='colonne-suite'>
                <input min='1' required
                    type='number'
                    id='node_width'
                    name='". $this->get_form_value_name('node_width') ."'
                    class='saisie-20em'
                    value='". intval($nodeWidth) ."' />
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='node_height'>"
                 . $this->format_text($this->msg['cms_module_graph_view_spacetree_node_height']) .
                "</label>
            </div>
            <div class='colonne-suite'>
                <input min='1' required
                    type='number'
                    id='node_height'
                    name='". $this->get_form_value_name('node_height') ."'
                    class='saisie-20em'
                    value='". intval($nodeHeight) ."' />
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='node_color'>"
                 . $this->format_text($this->msg['cms_module_graph_view_spacetree_node_color']) .
                "</label>
            </div>
            <div class='colonne-suite'>
                <input required
                    type='color'
                    id='node_color'
                    name='". $this->get_form_value_name('node_color') ."'
                    class='saisie-20em'
                    value='". $nodeColor ."' />
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='node_color_selected'>"
                 . $this->format_text($this->msg['cms_module_graph_view_spacetree_node_color_selected']) .
                "</label>
            </div>
            <div class='colonne-suite'>
                <input required
                    type='color'
                    id='node_color_selected'
                    name='". $this->get_form_value_name('node_color_selected') ."'
                    class='saisie-20em'
                    value='". $nodeColorSelected ."' />
            </div>
        </div>

        <hr>
        <div class='row'>
            <div class='colonne3'>
                <label for='label_size'>"
                 . $this->format_text($this->msg['cms_module_graph_view_spacetree_label_size']) .
                "</label>
            </div>
            <div class='colonne-suite'>
                <input min='1' required
                    type='number'
                    id='label_size'
                    name='". $this->get_form_value_name('label_size') ."'
                    class='saisie-20em'
                    value='". intval($labelSize) ."' />
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='label_style'>"
                 . $this->format_text($this->msg['cms_module_graph_view_spacetree_label_style']) .
                "</label>
            </div>
            <div class='colonne-suite'>
                <select
                    id='label_style' required
                    name='". $this->get_form_value_name('label_style') ."'
                    class='saisie-20em'>
                    {$labelStyleOptions}
                </select>
            </div>
        </div>
        <div class='row'>
            <div class='colonne3'>
                <label for='label_color'>"
                 . $this->format_text($this->msg['cms_module_graph_view_spacetree_label_color']) .
                "</label>
            </div>
            <div class='colonne-suite'>
                <input required
                    type='color'
                    id='label_color'
                    name='". $this->get_form_value_name('label_color') ."'
                    class='saisie-20em'
                    value='". $labelColor ."' />
            </div>
        </div>
        <hr>
        <div class='row'>
            <div class='colonne3'>
                <label for='tooltip_template_folder'>"
                 . $this->format_text($this->msg['cms_module_graph_view_spacetree_tooltip_template_folder']) .
                "</label>
            </div>
            <div class='colonne-suite'>
                <select class='saisie-20em'
                    id='tooltip_template_folder'
                    name='". $this->get_form_value_name('tooltip_template_folder') ."'
                    required>
                    ". $this->get_directories_options($tooltipTemplateFolder) ."
                </select>
            </div>
        </div>
        ";

        return $form;
    }

    /**
     * Permet d'enregistrer le parametrage du formulaire
     *
     * @return boolean
     */
    public function save_form()
    {
        $this->parameters = [];

        $tooltipTemplateFolder = $this->get_value_from_form("tooltip_template_folder") ?? "common";
        $this->parameters["tooltip_template_folder"] = $tooltipTemplateFolder;

        foreach (static::DEFAULT_VALUE as $parameter => $defaultValue) {
            $value = $this->get_value_from_form($parameter) ?? $defaultValue;

            switch ($parameter) {
                case 'distance':
                case 'node_width':
                case 'node_height':
                case 'label_size':
                case 'levels_to_show':
                    $this->parameters[$parameter] = intval($value);
                    break;
                default:
                    $this->parameters[$parameter] = $value;
                    break;
            }
        }

        return parent::save_form();
    }

    /**
     * Permet de faire le rendu cote OPAC
     *
     * @param array $data
     * @return string
     */
    public function render($data)
    {
        if (!$data) {
            return "";
        }
        $tree = $data["tree"] ?? [];
        $template_path = false;
        if ('none' != $tree['data']['entity_type']) {
            $template_path = $this->find_template($tree['data']['entity_type'], $this->parameters["tooltip_template_folder"]);
        }
        if ($template_path) {
            $tree = $this->computeTooltip($tree, $template_path);
        }

        $json = json_encode($tree) ?? "{}";

        $this->parameters['api'] = "./ajax.php?module=cms&categ=module&elem=cms_module_graph&id={$this->cadre_parent}&action=ajax";
        $cmsOption = json_encode($this->parameters) ?? "{}";

        return "
            <div id='{$this->get_form_value_name('infovis')}' class='infovis-graph'></div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const spacetree = SpaceTree.makeInstance('{$this->get_form_value_name('infovis')}');
                    spacetree.setCMSOption({$cmsOption});
                    spacetree.load({$json});
                    spacetree.render(\"{$data['current']}\");
                });
            </script>
        ";
    }

    /**
     * Permet d'ajouter des meta dans la page en OAPC
     *
     * @param array $data
     * @return array
     */
    public function get_headers($data = [])
    {
        global $opac_url_base;

        return [
            "add" => [
                '<script src="'. $opac_url_base .'includes/javascript/jit/SpaceTree.js"></script>',
            ],
        ];
    }

    /**
     * Retourne la liste des dossiers
     *
     * @param string $selected
     * @return string
     */
    protected function get_directories_options(string $selected = "common")
    {
        $tpl = "";
        $dirs = glob('./opac_css/includes/templates/cms/modules/graph/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $dirname = basename($dir);
            $selected = $dirname == $selected ? "selected='selected'" : "";

            if ($dirname != "CVS") {
                $tpl .= "<option value='{$dirname}' {$selected}>{$dirname}</option>";
            }
        }
        return $tpl;
    }

    protected function computeTooltip($node, $template_path)
    {
        if (!empty($node['data']['entity']) && !is_a($node['data']['entity'], StdClass::class)) {
            $h2o = H2o_collection::get_instance($template_path);
            $h2o->set('authority', $node['data']['entity']);
            $node['data']['tooltip'] = $h2o->render([]);
        }

        if (!empty($node['children'])) {
            foreach ($node['children'] as $key => $child) {
                $node['children'][$key] = $this->computeTooltip($child, $template_path);
            }
        }

        return $node;
    }

    protected function get_file_name_from_type($type_object) {
        $filename = "";

        switch ($type_object) {
            case TYPE_AUTHOR :
                $filename = 'author';
                break;
            case TYPE_CATEGORY :
                $filename = 'category';
                break;
            case TYPE_PUBLISHER :
                $filename = 'publisher';
                break;
            case TYPE_COLLECTION :
                $filename = 'collection';
                break;
            case TYPE_SUBCOLLECTION :
                $filename = 'subcollection';
                break;
            case TYPE_SERIE :
                $filename = 'serie';
                break;
            case TYPE_TITRE_UNIFORME :
                $filename = 'titre_uniforme';
                break;
            case TYPE_INDEXINT :
                $filename = 'indexint';
                break;
            case TYPE_CONCEPT :
                $filename = 'concept';
                break;
            case TYPE_AUTHPERSO :
                $filename = 'authperso';
                break;
            case TYPE_RECORD :
                $filename = 'record';
                break;

            default:
                break;
        }

        return $filename;
    }

    protected function find_template($entity_type, $template_folder = "common")
    {
        global $include_path;

        $template_path = $include_path.'/templates/cms/modules/graph/'.$template_folder."/";

        $filename = $this->get_file_name_from_type($entity_type);
        $template = $filename.'.html';
        $subst = $filename.'_subst.html';

        if (file_exists($template_path.$subst)) {
            return $template_path.$subst;
        }
        if (file_exists($template_path.$template)) {
            return $template_path.$template;
        }

        if ($template_folder != "common") {
            return $this->find_template($entity_type);
        }
        return false;
    }
}
