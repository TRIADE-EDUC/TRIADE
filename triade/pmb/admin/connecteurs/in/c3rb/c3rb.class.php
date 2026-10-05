<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: c3rb.class.php,v 1.2.4.4 2025/05/12 15:05:27 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path,$base_path;
require_once $base_path."/admin/connecteurs/in/oai/oai.class.php" ;
require_once $class_path."/sessions_tokens.class.php";

class c3rb extends oai {

    protected const XSLT_PATH = __DIR__.'/xslt';

    /**
     * tokens sha256 deja calcules
     * @var array
     */
    static protected $tokens = [];

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
        return "c3rb";
    }

    /**
     *
     * {@inheritdoc}
     * @see connector::get_messages($connector_path)
     */
    public function get_messages($connector_path)
    {
        global $lang;

        $oai_file_name = '';
        if (file_exists($connector_path . "/../oai/messages/" . $lang . ".xml")) {
            $oai_file_name = $connector_path . "/../oai/messages/" . $lang . ".xml";
        } else if (file_exists($connector_path . "/../oai/messages/fr_FR.xml")) {
            $oai_file_name = $connector_path . "/../oai/messages/fr_FR.xml";
        }

        $file_name = '';
        if (file_exists($connector_path . "/messages/" . $lang . ".xml")) {
            $file_name = $connector_path . "/messages/" . $lang . ".xml";
        } else if (file_exists($connector_path . "/messages/fr_FR.xml")) {
            $file_name = $connector_path . "/messages/fr_FR.xml";
        }

        if ($oai_file_name) {
            $xmllist = new XMLlist($oai_file_name);
            $xmllist->analyser();
            $this->msg = $xmllist->table;
        }
        if ($file_name) {
            $xmllist = new XMLlist($file_name);
            $xmllist->analyser();
            $this->msg += $xmllist->table;
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see oai::source_get_property_form()
     */
    public function source_get_property_form($source_id)
    {
        //Recuperation formulaire generique OAI
        $form = parent::source_get_property_form($source_id);

        //Recuperation source authentification c3rb
        $c3rb_authentication_source_id = 0;
        $params = empty($this->params) ? $this->unserialize_source_params($source_id) : $this->params;
        if (!empty($params['PARAMETERS']['c3rb_authentication_source_id'])) {
            $c3rb_authentication_source_id = $params['PARAMETERS']['c3rb_authentication_source_id'];
        }

       //Selecteur source authentification c3rb
        $authentication_sources_selector = $this->getAuthenticationSourcesSelector($c3rb_authentication_source_id);
        if($authentication_sources_selector == '') {
            $form.= "
                <div class='row'>&nbsp;</div>
                <div class='row'>
                    <h3 >".$this->msg['c3rb_authentication_source_error']."</h3>
                </div>
                <div class='row'></div>";
        } else {
            $form.= "
                <div class='row'>
                    <div class='colonne3'>
                        <label for='clean_html'>".$this->msg['c3rb_authentication_source']."</label>
                    </div>
                    <div class='colonne_suite'>".$authentication_sources_selector."</div>
                </div>
                <div class='row'></div>";
        }
        return $form;
    }

    /**
     * Generation selecteur html pour choix source d'authentification C3rb
     *
     * @param int $c3rb_authentication_source_id
     *
     * @return string
     */
    protected function getAuthenticationSourcesSelector(int $c3rb_authentication_source_id)
    {
        global $charset;

        $sources = $this->getAuthenticationSources();
        if(empty($sources)) {
            return '';
        }
        $selector = "<select id='c3rb_authentication_source_id' name='c3rb_authentication_source_id'>";
        foreach($sources as $v) {
            $selector.= "<option value='".$v['id']."' ";
            if($c3rb_authentication_source_id == $v['id']) {
                $selector.= "selected ";
            }
            $selector.=">";
            $selector.= htmlentities($v['name'], ENT_QUOTES, $charset);
            $selector.= "</option>";
        }
        $selector.= "</select>";
        return $selector;
    }

    /**
     * Recuperation des sources d'authentification C3rb
     *
     * @return [
     *  'id' => 'identifiant source'
     *  'name' => 'nom source'
     *  ]
     */
    protected function getAuthenticationSources()
    {
        $connectors_out_list = new connecteurs_out();
        $sources = [];

        foreach($connectors_out_list->connectors as $connector_out) {
            if($connector_out->path == 'c3rb_out') {
                foreach($connector_out->sources as $source) {
                    $sources[] = [
                        'id'    => $source->id,
                        'name'  => $source->name,
                    ];
                }
            }
        }
        return $sources;
    }


    /**
     *
     * {@inheritDoc}
     * @see oai::make_serialized_source_properties()
     */
    public function make_serialized_source_properties($source_id)
    {
        //donnees generiques OAI
        parent::make_serialized_source_properties($source_id);
        $params = unserialize($this->sources[$source_id]["PARAMETERS"]);

        //donnees specifiques C3RB
        global $c3rb_authentication_source_id;
        if(empty($c3rb_authentication_source_id) || !is_numeric($c3rb_authentication_source_id)) {
            $c3rb_authentication_source_id = 0;
        }
        $params['c3rb_authentication_source_id'] = $c3rb_authentication_source_id;

        $this->sources[$source_id]["PARAMETERS"] = serialize($params);
    }

}