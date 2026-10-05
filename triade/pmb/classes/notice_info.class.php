<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_info.class.php,v 1.87.2.1.2.4 2025/04/24 10:18:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}


use Pmb\Ark\Models\ArkModel;
use Pmb\Ark\Entities\ArkRecord;
use Pmb\Ark\Entities\ArkBulletin;
use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

// Récupération des info de notices
global $class_path, $include_path;
require_once($class_path."/parametres_perso.class.php");
require_once($include_path."/notice_authors.inc.php");
require_once("$class_path/author.class.php");
require_once("$class_path/collection.class.php");
require_once("$class_path/subcollection.class.php");
require_once($include_path."/notice_categories.inc.php");
require_once($include_path."/explnum.inc.php");
require_once($include_path."/interpreter/bbcode.inc.php");
require_once("$class_path/authperso_notice.class.php");
require_once("$class_path/map/map_objects_controler.class.php");
require_once("$class_path/map_info.class.php");
require_once($class_path."/avis_records.class.php");
require_once($class_path."/notice_relations_collection.class.php");
require_once($class_path."/thumbnail.class.php");
require_once($class_path."/caddie.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/serials.class.php");

global $tdoc;
if (empty($tdoc)) {
    $tdoc = marc_list_collection::get_instance('doctype');
}

global $fonction_auteur;
if (empty($fonction_auteur)) {
    $fonction_auteur = new marc_list('function');
    $fonction_auteur = $fonction_auteur->table;
}

class notice_info
{
    public $notice;

    public $notice_id;
    public $environement;
    protected $isbd;
    protected $memo_isbn;
    protected $memo_typdoc;
    protected $memo_icondoc;
    protected $memo_iconcart;
    protected $niveau_biblio;
    protected $niveau_hierar;

    protected $serial_title;
    protected $bulletin_id;
    protected $bulletin_numero;
    protected $bulletin_mention_date;
    protected $bulletin_date_date;

    protected $memo_series;
    protected $memo_titre;
    protected $memo_titre_serie;

    protected $memo_notice_bulletin;
    protected $memo_bulletin;

    protected $memo_complement_titre;
    protected $memo_titre_parallele;
    protected $memo_notice;
    protected $memo_mention_edition;

    protected $memo_lang;
    protected $memo_lang_or;
    protected $authors;
    protected $responsabilites;
    protected $memo_auteur_principal;
    protected $memo_mention_resp_1;
    protected $memo_auteur_autre_tab;
    protected $memo_auteur_autre;
    protected $memo_mention_resp_2;
    protected $memo_auteur_secondaire_tab;
    protected $memo_auteur_secondaire;
    protected $memo_libelle_mention_resp;

    protected $memo_collection;
    protected $editeurs;
    protected $memo_ed1;
    protected $memo_ed1_name;
    protected $memo_ed1_place;
    protected $memo_ed2;
    protected $memo_ed2_name;
    protected $memo_ed2_place;

    protected $memo_year;
    protected $memo_collation;

    protected $memo_map;
    protected $memo_map_isbd;
    protected $memo_map_echelle;
    protected $memo_map_projection;
    protected $memo_map_ref;
    protected $memo_map_equinoxe;

    protected $memo_dewey;
    protected $memo_exemplaires;
    protected $memo_categories;
    protected $memo_authperso_all_isbd;
    protected $memo_authperso_all_isbd_list;
    protected $parametres_auth_perso;
    protected $parametres_perso;

    protected $memo_notice_mere;
    protected $memo_notice_mere_relation_type;
    protected $memo_notice_fille;
    protected $memo_notice_fille_relation_type;
    protected $memo_notice_horizontale;
    protected $memo_notice_horizontale_relation_type;

    protected $memo_notice_article;
    protected $memo_bulletinage;
    protected $memo_article_bulletinage;

    protected $memo_explnum;
    protected $memo_explnum_assoc;
    protected $memo_explnum_assoc_number;

    protected $memo_image;
    protected $memo_url_image;
    protected $permalink;
    protected $memo_avis;
    protected $memo_tu;
    protected $memo_statut;

    protected $memo_collstate;

    protected $map;
    protected $map_info;

    protected $ark_link;
    public $notices_parents;
    public $notices_childs;

    public $print_mode = 0;

    public function __construct($id, $environement = [])
    {
        $this->notice_id = intval($id);
        $this->environement = $environement;

        if (!isset($this->environement["short"])) {
            $this->environement["short"] = 6;
        }
        if (!isset($this->environement["ex"])) {
            $this->environement["ex"] = 0;
        }
        if (!isset($this->environement["exnum"])) {
            $this->environement["exnum"] = 1;
        }

        if (!isset($this->environement["link"])) {
            $this->environement["link"] = notice::get_pattern_link();
        }
        if (!isset($this->environement["link_analysis"])) {
            $this->environement["link_analysis"] = analysis::get_pattern_link();
        }
        if (!isset($this->environement["link_explnum"])) {
            $this->environement["link_explnum"] = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!" ;
        }
        if (!isset($this->environement["link_bulletin"])) {
            $this->environement["link_bulletin"] = bulletinage::get_pattern_link();
        }

        $this->fetch_data();
    }

    public function fetch_analysis_info()
    {
        if (($this->niveau_biblio == "a") && ($this->niveau_hierar == 2)) {
            $requete = "select tit1,bulletin_numero,date_date,mention_date from analysis join bulletins on (analysis_bulletin=bulletin_id) join notices on (bulletin_notice=notice_id) where " .
                    "analysis_notice=".$this->notice_id;
            $resultat = pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat)) {
                $r = pmb_mysql_fetch_object($resultat);
				pmb_mysql_free_result($resultat);

                $this->serial_title = $r->tit1;
                $this->bulletin_numero = $r->bulletin_numero;
                $this->bulletin_mention_date = $r->mention_date;
                $this->bulletin_date_date = formatdate($r->date_date);
            }
        }
    }

    public function fetch_data()
    {
        global $msg;

        if (!$this->notice_id) {
            return false;
        }

        // Recuperation des infos de la notice
        $requete = "select * from notices where notice_id=".$this->notice_id;
        $resultat = pmb_mysql_query($requete);
        if (!pmb_mysql_num_rows($resultat)) {
            return false;
        }

        $res = pmb_mysql_fetch_object($resultat);
        pmb_mysql_free_result($resultat);

        $this->notice = $res;
        $this->get_niveau_biblio();
        $this->get_niveau_hierar();

        // Recherche des infos du périodique
        $this->fetch_analysis_info();

        // Titres
        $this->memo_titre = '';
        if ($res->tparent_id) {
            $requete = "select * from series where serie_id=".$res->tparent_id;
            $resultat = pmb_mysql_query($requete);
            if (($serie = pmb_mysql_fetch_object($resultat))) {
				pmb_mysql_free_result($resultat);

                $this->memo_titre = $serie->serie_name;
                if ($this->notice->tnvol) {
                    $this->memo_titre .= ', '.$res->tnvol;
                }
            }
        } elseif ($this->notice->tnvol) {
            $this->memo_titre .= $res->tnvol;
        }

        $this->memo_titre ? $this->memo_titre .= '. '.$res->tit1 : $this->memo_titre = $res->tit1;

        $this->memo_notice_bulletin = new stdClass();
        $this->memo_bulletin = new stdClass();
        if ($res->niveau_biblio == 'b') {
            $rqt = "select tit1, date_format(date_date, '".$msg["format_date"]."') as aff_date_date, bulletin_numero as num_bull,bulletin_notice from bulletins,notices where bulletins.num_notice='".$this->notice_id."' and notices.notice_id=bulletins.bulletin_notice";
            $execute_query = pmb_mysql_query($rqt);
            $row = pmb_mysql_fetch_object($execute_query);
            $this->memo_titre .= " ".(!$row->aff_date_date ? sprintf($msg["bul_titre_perio"], $row->tit1) : sprintf($msg["bul_titre_perio"], $row->tit1.", ".$row->num_bull." [".$row->aff_date_date."]"));

            // recherche editeur de la notice de perio
            $rqt_perio = "select * from notices where notice_id=".$row->bulletin_notice;
            $execute_query_perio = pmb_mysql_query($rqt_perio);
            $row_perio = pmb_mysql_fetch_object($execute_query_perio);
            if (!$this->notice->ed1_id) {
                $this->notice->ed1_id = $row_perio->ed1_id;
            }
            //issn pour les notices de bulletin
            if (!$this->notice->code) {
                $this->memo_isbn = $row_perio->code;
            }
        } elseif ($res->niveau_biblio == 'a' && $res->niveau_hierar == 2) {
            $requete = "SELECT b.* ";
            $requete .= "from analysis a, notices b, bulletins c";
            $requete .= " WHERE a.analysis_notice=".$this->notice_id;
            $requete .= " AND c.bulletin_id=a.analysis_bulletin";
            $requete .= " AND c.bulletin_notice=b.notice_id";
            $requete .= " LIMIT 1";
            $myQuery = pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($myQuery)) {
                $row_perio = pmb_mysql_fetch_object($myQuery);
                if (!$this->notice->ed1_id) {
                    $this->notice->ed1_id = $row_perio->ed1_id;
                }
                //issn pour les notice de dépouillement
                if (!$this->notice->code) {
                    $this->memo_isbn = $row_perio->code;
                }
            }

            //	info du bulletin de ce dépouillement
            $req_bulletin = "SELECT  c.* from analysis a, bulletins c WHERE c.bulletin_id=a.analysis_bulletin AND analysis_notice=".$res->notice_id;
            $result_bull = pmb_mysql_query($req_bulletin);
            if (($bull = pmb_mysql_fetch_object($result_bull))) {
                $this->memo_bulletin = $bull;
                $this->memo_notice_bulletin = $bull;
                $this->bulletin_mention_date = $bull->mention_date;
                $this->bulletin_date_date = formatdate($bull->date_date);
                $this->bulletin_numero = $bull->bulletin_numero;
            }
        }
        $this->memo_notice = $res;

        //Titre du pério pour les notices de bulletin
        if ($res->niveau_biblio == 'b' && $res->niveau_hierar == '2') {
            $req_bulletin = "SELECT bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, bulletin_numero, tit1 as titre from bulletins, notices WHERE bulletin_notice=notice_id AND num_notice=".$res->notice_id;
            $result_bull = pmb_mysql_query($req_bulletin);
            while(($bull = pmb_mysql_fetch_object($result_bull))) {
                $this->memo_notice_bulletin = $bull;
                $this->memo_bulletin = $bull;
                $this->serial_title = $bull->titre;
                $this->bulletin_mention_date = $bull->mention_date;
                $this->bulletin_date_date = formatdate($bull->date_date);
                $this->bulletin_numero = $bull->bulletin_numero;
                $this->bulletin_id = $bull->bulletin_id;
            }
        }
    }

    public function get_isbd()
    {
        global $tdoc;
        if (!isset($this->isbd)) {
            $this->isbd = '';
            if ($this->notice->tparent_id) {
                $this->isbd .= $this->get_memo_titre_serie();
                if ($this->notice->tnvol) {
                    $this->isbd .= ',&nbsp;'.$this->notice->tnvol;
                }
            }
            $this->isbd ? $this->isbd .= '.&nbsp;'.$this->notice->tit1 : $this->isbd = $this->notice->tit1;
            $tit2 = $this->notice->tit2;
            $tit3 = $this->notice->tit3;
            $tit4 = $this->notice->tit4;
            if ($tit3) {
                $this->isbd .= "&nbsp;= $tit3";
            }
            if ($tit4) {
                $this->isbd .= "&nbsp;: $tit4";
            }
            if ($tit2) {
                $this->isbd .= "&nbsp;; $tit2";
            }
            $this->isbd .= ' ['.$tdoc->table[$this->notice->typdoc].']';
            if ($this->get_memo_libelle_mention_resp()) {
                $this->isbd .= "&nbsp;/ ".$this->get_memo_libelle_mention_resp();
            }
            if ($this->get_editeurs()) {
                $this->isbd .= ".&nbsp;-&nbsp;".$this->get_editeurs();
            }
            if ($this->get_memo_collation()) {
                $this->isbd .= ".&nbsp;-&nbsp;".$this->get_memo_collation();
            }
            if ($this->get_memo_map_isbd()) {
                $this->isbd .= ".&nbsp;-&nbsp;".$this->get_memo_map_isbd();
            }
            if ($this->get_memo_collection()) {
                if ($this->notice->nocoll) {
                    $nocoll = '; '.$this->notice->nocoll;
                } else {
                    $nocoll = '';
                }
                $this->isbd .= ".&nbsp;-&nbsp;(".$this->get_memo_collection().$nocoll.")".' ';
            }
            if (substr(trim($this->isbd), -1) != ".") {
                $this->isbd .= '.';
            }
        }
        return $this->isbd;
    }

    public function get_memo_isbn()
    {
        if (!isset($this->memo_isbn)) {
            $this->memo_isbn = $this->notice->code;
        }
        return $this->memo_isbn;
    }

    public function get_memo_typdoc()
    {
        global $tdoc;
        if (!isset($this->memo_typdoc)) {
            $this->memo_typdoc = $tdoc->table[$this->notice->typdoc];
        }
        return $this->memo_typdoc;
    }

    //Icone type de Document
    public function get_memo_icondoc()
    {
        global $tdoc;

        if (!isset($this->memo_icondoc)) {
            $icon_doc = marc_list_collection::get_instance('icondoc');
            $icon = $icon_doc->table[$this->notice->niveau_biblio.$this->notice->typdoc];
            if ($icon) {
                $biblio_doc = marc_list_collection::get_instance('nivbiblio');
                $info_bulle_icon = $biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc];
                $this->memo_icondoc = "<img src=\"".get_url_icon($icon)."\" alt=\"$info_bulle_icon\" title=\"$info_bulle_icon\" class='align_top' />";
            } else {
                $this->memo_icondoc = "";
            }
        }
        return $this->memo_icondoc;
    }

    public function get_memo_iconcart()
    {
        global $msg;
        global $use_opac_url_base, $opac_url_base, $pmb_url_base;

        if (!isset($this->memo_iconcart)) {
            if ($use_opac_url_base) {
                if (isset($_SESSION["cart"]) && in_array($this->notice_id, $_SESSION["cart"])) {
                    $this->memo_iconcart = "<span id='baskets".$this->notice_id."'><a href='#' class=\"img_basket_exist\" title=\"".$msg['notice_title_basket_exist']."\"><img src=\"".$opac_url_base."images/basket_exist.png\" border=\"0\" alt=\"".$msg['notice_title_basket_exist']."\" /></a></span>";
                } else {
					$title=$this->notice_header;
					if(!$title)$title=$this->notice->tit1;
                    $this->memo_iconcart = "<span id='baskets".$this->notice_id."'><a href=\"cart_info.php?id=".$this->notice_id."&header=".rawurlencode(strip_tags($title))."\" target=\"cart_info\" class=\"img_basket\" title=\"".$msg['notice_title_basket']."\"><img src=\"".$opac_url_base."images/basket_small_20x20.png\" border=\"0\" title=\"".$msg['notice_title_basket']."\" alt=\"".$msg['notice_title_basket']."\" /></a></span>";
                }
            } else {
                $this->memo_iconcart = "<img src=\"".$pmb_url_base."images/basket_small_20x20.gif\" align='absmiddle' style='border:0px' title='".$msg["400"]."' alt='".$msg["400"]."' />";
            }
        }
        return $this->memo_iconcart;
    }

    public function get_niveau_biblio()
    {
        if (!isset($this->niveau_biblio)) {
            $this->niveau_biblio = $this->notice->niveau_biblio;
        }
        return $this->niveau_biblio;
    }

    public function get_niveau_hierar()
    {
        if (!isset($this->niveau_hierar)) {
            $this->niveau_hierar = $this->notice->niveau_hierar;
        }
        return $this->niveau_hierar;
    }

    public function get_serial_title()
    {
        return $this->serial_title;
    }

    public function get_bulletin_numero()
    {
        return $this->bulletin_numero;
    }

    public function get_bulletin_mention_date()
    {
        return $this->bulletin_mention_date;
    }

    public function get_bulletin_date_date()
    {
        return $this->bulletin_date_date;
    }

    //Titre
    public function get_memo_titre()
    {
        return $this->memo_titre;
    }

    //Titre de serie
    public function get_memo_titre_serie()
    {
        if (!isset($this->memo_titre_serie)) {
            $this->memo_series = [];
            $this->memo_titre_serie = '';
            if ($this->notice->tparent_id) {
                $requete = "select * from series where serie_id=".$this->notice->tparent_id;
                $resultat = pmb_mysql_query($requete);
                if (($serie = pmb_mysql_fetch_object($resultat))) {
                    $this->memo_series[] = $serie;
                    $this->memo_titre_serie = $serie->serie_name;
                    if ($this->notice->tnvol) {
                        $this->memo_titre_serie .= ', '.$this->notice->tnvol;
                    }
                }
            }
        }
        return $this->memo_titre_serie;
    }

    public function get_memo_notice_bulletin()
    {
        return $this->memo_notice_bulletin;
    }

    public function get_memo_bulletin()
    {
        return $this->memo_bulletin;
    }

    public function get_memo_complement_titre()
    {
        if (!isset($this->memo_complement_titre)) {
            $this->memo_complement_titre = $this->notice->tit4;
        }
        return $this->memo_complement_titre;
    }

    public function get_memo_titre_parallele()
    {
        if (!isset($this->memo_titre_parallele)) {
            $this->memo_titre_parallele = $this->notice->tit3;
        }
        return $this->memo_titre_parallele;
    }

    public function get_memo_notice()
    {
        return $this->memo_notice;
    }

    //mention d'édition
    public function get_memo_mention_edition()
    {
        if (!isset($this->memo_mention_edition)) {
            $this->memo_mention_edition = $this->notice->mention_edition;
        }
        return $this->memo_mention_edition;
    }

    // langues de la publication
    public function get_memo_lang()
    {
        if (!isset($this->memo_lang)) {
            $this->memo_lang = get_notice_langues($this->notice_id, 0);
        }
        return $this->memo_lang;
    }

    // langues originales
    public function get_memo_lang_or()
    {
        if (!isset($this->memo_lang_or)) {
            $this->memo_lang_or = get_notice_langues($this->notice_id, 1);
        }
        return $this->memo_lang_or;
    }

    // auteurs
    public function get_authors()
    {
        global $fonction_auteur;
        if (!isset($this->authors)) {
            $this->authors = [];
            //Recherche des auteurs;
            $this->responsabilites = get_notice_authors($this->notice_id);
            $mention_resp = $mention_resp_1 = $mention_resp_2 = [] ;
            $isbd_entry_1 = $isbd_entry_2 = [] ;
            $as = array_search("0", $this->responsabilites["responsabilites"]) ;
            if ($as !== false && $as !== null) {
                $auteur_0 = $this->responsabilites["auteurs"][$as] ;
                $auteur = new auteur($auteur_0["id"]);
                if (!empty($auteur_0["fonction"]) && !empty($fonction_auteur[$auteur_0["fonction"]])) {
                    $auteur->fonction = $fonction_auteur[$auteur_0["fonction"]];
                }
                $this->authors[] = $auteur;
                if ($this->print_mode) {
                    $mention_resp_lib = $auteur->get_isbd();
                } else {
					//else $mention_resp_lib = '';
                    $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
                }
                if (!$this->print_mode) {
                    $mention_resp_lib .= $auteur->author_web_link ;
                }
                if (!empty($auteur_0["fonction"]) && !empty($fonction_auteur[$auteur_0["fonction"]])) {
                    $mention_resp_lib .= ", ".$fonction_auteur[$auteur_0["fonction"]];
                }
                $mention_resp[] = $mention_resp_lib;
                $this->memo_auteur_principal = $auteur->get_isbd();
            }
            $as = array_keys($this->responsabilites["responsabilites"], "1") ;
            for ($i = 0 ; $i < count($as) ; $i++) {
                $indice = $as[$i] ;
                $auteur_1 = $this->responsabilites["auteurs"][$indice] ;
                $auteur = new auteur($auteur_1["id"]);
                if ($auteur_1["fonction"] != '0' && !empty($auteur_1["fonction"]) && !empty($fonction_auteur[$auteur_1["fonction"]])) {
                    $auteur->fonction = $fonction_auteur[$auteur_1["fonction"]];
                }
                $this->authors[] = $auteur;
                if ($this->print_mode) {
                    $mention_resp_lib = $auteur->get_isbd();
                } else {
					//else $mention_resp_lib = '';
                    $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
                }
                if (!$this->print_mode) {
                    $mention_resp_lib .= $auteur->author_web_link ;
                }
                if (!empty($auteur_1["fonction"]) && !empty($fonction_auteur[$auteur_1["fonction"]])) {
                    $mention_resp_lib .= ", ".$fonction_auteur[$auteur_1["fonction"]];
                }
                $mention_resp[] = $mention_resp_lib;
                $mention_resp_1[] = $mention_resp_lib;
                $isbd_entry_1[] = $auteur->get_isbd();
            }
            $this->memo_mention_resp_1 = implode("; ", $mention_resp_1);
            $this->memo_auteur_autre_tab = $isbd_entry_1;
            $this->memo_auteur_autre = implode("; ", $isbd_entry_1);

            $as = array_keys($this->responsabilites["responsabilites"], "2") ;
            for ($i = 0 ; $i < count($as) ; $i++) {
                $indice = $as[$i] ;
                $auteur_2 = $this->responsabilites["auteurs"][$indice] ;
                $auteur = new auteur($auteur_2["id"]);
                if ($auteur_2["fonction"] != '0' && !empty($auteur_2["fonction"]) && !empty($fonction_auteur[$auteur_2["fonction"]])) {
                    $auteur->fonction = $fonction_auteur[$auteur_2["fonction"]];
                }
                $this->authors[] = $auteur;
                if ($this->print_mode) {
                    $mention_resp_lib = $auteur->get_isbd();
                } else {
					//else $mention_resp_lib = '';
                    $mention_resp_lib = $auteur->isbd_entry_lien_gestion;
                }
                if (!$this->print_mode) {
                    $mention_resp_lib .= $auteur->author_web_link ;
                }
                if (!empty($auteur_2["fonction"]) && !empty($fonction_auteur[$auteur_2["fonction"]])) {
                    $mention_resp_lib .= ", ".$fonction_auteur[$auteur_2["fonction"]];
                }
                $mention_resp[] = $mention_resp_lib;
                $mention_resp_2[] = $mention_resp_lib;
                $isbd_entry_2[] = $auteur->get_isbd();
            }
            $this->memo_mention_resp_2 = implode("; ", $mention_resp_2);
            $this->memo_auteur_secondaire_tab = $isbd_entry_2;
            $this->memo_auteur_secondaire = implode("; ", $isbd_entry_2);

            $this->memo_libelle_mention_resp = implode("; ", $mention_resp);
        }
        return $this->authors;
    }

    public function get_responsabilites()
    {
        return $this->responsabilites;
    }

    public function get_memo_auteur_principal()
    {
        if (!isset($this->memo_auteur_principal)) {
            $this->memo_auteur_principal = '';
            $this->get_authors();
        }
        return $this->memo_auteur_principal;
    }

    public function get_memo_mention_resp_1()
    {
        if (!isset($this->memo_mention_resp_1)) {
            $this->memo_mention_resp_1 = '';
        }
        return $this->memo_mention_resp_1;
    }

    public function get_memo_auteur_autre_tab()
    {
        if (!isset($this->memo_auteur_autre_tab)) {
            $this->memo_auteur_autre_tab = '';
            $this->get_authors();
        }
        return $this->memo_auteur_autre_tab;
    }

    public function get_memo_auteur_autre()
    {
        if (!isset($this->memo_auteur_autre)) {
            $this->memo_auteur_autre = '';
            $this->get_authors();
        }
        return $this->memo_auteur_autre;
    }

    public function get_memo_mention_resp_2()
    {
        if (!isset($this->memo_mention_resp_2)) {
            $this->memo_mention_resp_2 = '';
            $this->get_authors();
        }
        return $this->memo_mention_resp_2;
    }

    public function get_memo_auteur_secondaire_tab()
    {
        if (!isset($this->memo_auteur_secondaire_tab)) {
            $this->memo_auteur_secondaire_tab = '';
            $this->get_authors();
        }
        return $this->memo_auteur_secondaire_tab;
    }

    public function get_memo_auteur_secondaire()
    {
        if (!isset($this->memo_auteur_secondaire)) {
            $this->memo_auteur_secondaire = '';
            $this->get_authors();
        }
        return $this->memo_auteur_secondaire;
    }

    public function get_memo_libelle_mention_resp()
    {
        if (!isset($this->memo_libelle_mention_resp)) {
            $this->memo_libelle_mention_resp = '';
            $this->get_authors();
        }
        return $this->memo_libelle_mention_resp;
    }

    // collection
    public function get_memo_collection()
    {
        if (!isset($this->memo_collection)) {
            $this->memo_collection = '';
            if ($this->notice->subcoll_id) {
                $collection = new subcollection($this->notice->subcoll_id);
                $this->memo_collection = $collection->get_isbd();
            } elseif ($this->notice->coll_id) {
                $collection = new collection($this->notice->coll_id);
                $this->memo_collection = $collection->get_isbd();
            }
        }
        return $this->memo_collection;
    }

    public function get_editeurs()
    {
        if (!isset($this->editeurs)) {
            $this->editeurs = '';
            if ($this->notice->subcoll_id) {
                $collection = new subcollection($this->notice->subcoll_id);
                $info = $this->get_info_editeur($collection->editeur);
                $this->editeurs = $info["isbd_entry"];
            } elseif ($this->notice->coll_id) {
                $collection = new collection($this->notice->coll_id);
                $info = $this->get_info_editeur($collection->parent);
                $this->editeurs = $info["isbd_entry"];
            } elseif ($this->notice->ed1_id) {
                $info = $this->get_info_editeur($this->notice->ed1_id);
                $this->editeurs = $info["isbd_entry"];
            }
            if ($this->notice->ed2_id) {
                $info = $this->get_info_editeur($this->notice->ed2_id);
                $this->editeurs ? $this->editeurs .= '&nbsp;; '.$info["isbd_entry"] : $this->editeurs = $info["isbd_entry"];
            }
            if ($this->notice->year) {
                $this->editeurs ? $this->editeurs .= ', '.$this->notice->year : $this->editeurs = $this->notice->year;
            } elseif ($this->notice->niveau_biblio != 'b') {
                $this->editeurs ? $this->editeurs .= ', [s.d.]' : $this->editeurs = "[s.d.]";
            }
        }
        return $this->editeurs;
    }

    public function get_memo_ed1()
    {
        if (!isset($this->memo_ed1)) {
            $this->memo_ed1 = '';
            if ($this->notice->subcoll_id) {
                $collection = new subcollection($this->notice->subcoll_id);
                $info = $this->get_info_editeur($collection->editeur);
                $this->memo_ed1 = $info["isbd_entry"];
            } elseif ($this->notice->coll_id) {
                $collection = new collection($this->notice->coll_id);
                $info = $this->get_info_editeur($collection->parent);
                $this->memo_ed1 = $info["isbd_entry"];
            } elseif ($this->notice->ed1_id) {
                $info = $this->get_info_editeur($this->notice->ed1_id);
                $this->memo_ed1 = $info["isbd_entry"];
            }
        }
        return $this->memo_ed1;
    }

    public function get_memo_ed1_name()
    {
        if (!isset($this->memo_ed1_name)) {
            $this->memo_ed1_name = '';
            if ($this->notice->subcoll_id) {
                $collection = new subcollection($this->notice->subcoll_id);
                $info = $this->get_info_editeur($collection->editeur);
                $this->memo_ed1_name = $info["name"];
            } elseif ($this->notice->coll_id) {
                $collection = new collection($this->notice->coll_id);
                $info = $this->get_info_editeur($collection->parent);
                $this->memo_ed1_name = $info["name"];
            } elseif ($this->notice->ed1_id) {
                $info = $this->get_info_editeur($this->notice->ed1_id);
                $this->memo_ed1_name = $info["name"];
            }
        }
        return $this->memo_ed1_name;
    }

    public function get_memo_ed1_place()
    {
        if (!isset($this->memo_ed1_place)) {
            $this->memo_ed1_place = '';
            if ($this->notice->subcoll_id) {
                $collection = new subcollection($this->notice->subcoll_id);
                $info = $this->get_info_editeur($collection->editeur);
                $this->memo_ed1_place = $info["place"];
            } elseif ($this->notice->coll_id) {
                $collection = new collection($this->notice->coll_id);
                $info = $this->get_info_editeur($collection->parent);
                $this->memo_ed1_place = $info["place"];
            } elseif ($this->notice->ed1_id) {
                $info = $this->get_info_editeur($this->notice->ed1_id);
                $this->memo_ed1_place = $info["place"];
            }
        }
        return $this->memo_ed1_place;
    }

    public function get_memo_ed2()
    {
        if (!isset($this->memo_ed2)) {
            if ($this->notice->ed2_id) {
                $info = $this->get_info_editeur($this->notice->ed2_id);
                $this->memo_ed2 = $info["isbd_entry"];
            } else {
                $this->memo_ed2 = '';
            }
        }
        return $this->memo_ed2;
    }

    public function get_memo_ed2_name()
    {
        if (!isset($this->memo_ed2_name)) {
            if ($this->notice->ed2_id) {
                $info = $this->get_info_editeur($this->notice->ed2_id);
                $this->memo_ed2_name = $info["name"];
            } else {
                $this->memo_ed2_name = '';
            }
        }
        return $this->memo_ed2_name;
    }

    public function get_memo_ed2_place()
    {
        if (!isset($this->memo_ed2_place)) {
            if ($this->notice->ed2_id) {
                $info = $this->get_info_editeur($this->notice->ed2_id);
                $this->memo_ed2_place = $info["place"];
            } else {
                $this->memo_ed2_place = '';
            }
        }
        return $this->memo_ed2_place;
    }

    public function get_memo_year()
    {
        if (!isset($this->memo_year)) {
            $this->memo_year = $this->notice->year;
        }
        return $this->memo_year;
    }

    // zone de la collation (ne concerne que a2)
    public function get_memo_collation()
    {
        if (!isset($this->memo_collation)) {
            $this->memo_collation = '';
            if ($this->notice->npages) {
                $this->memo_collation .= $this->notice->npages;
            }
            if ($this->notice->ill) {
                $this->memo_collation .= ' : '.$this->notice->ill;
            }
            if ($this->notice->size) {
                $this->memo_collation .= ' ; '.$this->notice->size;
            }
            if ($this->notice->accomp) {
                $this->memo_collation .= ' + '.$this->notice->accomp;
            }
        }
        return $this->memo_collation;
    }

    public function get_map()
    {
        if (!isset($this->map)) {
            $ids = [];
            $ids[] = $this->notice_id;
            $this->map = new map_objects_controler(TYPE_RECORD, $ids);
        }
        return $this->map;
    }

    public function get_map_info()
    {
        if (!isset($this->map_info)) {
            $this->map_info = new map_info($this->notice_id);
        }
        return $this->map_info;
    }

    public function get_memo_map_isbd()
    {
        global $pmb_map_activate;
        if (!isset($this->memo_map_isbd)) {
            $this->memo_map_isbd = '';
            if ($pmb_map_activate == 1 || $pmb_map_activate == 2) {
                $this->memo_map_isbd = $this->get_map_info()->get_isbd();
            }
        }
        return $this->memo_map_isbd;
    }

    public function get_memo_map_echelle()
    {
        global $pmb_map_activate;
        if (!isset($this->memo_map_echelle)) {
            $this->memo_map_echelle = '';
            if ($pmb_map_activate == 1 || $pmb_map_activate == 2) {
                if (isset($this->get_map_info()->map['echelle'])) {
                    $this->memo_map_echelle = $this->get_map_info()->map['echelle'];
                }
            }
        }
        return $this->memo_map_echelle;
    }

    public function get_memo_map_projection()
    {
        global $pmb_map_activate;
        if (!isset($this->memo_map_projection)) {
            $this->memo_map_projection = '';
            if ($pmb_map_activate == 1 || $pmb_map_activate == 2) {
                if (isset($this->get_map_info()->map['projection'])) {
                    $this->memo_map_projection = $this->get_map_info()->map['projection'];
                }
            }
        }
        return $this->memo_map_projection;
    }

    public function get_memo_map_ref()
    {
        global $pmb_map_activate;
        if (!isset($this->memo_map_ref)) {
            $this->memo_map_ref = '';
            if ($pmb_map_activate == 1 || $pmb_map_activate == 2) {
                if (isset($this->get_map_info()->map['ref'])) {
                    $this->memo_map_ref = $this->get_map_info()->map['ref'];
                }
            }
        }
        return $this->memo_map_ref;
    }

    public function get_memo_map_equinoxe()
    {
        global $pmb_map_activate;
        if (!isset($this->memo_map_equinoxe)) {
            $this->memo_map_equinoxe = '';
            if ($pmb_map_activate == 1 || $pmb_map_activate == 2) {
                if (isset($this->get_map_info()->map['equinoxe'])) {
                    $this->memo_map_equinoxe = $this->get_map_info()->map['equinoxe'];
                }
            }
        }
        return $this->memo_map_equinoxe;
    }

    public function get_memo_map()
    {
        global $pmb_map_activate;
        if (!isset($this->memo_map)) {
            $this->memo_map = '';
            if ($pmb_map_activate == 1 || $pmb_map_activate == 2) {
                $this->memo_map = $this->get_map()->get_map();
            }
        }
        return $this->memo_map;
    }

    //Recherche du code dewey
    public function get_memo_dewey()
    {
        if (!isset($this->memo_dewey)) {
            $requete = "select * from indexint where indexint_id=".$this->notice->indexint;
            $resultat = pmb_mysql_query($requete);
            if (($code_dewey = pmb_mysql_fetch_object($resultat))) {
                $this->memo_dewey = $code_dewey;
            } else {
                $this->memo_dewey = '';
            }
        }
        return $this->memo_dewey;
    }

    //Traitement des exemplaires
    public function get_memo_exemplaires()
    {
        global $opac_sur_location_activate;

        if (!isset($this->memo_exemplaires)) {
            $this->memo_exemplaires = [];
            $requete = "select expl_id, expl_cb, expl_cote, expl_statut,statut_libelle, expl_typdoc, tdoc_libelle, expl_note, expl_comment,
					expl_section, section_libelle, expl_owner, lender_libelle, expl_codestat, codestat_libelle,
					expl_date_retour, expl_date_depot, expl_note, pret_flag, expl_location, location_libelle, expl_prix ";
            if ($opac_sur_location_activate) {
                $requete .= ", ifnull(surloc_id,0) as surloc_id, ifnull(surloc_libelle,'') as surloc_libelle ";
            }
            $requete .= " from exemplaires
					left join docs_statut on expl_statut=idstatut
					left join docs_type on expl_typdoc=idtyp_doc
					left join docs_section on expl_section=idsection
					left join docs_codestat on expl_codestat=idcode
					left join lenders on expl_owner=idlender
					left join docs_location on expl_location=idlocation
					";
            if ($opac_sur_location_activate) {
                $requete .= " left join sur_location on surloc_num=surloc_id ";
            }
            $requete .= " where expl_notice=".$this->notice_id;
            $requete .= " union ";
            $requete .= " select expl_id, expl_cb, expl_cote, expl_statut,statut_libelle, expl_typdoc, tdoc_libelle, expl_note, expl_comment,
					expl_section, section_libelle, expl_owner, lender_libelle, expl_codestat, codestat_libelle,
					expl_date_retour, expl_date_depot, expl_note, pret_flag, expl_location, location_libelle, expl_prix ";
            if ($opac_sur_location_activate) {
                $requete .= ", ifnull(surloc_id,0) as surloc_id, ifnull(surloc_libelle,'') as surloc_libelle ";
            }
            $requete .= " from exemplaires
					left join bulletins on expl_bulletin=bulletin_id
					left join docs_statut on expl_statut=idstatut
					left join docs_type on expl_typdoc=idtyp_doc
					left join docs_section on expl_section=idsection
					left join docs_codestat on expl_codestat=idcode
					left join lenders on expl_owner=idlender
					left join docs_location on expl_location=idlocation
					";
            if ($opac_sur_location_activate) {
                $requete .= " left join sur_location on surloc_num=surloc_id ";
            }
            $requete .= " where bulletins.num_notice=".$this->notice_id;
            $resultat = pmb_mysql_query($requete);
            while (($ex = pmb_mysql_fetch_object($resultat))) {
                //Champs perso d'exemplaires
                $parametres_perso = [];
                $mes_pp = new parametres_perso("expl");
                if (!$mes_pp->no_special_fields) {
                    $mes_pp->get_values($ex->expl_id);
                    $values = $mes_pp->values;
                    foreach ($values as $field_id => $vals) {
                        $parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["TITRE"] = $mes_pp->t_fields[$field_id]["TITRE"];
                        foreach ($vals as $value) {
                            $parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["VALUE"][] = $mes_pp->get_formatted_output([$value], $field_id);
                        }
                    }
                }
                $ex->parametres_perso = $parametres_perso;
                $ex->caddies = [];
                $caddies = caddie::get_cart_list_from_item('EXPL', 0, $ex->expl_id);
                if (sizeof($caddies)) {
                    foreach ($caddies as $caddie) {
                        $ex->caddies[] = $caddie['idcaddie'];
                    }
                }
                $this->memo_exemplaires[] = $ex;
            }
        }
        return $this->memo_exemplaires;
    }

    //Traitement des exemplaires
    public function get_memo_explnum()
    {
        global $pmb_explnum_order;

        if (!isset($this->memo_explnum)) {
            $this->memo_explnum = [];
            $requete = "SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut
				FROM explnum WHERE explnum_notice='".$this->notice_id."'
				UNION SELECT explnum_id, explnum_notice, explnum_bulletin, explnum_nom, explnum_mimetype, explnum_url, explnum_vignette, explnum_nomfichier, explnum_extfichier, explnum_docnum_statut
				FROM explnum, bulletins
				WHERE bulletin_id = explnum_bulletin
				AND bulletins.num_notice='".$this->notice_id."'";
            if ($pmb_explnum_order) {
                $requete .= " order by ".$pmb_explnum_order;
            } else {
                $requete .= " order by explnum_mimetype, explnum_id ";
            }
            $resultat = pmb_mysql_query($requete);
            while($explnum = pmb_mysql_fetch_object($resultat)) {
                //Champs perso de documents numériques
                $parametres_perso = [];
                $mes_pp = new parametres_perso("explnum");
                if (!$mes_pp->no_special_fields) {
                    $mes_pp->get_values($explnum->explnum_id);
                    $values = $mes_pp->values;
                    foreach ($values as $field_id => $vals) {
                        $parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["TITRE"] = $mes_pp->t_fields[$field_id]["TITRE"];
                        foreach ($vals as $value) {
                            $parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["VALUE"][] = $mes_pp->get_formatted_output([$value], $field_id);
                        }
                    }
                }
                $explnum->parametres_perso = $parametres_perso;
                $this->memo_explnum[] = $explnum;
            }
        }
        return $this->memo_explnum;
    }

    //Descripteurs
    public function get_memo_categories()
    {
        if (!isset($this->memo_categories)) {
            $requete = "SELECT libelle_categorie FROM categories, notices_categories WHERE notcateg_notice=".$this->notice_id." and categories.num_noeud = notices_categories.num_noeud ORDER BY ordre_categorie";
            $resultat = pmb_mysql_query($requete);
            $this->memo_categories = [];
            while (($cat = pmb_mysql_fetch_object($resultat))) {
                $this->memo_categories[] = $cat;
            }
        }
        return $this->memo_categories;
    }

    public function get_memo_authperso_all_isbd()
    {
        if (!isset($this->memo_authperso_all_isbd)) {
            $authperso = new authperso_notice($this->notice_id);
            $this->memo_authperso_all_isbd = $authperso->get_notice_display();
        }
        return $this->memo_authperso_all_isbd;
    }

    public function get_memo_authperso_all_isbd_list()
    {
        if (!isset($this->memo_authperso_all_isbd_list)) {
            $authperso = new authperso_notice($this->notice_id);
            $this->memo_authperso_all_isbd_list = $authperso->get_notice_display_list();
        }
        return $this->memo_authperso_all_isbd_list;
    }

    public function get_parametres_auth_perso()
    {
        if (!isset($this->parametres_auth_perso)) {
            $authperso = new authperso_notice($this->notice_id);
            foreach ($authperso->auth_info as $fields) {
                foreach ($fields["info_fields"] as $field) {
                    if (is_array($field["values"]) && count($field["values"])) {
                        $tvalues = [];
                        foreach ($field["values"] as $values) {
                            $tvalues[] = $values["format_value"];
                        }
                        $this->parametres_auth_perso[$field["name"]]["TITRE"][] = $field["label"];
                        $this->parametres_auth_perso[$field["name"]]["VALUE"][] = $tvalues;
                    }
                }
            }
        }
        return $this->parametres_auth_perso;
    }

    //Champs perso de notice traite par la table notice_custom
    public function get_parametres_perso()
    {
        if (!isset($this->parametres_perso)) {
            $mes_pp = new parametres_perso("notices");
            $mes_pp->get_values($this->notice_id);
            $values = $mes_pp->values;
            $this->parametres_perso = [];
            foreach ($values as $field_id => $vals) {
                $this->parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["TITRE"] = $mes_pp->t_fields[$field_id]["TITRE"];
                foreach ($vals as $value) {
                    $this->parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["VALUE"][] = $mes_pp->get_formatted_output([$value], $field_id);
                    $this->parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["VALUE_IN_DATABASE"][] = $value;
                }
            }
        }
        return $this->parametres_perso;
    }

    //les notices mères
    public function get_memo_notice_mere()
    {
        if (!isset($this->memo_notice_mere)) {
            //Notices liées, relations entre notices
            $notice_relations = notice_relations_collection::get_object_instance($this->notice_id);
            $this->memo_notice_mere = [];
            $this->memo_notice_mere_relation_type = [];
            foreach ($notice_relations->get_parents() as $relation_type => $relations) {
                foreach ($relations as $rank => $relation) {
                    $this->memo_notice_mere[$relation_type."_".$rank] = $relation->get_linked_notice();
                    $this->memo_notice_mere_relation_type[$relation_type."_".$rank] = $relation_type;
                }
            }
        }
        return $this->memo_notice_mere;
    }

    public function get_memo_notice_mere_relation_type()
    {
        if (!isset($this->memo_notice_mere_relation_type)) {
            $this->get_memo_notice_mere();
        }
        return $this->memo_notice_mere_relation_type;
    }

    //les notices filles
    public function get_memo_notice_fille()
    {
        if (!isset($this->memo_notice_fille)) {
            //Notices liées, relations entre notices
            $notice_relations = notice_relations_collection::get_object_instance($this->notice_id);
            $this->memo_notice_fille = [];
            $this->memo_notice_fille_relation_type = [];
            foreach ($notice_relations->get_childs() as $relation_type => $relations) {
                foreach ($relations as $rank => $relation) {
                    $this->memo_notice_fille[$relation_type."_".$rank] = $relation->get_linked_notice();
                    $this->memo_notice_fille_relation_type[$relation_type."_".$rank] = $relation_type;
                }
            }
        }
        return $this->memo_notice_fille;
    }

    public function get_memo_notice_fille_relation_type()
    {
        if (!isset($this->memo_notice_fille_relation_type)) {
            $this->get_memo_notice_fille();
        }
        return $this->memo_notice_fille_relation_type;
    }

    //les notices horizontales
    public function get_memo_notice_horizontale()
    {
        if (!isset($this->memo_notice_horizontale)) {
            //Notices liées, relations entre notices
            $notice_relations = notice_relations_collection::get_object_instance($this->notice_id);
            $this->memo_notice_horizontale = [];
            $this->memo_notice_horizontale_relation_type = [];
            foreach ($notice_relations->get_pairs() as $relation_type => $relations) {
                foreach ($relations as $rank => $relation) {
                    $this->memo_notice_horizontale[$relation_type."_".$rank] = $relation->get_linked_notice();
                    $this->memo_notice_horizontale_relation_type[$relation_type."_".$rank] = $relation_type;
                }
            }
        }
        return $this->memo_notice_horizontale;
    }

    public function get_memo_notice_horizontale_relation_type()
    {
        if (!isset($this->memo_notice_horizontale_relation_type)) {
            $this->get_memo_notice_horizontale();
        }
        return $this->memo_notice_horizontale_relation_type;
    }

    // liens vers les périodiques pour les notices d'article
    public function get_memo_notice_article()
    {
        if (!isset($this->memo_notice_article)) {
            $this->memo_notice_article = [];
            $req_perio_link = "SELECT notice_id, tit1, code from bulletins,analysis,notices WHERE bulletin_notice=notice_id and bulletin_id=analysis_bulletin and analysis_notice=".$this->notice_id;
            $result_perio_link = pmb_mysql_query($req_perio_link);
            while(($notice_perio_link = pmb_mysql_fetch_object($result_perio_link))) {
                $this->memo_notice_article[] = $notice_perio_link->notice_id;
            }
        }
        return $this->memo_notice_article;
    }

    // bulletinage pour les notices de pério
    public function get_memo_bulletinage()
    {
        if (!isset($this->memo_bulletinage)) {
            $this->memo_bulletinage = [];
            $req_bulletinage = "SELECT bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, bulletin_numero from bulletins, notices WHERE bulletin_notice = notice_id AND notice_id=".$this->notice_id;
            $result_bulletinage = pmb_mysql_query($req_bulletinage);
            while(($notice_bulletinage = pmb_mysql_fetch_object($result_bulletinage))) {
                $this->memo_bulletinage[] = $notice_bulletinage->bulletin_id;
            }
        }
        return $this->memo_bulletinage;
    }

    // liens vers les bulletins pour les notices d'article
    public function get_memo_article_bulletinage()
    {
        if (!isset($this->memo_article_bulletinage)) {
            $this->memo_article_bulletinage = [];
            $req_bull_link = "SELECT bulletin_id, bulletin_numero, date_date, mention_date, bulletin_titre, bulletin_numero from bulletins, analysis WHERE bulletin_id=analysis_bulletin and analysis_notice=".$this->notice_id;
            $result_bull_link = pmb_mysql_query($req_bull_link);
            while(($notice_bull_link = pmb_mysql_fetch_object($result_bull_link))) {
                $this->memo_article_bulletinage[] = $notice_bull_link->bulletin_id;
            }
        }
        return $this->memo_article_bulletinage;
    }

    public function get_memo_explnum_assoc()
    {
        if (!isset($this->memo_explnum_assoc)) {
            $paramaff = [];
            $paramaff["mine_type"] = 1;
            $this->memo_explnum_assoc = show_explnum_per_notice($this->notice_id, 0, "", $paramaff);
        }
        return $this->memo_explnum_assoc;
    }

    public function get_memo_explnum_assoc_number()
    {
        if (!isset($this->memo_explnum_assoc_number)) {
            $paramaff = [];
            $paramaff["mine_type"] = 1;
            $this->memo_explnum_assoc_number = show_explnum_per_notice($this->notice_id, 0, "", $paramaff, true);
        }
        return $this->memo_explnum_assoc_number;
    }

    public function get_memo_image()
    {
        global $use_opac_url_base;

        if (!isset($this->memo_image)) {
            $this->memo_image = "";
            $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
            if ($use_opac_url_base) {
                $this->memo_url_image = $thumbnailSourcesHandler->generateSrcBase64(TYPE_NOTICE, $this->notice_id);
            } else {
                $this->memo_url_image = $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $this->notice_id);
            }
            if (!empty($this->memo_url_image)) {
                $this->memo_image = "<img class='vignetteimg align_right' src='".$this->memo_url_image."' title=\"\" hspace='4' vspace='2' style='max-width : 140px; max-height: 200px;' >";
            }
        }
        return $this->memo_image;
    }

    public function get_memo_url_image()
    {
        if (!isset($this->memo_url_image)) {
            $this->get_memo_image();
        }
        return $this->memo_url_image;
    }

    //calcul du permalink...
    public function get_permalink()
    {
        global $opac_url_base;
        global $pmb_ark_activate;

        if ($pmb_ark_activate && !empty($this->get_ark_link())) {
            return $this->get_ark_link();
        }

        if (!isset($this->permalink)) {
            if ($this->notice->niveau_biblio != "b") {
                $this->permalink = $opac_url_base."index.php?lvl=notice_display&id=".$this->notice_id;
            } else {
                $this->permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$this->bulletin_id;
            }
        }
        return $this->permalink;
    }

    public function get_ark_link()
    {
        if (empty($this->ark_link)) {
            global $pmb_ark_activate;
            if ($pmb_ark_activate) {
                if ($this->notice->niveau_biblio == 'b'&& $this->notice->niveau_hierar == '2') {
                    $arkEntity = new ArkBulletin(intval($this->bulletin_id));
                } else {
                    $arkEntity = new ArkRecord(intval($this->notice_id));
                }

                $ark = ArkModel::getArkFromEntity($arkEntity);
                $this->ark_link = $ark->getArkLink();
            }
        }
        return $this->ark_link;
    }

    //Traitement des avis
    public function get_memo_avis()
    {
        if (!isset($this->memo_avis)) {
            $avis_records = new avis_records($this->notice_id);
            $this->memo_avis = $avis_records->get_data();
        }
        return $this->memo_avis;
    }

    //Titres uniformes
    public function get_memo_tu()
    {
        if (!isset($this->memo_tu)) {
            $this->memo_tu = [];
            $requete = "select * from notices_titres_uniformes where ntu_num_notice=".$this->notice_id." order by ntu_ordre";
            $resultat = pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat)) {
                while(($tu = pmb_mysql_fetch_object($resultat))) {
                    $tu_memo = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $tu->ntu_num_tu);
                    $tu_memo->parametres_perso = [];

                    $mes_pp = new parametres_perso("tu");
                    $mes_pp->get_values($tu->ntu_num_tu);
                    $values = $mes_pp->values;
                    foreach ($values as $field_id => $vals) {
                        $tu_memo->parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["TITRE"] = $mes_pp->t_fields[$field_id]["TITRE"];
                        foreach ($vals as $value) {
                            $tu_memo->parametres_perso[$mes_pp->t_fields[$field_id]["NAME"]]["VALUE"][] = $mes_pp->get_formatted_output([$value], $field_id);
                        }
                    }

                    $this->memo_tu[] = $tu_memo;
                }
            }
        }
        return $this->memo_tu;
    }

    //statut
    public function get_memo_statut()
    {
        if (!isset($this->memo_statut)) {
            $this->memo_statut = [];
            $this->memo_statut['id_notice_statut'] = $this->notice->statut;
            $this->memo_statut['gestion_statut_libelle'] = '';
            $this->memo_statut['opac_statut_libelle'] = '';
            if ($this->memo_statut['id_notice_statut']) {
                $requete = "SELECT * FROM notice_statut WHERE id_notice_statut=".($this->memo_statut['id_notice_statut'] * 1);
                $resultat = pmb_mysql_query($requete);
                if ($resultat) {
                    $statut = pmb_mysql_fetch_object($resultat);
                    $this->memo_statut['gestion_statut_libelle'] = $statut->gestion_libelle;
                    $this->memo_statut['opac_statut_libelle'] = $statut->opac_libelle;
                }
            }
        }
        return $this->memo_statut;
    }

    public function get_info_editeur($id)
    {
        $info = [];
        if ($id) {
            $requete = "SELECT * FROM publishers WHERE ed_id=$id LIMIT 1 ";
            $result = pmb_mysql_query($requete);
            if ($result && pmb_mysql_num_rows($result)) {
                $temp = pmb_mysql_fetch_object($result);
                pmb_mysql_free_result($result);
                $id		= $temp->ed_id;
                $name		= $temp->ed_name;
                $adr1		= $temp->ed_adr1;
                $adr2		= $temp->ed_adr2;
                $cp		= $temp->ed_cp;
                $ville	= $temp->ed_ville;
                $pays		= $temp->ed_pays;
                // 				$web		= $temp->ed_web;
                // 				$ed_comment= $temp->ed_comment	;

                // Determine le lieu de publication
                $l = '';
                if ($adr1) {
                    $l = $adr1;
                }
                if ($adr2) {
                    $l = ($l == '') ? $adr2 : $l.', '.$adr2;
                }
                if ($cp) {
                    $l = ($l == '') ? $cp : $l.', '.$cp;
                }
                if ($pays) {
                    $l = ($l == '') ? $pays : $l.', '.$pays;
                }
                if ($ville) {
                    $l = ($l == '') ? $ville : $ville.' ('.$l.')';
                }
                if ($l == '') {
                    $l = '[S.l.]';
                }

                // Determine le nom de l'editeur
                if ($name) {
                    $n = $name;
                } else {
                    $n = '[S.n.]';
                }

                // Constitue l'ISBD pour le coupe lieu/editeur
                if ($l == '[S.l.]' && $n == '[S.n.]') {
                    $isbd_entry = '[S.l.&nbsp;: s.n.]';
                } else {
                    $isbd_entry = $l.'&nbsp;: '.$n;
                }
                $info['isbd_entry'] = $isbd_entry;
                $info['name'] = $name;
                $info['place'] = $l;
            }
        }
        return($info);
    }

    public function fetch_notices_parents()
    {
        $this->notices_parents = [];
        $this->get_memo_notice_mere();
        if (count($this->memo_notice_mere)) {
            foreach ($this->memo_notice_mere as $notice_id) {
                $this->notices_parents[] = new notice_info($notice_id);
            }
        }
    }

    public function fetch_notices_childs()
    {
        $this->notices_childs = [];
        $this->get_memo_notice_fille();
        if (count($this->memo_notice_fille)) {
            foreach ($this->memo_notice_fille as $notice_id) {
                $this->notices_childs[] = new notice_info($notice_id);
            }
        }
    }

    public function fetch_notices_pairs()
    {
        $this->notices_childs = [];
        $this->get_memo_notice_horizontale();
        if (count($this->memo_notice_horizontale)) {
            foreach ($this->memo_notice_horizontale as $notice_id) {
                $this->notices_childs[] = new notice_info($notice_id);
            }
        }
    }

    //Recherche des etats de collection
    public function get_memo_collstate()
    {
        if (!isset($this->memo_collstate)) {
            if (($this->niveau_biblio == 's') && ($this->niveau_hierar == 1)) {
                global $opac_sur_location_activate;

                //Traitement des exemplaires
                $this->memo_collstate = [];

                $q = "select collstate_id, id_serial, state_collections, collstate_origine, collstate_cote, collstate_archive, collstate_lacune, collstate_note, ";
                $q .= "idlocation, location_libelle, ";
                $q .= "archempla_id, archempla_libelle, ";
                $q .= "archtype_id, archtype_libelle, ";
                $q .= "archstatut_id, archstatut_opac_libelle ";
                if ($opac_sur_location_activate) {
                    $q .= ", ifnull(surloc_id,0) as surloc_id, ifnull(surloc_libelle,'') as surloc_libelle ";
                }
                $q .= "from collections_state ";
                $q .= "join docs_location on location_id=idlocation ";
                if ($opac_sur_location_activate) {
                    $q .= "left join sur_location on surloc_num=surloc_id ";
                }
                $q .= "join arch_emplacement on collstate_emplacement=archempla_id ";
                $q .= "join arch_type on collstate_type=archtype_id ";
                $q .= "join arch_statut on collstate_statut=archstatut_id ";
                $q .= "where id_serial = '".$this->notice_id."' ";
                //pour l'opac
                //$q.= "and ((archstatut_visible_opac=1 and archstatut_visible_opac_abon=0)".($_SESSION["user_code"]?" or (archstatut_visible_opac_abon=1 and archstatut_visible_opac=1)":"").")";
                $r = pmb_mysql_query($q);
                if ($r) {
                    while (($cs = pmb_mysql_fetch_object($r))) {
                        //Champs perso d'etats de collection
                        $parametres_perso = [];
                        $pp = new parametres_perso("collstate");
                        if (!$pp->no_special_fields) {
                            $pp->get_values($cs->expl_id);
                            $values = $pp->values;
                            foreach ($values as $field_id => $vals) {
                                foreach ($vals as $value) {
                                    $parametres_perso[$pp->t_fields[$field_id]["NAME"]]["TITRE"] = $pp->t_fields[$field_id]["TITRE"];
                                    $parametres_perso[$pp->t_fields[$field_id]["NAME"]]["VALUE"] = $pp->get_formatted_output([$value], $field_id);
                                }
                            }
                        }
                        $cs->parametres_perso = $parametres_perso;
                        $this->memo_collstate[] = $cs;
                    }
                }
            }
        }
        return $this->memo_collstate;
    }

    private function look_for_attribute_in_class($class, $attribute, $parameters = [])
    {
        if (is_object($class) && isset($class->{$attribute})) {
            return $class->{$attribute};
        } elseif (method_exists($class, $attribute)) {
            return call_user_func_array([$class, $attribute], $parameters);
        } elseif (method_exists($class, "get_".$attribute)) {
            return call_user_func_array([$class, "get_".$attribute], $parameters);
        } elseif (method_exists($class, "is_".$attribute)) {
            return call_user_func_array([$class, "is_".$attribute], $parameters);
        }
        return null;
    }

    public function __get($name)
    {
        return $this->look_for_attribute_in_class($this, $name);
    }
}
