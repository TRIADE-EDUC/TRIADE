<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mirabel.class.php,v 1.5.4.1 2025/04/16 12:16:50 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;

require_once $class_path . "/connecteurs.class.php";
require_once __DIR__ . "/mirabel_client.class.php";
require_once $include_path . "/isbn.inc.php";

class mirabel extends connector {

    protected $mirabel_api_url = '';

    protected $mirabel_api_key = '';

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
        return "mirabel";
    }

    protected function unserialize_source_params($source_id)
    {
        $params = parent::unserialize_source_params($source_id);
        if (! empty($params['PARAMETERS']['mirabel_api_url'])) {
            $this->mirabel_api_url = $params['PARAMETERS']['mirabel_api_url'];
        }
        if (! empty($params['PARAMETERS']['mirabel_api_key'])) {
            $this->mirabel_api_key = $params['PARAMETERS']['mirabel_api_key'];
        }
        return $params;
    }

    public function make_serialized_source_properties($source_id)
    {
        global $mirabel_api_url, $mirabel_api_key;

        if (empty($mirabel_api_url)) {
            $mirabel_api_url = '';
        }
        if (empty($mirabel_api_key)) {
            $mirabel_api_key = '';
        }

        $this->sources[$source_id]['PARAMETERS'] = serialize([
            'mirabel_api_url' => stripslashes($mirabel_api_url),
            'mirabel_api_key' => stripslashes($mirabel_api_key)
        ]);
    }

    public function source_get_property_form($source_id)
    {
        global $charset;

        $this->unserialize_source_params($source_id);

        if (! $this->mirabel_api_url) {
            $this->mirabel_api_url = mirabel_client::API_URL_DEFAULT;
        }

        $form = "
            <h3>" . $this->msg['mirabel_ws'] . "</h3>
            <div class='row'>&nbsp;</div>
            <div class='row'>
                <div class='colonne3'>
                    <label for='mirabel_api_url'>" . $this->msg["mirabel_api_url"] . "</label>
                </div>
                <div class='colonne_suite'>
                    <input type='text' name='mirabel_api_url' id='mirabel_api_url' class='saisie-80em' value='" . htmlentities($this->mirabel_api_url, ENT_QUOTES, $charset) . "' />
                </div>
            </div>
            <div class='row'>
                <div class='colonne3'>
                    <label for='mirabel_api_key' >" . $this->msg["mirabel_api_key"] . "</label>
                </div>
                <div class='colonne_suite'>
                    <input type='text' name='mirabel_api_key' id='mirabel_api_key' class='saisie-30em' autocomplete='off' value='" . htmlentities($this->mirabel_api_key, ENT_QUOTES, $charset) . "' />
                </div>
            </div>";

        return $form;
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::enrichment_is_allow()
     */
    public function enrichment_is_allow()
    {
        return connector::ENRICHMENT_YES;
    }

    public function getTypeOfEnrichment($source_id)
    {
        $type = [];
        $type['type'] = array(
            array(
                'code' => "mirabel_access_list",
                'label' => $this->msg["mirabel_access_list"]
            )
        );
        $type['source_id'] = $source_id;
        return $type;
    }

    public function getEnrichmentHeader()
    {
        $header = array();
        $header[] = "<!-- Script d'enrichissement pour Mir@bel-->";
        $header[] = "<script>
            function load_mirabel_access_list(notice_id, type, label){
                let	httpr = new http_request();
                let content = document.getElementById('div_'+type+notice_id);
                content.innerHTML = '';
                let patience= document.createElement('img');
                patience.setAttribute('src','" . get_url_icon('patience.gif') . "');
                patience.setAttribute('align','middle');
                patience.setAttribute('id','patience'+notice_id);
                content.appendChild(patience);
                httpr.request('./ajax.php?module=ajax&categ=enrichment&action=enrichment&type='+type+'&id='+notice_id,true,'&enrich_params[label]='+label,true,gotEnrichment);
            }
        </script>";
        return $header;
    }

    public function getEnrichment($notice_id, $source_id, $type = "", $enrich_params = array())
    {
        global $charset;
        $notice_id = intval($notice_id);
        $enrichment = [];
        $enrichment['mirabel_access_list']['content'] = '';
        $enrichment['source_label'] = '';
        $code = '';
        if (! $notice_id) {
            return $enrichment;
        }
        $query = "select code from notices where notice_id=$notice_id and niveau_biblio='s' ";
        $result = pmb_mysql_query($query);
        if (! pmb_mysql_num_rows($result)) {
            return $enrichment;
        }
        $code = trim(pmb_mysql_result($result, 0, 0));
        if ('' == $code) {
            return $enrichment;
        }
        if (! isISSN($code)) {
            return $enrichment;
        }
        $this->unserialize_source_params($source_id);
        if (! $this->mirabel_api_url) {
            $this->mirabel_api_url = mirabel_client::API_URL_DEFAULT;
        }
        $c = new mirabel_client($this->mirabel_api_key, $this->mirabel_api_url);
        $r = $c->getAccesRevue($code);
        if (! $r) {
            return $enrichment;
        }
        $formatted_content = $this->formatAccesRevueContent($c->getResult());

        $enrichment['mirabel_access_list']['content'] = $formatted_content;
        $enrichment['source_label'] = htmlentities($this->msg['mirabel_access_list'], ENT_QUOTES, $charset);
        return $enrichment;
    }

    /**
     * Formatage du contenu des acces aux revues
     *
     * @param array $content
     * @return string
     */
    protected function formatAccesRevueContent(array $content = [])
    {
        global $charset;
        $formatted_content = '';
        if (empty($content)) {
            return $formatted_content;
        }
        $formatted_content = '<table id="mirabel" ><thead><tr>
            <th>' . htmlentities($this->msg['mirabel_content'], ENT_QUOTES, $charset) . '</th>
            <th>' . htmlentities($this->msg['mirabel_resource'], ENT_QUOTES, $charset) . '</th>
            <th>' . htmlentities($this->msg['mirabel_diffusion'], ENT_QUOTES, $charset) . '</th>
            <th>' . htmlentities($this->msg['mirabel_numbers'], ENT_QUOTES, $charset) . '</th>
        </tr></thead><tbody>';
        for ($i = 0; $i < count($content); $i ++) {
            $contenu = ! empty($content[$i]['contenu']) ? htmlentities($content[$i]['contenu'], ENT_QUOTES, $charset) : '';
            $ressource = ! empty($content[$i]['ressource']) ? htmlentities($content[$i]['ressource'], ENT_QUOTES, $charset) : '';
            $contenu_aria_labelledby = $this->msg['mirabel_content_aria-labelledby'];
            $url = ! empty($content[$i]['url']) ? $content[$i]['url'] : '';
            $diffusion = ! empty($content[$i]['diffusion']) ? htmlentities($content[$i]['diffusion'], ENT_QUOTES, $charset) : '';
            $numeros = '';
            $datedebut = ! empty($content[$i]['datedebut']) ? htmlentities($content[$i]['datedebut'], ENT_QUOTES, $charset) : '';
            $datefin = ! empty($content[$i]['datefin']) ? htmlentities($content[$i]['datefin'], ENT_QUOTES, $charset) : '';
            $numerodebut = ! empty($content[$i]['numerodebut']) ? htmlentities($content[$i]['numerodebut'], ENT_QUOTES, $charset) : '';
            $numerofin = ! empty($content[$i]['numerofin']) ? htmlentities($content[$i]['numerofin'], ENT_QUOTES, $charset) : '';
            $numeros = $datedebut;
            if ($numerodebut) {
                $numeros .= ' (' . $numerodebut . ')';
            }
            if ($numeros && $datefin) {
                $numeros .= ' - ';
            }
            if ($datefin) {
                $numeros .= $datefin;
            }
            if ($numerofin) {
                $numeros .= ' (' . $numerofin . ')';
            }
            $formatted_content .= '<tr>';
            $formatted_content .= '<td><a href="' . $url . '" aria-labelledby="' . $contenu_aria_labelledby . '"  target="_blank_">' . $contenu . '</a></td>';
            $formatted_content .= '<td>' . $ressource . '</td>';
            $formatted_content .= '<td>' . $diffusion . '</td>';
            $formatted_content .= '<td>' . $numeros . '</td>';
            $formatted_content .= '</tr>';
        }
        $formatted_content .= '</tbody></table>';
        return $formatted_content;
    }

}
