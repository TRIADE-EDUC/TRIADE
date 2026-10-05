<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visionneuse.class.php,v 1.23.2.1.2.2 2025/02/07 13:49:04 qvarin Exp $

use Pmb\Digitalsignature\Models\DocnumCertifier;

require_once($visionneuse_path."/api/params.interface.php");
require_once($visionneuse_path."/classes/docNum.class.php");
require_once($visionneuse_path."/includes/templates/visionneuse.tpl.php");
require_once($visionneuse_path."/classes/messages.class.php");
require_once($class_path."/explnum_licence/explnum_licence.class.php");

class visionneuse
{
    public $visionneuse_path = "";
    public $classParam;				//classe de paramétrage de la visionneuse
    public $docToRender;
    public $mimetypeClass;
    public $message;			//messages localisés

    public function __construct($driver, $visionneuse_path, $lvl = "visionneuse", $lang = "fr_FR", $tab_params = [])
    {

        $this->visionneuse_path = $visionneuse_path;
        //on instancie la bonne classe
        $this->classParam = new $driver($tab_params, $this->visionneuse_path);
        //on instancie également les messages localisés...
        $this->message = new message($this->visionneuse_path."/includes/message/$lang.xml");
        switch ($lvl) {
            case "visionneuse":
                $this->display();
                $this->classParam->cleanCache();
                break;
            case "afficheur":
                $this->classParam->getDocById($tab_params["explnum"]);
                $this->renderDoc();
                break;
            case "ajax":
                $this->exec($tab_params['method']);
                break;
        }
    }

    public function display()
    {
        global $visionneuse, $visionneuse_path;
        global $charset,$opac_url_base;
        global $opac_rgaa_active;

        if ($this->classParam->getNbDocs() > 0) {
            $this->classParam->current = intval($this->classParam->current);

            //on commence par remettre les champs cachés du formulaire...
            $hiddenFields = "";
            foreach($this->classParam->params as $key => $value) {
                //sauf les paramètres qui n'ont pas été postés, mais créés à la main ou modifiés plus tard en javascript...
                if ($key != "position" && $key != "start") {
                    $hiddenFields .= "
				<input type='hidden' name='$key' id='$key' value='".htmlentities(stripslashes($value), ENT_QUOTES, $charset)."' />";
                }
            }

            //et c'est parti
            //on s'occupe en premier du conteneur du document
            $visionneuse = str_replace("!!height!!", $this->classParam->getParam("maxY"), $visionneuse);
            //on insère le contenu propre au document;
            $docNum = new docNum($this->classParam->getCurrentDoc(), $this->classParam);
            $this->do_stat_opac($docNum->id);


            $link = "";
            if ($this->classParam->is_downloadable($docNum->id)) {
                $link = "<span id='visio_current_download'><a href='!!expnum_download!!' title='!!expnum_download_lib!! - !!expnum_download_tit!!' target='_blank'>!!expnum_download_lib!!</a></span>";
                $url_download_explnum = $this->classParam->getDocumentUrl($docNum->id);
                $link = str_replace("!!expnum_download!!", $url_download_explnum, $link);
                $link = str_replace("!!expnum_download_lib!!", htmlentities($this->message->table['download_doc'], ENT_QUOTES, $charset), $link);
                $link = str_replace("!!expnum_download_tit!!", htmlentities($this->message->table['new_tab'], ENT_QUOTES, $charset), $link);
            }

            global $pmb_digital_signature_activate;
            if ($pmb_digital_signature_activate) {
                $link .= DocnumCertifier::getJsCheck();
                $certifier = new DocnumCertifier(new explnum($docNum->id));

                if ($certifier->checkSignExists()) {
                    $link .= "
                        <span id='docnum_check_sign_" . $docNum->id . "'></span>
                        <script>
                            certifier.chksign(" . $docNum->id . ", 'docnum', true);
                        </script>
                    ";
                }
            }


            $visionneuse = str_replace("!!explnum_licence_picto!!", explnum_licence::get_explnum_licence_picto($docNum->id), $visionneuse);
            $visionneuse = str_replace("!!download!!", $link, $visionneuse);
            $docToDisplay = $docNum->fetchDisplay();
            foreach($docToDisplay as $key => $value) {
                //le cas ou le document n'est pas autorisé!
                if ($key == "doc" && $value == false) {
                    $visionneuse = str_replace("!!$key!!", "<br/><br/><br/><h4>".htmlentities($this->message->table['forbidden_resources'], ENT_QUOTES, $charset)."</h4><br/><br/>".$this->classParam->forbidden_callback(), $visionneuse);
                } elseif ($key != "post") {
                    $visionneuse = str_replace("!!$key!!", $value, $visionneuse);
                }
            }

            //maintenant le kit de survie du navigateur

            if ($opac_rgaa_active) {
                $visionneuse_paginator = "
					<form method='POST' action='' name='docnumForm' id='docnumForm'>
						!!hiddenFields!!
						<input type='hidden' id='position' name='position' value='!!position!!' />
						<div id='visio_paginator'>
							<nav id='visio_paginator_nav' role='navigation' aria-label='" . $this->message->table['paginator_label'] ."'>
								<ol>
									<li>
										<button id='visio-first' class='visio-pag-btn navbar_first navbar_page' ' type='button' onclick='visionneuseNav(\"first\");' !!button_prev!!>
											<img src='./images/!!img_first!!' id='first' alt='' />
											" . $this->message->table['paginator_first'] ."
										</button>
									</li>
									<li>
										<button id='visio-previous' class='visio-pag-btn navbar_prev navbar_page' type='button' onclick='visionneuseNav(\"previous\");' !!button_prev!!>
											<img src='./images/!!img_prev!!' id='previous' alt='' />
											" . $this->message->table['paginator_prev'] ."
										</button>
									</li>
									<li>
										<button id='visio-current' class='visio-pag-btn navbar_page' aria-current='page' title='". $this->message->table['paginator_current'] . ' ' . ($this->classParam->current + 1) ."' disabled>
											". ($this->classParam->current + 1) ."
										</button>
									</li>
									<li>
										<button id='visio-next' class='visio-pag-btn navbar_next navbar_page' type='button' onclick='visionneuseNav(\"next\");' !!button_next!!>
											<img src='./images/!!img_next!!' id='next' alt=''/>
											" . $this->message->table['paginator_next'] ."
										</button>
									</li>
									<li>
										<button id='visio-last' class='visio-pag-btn' type='button' onclick='visionneuseNav(\"last\");' !!button_next!!>
											<img src='./images/!!img_last!!' id='last' alt='' />
											" . $this->message->table['paginator_last'] ."
										</button>
									</li>
								</ol>
							</nav>
							<div id='visio-search'>!!current_position!!</div>
						</div>
					</form>
				";
                if ($this->classParam->current + 1 > 1) {
                    $visionneuse_paginator = str_replace("!!img_first!!", 'first.png', $visionneuse_paginator);
                    $visionneuse_paginator = str_replace("!!img_prev!!", 'prev.png', $visionneuse_paginator);
                    $visionneuse_paginator = str_replace("!!button_prev!!", '', $visionneuse_paginator);
                } else {
                    $visionneuse_paginator = str_replace("!!img_first!!", 'first-grey.png', $visionneuse_paginator);
                    $visionneuse_paginator = str_replace("!!img_prev!!", 'prev-grey.png', $visionneuse_paginator);
                    $visionneuse_paginator = str_replace("!!button_prev!!", 'disabled', $visionneuse_paginator);
                }

                if ($this->classParam->current + 1 < $this->classParam->getNbDocs()) {
                    $visionneuse_paginator = str_replace("!!img_last!!", 'last.png', $visionneuse_paginator);
                    $visionneuse_paginator = str_replace("!!img_next!!", 'next.png', $visionneuse_paginator);
                    $visionneuse_paginator = str_replace("!!button_next!!", '', $visionneuse_paginator);
                } else {
                    $visionneuse_paginator = str_replace("!!img_last!!", 'last-grey.png', $visionneuse_paginator);
                    $visionneuse_paginator = str_replace("!!img_next!!", 'next-grey.png', $visionneuse_paginator);
                    $visionneuse_paginator = str_replace("!!button_next!!", 'disabled', $visionneuse_paginator);
                }
                $visionneuse = str_replace("!!record_section_title!!", $this->message->table['record_section_title'], $visionneuse);
            } else {
                $visionneuse_paginator = "
					<form method='POST' action='' name='docnumForm' id='docnumForm'>
						!!hiddenFields!!
						<input type='hidden' id='position' name='position' value='!!position!!' />
						<table style='text-align:center;width:100%'>
							<tr>
								<td style='width:45%;text-align:right;'>
									<img src='".$visionneuse_path."/images/prev.gif' id='previous' style='display:!!previous_style!!' onclick='visionneuseNav(\"previous\");' />
								</td>
								<td style='width:10%;'>!!current_position!!</td>
								<td style='width:45%;text-align:left;'>
									<img src='".$visionneuse_path."/images/next.gif'  id='next' style='display:!!next_style!!' onclick='visionneuseNav(\"next\");'/>
								</td>
							</tr>
						</table>
					</form>
				";
            }

            if ($this->classParam->getNbDocs() > 1) {
                $visionneuse = str_replace("!!visio_navigator!!", $visionneuse_paginator, $visionneuse);
            } else {
                $visionneuse = str_replace("!!visio_navigator!!", '', $visionneuse);
            }
            $visionneuse = str_replace("!!hiddenFields!!", $hiddenFields, $visionneuse);

            $visionneuse = str_replace("!!position!!", $this->classParam->current, $visionneuse);

            if ($this->classParam->current == 0) {
                $visionneuse = str_replace("!!previous_style!!", "none;", $visionneuse);
                $visionneuse = str_replace("!!next_style!!", "block-inline;", $visionneuse);
            } elseif (is_countable($this->classParam->listeDocs) && $this->classParam->current == sizeof($this->classParam->listeDocs) - 1) {
                $visionneuse = str_replace("!!previous_style!!", "block-inline;", $visionneuse);
                $visionneuse = str_replace("!!next_style!!", "none;", $visionneuse);
            } else {
                $visionneuse = str_replace("!!previous_style!!", "block-inline;", $visionneuse);
                $visionneuse = str_replace("!!next_style!!", "block-inline;", $visionneuse);
            }
            $visionneuse = str_replace("!!max_pos!!", $this->classParam->getNbDocs() - 1, $visionneuse);
            $visionneuse = str_replace("!!current_position!!", "
				<label for='go_page' class='visually-hidden'>". $this->message->table['go_page_label'] ."</label>
				<input id='go_page' style='width:20%' type='text' value='".($this->classParam->current + 1)."' name='go_page' onKeyPress='if (event.keyCode == 13) visionneuseNav(\"custom\");'/>
				<button type='button' onclick='visionneuseNav(\"custom\");'>" . $this->message->table['go_new_doc'] ."</button>
				<span>(".($this->classParam->current + 1)." / ".$this->classParam->getNbDocs() .")</span>
				", $visionneuse);


            //on localise les messages
            $visionneuse = str_replace("!!close!!", $this->message->table['close'], $visionneuse);
            $visionneuse = str_replace("!!fullscreen!!", $this->message->table['fullscreen'], $visionneuse);
            $visionneuse = str_replace("!!normal!!", $this->message->table['normal'], $visionneuse);

            //tout est bon, on affiche le tout...
            print $visionneuse;
        } else {
            print htmlentities($this->message->table['nothing_to_display'], ENT_QUOTES, $charset);
        }
    }

    public function renderDoc()
    {
        $docNum = new docNum($this->classParam->getCurrentDoc(), $this->classParam);
        $docNum->setMessage($this->message);
        $docNum->render();
    }

    public function exec($method)
    {
        $docNum = new docNum($this->classParam->getCurrentDoc(), $this->classParam);
        $docNum->exec($method);
    }

    public function do_stat_opac($id_explnum)
    {
        global $pmb_logs_activate;

        if ($pmb_logs_activate && function_exists('generate_log')) {
            global $infos_explnum;

            if (!isset($infos_explnum) || !is_array($infos_explnum)) {
                $infos_explnum = [];
            }

            $rqt_explnum = "SELECT explnum_id, explnum_notice, explnum_bulletin, if (location_libelle IS null, '', location_libelle) AS location_libelle, explnum_nom, explnum_mimetype, explnum_url, explnum_extfichier, if (explnum_nomfichier IS null, '', explnum_nomfichier) AS nomfichier, explnum_path, if (rep.repertoire_nom IS null, '', rep.repertoire_nom) AS nomrepertoire
				from explnum ex_n
				LEFT JOIN explnum_location ex_l ON ex_n.explnum_id= ex_l.num_explnum
				LEFT JOIN docs_location dl ON ex_l.num_location= dl.idlocation
				LEFT JOIN upload_repertoire rep ON ex_n.explnum_repertoire= rep.repertoire_id
				where explnum_id='".$id_explnum."'";
            $res_explnum = pmb_mysql_query($rqt_explnum);
            while(($explnum = pmb_mysql_fetch_array($res_explnum, PMB_MYSQL_ASSOC))) {
                $infos_explnum[] = $explnum;
            }


            //Accessibilité des documents numériques aux abonnés en opac
            $id_notice_droit = 0;
            if ($infos_explnum[0]["explnum_notice"]) {
                $id_notice_droit = $infos_explnum[0]["explnum_notice"];
            } else {
                $requete = "SELECT bulletin_notice, num_notice FROM bulletins WHERE bulletin_id='".$infos_explnum[0]["explnum_bulletin"]."'";
                $res = pmb_mysql_query($requete);
                if ($res && pmb_mysql_num_rows($res)) {
                    if ($id_noti_bull = pmb_mysql_result($res, 0, 1)) {
                        $id_notice_droit = $id_noti_bull;
                    } else {
                        $id_notice_droit = pmb_mysql_result($res, 0, 0);
                    }
                }
            }

            if ($id_notice_droit) {
                $req_restriction_abo = "SELECT explnum_visible_opac, explnum_visible_opac_abon FROM notices,notice_statut WHERE notice_id='".$id_notice_droit."' AND statut=id_notice_statut ";
                $result = pmb_mysql_query($req_restriction_abo);
                $expl_num = pmb_mysql_fetch_array($result, PMB_MYSQL_ASSOC);
                $infos_restriction_abo = [];
                foreach ($expl_num as $key => $value) {
                    $infos_restriction_abo[$key] = $value;
                }
            } else {
				$infos_restriction_abo = null;
			}

            generate_log('visionneuse', [
                'explnum' => $infos_explnum,
				'restriction_abo' => $infos_restriction_abo
            ], true);
        }
    }
}
