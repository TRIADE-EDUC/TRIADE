<?php

// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_editorial_data.class.php,v 1.11.2.1.2.1 2025/04/25 11:52:14 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

require_once($class_path."/cms/cms_root.class.php");
require_once($class_path."/cms/cms_logo.class.php");
require_once($class_path."/cms/cms_editorial_publications_states.class.php");
require_once($class_path."/cms/cms_editorial_parametres_perso.class.php");

require_once($class_path."/categories.class.php");
require_once($include_path."/templates/cms/cms_editorial.tpl.php");
require_once($class_path."/double_metaphone.class.php");
require_once($class_path."/stemming.class.php");
require_once($class_path."/cms/cms_collections.class.php");
require_once($class_path."/index_concept.class.php");
require_once($class_path."/cms/cms_concept.class.php");
require_once($class_path.'/audit.class.php');
require_once($class_path.'/indexation.class.php');

class cms_editorial_data extends cms_root
{
    public const TYPE_ARTICLE = "article";
    public const TYPE_SECTION = "section";

    protected $id;						// identifiant du contenu
    protected $num_parent;				// id du parent
    protected $title;					// le titre du contenu
    protected $resume;					// résumé du contenu
    protected $contenu;				// contenu
    protected $logo;					// objet gérant le logo
    protected $publication_state;		// statut de publication
    protected $start_date;				// date de début de publication
    protected $end_date;				// date de fin de publication
    protected $descriptors;			// descripteurs
    protected $type;				// le type de l'objet
    protected $num_type;				// id du type de contenu
    protected $type_content;		// libellé du type de contenu
    protected $fields_type;
    protected $opt_elements;		// les éléments optionnels constituants l'objet
    protected $create_date;			//
    protected $documents_linked;		//tableau des docs liés
    protected $last_update_date = "";		//date de dernière modification
    protected $documents;
    protected $concepts;
    protected $children;
    protected $parent;
    protected $link;
    protected $articles;
    protected $num_page; //Id de la page sur laquelle seras affiché l'élément (défini par le type en administration)
    protected $var_name; //Nom de la variable d'environnement utilisé sur la page pour afficher l'élément (défini par le type également)
    protected $links_patterns;

    /**
     * Nom du calendrier
     *
     * @var string
     */
    public $calendar = "";

    /**
     * Couleur du calendrier
     *
     * @var string
     */
    public $color = "";

    /**
     * Identifiant du Type d'evenement
     *
     * @var integer
     */
    public $id_type = 0;

    /**
     * Identifiant de l'evenement
     *
     * @var integer
     */
    public $id_event = 0;

    /**
     * Titre de l'evenement
     *
     * @var string
     */
    public $event_title = "";

    /**
     * Date de debut de l'evenement
     *
     * @var array
     */
    public $event_start = [];

    /**
     * Date de fin de l'evenement
     *
     * @var array
     */
    public $event_end = [];

    /**
     * Concepts associés
     * @var index_concept
     */
    protected $index_concept = null;

    public function __construct($id, $type, $links_patterns = [])
    {
        $this->type = $type;
        $id = intval($id);
        $this->links_patterns = $links_patterns;
        if($id) {
            $this->id = $id;
            $this->fetch_data();
        }
    }

    protected function fetch_data()
    {
        if(!$this->id || ($this->type != "article" && $this->type != "section")) {
            return false;
        }

        // les infos générales...
        if ($this->type == 'article') {
            $require_fields_name = 'article_contenu, num_section';
        } else {
            $require_fields_name = $this->type . '_num_parent';
        }

        $rqt = "
            SELECT
                " . $this->type . "_num_type,
                " . $this->type . "_title,
                " . $this->type . "_resume,
                " . $this->type . "_publication_state,
                " . $this->type . "_start_date,
                " . $this->type . "_end_date,
                " . $this->type . "_creation_date,
                " . $this->type . "_update_timestamp,
                " . $require_fields_name . "
            FROM cms_".$this->type."s
            WHERE id_".$this->type." ='".$this->id."'";
        $res = pmb_mysql_query($rqt);

        if (pmb_mysql_num_rows($res)) {
            $row = pmb_mysql_fetch_assoc($res);

            $this->num_type = $row[$this->type."_num_type"];
            $this->title = $row[$this->type."_title"];
            $this->resume = $row[$this->type."_resume"];
            $this->contenu = ($row[$this->type."_contenu"] ?? "");
            $this->publication_state = $row[$this->type."_publication_state"];
            $this->start_date = $row[$this->type."_start_date"];
            $this->end_date = $row[$this->type."_end_date"];
            $this->num_parent = ($row[$this->type."_num_parent"] ?? $row["num_section"]);
            $this->create_date = $row[$this->type."_creation_date"];
            $this->last_update_date = $row[$this->type."_update_timestamp"];

            if (!empty($this->links_patterns[$this->type])) {
                $this->link = str_replace('!!id!!', $this->id, $this->links_patterns[$this->type]);
            }

            unset($row);
            pmb_mysql_free_result($res);
        }

        if (strpos($this->start_date, "0000-00-00") !== false) {
            $this->start_date = "";
        }
        if (strpos($this->end_date, "0000-00-00") !== false) {
            $this->end_date = "";
        }
    }

    public function get_descriptors()
    {
        global $lang;
        if(!isset($this->descriptors)) {
            $this->descriptors = [];
            // les descripteurs...
            $rqt = "select num_noeud from cms_".$this->type."s_descriptors where num_".$this->type." = '".$this->id."' order by ".$this->type."_descriptor_order";
            $res = pmb_mysql_query($rqt);
            if(pmb_mysql_num_rows($res)) {
                while($row = pmb_mysql_fetch_object($res)) {
                    $descriptors = [];
                    $categ = new categories($row->num_noeud, $lang);
                    $descriptors["id"] = $categ->num_noeud;
                    $descriptors["lang"] = $categ->langue;
                    $descriptors["name"] = $categ->libelle_categorie;
                    $descriptors["comment"] = $categ->comment_public;
                    $this->descriptors[] = $descriptors;
                }
            }
        }
        return $this->descriptors;
    }

    public function get_fields_type()
    {
        if(!isset($this->fields_type)) {
            $this->fields_type = [];
            $query = "select id_editorial_type from cms_editorial_types where editorial_type_element = '".$this->type."_generic'";
            $result = pmb_mysql_query($query);
            $num_type = 0;
            if(pmb_mysql_num_rows($result)) {
                $num_type = pmb_mysql_result($result, 0, 0);//par defaut on selectionne le type generique
                $fields_type = new cms_editorial_parametres_perso($num_type);
                $this->fields_type = $fields_type->get_out_values($this->id);
            }
            if (!empty($this->num_type)) {
                $num_type = $this->num_type;// si besoin on selectionne le type specifique
            }
            if($num_type) {
                $query = "select editorial_type_label, editorial_type_permalink_num_page, editorial_type_permalink_var_name from cms_editorial_types where id_editorial_type = ".$num_type;
                $result = pmb_mysql_query($query);
                if(pmb_mysql_num_rows($result)) {
                    $row = pmb_mysql_fetch_object($result);
                    $this->num_page = $row->editorial_type_permalink_num_page;
                    $this->var_name = $row->editorial_type_permalink_var_name;
                    if(!$this->num_page || !$this->var_name) { //Récupération des éléments composants le permalien
                        $cms_editorial_types = new cms_editorial_types($this->type);
                        if($cms_editorial_types->get_generic_type()) {
                            $generic_type = $cms_editorial_types->get_generic_type();
                            if($generic_type['var_name'] && $generic_type['num_page']) {
                                $this->var_name = $generic_type['var_name'];
                                $this->num_page = $generic_type['num_page'];
                            }
                        }
                    }

                    $this->type_content = $row->editorial_type_label;
                    //on merge eventuellement avec les cp specifiques
                    if ($this->num_type) {
                        $fields_type = new cms_editorial_parametres_perso($this->num_type);
                        $this->fields_type = array_merge($this->fields_type, $fields_type->get_out_values($this->id));
                    }
                }
            }
        }
        return $this->fields_type;
    }

    public function get_documents()
    {
        if(!isset($this->documents)) {
            $documents_linked = [];
            $query = "select document_link_num_document from cms_documents_links join cms_documents on document_link_num_document = id_document where document_link_type_object = '".$this->type."' and document_link_num_object = ".$this->id." order by document_create_date desc";
            $result = pmb_mysql_query($query);
            if(pmb_mysql_num_rows($result)) {
                while($row = pmb_mysql_fetch_object($result)) {
                    $documents_linked[] = $row->document_link_num_document;
                }
            }
            foreach($documents_linked as $id_doc) {
                $document = new cms_document($id_doc);
                $this->documents[] = $document->format_datas();
            }
        }
        return $this->documents;
    }

    public function get_nb_documents()
    {
        if(!isset($this->documents)) {
            $this->get_documents();
        }
        return count($this->documents);
    }

    public function get_permalink()
    {
        //on appelle get_fields_type pour recuperer le num_page et var_name
        $this->get_fields_type();
        if($this->num_page && $this->var_name) { //Le type d'élément sur lequel on se trouve a une page et une variable d'environnement renseignés
            return "./index.php?lvl=cmspage&pageid=".$this->num_page."&".$this->var_name."=".$this->id;
        }
        return '';
    }

    public function get_num_page()
    {
        return $this->num_page;
    }

    public function get_var_name()
    {
        return $this->var_name;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function get_logo()
    {
        if (!isset($this->logo)) {
            $this->logo = new cms_logo($this->id, $this->type);
        }
        return $this->logo->format_datas();
    }

    public function get_start_date()
    {
        return format_date($this->start_date);
    }

    public function get_end_date()
    {
        return format_date($this->end_date);
    }

    public function get_create_date()
    {
        return format_date($this->create_date);
    }

    public function get_last_update_date()
    {
        return format_date($this->last_update_date);
    }

    public function get_last_update_sql_date()
    {
        return $this->last_update_date;
    }

    public function get_concepts()
    {
        if (isset($this->concepts)) {
            return $this->concepts;
        }
        $this->concepts = [];
        $type_constant = 0;
        switch ($this->type) {
            case 'section':
                $type_constant = TYPE_CMS_SECTION;
                break;
            case 'article':
                $type_constant = TYPE_CMS_ARTICLE;
                break;
        }
        $query = "
            SELECT num_concept, order_concept
            FROM index_concept
            WHERE num_object = ".$this->id." AND type_object = ".$type_constant."
            ORDER BY order_concept";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $this->concepts[] = authorities_collection::get_authority(AUT_TABLE_INDEX_CONCEPT, $row["num_concept"]);
            }
        }
        return $this->concepts;
    }

    public function get_type()
    {
        if (!isset($this->type_content)) {
            $this->get_fields_type();
        }
        return $this->type_content;
    }

    public function get_parent()
    {
        if (isset($this->parent)) {
            return $this->parent;
        }
        $parent = new cms_section($this->num_parent);
        $this->parent = $parent->format_datas($this->links_patterns);
        return $this->parent;
    }

    public function get_children()
    {
        if (isset($this->children)) {
            return $this->children;
        }
        $this->children = [];
        if ($this->type == "section") {
            if($this->id) {
                $query = "
                    SELECT id_section
                    FROM cms_sections
                    JOIN cms_editorial_publications_states ON section_publication_state=id_publication_state
                    WHERE section_num_parent = ".$this->id."
                    AND ((section_start_date != 0 AND to_days(section_start_date)<=to_days(now()) AND to_days(section_end_date)>=to_days(now()))
                    OR (section_start_date != 0 AND section_end_date =0 AND to_days(section_start_date)<=to_days(now()))
                    OR (section_start_date = 0 AND to_days(section_end_date)>=to_days(now()))
                    OR (section_start_date = 0 AND section_end_date = 0))
                    AND (editorial_publication_state_opac_show=1".(!$_SESSION['id_empr_session'] ? " AND editorial_publication_state_auth_opac_show = 0" : "").")
                    ORDER BY section_order";
                $result = pmb_mysql_query($query);
                if(pmb_mysql_num_rows($result)) {
                    while ($row = pmb_mysql_fetch_object($result)) {
                        $child = new cms_section($row->id_section);
                        $this->children[] = $child->format_datas($this->links_patterns);
                    }
                }
            }
        }
        return $this->children;
    }

    public function get_social_media_sharing()
    {
        global $opac_url_base;
        return "
			<div id='el".$this->type.$this->id."addthis' class='addthis_toolbox addthis_default_style '
				addthis:url='".$opac_url_base.$this->get_permalink()."'>
			</div>
			<script type='text/javascript'>
				if(param_social_network){
					creeAddthis('el".$this->type.$this->id."');
				}else{
					waitingAddthisLoaded('el".$this->type.$this->id."');
				}
			</script>";
    }

    public function get_articles()
    {
        if (isset($this->articles)) {
            return $this->articles;
        }
        $articles = [];
        if ($this->type == "section") {
            if($this->id) {
                $query = "
                    SELECT id_article
                    FROM cms_articles
                    JOIN cms_editorial_publications_states
                    ON article_publication_state=id_publication_state WHERE num_section = ".$this->id."
                    AND ((article_start_date != 0 AND to_days(article_start_date)<=to_days(now())
                    AND to_days(article_end_date)>=to_days(now()))
                    OR (article_start_date != 0 AND article_end_date =0 AND to_days(article_start_date)<=to_days(now()))
                    OR (article_start_date=0 AND article_end_date=0)
                    OR (article_start_date = 0 AND to_days(article_end_date)>=to_days(now())))
                    AND (editorial_publication_state_opac_show=1".(!$_SESSION['id_empr_session'] ? " AND editorial_publication_state_auth_opac_show = 0" : "").")
                    ORDER BY article_order";
                $result = pmb_mysql_query($query);
                if(pmb_mysql_num_rows($result)) {
                    while ($row = pmb_mysql_fetch_object($result)) {
                        $article = new cms_article($row->id_article);
                        $articles[] = $article->format_datas($this->links_patterns);
                    }
                }
            }
        }
        $this->articles = $articles;
        return $this->articles;
    }

    public function get_resume()
    {
        return $this->resume;
    }

    public function get_content()
    {
        // Anciennement, le contenu d'un article sortait sous la variable Django {{content}}.
        // On ne peut pas changer ce comportement sans avoir à repasser PARTOUT.
        // Donc on ajoute la feinte qui va bien...
        return $this->contenu;
    }


    private function look_for_attribute_in_class($class, $attribute, $parameters = [])
    {
        if (is_object($class)) {
            //Test du getter en premier pour le get_type() afin d'être compatible à l'existant
            if (method_exists($class, "get_".$attribute)) {
                return call_user_func_array([$class, "get_".$attribute], $parameters);
            } elseif (isset($class->{$attribute})) {
                return $class->{$attribute};
            } elseif (method_exists($class, $attribute)) {
                return call_user_func_array([$class, $attribute], $parameters);
            } elseif (method_exists($class, "is_".$attribute)) {
                return call_user_func_array([$class, "is_".$attribute], $parameters);
            }
        }
        return null;
    }

    public function __get($name)
    {
        return $this->look_for_attribute_in_class($this, $name);
    }


    public function get_link()
    {
        if (!empty($this->link)) {
            return $this->link;
        }
        if (!empty($this->links_patterns[$this->type])) {
            $this->link = str_replace('!!id!!', $this->id, $this->links_patterns[$this->type]);
        }
        return $this->link;
    }

    public static function get_cms_article_from_concept($concept)
    {
        $tab_id = [];
        $concept_id = $concept->get_num_object();

        $query = "select num_object from index_concept where num_concept = ".$concept_id ." AND type_object=". TYPE_CMS_ARTICLE ;
        $result = pmb_mysql_query($query);

        if (pmb_mysql_num_rows($result)) {
            while ($rows = pmb_mysql_fetch_assoc($result)) {
                $tab_id[] = $rows['num_object'];
            }
        }
        return $tab_id;
    }

    public static function get_cms_section_from_concept($concept)
    {
        $tab_id = [];
        $concept_id = $concept->get_num_object();

        $query = "select num_object from index_concept where num_concept = ".$concept_id ." AND type_object=" . TYPE_CMS_SECTION;
        $result = pmb_mysql_query($query);

        if (pmb_mysql_num_rows($result)) {
            while ($rows = pmb_mysql_fetch_assoc($result)) {
                $tab_id[] = $rows['num_object'];
            }
        }
        return $tab_id;
    }
}
