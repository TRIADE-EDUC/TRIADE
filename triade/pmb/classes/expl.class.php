<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl.class.php,v 1.144.2.1.2.4 2025/05/30 12:00:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $pmb_numero_exemplaire_auto_script, $pmb_numero_exemplaire_auto, $base_transferts_popup;

// classe de gestion des exemplaires
global $base_path, $class_path, $include_path;
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/audit.class.php");
require_once($class_path."/sur_location.class.php");
require_once($class_path."/serialcirc.class.php");
require_once($class_path."/index_concept.class.php");
require_once($base_path."/catalog/expl/prix_expl.inc.php");
require_once($base_path.'/admin/convert/export.class.php');
require_once($class_path."/import/import_expl.class.php");
require_once($class_path."/pret_archive.class.php");

if(!isset($pmb_numero_exemplaire_auto)) {
    $pmb_numero_exemplaire_auto = '';
}
if (empty($base_transferts_popup)) {
    if ($pmb_numero_exemplaire_auto) {
    	if ($pmb_numero_exemplaire_auto_script && file_exists($include_path."/$pmb_numero_exemplaire_auto_script")) {
            require_once($include_path."/$pmb_numero_exemplaire_auto_script");
        }else {
            require_once($include_path."/gen_code/gen_code_exemplaire.php");
        }
    }else {
        // Utile pour les expl numériques de PNB
        require_once($include_path."/gen_code/gen_code_exemplaire.php");
    }
}

//require_once($include_path."/gen_code/gen_code_exemplaire.php");
if ( ! defined( 'EXEMPLAIRE_CLASS' ) ) {
    define( 'EXEMPLAIRE_CLASS', 1 );

    class exemplaire {

        public $expl_id = 0;
        public $cb = '';
        public $id_notice = 0;
        public $id_bulletin = 0;
        public $id_bulletin_notice = 0;
        public $id_num_notice = 0;
        public $typdoc_id = 0;
        public $typdoc = '';
        public $duree_pret = 0;
        public $cote = '';
        public $section_id = 0;
        public $section = '';
        public $statut_id = 0;
        public $statut = '';
        public $pret = 0;
        public $location_id = 0;
        public $location = '';
        public $codestat_id = 0;
        public $codestat = '';
        public $date_depot = '0000-00-00';
        public $date_retour = '0000-00-00';
        public $note = '';
        public $prix = '';
        public $owner_id = 0;
        public $lastempr = 0;
        public $last_loan_date = '0000-00-00';
        public $create_date = '0000-00-00';
        public $update_date = '0000-00-00';
        public $type_antivol="";
        public $tranfert_location_origine = 0;
        public $tranfert_statut_origine = 0;
        public $tranfert_section_origine = 0;
        public $expl_comment='';
        public $nbparts = 1;
        public $expl_retloc = 0;
        public $expl_pnb_flag = 0;

        public $sur_loc_libelle = '';
        public $sur_loc_id = 0;

        public $ajax_cote_fields = '';
        public $explr_acces_autorise="MODIF" ; // sera égal à INVIS, MODIF ou UNMOD en fonction des droits de l'utilisateur sur la localisation
        public $error = false;
        public static $digital_ids = array();
        /**
         * @var integer de l'exemplaire dont le fantome est issus
         */
        public $ref_num = 0;

        protected static $long_maxi_cb;
        protected static $long_maxi_cote;
        protected static $long_maxi_prix;
        public $transfert_location_origine;
        public $transfert_statut_origine;
        public $transfert_section_origine;

        // constructeur
        public function __construct($cb='', $id=0, $id_notice=0, $id_bulletin=0) {
            global $class_path;
            global $pmb_sur_location_activate;

            // on checke si l'exemplaire est connu
            if ($cb && !$id) $clause_where = " WHERE expl_cb like '$cb' ";

            if ( (!$cb && $id) || ($cb && $id) ) $clause_where = " WHERE expl_id = '$id' ";

            if ($cb || $id) {
                $requete = "SELECT *, section_libelle, location_libelle";
                $requete .= " FROM exemplaires LEFT JOIN docs_section ON (idsection = expl_section)
					LEFT JOIN docs_location ON (idlocation = expl_location)
					LEFT JOIN docs_type ON (idtyp_doc = expl_typdoc)";
                $requete .= $clause_where ;
                $result = @pmb_mysql_query($requete);

                if(pmb_mysql_num_rows($result)) {
                    $item = pmb_mysql_fetch_object($result);
                    $this->expl_id		= $item->expl_id;
                    $this->cb			= $item->expl_cb;
                    $this->id_notice	= $item->expl_notice;
                    $this->id_bulletin	= $item->expl_bulletin;
                    $this->typdoc_id	= $item->expl_typdoc;
                    $this->typdoc		= $item->tdoc_libelle;
                    $this->duree_pret	= $item->duree_pret;
                    $this->cote			= $item->expl_cote;
                    $this->section_id	= $item->expl_section;
                    $this->section		= $item->section_libelle;
                    $this->statut_id	= $item->expl_statut;
                    //$this->statut		= $item->statut_libelle;
                    //$this->pret		= $item->pret_flag;
                    $this->location_id	= $item->expl_location;
                    $this->location		= $item->location_libelle;
                    $this->codestat_id	= $item->expl_codestat;
                    //$this->codestat	= $item->codestat_libelle;
                    $this->date_depot 	= $item->expl_date_depot ;
                    $this->date_retour 	= $item->expl_date_retour ;
                    $this->note			= $item->expl_note;
                    $this->prix			= $item->expl_prix;
                    $this->owner_id		= $item->expl_owner;
                    $this->lastempr		= $item->expl_lastempr;
                    $this->last_loan_date =  $item->last_loan_date;
                    $this->create_date 	= $item->create_date;
                    $this->update_date 	= $item->update_date;
                    $this->type_antivol = $item->type_antivol ;
                    $this->transfert_location_origine = $item->transfert_location_origine;
                    $this->transfert_statut_origine = $item->transfert_statut_origine;
                    $this->transfert_section_origine = $item->transfert_section_origine;
                    $this->expl_comment	= $item->expl_comment;
                    $this->nbparts		= $item->expl_nbparts;
                    $this->expl_retloc	= $item->expl_retloc;
                    $this->ref_num = $item->expl_ref_num;
                    $this->expl_pnb_flag = $item->expl_pnb_flag;

                    if($pmb_sur_location_activate){
                        $sur_loc= sur_location::get_info_surloc_from_location($item->expl_location);
                        $this->sur_loc_libelle=$sur_loc->libelle;
                        $this->sur_loc_id=$sur_loc->id;
                    }
                    // visibilite des exemplaires
                    global $explr_invisible, $explr_visible_unmod, $explr_visible_mod, $pmb_droits_explr_localises ;
                    if ($pmb_droits_explr_localises) {
                        $tab_invis=explode(",",$explr_invisible);
                        $tab_unmod=explode(",",$explr_visible_unmod);

                        $as_invis = array_search($this->location_id,$tab_invis);
                        $as_unmod = array_search($this->location_id,$tab_unmod);
                        if ($as_invis!== FALSE && $as_invis!== NULL) {
                            $this->explr_acces_autorise="INVIS" ;
                        } elseif ($as_unmod!== FALSE && $as_unmod!== NULL) {
                            $this->explr_acces_autorise="UNMOD" ;
                        } else {
                            $this->explr_acces_autorise="MODIF" ;
                        }
                    } else {
                        $this->explr_acces_autorise="MODIF" ;
                    }

                } else { // rien trouvé en base
                    $this->cb = $cb;
                    $this->id_notice = intval($id_notice);
                    $this->id_bulletin = intval($id_bulletin);
                    $this->set_deflt_typdoc_id();
                    global $explr_invisible, $explr_visible_unmod, $explr_visible_mod, $pmb_droits_explr_localises ;
                    if ($pmb_droits_explr_localises) {
                        if ($explr_visible_mod) {
                            $this->explr_acces_autorise="MODIF" ;
                        } else {
                            $this->explr_acces_autorise="UNMOD" ;
                        }
                    } else {
                        $this->explr_acces_autorise="MODIF" ;
                    }
                }
            } else { // rien de fourni apparemment
                $this->cb = $cb;
                $this->id_notice = intval($id_notice);
                $this->id_bulletin = intval($id_bulletin);
                $this->set_deflt_typdoc_id();
                global $explr_invisible, $explr_visible_unmod, $explr_visible_mod, $pmb_droits_explr_localises ;
                if ($pmb_droits_explr_localises) {
                    if ($explr_visible_mod) {
                        $this->explr_acces_autorise="MODIF" ;
                    } else {
                        $this->explr_acces_autorise="UNMOD" ;
                    }
                } else {
                    $this->explr_acces_autorise="MODIF" ;
                }
            }
            if ($this->id_bulletin) {
                $qb="select bulletin_notice, num_notice from bulletins where bulletin_id='".$this->id_bulletin."' ";
                $rb=@pmb_mysql_query($qb);
                if (pmb_mysql_num_rows($rb)) {
                    $this->id_bulletin_notice=pmb_mysql_result($rb,0,0);
                    $this->id_num_notice=pmb_mysql_result($rb,0,1);
                }
            }
        }

        public function set_properties_from_form() {
            global $f_ex_cb, $f_ex_typdoc, $f_ex_cote;
            global $f_ex_section, $f_ex_statut, $f_ex_location;
            global $f_ex_cstat, $f_ex_note, $f_ex_comment;
            global $f_ex_prix, $f_ex_owner, $type_antivol, $f_ex_nbparts;

            $this->cb = $f_ex_cb;

            $this->typdoc_id = intval($f_ex_typdoc);
            $this->cote = stripslashes($f_ex_cote);
            $this->section_id = intval($f_ex_section);
            //Exception pour certains formulaires
            if(!$this->section_id) {
                global ${'f_ex_section'.$f_ex_location};
                $this->section_id = intval(${'f_ex_section'.$f_ex_location});
            }
            $this->statut_id = intval($f_ex_statut);
            $this->location_id = intval($f_ex_location);
            $this->codestat_id = intval($f_ex_cstat);
            $this->note = stripslashes($f_ex_note);
            $this->expl_comment = stripslashes($f_ex_comment);
            $this->prix = stripslashes($f_ex_prix);
            $this->owner_id = intval($f_ex_owner);
            if(isset($type_antivol)) {
            	$this->type_antivol = intval($type_antivol);
            }
            if(isset($f_ex_nbparts)) {
            	$this->nbparts = intval($f_ex_nbparts);
            }
        }

        //sauvegarde en base
		public function save($no_index = false) {
            global $thesaurus_concepts_active;

            $this->error=false;

            if ((trim($this->cb)!=='')
                && ($this->id_notice || $this->id_bulletin)
                && ($this->typdoc_id)
                && (trim($this->cote)!=='')
                && ($this->location_id)
                && ($this->section_id)
                && ($this->codestat_id)
                && ($this->owner_id)
                && ($this->statut_id)	) {

                    if ($this->expl_id) {
                        $q = "update exemplaires set ";
                    } else {
                        $q = "insert into exemplaires set ";
                    }
                    $q.= "expl_cb = '".$this->cb."', ";
                    $q.= "expl_notice = '".$this->id_notice."', ";
                    if ($this->id_notice) {
                        $q.= "expl_bulletin = '0', ";
                    } else {
                        $q.= "expl_bulletin = '".$this->id_bulletin ."', ";
                    }

                    $transfert_origine="";
                    if($this->expl_id){
                        $rqt = "SELECT id_transfert FROM transferts, transferts_demande WHERE num_transfert=id_transfert and etat_transfert=0 AND num_expl='".$this->expl_id."' " ;
                        $res = pmb_mysql_query( $rqt );
                        if (!pmb_mysql_num_rows($res)){
                            // pas de transfert en cours, on met à jour transfert_location_origine
                            $transfert_origine= ", transfert_location_origine='".$this->location_id."', transfert_statut_origine='".$this->statut_id."', transfert_section_origine='".$this->section_id."' ";
                        }
                    }else{
                        // en création
                        $transfert_origine= ", transfert_location_origine='".$this->location_id."', transfert_statut_origine='".$this->statut_id."', transfert_section_origine='".$this->section_id."' ";
                    }
                    if($this->expl_id){
                        $audit=new audit();
                        $audit->get_old_infos("SELECT expl_statut, expl_location, transfert_location_origine, transfert_statut_origine, transfert_section_origine, expl_owner FROM exemplaires WHERE expl_cb='".$this->cb."' ");
                    } else {
                        $q.= "create_date = '" . date("Y-m-d H:i:s") . "', ";
                    }

                    $q.= "expl_typdoc = '".$this->typdoc_id."', ";
                    $q.= "expl_cote = '".addslashes(trim($this->cote))."', ";
                    $q.= "expl_section = '".$this->section_id."', ";
                    $q.= "expl_statut =  '".$this->statut_id."', ";
                    $q.= "expl_location = '".$this->location_id."' $transfert_origine , ";
                    $q.= "expl_codestat = '".$this->codestat_id."', ";
                    $q.= "expl_date_depot = '".$this->date_depot."', ";
                    $q.= "expl_date_retour = '".$this->date_retour."', ";
                    $q.= "expl_note = '".addslashes($this->note)."', ";
                    $q.= "expl_prix = '".addslashes($this->prix)."', ";
                    $q.= "expl_owner = '".$this->owner_id."', ";
                    $q.= "expl_lastempr = '".$this->lastempr."', ";
                    $q.= "last_loan_date = '".$this->last_loan_date."', ";
                    $q.= "type_antivol = '".$this->type_antivol."', ";
                    $q.= "expl_comment = '".addslashes($this->expl_comment)."', ";
                    $q.= "expl_nbparts = '".$this->nbparts."', ";
                    $q.= "expl_retloc = '".$this->expl_retloc."', ";
                    $q.= "expl_ref_num= '".$this->ref_num."', ";
                    $q.= "expl_pnb_flag= '".$this->expl_pnb_flag."' ";

                    if ($this->expl_id) {
                        $q.= "where expl_id='".$this->expl_id."' ";
                    }
                    $r = pmb_mysql_query($q);
                    if ($r) {
                        if(!$this->expl_id) {
                            $this->expl_id = pmb_mysql_insert_id();
                            audit::insert_creation (AUDIT_EXPL, $this->expl_id) ;
                        } else{
                            $audit->get_new_infos("SELECT expl_statut, expl_location, transfert_location_origine, transfert_statut_origine, transfert_section_origine, expl_owner FROM exemplaires WHERE expl_cb='".$this->cb."' ");
                            $audit->save_info_modif(AUDIT_EXPL, $this->expl_id ,"expl.class.php");
                        }

                        // traitement des concepts
                        if($thesaurus_concepts_active == 1){
                            $index_concept = new index_concept($this->expl_id, TYPE_EXPL);
                            $index_concept->save();
                        }

                        //Insertion des champs personalisés
                        $p_perso=new parametres_perso("expl");
                        $p_perso->rec_fields_perso($this->expl_id);
                        // Mise a jour de la table notices_mots_global_index

							if (!$no_index) {
							    $this->index_notice();
							}

						} else {
							$this->error=true;
						}
					} else {
						$this->error=true;
					}
					return !$this->error;
		}

		public function index_notice() {
                        //On teste ici les différentes notices associées à l'exemplaire
                        //Notice de périodique
                        if($this->id_bulletin_notice){
                            notice::majNoticesMotsGlobalIndex($this->id_bulletin_notice,'expl');
                        }
                        //Notice du bulletin
                        if($this->id_num_notice){
                            notice::majNoticesMotsGlobalIndex($this->id_num_notice,'expl');
                        }
                        //Notice de monographie
                        if($this->id_notice){
                            notice::majNoticesMotsGlobalIndex($this->id_notice,'expl');
                        }
        }

        // Mise a jour de la table notices_mots_global_index pour toutes les notices en relation avec l'exemplaire
        public static function majNoticesMotsGlobalIndex($expl_id=0, $id_notice=0, $id_bulletin=0) {
			$expl_id = intval($expl_id);
			$id_notice = intval($id_notice);
			$id_bulletin = intval($id_bulletin);
			if(empty($id_notice) && empty($id_bulletin)) {
				$id_notice = static::get_expl_notice_from_id($expl_id);
				$id_bulletin = static::get_expl_bulletin_from_id($expl_id);
			}
			if($id_bulletin) {
				$req_maj="SELECT bulletin_notice,num_notice, analysis_notice FROM bulletins LEFT JOIN analysis ON analysis_bulletin=bulletin_id WHERE bulletin_id='".$id_bulletin."'";
	        	$res_maj=pmb_mysql_query($req_maj);
	        	if($res_maj && pmb_mysql_num_rows($res_maj)){
	        		$first=true;//Pour la premiere ligne de résultat on doit indexer aussi la notice de périodique et de bulletin au besoin
	        		while ( $ligne=pmb_mysql_fetch_object($res_maj) ) {
	        			if($first){
	        				if($ligne->bulletin_notice){
                                indexation_stack::push($ligne->bulletin_notice, TYPE_NOTICE, 'expl');
	        				}
	        				if($ligne->num_notice){
                                indexation_stack::push($ligne->num_notice, TYPE_NOTICE, 'expl');
	        				}
	        			}
	        			if($ligne->analysis_notice){
                            indexation_stack::push($ligne->analysis_notice, TYPE_NOTICE, 'expl');
	        			}
	        			$first=false;
	        		}
	        	}
			} else {
                indexation_stack::push($id_notice, TYPE_NOTICE, 'expl');
			}
        }

        public static function gen_antivol_selector($type_antivol='') {
            global $msg;
            global $value_deflt_antivol;

            if ($type_antivol=="") $type_antivol=$value_deflt_antivol;
            $selector = "<select name='type_antivol' id='type_antivol'>
				<option value='0' ".($type_antivol == 0 ? "selected='selected'" : "").">".$msg["type_antivol_aucun"]."</option>
				<option value='1' ".($type_antivol == 1 ? "selected='selected'" : "").">".$msg["type_antivol_magnetique"]."</option>
				<option value='2' ".($type_antivol == 2 ? "selected='selected'" : "").">".$msg["type_antivol_autre"]."</option>
			</select>";
            return $selector;
        }

        public function gen_cb() {
            $requete="DELETE from exemplaires_temp where sess not in (select SESSID from sessions)";
            pmb_mysql_query($requete);

            //Appel à la fonction de génération automatique de cb
            $code_exemplaire =init_gen_code_exemplaire($this->id_notice,$this->id_bulletin);
            do {
                $code_exemplaire = gen_code_exemplaire($this->id_notice,$this->id_bulletin,$code_exemplaire);
                $requete="select expl_cb from exemplaires WHERE expl_cb='$code_exemplaire'";
                $res0 = pmb_mysql_query($requete);
                $requete="select cb from exemplaires_temp WHERE cb='$code_exemplaire' AND sess <>'".SESSid."'";
                $res1 = pmb_mysql_query($requete);
            } while((pmb_mysql_num_rows($res0)||pmb_mysql_num_rows($res1)));

            //Memorise dans temps le cb et la session pour le cas de multi utilisateur session
            $this->cb = $code_exemplaire;
            $requete="INSERT IGNORE INTO exemplaires_temp (cb ,sess) VALUES ('$this->cb','".SESSid."')";
            pmb_mysql_query($requete);
            return $code_exemplaire;
        }

        public function fill_form (&$form, $action) {
            global $charset, $msg;
            global $pmb_antivol;
            global $antivol_form;
            global $option_num_auto;
            global $pmb_rfid_activate,$pmb_rfid_serveur_url;
            global $pmb_expl_show_dates, $pmb_expl_show_lastempr;
            global $thesaurus_concepts_active;
            global $expl_create_update_date_form, $expl_filing_return_date_form;

            if (isset($option_num_auto)) {
                $this->gen_cb();
            }

            //on compte le nombre de prets pour cet exemplaire
            if($this->expl_id) {
                $query = "select count(arc_expl_id) as nb_prets from pret_archive where arc_expl_id = ".$this->expl_id;
                $result = pmb_mysql_query($query);
                if(pmb_mysql_num_rows($result)){
                    $row = pmb_mysql_fetch_object($result);
                    $nb_prets = $row->nb_prets ;
                }else $nb_prets = 0;
                if($nb_prets){
                    //dernière date de pret pour cet exemplaire
                    $query = "select date_format(last_loan_date, '".$msg["format_date"]."') as formatted_last_loan_date from exemplaires where expl_id = ".$this->expl_id;
                    $result = pmb_mysql_query($query);
                    if(pmb_mysql_num_rows($result)){
                        $expl_pret = pmb_mysql_fetch_object($result);
                        $formatted_last_loan_date = $expl_pret->formatted_last_loan_date ;
                        $info_nb_prets=str_replace("!!nb_prets!!",$nb_prets,$msg['expl_nbprets']);
                        $query = "select count(pret_idexpl) ";
                        $query .= "from pret, empr where pret_idexpl='".$this->expl_id."' and pret_idempr=id_empr ";
                        $result = pmb_mysql_query($query);
                        if ($result && pmb_mysql_result($result,0,0)) {
                            $info_date_last = str_replace("!!date_last!!",$formatted_last_loan_date,$msg['expl_lastpret_encours']);
                        } else {
                            $info_date_last = str_replace("!!date_last!!",pret_archive::get_formatted_last_return_date($this->expl_id),$msg['expl_lastpret_retour']);
                        }
                        print $info_nb_prets." ".$info_date_last;
                    }
                }
            }

            $form = str_replace('!!action!!', $action, $form);
            if ($this->id_notice) {
                $form = str_replace('!!id!!', $this->id_notice, $form);
            } else {
                $form = str_replace('!!id!!', $this->id_bulletin, $form);
            }
            $form = str_replace('!!cb!!',   htmlentities($this->cb  , ENT_QUOTES, $charset), $form);
            $form = str_replace('!!nbparts!!',   htmlentities($this->nbparts  , ENT_QUOTES, $charset), $form);
            $form = str_replace('!!note!!', htmlentities($this->note, ENT_QUOTES, $charset), $form);
            $form = str_replace('!!comment!!', htmlentities($this->expl_comment, ENT_QUOTES, $charset), $form);
            if ($this->id_notice) {
                $form = str_replace('!!cote!!', htmlentities(prefill_cote($this->id_notice,$this->cote), ENT_QUOTES, $charset), $form);
            } else {
                $form = str_replace('!!cote!!', htmlentities(prefill_cote($this->id_bulletin_notice,$this->cote), ENT_QUOTES, $charset), $form);
            }
            if ($this->id_notice) {
                $form = str_replace('!!prix!!', htmlentities(prefill_prix($this->id_notice,$this->prix), ENT_QUOTES, $charset), $form);
            } else {
                $form = str_replace('!!prix!!', htmlentities($this->prix, ENT_QUOTES, $charset), $form);
            }

            // select "type document"
            $form = str_replace('!!type_doc!!', do_selector('docs_type', 'f_ex_typdoc', $this->typdoc_id), $form);

            // select "section"
            $form = str_replace('!!section!!', $this->do_selector(), $form);

            // select "statut"
            $form = str_replace('!!statut!!', do_selector('docs_statut', 'f_ex_statut', $this->statut_id), $form);

            // select "localisation"
            //visibilité des exemplaires
            global $explr_visible_mod, $pmb_droits_explr_localises ;
            if ($pmb_droits_explr_localises) $where_clause_explr = "idlocation in (".$explr_visible_mod.") and";
            else $where_clause_explr = "";
            $form = str_replace('!!localisation!!', gen_liste ("select distinct idlocation, location_libelle from docs_location, docsloc_section where $where_clause_explr num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'f_ex_location', "calcule_section(this);", $this->location_id, "", "","","",0), $form);

            // select "code statistique"
            $form = str_replace('!!codestat!!', do_selector('docs_codestat', 'f_ex_cstat', $this->codestat_id), $form);

            if ($pmb_antivol) {
                $antivol_form = str_replace('!!type_antivol!!', exemplaire::gen_antivol_selector($this->type_antivol), $antivol_form);
                $form = str_replace('!!antivol_form!!', $antivol_form, $form);
            } else {
                $form = str_replace('!!antivol_form!!', '', $form);
            }

            // select "owner"
            $form = str_replace('!!owner!!', do_selector('lenders', 'f_ex_owner', $this->owner_id), $form);

            //dates creation / modification
            if ($this->expl_id && ($pmb_expl_show_dates=='1' || $pmb_expl_show_dates=='3')) {
                $create_update_date_form = $expl_create_update_date_form;
                $create_update_date_form = str_replace('!!create_date!!',format_date($this->create_date),$create_update_date_form);
                $create_update_date_form = str_replace('!!update_date!!',format_date($this->update_date),$create_update_date_form);
                $form = str_replace('!!create_update_date_form!!',$create_update_date_form,$form);
            } else {
                $form = str_replace('!!create_update_date_form!!','',$form);
            }

            //dates dépôt / retour
            if ($this->expl_id && ($pmb_expl_show_dates=='2' || $pmb_expl_show_dates=='3')) {
                $filing_return_date_form = $expl_filing_return_date_form;
                $filing_return_date_form = str_replace('!!filing_date!!',format_date($this->date_depot),$filing_return_date_form);
                $filing_return_date_form = str_replace('!!return_date!!',format_date($this->date_retour),$filing_return_date_form);
                $form = str_replace('!!filing_return_date_form!!',$filing_return_date_form,$form);
            } else {
                $form = str_replace('!!filing_return_date_form!!','',$form);
            }

            // Indexation concept
            if($thesaurus_concepts_active == 1){
                $index_concept = new index_concept($this->expl_id, TYPE_EXPL);
                $form = str_replace('<!-- index_concept_form -->', $index_concept->get_form("expl"), $form);
            }

            $perso = '';
            $p_perso=new parametres_perso("expl");
            if (!$p_perso->no_special_fields) {
                $perso_=$p_perso->show_editable_fields($this->expl_id);
                for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
                    if(($i == count($perso_["FIELDS"])-1) && ($i%2 == 0)) $element_class = 'row';
                    else $element_class = 'colonne2';
                    $p=$perso_["FIELDS"][$i];
                    $perso.="<div id='el9Child_".$p["ID"]."' class='".$element_class."' movable='yes' title=\"".htmlentities($p["TITRE"], ENT_QUOTES, $charset)."\">
							<label for='".$p["NAME"]."' class='etiquette'>".$p["TITRE"]." </label>".$p["COMMENT_DISPLAY"]."
							<div class='row'>".$p["AFF"]."</div>
						</div>\n";
                }
                $perso=$perso_["CHECK_SCRIPTS"]."\n".$perso;
            } else {
                $perso="\n<script>function check_form() { return true; }</script>\n";
            }
            $form = str_replace("!!champs_perso!!",$perso,$form);

            if($this->id_bulletin) {
                // circulation des périodique
                $perio_circ_tpl="";
                $in_circ=0;
                if($this->expl_id){
                    $req = "select * from serialcirc_expl where num_serialcirc_expl_id=".$this->expl_id;
                    $res_in_circ = pmb_mysql_query($req);
                    if(pmb_mysql_num_rows($res_in_circ)){
                        $in_circ=1;
                        $perio_circ_tpl="<label class='etiquette'>".$msg['serialcirc_expl_in_circ']."</label>";
                    }
                }
                if(!$in_circ){
                    $req = "select * from abts_abts, bulletins, serialcirc where abts_abts.num_notice =bulletin_notice and  bulletin_id=".$this->id_bulletin." and num_serialcirc_abt=abt_id order by abt_name";
                    $res_circ = pmb_mysql_query($req);
                    if($nb=pmb_mysql_num_rows($res_circ)){
                        $perio_circ_tpl="<input type='checkbox' name='serial_circ_add' value='1'> ".$msg['serialcirc_add_expl'];
                        if($nb>1){
                            $perio_circ_tpl.="<select name='abt_id'>";
                        }
                        while($circ = pmb_mysql_fetch_object($res_circ)){
                            if($nb==1){
                                $perio_circ_tpl.="<input type='hidden' name='abt_id' value='".$circ->abt_id."' >";
                                break;
                            }
                            $perio_circ_tpl.="<option value='".$circ->abt_id."'> ".htmlentities($circ->abt_name,ENT_QUOTES,$charset)."</option>";
                        }
                        if($nb>1){
                            $perio_circ_tpl.="</select>";
                        }
                    }
                }
                $form = str_replace("!!perio_circ_tpl!!",$perio_circ_tpl,$form);
            } else {
                $form = str_replace("!!perio_circ_tpl!!",'',$form);
            }

            if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url) {
                $form = str_replace('!!questionrfid!!', "if(script_rfid_encode()==false) return false;", $form);
            } else {
                $form = str_replace('!!questionrfid!!', '', $form);
            }

            // boutons
            $modifier = "";
            $dupliquer = "";
            $supprimer = "";
            if ($this->explr_acces_autorise=="MODIF") {
                if($this->expl_id) {
                    if($this->id_notice) {
                        $delete_msg = $msg[314]." ?";
                        $delete_action = "./catalog.php?categ=del_expl&id=".$this->id_notice."&cb=".urlencode($this->cb)."&expl_id=".$this->expl_id;
                        $duplicate_action = "./catalog.php?categ=dupl_expl&id=".$this->id_notice."&cb=".urlencode($this->cb)."&expl_id=".$this->expl_id;
                    } else {
                        $delete_msg = $msg['confirm_suppr_serial_expl'];
                        $delete_action = "./catalog.php?categ=serials&sub=bulletinage&action=expl_delete&bul_id=".$this->id_bulletin."&expl_id=".$this->expl_id;
                        $duplicate_action = "./catalog.php?categ=serials&sub=bulletinage&action=dupl_expl&bul_id=".$this->id_bulletin."&expl_id=".$this->expl_id;
                    }
                    $supprimer = "
					<script type=\"text/javascript\">
						function confirm_delete() {
							result = confirm(\"".$delete_msg."\");
							if(result) document.location = \"".$delete_action."\";
							else unload_on();
						}
					</script>
					<input type='button' class='bouton' value=\"{$msg['63']}\" name='del_ex' id='del_ex' onClick=\"unload_off();confirm_delete();\" />";
                    $dupliquer = "&nbsp;<input type='button' class='bouton' value=\"".$msg['dupl_expl_bt']."\" name='dupl_ex' id='dupl_ex' onClick=\"unload_off();document.location='".$duplicate_action."' ; \" />";
                }
                $modifier = "<input type='submit' class='bouton' value=' $msg[77] ' onClick=\"unload_off();return test_form(this.form);\" />";
            }

            $form = str_replace('!!supprimer!!', $supprimer, $form);
            $form = str_replace('!!dupliquer!!', $dupliquer, $form);
            $form = str_replace('!!modifier!!', $modifier, $form);

            if($this->id_notice) {
                //Remplissage ajax de la cote
                global $pmb_prefill_cote_ajax;
                if($pmb_prefill_cote_ajax)
                    $expl_ajax_cote=" completion='expl_cote' listfield='".$this->ajax_cote_fields.",f_ex_cb,f_ex_typdoc,f_ex_location,f_ex_owner,f_ex_statut,f_ex_cstat".($pmb_antivol>0 ? ",type_antivol":"")."' ";
                    else $expl_ajax_cote="";
                    $form = str_replace("!!expl_ajax_cote!!",$expl_ajax_cote,$form);
            }

            // zone du dernier emrunteur
            $last_pret = "";
            if ($pmb_expl_show_lastempr && $this->lastempr) {
                $lastempr = new emprunteur($this->lastempr, '', FALSE, 0) ;
                $last_pret = "<hr /><div class='row'><b>$msg[expl_lastempr] </b>";
                $link = "<a href='./circ.php?categ=pret&form_cb=".rawurlencode($lastempr->cb)."'>";
                $last_pret .= $link.$lastempr->prenom.' '.$lastempr->nom.' ('.$lastempr->cb.')</a>';
                $last_pret .= "</div>";
            }

            // zone de l'emprunteur
            $query = "select empr_cb, empr_nom, empr_prenom, ";
            $query .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
            $query .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
            $query .= " IF(pret_retour>sysdate(),0,1) as retard " ;
            $query .= " from pret, empr where pret_idexpl='".$this->expl_id."' and pret_idempr=id_empr ";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                $pret = pmb_mysql_fetch_object($result);
                $last_pret .= "<hr /><div class='row'><b>$msg[380]</b> ";
                $link = "<a href='./circ.php?categ=pret&form_cb=".rawurlencode($pret->empr_cb)."'>";
                $last_pret .= $link.$pret->empr_prenom.' '.$pret->empr_nom.' ('.$pret->empr_cb.')</a>';
                $last_pret .= "&nbsp;{$msg[381]}&nbsp;".$pret->aff_pret_date;
                $last_pret .= ".&nbsp;{$msg[358]}&nbsp;".$pret->aff_pret_retour.".";
                $last_pret .= "</div>";
            }
            $form = $form.$last_pret;
        }

        public function expl_form ($action='', $annuler='') {
            global $expl_form;
            global $pmb_type_audit;

            if ($action && $this->id_notice) {
                $action .= '&id='.$this->id_notice.'&org_cb='.urlencode($this->cb);
            }
            if ($action && $this->id_bulletin) {
                $action .= '&expl_bulletin='.$this->id_bulletin.'&org_cb='.urlencode($this->cb);
            }
            if($this->id_bulletin) {
                $expl_form = str_replace('!!grid_type!!', 'expl_bulletin', $expl_form);
            } else {
                $expl_form = str_replace('!!grid_type!!', 'expl', $expl_form);
            }
            $this->fill_form ($expl_form, $action);

            if ($pmb_type_audit && $this->expl_id) $link_audit =  audit::get_dialog_button($this->expl_id, 2);
            else $link_audit = "" ;

            $expl_form = str_replace('!!link_audit!!', $link_audit, $expl_form);
            $expl_form = str_replace('!!id_form!!', md5(microtime()), $expl_form);

            // affichage
            return $expl_form;
        }

        public function zexpl_form($action) {
            global $expl_form;

            $this->fill_form ($expl_form, $action);

            $expl_form = str_replace('!!supprimer!!', "", $expl_form);
            $expl_form = str_replace('!!link_audit!!', "", $expl_form);

            // affichage
            print "<span class='zexpl_form'>".pmb_bidi($expl_form)."</span>";
        }

        public function get_note_content_form() {
            $interface_content_form = new interface_content_form();
            $interface_content_form->add_element('message_content', 'expl_message')
            ->add_textarea_node($this->note)
            ->set_class('saisie-80em');
            $interface_content_form->add_element('f_ex_comment', 'expl_zone_comment')
            ->add_textarea_node($this->expl_comment)
            ->set_class('saisie-80em');
            return $interface_content_form->get_display();
        }
        
        public function get_note_form() {
            $interface_circ_note_ex_form = new interface_circ_note_ex_form('msg_form');
            if($this->id_notice) {
                $notice = new mono_display($this->id_notice, 0);
                $interface_circ_note_ex_form->set_label($notice->header);
            } elseif ($this->id_bulletin) {
                $notice = new bulletinage_display($this->id_bulletin);
                $interface_circ_note_ex_form->set_label($notice->display);
            }
            $interface_circ_note_ex_form->set_object_id($this->expl_id)
            ->set_content_form($this->get_note_content_form())
            ->set_field_focus('message_content');
            return $interface_circ_note_ex_form->get_display();
        }
        
        public static function set_note_in_database($id, $expl_note='', $expl_comment='') {
            $id = intval($id);
            $query = "UPDATE exemplaires SET expl_note='".addslashes($expl_note)."', expl_comment='".addslashes($expl_comment)."' WHERE expl_id=$id ";
            pmb_mysql_query($query);
        }
        // ----------------------------------------------------------------------------
        //	fonction do_selector qui génère des combo_box avec tout ce qu'il faut
        // ----------------------------------------------------------------------------
        public function do_selector($reset_add_idem = false, $selected='') {
            global $charset;
            global $msg;
            global $deflt_docs_section;
            global $deflt_docs_location;

            if (!$this->section_id) $this->section_id=$deflt_docs_section ;
            if (!$this->location_id) $this->location_id=$deflt_docs_location;

            $selector = '';
            $rqtloc = "SELECT idlocation FROM docs_location order by location_libelle";
            $resloc = pmb_mysql_query($rqtloc);
            while ($loc=pmb_mysql_fetch_object($resloc)) {
                $requete = "SELECT idsection, section_libelle FROM docs_section, docsloc_section where idsection=num_section and num_location='$loc->idlocation' order by section_libelle";
                $result = pmb_mysql_query($requete);
                $nbr_lignes = pmb_mysql_num_rows($result);
                if ($nbr_lignes) {
                    if ($loc->idlocation==$this->location_id) $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:block\">\r\n";
                    else $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:none\">\r\n";
                    $selector .= "<select name='f_ex_section".$loc->idlocation."' id='f_ex_section".$loc->idlocation."'>";
                    if ($reset_add_idem) {
                        if(!$selected) {
                            $selector.= "<option value='0' SELECTED>".$msg["reset_same_section"]."</option>";
                        }else {
                            $selector.= "<option value='0' >".$msg["reset_same_section"]."</option>";
                        }
                    }
                    while ($line = pmb_mysql_fetch_row($result)) {
                        $selector .= "<option value='$line[0]'";
                        if (!$reset_add_idem) {
                            $selector.= ($line[0] == $this->section_id ? ' SELECTED' : '');
                        }else{
                            $selector.= ($line[0] == $selected ? ' SELECTED' : '');
                        }
                        $selector.= '>'.htmlentities($line[1],ENT_QUOTES, $charset).'</option>';
                    }
                    $selector.= '</select></div>';
                    $this->ajax_cote_fields .= ($this->ajax_cote_fields != '' ? ",f_ex_section".$loc->idlocation : "f_ex_section".$loc->idlocation);
                }
            }
            return $selector;
        }


        // ---------------------------------------------------------------
        //		import() : import d'un exemplaire
        // ---------------------------------------------------------------
        // fonction d'import d'exemplaire (membre de la classe 'exemplaire');
        public static function import($data) {

            global $msg;

            // cette méthode prend en entrée un tableau constitué des informations exemplaires suivantes :
            //	$data['cb']
            //	$data['notice']
            //  $data['bulletin']
            //	$data['typdoc']
            //	$data['cote']
            //	$data['section']
            //	$data['statut']
            //	$data['location']
            //	$data['codestat']
            //	$data['creation']
            //	$data['modif']
            //	$data['note']
            //	$data['prix']
            //	$data['expl_owner']
            //	$data['expl_pnb_flag']
            //	$data['cote_mandatory'] cote obligatoire = 1, non obligatoire = 0
            //	$data['quoi_faire'] que faire de cet exemplaire :
            //		0 : supprimer, 1 ou vide : Mettre à jour ou ajouter, 2 : ajouter si possible, sinon rien.

            if(!defined('SESSid')) {
                define('SESSid', 0);
            }

            // check sur le type de  la variable passée en paramètre
            if (!is_array($data) || empty($data)) {
                // si ce n'est pas un tableau ou un tableau vide, on retourne 0
            	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '". addslashes($msg[544]) . "') ") ;
                return 0;
            }

            if (!isset($data['quoi_faire']) || (string)$data['quoi_faire']=="" || pmb_strlen($data['quoi_faire']) > 1) {
                $data['quoi_faire']="2" ;
            }
            if(!isset($data['cb'])) {
                $data['cb'] = '';
            }
            if ((string)$data['quoi_faire']=="0") {
                $sql_del = "delete from exemplaires where expl_cb='".addslashes($data['cb'])."' " ;
                pmb_mysql_query($sql_del) ;
                return -1 ;
            }

            // check sur les éléments du tableau (cb, cote, notice, typdoc, section, statut, location, codestat, owner sont requis).
            if(!isset(static::$long_maxi_cb)) {
                static::$long_maxi_cb = pmb_mysql_field_len(pmb_mysql_query("SELECT expl_cb FROM exemplaires limit 1"),0);
            }
            $data['cb'] = rtrim(substr(trim($data['cb']),0,static::$long_maxi_cb));

            if(!isset(static::$long_maxi_cote)) {
                static::$long_maxi_cote = pmb_mysql_field_len(pmb_mysql_query("SELECT expl_cote FROM exemplaires limit 1"),0);
            }
            if (!isset($data['cote'])) {
                $data['cote'] = "";
            }
            $data['cote'] = rtrim(substr(trim($data['cote']),0,static::$long_maxi_cote));
            if (!isset($data['comment'])) {
            	$data['comment'] = "";
            }
            if(!isset(static::$long_maxi_prix)) {
                static::$long_maxi_prix = pmb_mysql_field_len(pmb_mysql_query("SELECT expl_prix FROM exemplaires limit 1"),0);
            }
            if (!isset($data['prix'])) {
                $data['prix'] = "";
            }
            $data['prix'] = rtrim(substr(trim($data['prix']),0,static::$long_maxi_prix));

            if (!isset($data['expl_owner'])) {
                $data['expl_owner'] = "";
            }
            if ($data['expl_owner']=="") {
                pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', 'No lender given') ") ;
                return 0;
            }

            if($data['cb']=="") {
            	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '". addslashes($msg[545]) ."') ") ;
                return 0;
            }

            if (!isset($data['cote_mandatory'])) {
                $data['cote_mandatory'] = 0;
            }
            if ($data['cote']=="") {
                if ($data['cote_mandatory']==1) {
                	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '". addslashes($msg[546])."') ") ;
                    return 0;
                } else {
                	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".addslashes($msg[567])."') ") ;
                }
            }

            if (!isset($data['notice'])) {
            	$data['notice'] = 0;
            } else {
            	$data['notice'] = intval($data['notice']);
            }

            if(!isset($data['bulletin'])) {
            	$data['bulletin'] = 0;
            } else {
            	$data['bulletin'] = intval($data['bulletin']);
            }

            if($data['notice']==0 && $data['bulletin']==0) {
            	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".addslashes($msg[547])."') ") ;
            	return 0;
            }

            if (1 == pmb_mysql_num_rows(pmb_mysql_query('SELECT 1 FROM notices WHERE niveau_biblio = "a" AND niveau_hierar = 2 AND notice_id =' . $data['notice'] .' LIMIT 1'))) {
            	// On ne peut pas lie un exemplaire à un article
            	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".addslashes($msg['expl_import_on_article'])."') ") ;
            	return 0;
            }

            if ($data['notice']) {
            	$result = pmb_mysql_query('SELECT bulletin_id FROM bulletins WHERE num_notice = ' . $data['notice'] .' LIMIT 1');
            	if (1 == pmb_mysql_num_rows($result)) {
            		$bulletin_id = pmb_mysql_result($result, 0);
            		if (!$data['bulletin'] || $data['bulletin'] == $bulletin_id) {
            			$data['bulletin'] = $bulletin_id;
            			$data['notice'] = 0;
            		} else {
            			// Le bulletin doit etre cree pour etre lie a l'exemplaire
            			// On ne fait pas de lien entre la notice de bulletin et l'exemplaire
            			return 0;
            		}
            	}
            }

            if(!isset($data['typdoc'])) {
                $data['typdoc'] = 0;
            }
            if($data['typdoc']==0) {
            	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".addslashes($msg[548])."') ") ;
                return 0;
            }

            if(!isset($data['section'])) {
                $data['section'] = 0;
            }
            if($data['section']==0) {
            	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".addslashes($msg[549])."') ") ;
                return 0;
            }

            if(!isset($data['statut'])) {
                $data['statut'] = 0;
            }
            if($data['statut']==0) {
            	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".addslashes($msg[550])."') ") ;
                return 0;
            }

            if(!isset($data['location'])) {
                $data['location'] = 0;
            }
            if($data['location']==0) {
            	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".addslashes($msg[551])."') ") ;
                return 0;
            }

            if(!isset($data['codestat'])) {
                $data['codestat'] = 0;
            }
            if($data['codestat']==0) {
            	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '".addslashes($msg[552])."') ") ;
                return 0;
            }
            if (!isset($data['type_antivol'])) {
                $data['type_antivol'] = "";
            }
            if($data['type_antivol']=="") {
                $data['type_antivol']="1";
            }

            // préparation de la requête
            $key0 = addslashes($data['cb']);
            $key1 = addslashes($data['cote']);

            /* vérification que l'exemplaire existe ou pas */
            $exe = new stdClass();
            $query = "SELECT expl_id FROM exemplaires WHERE expl_cb='{$key0}' LIMIT 1 ";
            $result = pmb_mysql_query($query);
            if(!$result) {
                die("can't SELECT exemplaires ".$query);
            }
            if(pmb_mysql_num_rows($result)) {
                $exe  = pmb_mysql_fetch_object($result);
            }

            if (!isset($data['date_depot'])) {
                $data['date_depot'] = "";
            }
            if (!$data['date_depot'] || !preg_match('`^\d{4}\-\d{2}\-\d{2}$`',$data['date_depot'])) {
                $data['date_depot']="sysdate()";
            } else {
                $data['date_depot']="'".$data['date_depot']."'" ;
            }

            if (!isset($data['date_retour'])) {
                $data['date_retour'] = "";
            }
            if (!$data['date_retour'] || !preg_match('`^\d{4}\-\d{2}\-\d{2}$`',$data['date_retour'])) {
                $data['date_retour']="sysdate()";
            } else {
                $data['date_retour']="'".$data['date_retour']."'" ;
            }

            // l'exemplaire existe et on ne pouvait que l'ajouter, on retourne l'ID
            if (!empty($exe->expl_id) && (string)$data['quoi_faire']=="2") {
            	pmb_mysql_query("insert into error_log (error_origin, error_text) values ('expl_".addslashes(SESSid).".class', '". addslashes($msg[553].$data['cb']) ."') ") ;
                return $exe->expl_id;
            }

            // l'exemplaire existe et on doit le mettre à jour
            if (!empty($exe->expl_id) && (string)$data['quoi_faire']=="1") {
                $sql_a_faire = "update exemplaires SET " ;
                $sql_a_faire_suite = " where expl_cb='".addslashes($data['cb'])."' " ;
            }

            // l'exemplaire n'existe pas : on doit le créer
            if (empty($exe->expl_id)) {
                $sql_a_faire = "insert into exemplaires SET " ;
                $sql_a_faire_suite = "" ;
            }

            if (empty($data['expl_pnb_flag'])) {
                $data['expl_pnb_flag'] = 0;
            }

            $query  = $sql_a_faire ;
            $query .= "expl_cb='".$key0."', ";
            $query .= "expl_notice='".$data['notice']."', ";
            $query .= "expl_bulletin='".$data['bulletin']."', ";
            $query .= "expl_typdoc='".$data['typdoc']."', ";
            $query .= "expl_cote=trim('".$key1."'), ";
            $query .= "expl_section='".$data['section']."', ";
            $query .= "expl_statut='".$data['statut']."', ";
            $query .= "expl_location='".$data['location']."', ";
            $query .= "expl_codestat='".$data['codestat']."', ";
            $query .= "expl_note='".addslashes($data['note'])."', ";
            $query .= "expl_comment='".addslashes($data['comment'])."', ";
            $query .= "expl_prix='".addslashes($data['prix'])."', ";
            $query .= "expl_owner='".$data['expl_owner']."', ";
            $query .= "expl_date_depot=".$data['date_depot'].", ";
            $query .= "expl_date_retour=".$data['date_retour'].", ";

            $query .= "transfert_location_origine = ".$data['location'].", ";
            $query .= "transfert_statut_origine=".$data['statut'].", ";
            $query .= "transfert_section_origine=".$data['section'].", ";
            $query .= "expl_pnb_flag='".$data['expl_pnb_flag']."', ";
            //$query .= "type_antivol=".$data['type_antivol'].", ";
            if(isset($data['creation']) && $data['creation']) {
                $query .= "create_date='".$data['creation']."'";
            }else{
                $query .= "create_date=sysdate() ";
            }

            $query .= $sql_a_faire_suite ;
            $result = pmb_mysql_query($query);
            if(!$result) {
                die("can't INSERT into exemplaires ".$query);
            }

            if (empty($exe->expl_id)) {
                $exe->expl_id = pmb_mysql_insert_id();
                audit::insert_creation(AUDIT_EXPL, $exe->expl_id);
            } else {
                $sql_id = pmb_mysql_query("select expl_id from exemplaires where expl_cb='".addslashes($data['cb'])."' ") ;
                $exe  = pmb_mysql_fetch_object($sql_id);
                audit::insert_modif(AUDIT_EXPL,$exe->expl_id);
            }

            // Mise a jour de la table notices_mots_global_index pour toutes les notices en relation avec l'exemplaire
            static::majNoticesMotsGlobalIndex($exe->expl_id);

            // Imports > Exemplaires UNIMARC
            global $import_explajtEXPL, $import_expl_caddie_EXPL;
            if(!empty($import_explajtEXPL) && !empty($import_expl_caddie_EXPL)) {
                import_expl::add_object_caddie($exe->expl_id, 'EXPL', $import_expl_caddie_EXPL);
            }

            return $exe->expl_id;
        }


        // Suppression
        public static function del_expl($id=0) {
            global $explr_visible_mod, $pmb_droits_explr_localises;
            global $pmb_archive_warehouse;

            $id = intval($id);
            $sql_pret = pmb_mysql_query("select 1 from pret where pret_idexpl ='$id' ") ;
            if (pmb_mysql_num_rows($sql_pret)) return 0 ;

            // visibilite de l'exemplaire
            if ($pmb_droits_explr_localises) {
                $query = "select expl_location from exemplaires where expl_id='".$id."'";
                $result = pmb_mysql_query($query);
                $location_id = pmb_mysql_result($result,0,0);
                $tab_mod=explode(",",$explr_visible_mod);
                $as_modif = array_search($location_id,$tab_mod);
                if ($as_modif===false) return 0 ;
            }

            $requete = "select idcaddie FROM caddie where type='EXPL' ";
            $result = pmb_mysql_query($requete);
            for($i=0;$i<pmb_mysql_num_rows($result);$i++) {
                $temp=pmb_mysql_fetch_object($result);
                $requete_suppr = "delete from caddie_content where caddie_id='".$temp->idcaddie."' and object_id='".$id."' ";
                pmb_mysql_query($requete_suppr);
            }
            audit::delete_audit (AUDIT_EXPL, $id) ;
            $p_perso=new parametres_perso("expl");
            $p_perso->delete_values($id);

            // nettoyage transfert
            $requete_suppr = "delete from transferts_demande where num_expl='$id'";
            pmb_mysql_query($requete_suppr);

            // nettoyage circulation des périodiques
            serialcirc::delete_expl($id);

            //archivage
            if ($pmb_archive_warehouse) {
                static::save_to_agnostic_warehouse(array(0=>$id),$pmb_archive_warehouse);
            }

            // nettoyage doc. à ranger
            $requete_suppr = "delete from resa_ranger where resa_cb in (select expl_cb from exemplaires where expl_id='".$id."') ";
            pmb_mysql_query($requete_suppr);

            // nettoyage indexation concepts
            $index_concept = new index_concept($id, TYPE_EXPL);
            $index_concept->delete();

            // Suppression exemplaire prêt numérique
            pmb_mysql_query("DELETE FROM pnb_orders_expl WHERE pnb_order_expl_num='$id' ") ;

            pmb_mysql_query("delete from exemplaires where expl_id='$id' ") ;

            return 1 ;
        }

        //sauvegarde un ensemble de notices dans un entrepot agnostique a partir d'un tableau d'ids d'exemplaires
        public static function save_to_agnostic_warehouse($expl_ids=array(),$source_id=0,$keep_expl=1) {
            global $base_path,$class_path,$include_path;

            $source_id = intval($source_id);
            if (is_array($expl_ids) && count($expl_ids) && $source_id) {
                $export_params=array(
                    'genere_lien'=>1,
                    'notice_mere'=>1,
                    'notice_fille'=>1,
                    'mere'=>0,
                    'fille'=>0,
                    'bull_link'=>1,
                    'perio_link'=>1,
                    'art_link'=>0,
                    'bulletinage'=>0,
                    'notice_perio'=>0,
                    'notice_art'=>0,
                    'export_only_expl_ids'=> $expl_ids
                );
                $notice_ids=array();
                $bulletin_ids=array();
                $perio_ids=array();
                $q='select expl_notice,expl_bulletin,bulletin_notice from exemplaires left join bulletins on expl_bulletin=bulletin_id and expl_bulletin!=0 where expl_id in ('.implode(',',$expl_ids).')';
                $r=pmb_mysql_query($q);
                if (pmb_mysql_num_rows($r)) {
                    while($row=pmb_mysql_fetch_object($r)){
                        if($row->expl_notice) $notice_ids[]=$row->expl_notice;
                        if($row->expl_bulletin) $bulletin_ids[]=$row->expl_bulletin;
                        if($row->bulletin_notice) $perio_ids[]=$row->bulletin_notice;
                    }
                }
                if (count($notice_ids) || count($bulletin_ids)) {
                    require_once($base_path."/admin/connecteurs/in/agnostic/agnostic.class.php");
                    $conn=new agnostic($base_path.'/admin/connecteurs/in/agnostic');
                    $source_params = $conn->get_source_params($source_id);
                    $export_params['docnum']=1;
                    $export_params['docnum_rep']=$source_params['REP_UPLOAD'];
                }
                if (count($notice_ids)) {
                    $notice_ids=array_unique($notice_ids);
                    $e=new export($notice_ids);
                    $records=array();
                    do{
                        $nn = $e->get_next_notice('',array(),array(),$keep_expl,$export_params);
                        if ($e->notice) $records[] = $e->xml_array;
                    } while($nn);

                    $conn->rec_records_from_xml_array($records,$source_id);
                }
                if (count($bulletin_ids)) {
                    $bulletin_ids=array_unique($bulletin_ids);
                    $perio_ids=array_unique($perio_ids);
                    $e=new export($perio_ids);
                    $e->expl_bulletin_a_exporter=$bulletin_ids;
                    $records=array();
                    do{
                        $nn = $e->get_next_bulletin('',array(),array(),$keep_expl,$export_params);
                        if ($e->notice) $records[] = $e->xml_array;
                    } while($nn);
                    $conn->rec_records_from_xml_array($records,$source_id);
                }
            }
        }

        /**
         * Fonction de purge des exemplaires fantomes (appelée en fin de transfert)
         * @param int $expl_id Id de l'exemplaire à tester
         * @return boolean
         */
        public static function purge_ghost($expl_id){
            $rqt = "select expl_ref_num from exemplaires where expl_id = ".$expl_id." ;";
            $result = pmb_mysql_query($rqt);
            if($result){
                $parent_id = pmb_mysql_fetch_object($result);
                //Il s'agit d'un exemplaire fantome
                if($parent_id->expl_ref_num != 0){
                    self::del_expl($expl_id);
                    return true;
                }
                return false;
            }
            return false;
        }

        /**
         * Fonction de mise à jour du code-barres de l'exemplaire (avec tests)
         * @param string $old_cb Ancien code-barres
         * @param string $new_cb Nouveau code-barres
         * @return int Retourne un entier correspondant aux différents cas d'erreurs/réussite
         */
        public static function update_cb($old_cb, $new_cb){
            if(SESSrights & (CATALOGAGE_AUTH + CATAL_MODIF_CB_EXPL_AUTH)){
                $requete="select expl_cb from exemplaires WHERE expl_cb='".$new_cb."'";
                $result = pmb_mysql_query($requete);

                $requete="select cb from exemplaires_temp WHERE cb='".$new_cb."' AND sess <>'".SESSid."'";
                $result_tempo = pmb_mysql_query($requete);

                if(!pmb_mysql_num_rows($result) && !pmb_mysql_num_rows($result_tempo)){//Code-barres non existant en base
                    $requete = "update exemplaires set expl_cb = '".$new_cb."' where expl_cb='".$old_cb."'";
                    $result = pmb_mysql_query($requete);
                    if($result){ //La requete de mise à jour a réussi
                        return 1;
                    }
                    return 2;
                }
                //Le code-barres existe déjà en base
                return 0;
            }
            return 3;
        }

        public function get_notice_title() {
            $title = '';
            if($this->id_bulletin) {
                if($this->id_bulletin_notice) {
                    $title .= notice::get_notice_title($this->id_bulletin_notice);
                } else {
                    $title .= notice::get_notice_title($this->id_num_notice);
                }
            } else {
                $title .= notice::get_notice_title($this->id_notice);
            }
            return $title;
        }

        //renvoi le no d'exemplaire pour le tableau avec ou sans lien
        public static function get_cb_link($cb_expl) {
            global $base_path;

            $des_expl = "";
            if (SESSrights & CIRCULATION_AUTH) {
                $des_expl .= "<a href='".$base_path."/circ.php?categ=visu_ex&form_cb_expl=" . $cb_expl . "'>";
                $des_expl .= $cb_expl;
                $des_expl .= "</a>";
            } else {
                $des_expl .= $cb_expl;
            }
            return $des_expl;
        }

        public static function is_digital($id){
            if(!isset(static::$digital_ids[$id])){
                $id=intval($id);
                $query = "select pnb_order_expl_num from pnb_orders_expl where pnb_order_expl_num =".$id;
                $result = pmb_mysql_query($query);
                if(pmb_mysql_num_rows($result)){
                    static::$digital_ids[$id] = true;
                }else{
                    static::$digital_ids[$id] = false;
                }
            }
            return static::$digital_ids[$id];
        }

        public static function is_currently_borrowed($id) {
            $query = "select pret_idempr from pret where pret_idexpl=".$id." limit 1";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                return true;
            }
            return false;
        }

        // récupère l'id d'un exemplaire d'après son code barre
        public static function get_expl_id_from_cb($cb) {
            if (!$cb) return FALSE;
            $query = "select expl_id as id from exemplaires where expl_cb='".$cb."' limit 1";
            $result = pmb_mysql_query($query);
            return pmb_mysql_result($result, '0', 'id');
        }

        // récupère le code barre d'un exemplaire d'après son id
        public static function get_expl_cb_from_id($id) {
        	$id = intval($id);
        	if (!$id) return false;
        	$query ="select expl_cb from exemplaires where expl_id=$id";
        	$result = pmb_mysql_query($query);
        	return pmb_mysql_result($result, '0', 'expl_cb');
        }

        /**
         * Définition du type de support par défaut
         */
        protected function set_deflt_typdoc_id() {
            if ($this->typdoc_id) {
                return;
            }
            if ($this->id_bulletin) {
                global $deflt_serials_docs_type;
                $this->typdoc_id = $deflt_serials_docs_type;
                return;
            }
            if ($this->id_notice) {
                $notice = notice::get_notice($this->id_notice);
                switch ($notice->biblio_level) {
                    case 's' :
                    case 'b' :
                        global $deflt_serials_docs_type;
                        $this->typdoc_id = $deflt_serials_docs_type;
                        break;
                    default :
                        global $deflt_docs_type;
                        $this->typdoc_id = $deflt_docs_type;
                        break;
                }
            }
            return;
        }

        // Donne l'id de la notice par son identifiant d'expl
        public static function get_expl_notice_from_id($expl_id=0) {
        	$expl_id = intval($expl_id);
            $query = "select expl_notice, expl_bulletin from exemplaires where expl_id = ".$expl_id;
            $result = pmb_mysql_query($query);
            $row = pmb_mysql_fetch_object($result);
            if($row->expl_notice) {
                return $row->expl_notice;
            } else {
                $query = "select num_notice from bulletins where bulletin_id = ".$row->expl_bulletin;
                $result = pmb_mysql_query($query);
                return pmb_mysql_result($result, 0, 'num_notice');
            }
        }

        // Donne l'id du bulletin par son identifiant d'expl
        public static function get_expl_bulletin_from_id($expl_id=0) {
        	$expl_id = intval($expl_id);
            $query = "select expl_bulletin from exemplaires where expl_id = ".$expl_id;
            $result = pmb_mysql_query($query);
            return pmb_mysql_result($result, 0, 'expl_bulletin');
        }

        public static function get_nb_prets_from_id($expl_id=0) {
            $nb_prets = 0;
            $expl_id = intval($expl_id);
            $query = "select count(arc_expl_id) as nb_prets from pret_archive where arc_expl_id = ".$expl_id;
            $result = pmb_mysql_query($query);
            if(pmb_mysql_num_rows($result)){
                $row = pmb_mysql_fetch_object($result);
                $nb_prets = $row->nb_prets ;
            }
            return $nb_prets;
        }

        // récupère la localisation d'un exemplaire d'après son id
        public static function get_expl_location_from_id($expl_id=0) {
        	$expl_id = intval($expl_id);
        	if (!$expl_id) return FALSE;
        	$query = "select expl_location from exemplaires where expl_id='".$expl_id."'";
        	$result = pmb_mysql_query($query);
        	return pmb_mysql_result($result, '0', 'expl_location');
        }

        public static function self_renew($expl_cb, $is_self_renew_asked = 0, $check_resa = 0) {
            global $opac_pret_prolongation, $opac_pret_duree_prolongation,$pmb_pret_restriction_prolongation,$pmb_pret_nombre_prolongation, $opac_pret_prolongation_blocage,$msg;
            global $selfservice_pret_prolonge_non_msg, $charset, $pmb_utiliser_calendrier;
            global $pmb_location_reservation, $opac_resa_planning;

            $ret = array();
            $struct = array();
            $titre=$expl_cb;
            $error_message="";
            $due_date = exemplaire::sql_value_("select date_format(CURRENT_DATE(), '".$msg["format_date"]."')");
            $ok=1;
            $is_self_renew = 0;
            $ret["status"]="";
            $ret["cb"] = $expl_cb;
            if($opac_pret_prolongation){
                $prolongation=true;
                $requete="select expl_id, id_empr, expl_bulletin,expl_notice, type_antivol, empr_cb, expl_location, cpt_prolongation, retour_initial, niveau_relance, pret_date, pret_retour from exemplaires join pret on (expl_id=pret_idexpl) join empr on (pret_idempr=id_empr) where expl_cb='".addslashes($expl_cb)."'";
                $resultat=pmb_mysql_query($requete);
                if (!$resultat) {
                    $error_message=$msg["54"];
                } else {
                    $expl=pmb_mysql_fetch_object($resultat);
                    $remind = $expl->niveau_relance;
                    $expl_id=$expl->expl_id;
                    $id_empr=$expl->id_empr;
                    $cpt_prolongation = $expl->cpt_prolongation;
                    $retour_initial =  $expl->retour_initial;
                    $pret_date = $expl->pret_date;
                    $cpt_prolongation++;
                    $duree_prolongation=$opac_pret_duree_prolongation;
                    $today=exemplaire::sql_value_("SELECT CURRENT_DATE()");


                    if ($pmb_pret_restriction_prolongation==0) {
                        // Aucune limitation des prolongations
                        $prolongation=true;
                        $duree_prolongation=$opac_pret_duree_prolongation;
                    } else if($opac_pret_prolongation_blocage > 0 && $remind > 0){
                        //vérification du blocage des prolongations
                        $prolongation = false;
                    } else if ($pmb_pret_restriction_prolongation>0) {
                        $pret_nombre_prolongation=$pmb_pret_nombre_prolongation;
                        if(($pmb_pret_restriction_prolongation==1) && ($cpt_prolongation>$pret_nombre_prolongation)) {
                            // Limitation simple de la prolongation
                            $prolongation=false;
                        } else if($pmb_pret_restriction_prolongation==2) {
                            // Limitation du pret par les quotas
                            //Initialisation des quotas pour nombre de prolongations
                            $qt = new quota("PROLONG_NMBR_QUOTA");
                            //Tableau de passage des paramètres
                            $struct["READER"] = $id_empr;
                            $struct["EXPL"] = $expl_id;
                            $struct["NOTI"] = $expl->expl_notice;
                            $struct["BULL"] = $expl->expl_bulletin;
                            $pret_nombre_prolongation=$qt->get_quota_value($struct);

                            if($cpt_prolongation>$pret_nombre_prolongation) {
                                $prolongation=false;
                            } else {
                                //Initialisation des quotas la durée de prolongations
                                $qt = new quota("PROLONG_TIME_QUOTA");
                                $struct["READER"] = $id_empr;
                                $struct["EXPL"] = $expl_id;
                                $struct["NOTI"] = $expl->expl_notice;
                                $struct["BULL"] = $expl->expl_bulletin;
                                $duree_prolongation=$qt->get_quota_value($struct);
                            }
                        } // fin if gestion par quotas
                    }


                    if ($prolongation) {
                        $date_prolongation=exemplaire::sql_value_("SELECT DATE_ADD('$retour_initial', INTERVAL $duree_prolongation DAY)");
                        $diff=exemplaire::sql_value_("SELECT DATEDIFF('$retour_initial','$today')");
                        if($diff<-$duree_prolongation || $diff>$duree_prolongation) {
                            $prolongation=false;
                        }
                    }
                    if ($prolongation && $pmb_utiliser_calendrier) {
                        // Recherche de la nouvelle date de retour
                        $req_date_calendrier = "select date_ouverture from ouvertures where ouvert=1 and num_location='" . $expl->expl_location . "' and date_ouverture>=$date_prolongation order by date_ouverture asc";
                        $res_date_calendrier = pmb_mysql_query($req_date_calendrier);
                        while(($date_calendrier = pmb_mysql_fetch_object($res_date_calendrier))){
                            $ecart = exemplaire::sql_value_("SELECT DATEDIFF('$date_calendrier->date_ouverture','$date_prolongation')");
                            if($ecart >= 0 ){
                                $date_prolongation = $date_calendrier->date_ouverture;
                                break;
                            }
                        }
                        if($expl->pret_retour == $date_prolongation) {
                            $prolongation=false;
                        }
                    }
                    //Vérification des réservations de l'exemplaire
                    $isReserved = false;
                    if($prolongation && $check_resa){
                        $rqt_empr_location = "SELECT empr_location from empr WHERE id_empr = '$id_empr'";
                        $res_empr_location = pmb_mysql_query($rqt_empr_location);
                        $empr_location = pmb_mysql_fetch_object($res_empr_location)->empr_location;
                        //Vérification des résa planifiées
                        if($opac_resa_planning){
                            $rqt = "SELECT * FROM resa_planning WHERE resa_idnotice = '$expl->expl_notice' AND resa_idbulletin = '$expl->expl_bulletin'";
                            $res = pmb_mysql_query($rqt);
                            while($resa_plan = pmb_mysql_fetch_object($res)){

                                $dt_resa_date_debut = \DateTime::createFromFormat("Y-m-d", $resa_plan->resa_date_debut);
                                $dt_date_prolongation = \DateTime::createFromFormat("Y-m-d", $date_prolongation);
                                $dt_pret_date = \DateTime::createFromFormat("Y-m-d H:i:s", $pret_date);
                                //Si la date de début de résa planifiée est inclue dans l'intervalle du pret avec sa prolongation on ne peut prolonger
                                if(($dt_resa_date_debut->format('U') <= $dt_date_prolongation->format('U')) && ($dt_resa_date_debut->format('U') > $dt_pret_date->format('U'))){
                                    $prolongation = false;
                                    break;
                                }
                            }
                        }

                        $requete="select expl_id, id_empr, expl_bulletin,expl_notice, type_antivol, empr_cb, expl_location, cpt_prolongation, retour_initial, niveau_relance, pret_date, pret_retour from exemplaires join pret on (expl_id=pret_idexpl) join empr on (pret_idempr=id_empr) where expl_cb='".addslashes($expl_cb)."'";
                        $resultat=pmb_mysql_query($requete);
                        $expl=pmb_mysql_fetch_object($resultat);
                        // Verif s'il y a des résa et plus d'exemplaire dispo

                        // chercher le premier (par ordre de rang, donc de date de début de résa, non validé
                        $rqt = 	"SELECT count(1) FROM resa
						WHERE resa_idnotice='".$expl->expl_notice."' AND resa_idbulletin='".$expl->expl_bulletin."'
						AND resa_cb='' AND resa_date_fin='0000-00-00' ";
                        $res= pmb_mysql_query($rqt);
                        $nbresa = pmb_mysql_result($res, 0, 0);
                        if($nbresa){
                            $rqt="SELECT count(1) FROM exemplaires, docs_statut WHERE expl_statut=idstatut and pret_flag=1 and statut_visible_opac=1 AND expl_notice=".$expl->expl_notice." AND expl_bulletin=".$expl->expl_bulletin." ";
                            if ($pmb_location_reservation) {
                                $rqt.=" and expl_location in (select resa_loc from resa_loc where resa_emprloc=".$empr_location.") ";
                            }
                            $res= pmb_mysql_query($rqt);
                            $nbexpl = pmb_mysql_result($res, 0, 0);
                            $rqt="SELECT count(1) FROM pret,exemplaires WHERE pret_idexpl=expl_id AND expl_notice=".$expl->expl_notice." AND expl_bulletin=".$expl->expl_bulletin." ";
                            if($pmb_location_reservation) {
                                $rqt.=" and expl_location in (select resa_loc from resa_loc where resa_emprloc=".$empr_location.") ";
                            }
                            $res= pmb_mysql_query($rqt);
                            $nbexpl_en_pret = pmb_mysql_result($res, 0, 0);
                            if(($nbexpl-$nbexpl_en_pret) < $nbresa){
                                $prolongation = false;
                                $isReserved = true;
                            }
                        }
                    }

                    if($prolongation)	{
                        $is_self_renew = true;
                        $due_date=exemplaire::sql_value_("select date_format('".$date_prolongation."', '".$msg["format_date"]."')");
                        if (empty($is_self_renew_asked)) {
                            // Memorisation de la nouvelle date de prolongation
                            $query = "update pret set cpt_prolongation='".$cpt_prolongation."', pret_retour='".$date_prolongation."' where pret_idexpl=".$expl_id;
                            pmb_mysql_query($query);
                            $error_message = $msg['prolongation_ok'];
                            // Memorisation de la nouvelle date de prolongation dans la table d'archive
                            $res_arc=pmb_mysql_query("select pret_arc_id from pret where pret_idexpl=".$expl_id."");
                            if($res_arc && pmb_mysql_num_rows($res_arc)){
                                $query = "update pret_archive set arc_cpt_prolongation='".$cpt_prolongation."', arc_fin='".$date_prolongation."' where arc_id = ".pmb_mysql_result($res_arc,0,0);
                                pmb_mysql_query($query);
                            }
                        }
                    } else {
                        $ok=0;
                        if ($isReserved) {
                            $error_message = $msg['prolongation_pret_bloc_resa'];
                        } else {
                            $error_message = $msg['pret_plolongation_refuse'];
                        }
                    }
                }
            } else{
                $error_message="$selfservice_pret_prolonge_non_msg";
            }

            if ($charset!= "utf-8") {
                $error_message = encoding_normalize::utf8_normalize($error_message);
            }

            $ret["status"] = $ok;
            $ret["message"] = $error_message;
            $ret["is_self_renew"] = $is_self_renew;
            $ret["transaction_date"] = date("Ymd    His",time());
            if ($charset != "utf-8") {
                $ret["title"] = encoding_normalize::utf8_normalize($titre);
            } else {
                $ret["title"] = $titre;
            }
            $ret["due_date"] = $due_date;

            return $ret;
        }

        private static function sql_value_($rqt) {
            if(($result=pmb_mysql_query($rqt))) {
                if(($row = pmb_mysql_fetch_row($result)))	return $row[0];
            }
            return '';
        }

        // Donne l'id de l'exemplaire par l'identifiant de concep
        public static function get_expl_from_concept($concept = 0) {
            $tab_id = array();
            $concept_id = $concept->get_num_object();

            $query = "select num_object from index_concept where num_concept = ".$concept_id ." AND type_object=". TYPE_EXPL;
            $result = pmb_mysql_query($query);

            if (pmb_mysql_num_rows($result)) {
                while ($rows = pmb_mysql_fetch_assoc($result)) {
                    $tab_id[] = $rows['num_object'];
                }
            }

            return $tab_id;
        }

        public static function get_pattern_link() {
        	global $base_path;
        	return $base_path.'/catalog.php?categ=edit_expl&id=!!notice_id!!&cb=!!expl_cb!!&expl_id=!!expl_id!!';
        }

    } # fin de la classe exemplaire

} # fin de définition
