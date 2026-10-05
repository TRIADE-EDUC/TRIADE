<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: databnf.class.php,v 1.12.4.2 2025/04/16 12:16:50 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once "$class_path/connecteurs.class.php";

class databnf extends connector {

    //Variables internes pour la progression de la récupération des notices
    public $del_old;				//Supression ou non des notices dejà existantes

    public $profile;				//Profil wikipedia
    public $match;					//Tableau des critères wikipedia
    public $current_site;			//Site courant du profile (n°)
    public $searchindexes;			//Liste des indexes de recherche possibles pour le site
    public $current_searchindex;	//Numéro de l'index de recherche de la classe
    public $match_index;			//Type de recherche (power ou simple)
    public $types;					//Types de documents pour la conversino des notices

    //Résultat de la synchro
    public $error;					//Y-a-t-il eu une erreur
    public $error_message;			//Si oui, message correspondant

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
        return "databnf";
    }

    public function source_get_property_form($source_id) {
        global $charset, $sparql_endpoint_url;
        $params=$this->get_source_params($source_id);
        if ($params["PARAMETERS"]) {
            //Affichage du formulaire avec $params["PARAMETERS"]
            $vars=unserialize($params["PARAMETERS"]);
            foreach ($vars as $key=>$val) {
                global ${$key};
                ${$key}=$val;
            }
        }
        $form="
        <div class='row'>
            <div class='colonne3'>
                    <label for='sparql_endpoint_url'>".$this->msg["databnf_sparql_endpoint_url"]."</label>
            </div>
            <div class='colonne_suite'>
                <input type='text' class='saisie-40em' name='sparql_endpoint_url' id='sparql_endpoint_url' value='".htmlentities($sparql_endpoint_url ?? "", ENT_QUOTES, $charset)."' size='10'/>
            </div>
        </div>
        <div class='row'></div>";
        return $form;
    }

    public function make_serialized_source_properties($source_id) {
        global $sparql_endpoint_url;
        $t["sparql_endpoint_url"]=$sparql_endpoint_url;
        $this->sources[$source_id]["PARAMETERS"]=serialize($t);
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

    public function getEnrichmentHeader(){
        global $lang;
        $header= array();
        return $header;
    }

    public function getTypeOfEnrichment($source_id){
        $type['type'] = array(
            array(
                'code' => "databnf_oeuvre",
                'label' => $this->msg["databnf_oeuvre_label"]
            ),
            array(
                'code' => "databnf_bio",
                'label' => $this->msg["databnf_bio_label"]
            )
        );
        $type['source_id'] = $source_id;
        return $type;
    }

    public function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array()){
        $enrichment= array();
        $params=$this->unserialize_source_params($source_id);
        $sparql_end_point=$params["PARAMETERS"]["sparql_endpoint_url"];
        //on renvoi ce qui est demandé... si on demande rien, on renvoi tout..
        switch ($type){
            case "databnf_bio" :
                $enrichment['databnf_bio']['content'] = $this->get_author_page($notice_id,$sparql_end_point);
                break;
            case "databnf_oeuvre" :
            default :
                $enrichment['databnf_oeuvre']['content'] = $this->noticeInfos($notice_id,$sparql_end_point);
                break;
        }
        $enrichment['source_label']=$this->msg['databnf_enrichment_source'];
        return $enrichment;
    }

    public function get_author_page($notice_id,$sparql_end_point) {
        //on va chercher l'auteur principal...
        $query = "select responsability_author, authority_number from responsability join authorities_sources on (authority_type='author' and num_authority=responsability_author) where responsability_notice =".$notice_id." and responsability_type=0";
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)){
            $author_id = pmb_mysql_result($result,0,0);
            $author_number = pmb_mysql_result($result,0,1);
            $author_class = new auteur($author_id);
            $author =  $author_class->get_isbd();

            //On y va !
            $config = array(
                    'remote_store_endpoint' => $sparql_end_point,
                    'remote_store_timeout' => 10
            );
            $store = ARC2::getRemoteStore($config);

            //Recherche de l'URI de l'auteur !
            $sparql="prefix skos: <http://www.w3.org/2004/02/skos/core#>
            SELECT * WHERE {
                ?auteur rdf:type skos:Concept .
                FILTER regex(?auteur, \"^http://data\.bnf\.fr:8080/ark:/12148/cb".$author_number."\") .
            }";

            $rows=$store->query($sparql,'rows');

            if ($rows[0]["auteur"]) {
                $uri_auteur=$rows[0]["auteur"];

                //Biographie...
                $sparql="prefix foaf: <http://xmlns.com/foaf/0.1/>
                prefix dc: <http://purl.org/dc/terms/>
                prefix dcterm: <http://purl.org/dc/terms/>
                prefix bnf-onto: <http://data.bnf.fr/ontology/bnf-onto/>
                prefix rdagroup2elements: <http://RDVocab.info/ElementsGr2/>
                prefix skos: <http://www.w3.org/2004/02/skos/core#>
                SELECT * WHERE {
                    <$uri_auteur> foaf:focus ?person .
                    <$uri_auteur> skos:prefLabel ?isbd .
                    ?person foaf:page ?page .
                    OPTIONAL {
                        ?person rdagroup2elements:biographicalInformation ?biographie .
                    }
                    OPTIONAL {
                        ?person rdagroup2elements:dateOfBirth ?naissance .
                    }
                    OPTIONAL {
                        ?person rdagroup2elements:placeOfBirth ?lieunaissance .
                    }
                    OPTIONAL {
                        ?person rdagroup2elements:dateOfDeath ?mort .
                    }
                    OPTIONAL {
                        ?person rdagroup2elements:placeOfDeath ?lieumort .
                    }
                }";
                try {
                    $rows=$store->query($sparql,'rows');
                } catch(Exception $e) {
                    $rows=array();
                }
                $rows=encoding_normalize::utf8_decode($rows);
                $template="{% for record in result %}
                            <h3>{{record.isbd}}<div style='float:right'><a href='{{record.page}}' target='_blank'><img src='http://data.bnf.fr/data/85bec01e1d53356985bccf3036a2bb49/logo-data.gif' style='max-height:20px'/></a></div></h3>
                            <br />
                            <h3>Biographie (BNF)</h3>
                            <table>
                            <tr><td style='background:#EEEEEE'>Date de naissance</td><td>{{record.naissance}}</td></tr>
                            <tr><td style='background:#EEEEEE'>Lieu de naissance</td><td>{{record.lieunaissance}}</td></tr>
                            <tr><td style='background:#EEEEEE'>Date de décès</td><td>{{record.mort}}</td></tr>
                            <tr><td style='background:#EEEEEE'>Lieu de décès</td><td>{{record.lieumort}}</td></tr>
                            </table>
                            <br/>
                            <h4 style='font-size:1.2em'>{{record.biographie}}</h4>
                            <br/>
                            {% endfor %}";
                $html_to_return = H2o::parseString($template)->render(array("result"=>$rows));

                //Vignettes
                $sparql="prefix foaf: <http://xmlns.com/foaf/0.1/>
                        prefix dc: <http://purl.org/dc/elements/1.1/>
                        prefix dcterm: <http://purl.org/dc/terms/>
                        SELECT * WHERE {
                            <$uri_auteur> foaf:focus ?person .
                            ?person foaf:depiction ?url .
                        } LIMIT 5";
                try {
                    $rows=$store->query($sparql,'rows');
                } catch(Exception $e) {
                    $rows=array();
                }
                $rows=encoding_normalize::utf8_decode($rows);
                $template="<h3>Vignettes (BnF)</h3>
                            <table width='100%'>
                            <tr>
                            {% for record in result %}
                                <td class='center'><img src='{{record.url}}'/></td>
                            {% endfor %}
                            </tr>
                            </table>";
                $html_to_return .= H2o::parseString($template)->render(array("result"=>$rows));

                //Bibliographie
                $sparql="prefix foaf: <http://xmlns.com/foaf/0.1/>
                    prefix dc: <http://purl.org/dc/terms/>
                    prefix dcterm: <http://purl.org/dc/terms/>
                    prefix frbr-rda: <http://purl.org/vocab/frbr/core#>
                    prefix rdarelationships: <http://rdvocab.info/RDARelationshipsWEMI/>

                    SELECT ?oeuvre ?oeuvre_concept ?date ?title ?url ?gallica WHERE {
                        <$uri_auteur> foaf:focus ?person .
                        ?oeuvre dc:creator ?person .
                        ?oeuvre_concept foaf:focus ?oeuvre .
                        OPTIONAL {
                            ?oeuvre dc:date ?date
                        } .
                        ?oeuvre dc:title ?title .
                        OPTIONAL { ?oeuvre foaf:depiction ?url } .
                        OPTIONAL {
                            ?manifestation rdarelationships:workManifested ?oeuvre .
                            ?manifestation rdarelationships:electronicReproduction ?gallica .
                        } .
                    }  group by ?oeuvre order by ?date";
                try {
                    $rows=$store->query($sparql,'rows');
                } catch(Exception $e) {
                    $rows=array();
                }
                $rows=encoding_normalize::utf8_decode($rows);
                $template="<h3>Bibliographie (BNF)</h3>
                    <div class='center'>
                    <div style='overflow-x:scroll;overflow-y:auto;width:850px;'>
                    <table>
                        <tr>
                    {% for record in result %}
                        <td style='background:#DDDDDD;' width='200px'>{% if record.date %}{{record.date}}{% else %}&nbsp;{% endif %}</td>
                    {% endfor %}
                        </tr>
                        <tr>
                    {% for record in result %}
                        <td {% if loop.odd %}style='background:#EEEEEE;'{% endif %}>
                        {% if loop.odd %}
                            {% if record.url %}
                        <a href='index.php?uri={{record.oeuvre_concept}}%23frbr:Work&lvl=cmspage&pageid=13'><img src='{{record.url}}' style='max-height:50px'/></a>
                            {% else %}
                        &nbsp;
                            {% endif %}
                            {% if record.gallica %}
                            <a href='{{record.gallica}}' target='_blank'><img width='50px' src='http://gallica.bnf.fr/images/dynamic/perso/logo_gallica.png' /></a>
                            {% endif %}
                            <br />
                            <a href='index.php?uri={{record.oeuvre_concept}}%23frbr:Work&lvl=cmspage&pageid=13'>{{record.title}}</a>
                        {% else %}&nbsp;{% endif %}
                        </td>
                    {% endfor %}
                        </tr>
                        <tr>
                    {% for record in result %}
                        <td {% if loop.even %}style='background:#EEEEEE;'{% endif %}>{% if loop.even %}{% if record.url %}
                        <a href='index.php?uri={{record.oeuvre_concept}}%23frbr:Work&lvl=cmspage&pageid=13'><img src='{{record.url}}' style='max-height:50px'/></a>
                            {% else %}
                        &nbsp;
                            {% endif %}
                            {% if record.gallica %}
                            <a href='{{record.gallica}}' target='_blank'><img width='50px' src='http://gallica.bnf.fr/images/dynamic/perso/logo_gallica.png' /></a>
                            {% endif %}
                            <br />
                            <a href='index.php?uri={{record.oeuvre_concept}}%23frbr:Work&lvl=cmspage&pageid=13'>{{record.title}}</a>{% else %}&nbsp;{% endif %}
                        </td>
                    {% endfor %}
                        </tr>
                    </table>
                    </div>
                    </div>";
                $html_to_return .= H2o::parseString($template)->render(array("result"=>$rows));
            }
        }
//		print $html_to_return;
        return $html_to_return;
    }

    public function noticeInfos($notice_id,$sparql_end_point){
        global $lang,$charset;

        //On va rechercher l'isbn si il existe....
        $requete="select code from notices where notice_id=$notice_id";
        $resultat=pmb_mysql_query($requete);

        if (pmb_mysql_num_rows($resultat)) {
            $isbn=pmb_mysql_result($resultat,0,0);
        } else $isbn="";
        if ($isbn) {
            //On y va !
            $config = array(
                    'remote_store_endpoint' => $sparql_end_point,
                    'remote_store_timeout' => 10
            );
            $store = ARC2::getRemoteStore($config);

            $sparql="prefix bnf-onto: <http://data.bnf.fr/ontology/>
                prefix rdarelationships: <http://rdvocab.info/RDARelationshipsWEMI/>
                SELECT ?oeuvre WHERE {
                  ?manifestation bnf-onto:ISBN '$isbn' .
                  ?manifestation rdarelationships:workManifested ?oeuvre
                }";
            try {
                $rows=$store->query($sparql,'rows');
            } catch(Exception $e) {
                $rows=array();
            }
            if ($rows[0]["oeuvre"]) {
                $oeuvre=$rows[0]["oeuvre"];
                $sparql="prefix skos: <http://www.w3.org/2004/02/skos/core#>
                    prefix foaf: <http://xmlns.com/foaf/0.1/>
                    prefix dc: <http://purl.org/dc/terms/>
                    prefix bnf-onto: <http://data.bnf.fr/ontology/>
                    prefix rdarelationships: <http://rdvocab.info/RDARelationshipsWEMI/>
                    prefix rdagroup1Elements: <http://RDVocab.info/Elements/>
                    SELECT * WHERE {
                      <$oeuvre> rdfs:label ?titre .
                      OPTIONAL { <$oeuvre> dc:date ?date } .
                      OPTIONAL { <$oeuvre> foaf:depiction ?vignette } .
                      OPTIONAL { <$oeuvre> dc:description ?description } .
                      OPTIONAL { <$oeuvre> bnf-onto:subject ?sujet } .
                      OPTIONAL { <$oeuvre> dc:creator ?auteur .
                         ?auteur_concept foaf:focus ?auteur .
                         ?auteur_concept skos:prefLabel ?auteur_isbd .
                      } .
                      OPTIONAL { <$oeuvre> rdagroup1Elements:placeOfOriginOfTheWork ?lieu }
                    }";
                try {
                    $rows=$store->query($sparql,'rows');
                } catch(Exception $e) {
                    $rows=array();
                }
                $rows=encoding_normalize::utf8_decode($rows);
                $template="
                        <h3>{{result.0.titre}}<div style='float:right'><a href='$oeuvre' target='_blank'><img src='http://data.bnf.fr/data/85bec01e1d53356985bccf3036a2bb49/logo-data.gif' style='max-height:20px'/></a></div></h3>
                                    <br />
                                    <h3>Détail de l'oeuvre (BNF)</h3>
                        {% if result.0.vignette %}
                            <table>
                                <tr>
                                    <td><img src='{{result.0.vignette}}' height='150px'/></td>
                                    <td>
                        {% endif %}
                        <table>
                            <tr><td style='background:#EEEEEE'>Date</td><td>{{result.0.date}}</td></tr>
                            <tr><td style='background:#EEEEEE'>Sujet</td><td>{{result.0.sujet}}</td></tr>
                            <tr><td style='background:#EEEEEE'>Auteur</td><td><a href='index.php?uri={{result.0.auteur_concept}}&lvl=cmspage&pageid=12'>{{result.0.auteur_isbd}}</a></td></tr>
                        </table>
                        {% if result.0.vignette %}
                                </td>
                            </tr>
                          </table>
                        {% endif %}
                        <br/>
                        <h4>{{result.0.description}}</h4>
                ";
                $html_to_return = H2o::parseString($template)->render(array("result"=>$rows));

                //Récupération des exemplaires de Gallica
                $sparql="prefix skos: <http://www.w3.org/2004/02/skos/core#>
                    prefix foaf: <http://xmlns.com/foaf/0.1/>
                    prefix dc: <http://purl.org/dc/terms/>
                    prefix bnf-onto: <http://data.bnf.fr/ontology/>
                    prefix rdarelationships: <http://rdvocab.info/RDARelationshipsWEMI/>
                    prefix rdagroup1Elements: <http://RDVocab.info/Elements/>
                    SELECT * WHERE {
                        ?manifestation rdarelationships:workManifested <$oeuvre> .
                        OPTIONAL { ?manifestation rdarelationships:electronicReproduction ?gallica } .
                        OPTIONAL { ?manifestation bnf-onto:ISBN ?isbn } .
                        OPTIONAL { <$oeuvre> foaf:depiction ?vignette } .
                        OPTIONAL { ?manifestation dc:date ?date } .
                        OPTIONAL { ?manifestation rdagroup1Elements:publishersName ?publisher } .
                        OPTIONAL { ?manifestation rdagroup1Elements:note ?note } .
                        OPTIONAL { ?manifestation rdagroup1Elements:placeOfPublication ?place } .
                        OPTIONAL { ?manifestation rdagroup1Elements:dateOfCapture ?numerisele } .
                    } group by ?manifestation order by ?date
                ";
                try {
                    $rows=$store->query($sparql,'rows');
                } catch(Exception $e) {
                    $rows=array();
                }
                $rows=encoding_normalize::utf8_decode($rows);
                $template="
                        <h3>Editions numérisées dans Gallica</h3><br/>
                        <table>
                        {% for record in result %}
                            {% if record.gallica %}
                            <tr>
                                <td><a href='{{record.gallica}}' target='_blank'><img height='40px' src='http://gallica.bnf.fr/images/dynamic/perso/logo_gallica.png' /></a></td>
                                <td><a href='{{record.gallica}}' target='_blank'>Edition : {{record.date}} par {{record.publisher}} à {{record.place}}</a></td>
                                <td>{{record.note}}</td>
                                <td>{{record.numerisele}}</td>
                            </tr>
                            {% endif %}
                        {% endfor %}
                        </table>
                ";
                $html_to_return .= H2o::parseString($template)->render(array("result"=>$rows));
                $template="
                        <h3>Editions dans la bibliothèque</h3><br/>
                        <table>
                        {% for record in result %}
                            {% if record.isbn %}
                                {% sqlvalue i_catalog %}
                                    select count(expl_id) as nb,notice_id from exemplaires join notices on expl_notice=notice_id where code='{{record.isbn}}' group by notice_id
                                {% endsqlvalue %}
                                {% if i_catalog.0.nb %}
                                    <tr style='height:70px'>
                                        <td><a href='index.php?lvl=notice_display&id={{i_catalog.0.notice_id}}' target='_blank'>{% if record.vignette %}<img src='{{record.vignette}}' height='70px'/>{% else %}&nbsp;{% endif %}</a></td>
                                        <td><a href='index.php?lvl=notice_display&id={{i_catalog.0.notice_id}}' target='_blank'>Edition : {{record.date}} par {{record.publisher}} à {{record.place}}</a></td>
                                        <td>{{record.note}}</td>
                                        <td><a href='index.php?lvl=notice_display&id={{i_catalog.0.notice_id}}' target='_blank'>{{i_catalog.0.nb}} exemplaires disponible(s)</a></td>
                                    </tr>
                                {% endif %}
                            {% endif %}
                        {% endfor %}
                        </table>
                ";
                try {
                    $html_to_return .= H2o::parseString($template)->render(array("result"=>$rows));
                } catch (Exception $e) {
                    $html_to_return.=highlight_string(print_r($e,true),true);
                }
            }
        }
        return $html_to_return;
    }
}
?>