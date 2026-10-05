<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts_abonnements.class.php,v 1.68.4.2 2025/04/18 13:07:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/abts_abonnements.tpl.php");
require_once($class_path."/serial_display.class.php");
require_once($include_path."/abts_func.inc.php");
require_once($include_path."/misc.inc.php");
require_once($class_path."/abts_pointage.class.php");
require_once($class_path."/serialcirc_diff.class.php");
require_once($class_path."/serialcirc.class.php");
require_once($class_path."/abts_status.class.php");
require_once($class_path.'/translation.class.php');

class abts_abonnement {
    public $abt_id; //Numéro du modèle
    public $abt_name; //Nom du modèle
    public $abt_name_opac; //Nom OPAC du modèle
    public $base_modele_name;//
    public $base_modele_id;//
    public $num_notice; //numéro de la notice liée
    public $duree_abonnement; //Durée de l'abonnement
    public $date_debut; //Date de début de validité du modèle
    public $date_fin; //Date de fin de validité du modèle
    public $fournisseur;// id du fournisseur
    public $destinataire;
    public $error; //Erreur
    public $error_message; //Message d'erreur
    public $abt_numeric=0;
    public $cote;
    public $typdoc_id;
    public $exemp_auto;
    public $location_id;
    public $section_id;
    public $lender_id;
    public $statut_id;
    public $codestat_id;
    public $prix;
    public $type_antivol;
    public $abt_status;
    public $modele_list_dates = [];
    
    public function __construct($abt_id="") {
        $this->abt_id = intval($abt_id);
        $this->getData();
    }
    
    public function getData() {
        $this->abt_name = '';
        $this->abt_name_opac = '';
        $this->num_notice = '';
        $this->base_modele_name = '';
        $this->base_modele_id = '';
        $this->num_notice = ''; //numéro de la notice liée
        $this->duree_abonnement = ''; //Durée de l'abonnement
        $this->date_debut = ''; //Date de début de validité du modèle
        $this->date_fin = ''; //Date de fin de validité du modèle
        $this->fournisseur = '';// id du fournisseur
        $this->destinataire = '';
        $this->cote = '';
        $this->typdoc_id = '';
        $this->exemp_auto = '';
        $this->location_id = '';
        $this->section_id = '';
        $this->lender_id = '';
        $this->statut_id = '';
        $this->codestat_id = '';
        $this->type_antivol = '';
        $this->abt_numeric = '';
        $this->prix = '';
        $this->abt_status = '';
        if ($this->abt_id) {
            $requete="select * from abts_abts where abt_id=".$this->abt_id;
            $resultat=pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat)) {
                $r=pmb_mysql_fetch_object($resultat);
                $this->abt_id = $r->abt_id;
                $this->abt_name = $r->abt_name;
                $this->abt_name_opac = $r->abt_name_opac;
                $this->num_notice = $r->num_notice;
                $this->base_modele_name = $r->base_modele_name;
                $this->base_modele_id = $r->base_modele_id;
                $this->num_notice = $r->num_notice; //numéro de la notice liée
                $this->duree_abonnement = $r->duree_abonnement; //Durée de l'abonnement
                $this->date_debut = $r->date_debut; //Date de début de validité du modèle
                $this->date_fin = $r->date_fin; //Date de fin de validité du modèle
                $this->fournisseur = $r->fournisseur;// id du fournisseur
                $this->destinataire = $r->destinataire;
                $this->cote = $r->cote;
                $this->typdoc_id = $r->typdoc_id;
                $this->exemp_auto = $r->exemp_auto;
                $this->location_id = $r->location_id;
                $this->section_id = $r->section_id;
                $this->lender_id = $r->lender_id;
                $this->statut_id = $r->statut_id;
                $this->codestat_id = $r->codestat_id;
                $this->type_antivol = $r->type_antivol;
                $this->abt_numeric = $r->abt_numeric;
                $this->prix = $r->prix;
                $this->abt_status = $r->abt_status;
            } else {
                $this->error = true;
                $this->error_message = "Le modèle demandé n'existe pas";
            }
        }
    }
    
    public function set_perio($num_notice) {
        $this->num_notice = 0;
        $requete = "select niveau_biblio from notices where notice_id=".$num_notice;
        $resultat = pmb_mysql_query($requete);
        if (pmb_mysql_num_rows($resultat)) {
            if (pmb_mysql_result($resultat,0,0)=="s")
                $this->num_notice = $num_notice;
        } else {
            $this->error = true;
            $this->error_message = "La notice liée n'existe pas ou n'est pas un périodique";
        }
    }
    
    protected function get_tpl_empr_list() {
        global $msg;
        global $abonnement_serialcirc_empr_list_empr, $abonnement_serialcirc_empr_list_group, $abonnement_serialcirc_empr_list_group_elt;
        
        $tpl_empr_list = '';
        $serialcirc_diff=new serialcirc_diff(0,$this->abt_id);
        foreach($serialcirc_diff->diffusion as $diff){
            if($diff['empr_type']==SERIALCIRC_EMPR_TYPE_empr){
                $tpl_empr=$abonnement_serialcirc_empr_list_empr;
                $name_elt=$serialcirc_diff->empr_info[ $diff['empr']['id_empr']]['empr_libelle'];
            }else{
                $name_elt=$diff['empr_name'];
                $group_list_list="";
                if(count($diff['group'])){
                    $tpl_empr=$abonnement_serialcirc_empr_list_group;
                    foreach($diff['group'] as $empr){
                        $group_list=$abonnement_serialcirc_empr_list_group_elt;
                        $resp="";
                        if($empr['responsable']){
                            $resp=$msg["serialcirc_group_responsable"];
                        }
                        $group_list=str_replace('!!empr_libelle!!',$empr['empr']['empr_libelle'].$resp, $group_list);
                        $group_list_list.=$group_list;
                    }
                    $tpl_empr=str_replace('!!empr_list!!', $group_list_list, $tpl_empr);
                }else {
                    $tpl_empr=$abonnement_serialcirc_empr_list_empr;
                }
            }
            $tpl_empr=str_replace('!!id_diff!!', $diff['id'], $tpl_empr);
            if (isset($diff['empr']['view_link'])) {
                $tpl_empr=str_replace('!!empr_view_link!!', $diff['empr']['view_link'], $tpl_empr);
            } else {
                // un groupe
                $tpl_empr=str_replace('!!empr_view_link!!', '', $tpl_empr);
            }
            $tpl_empr=str_replace('!!empr_name!!', $name_elt, $tpl_empr);
            $tpl_empr_list .= $tpl_empr;
        }
        return $tpl_empr_list;
    }
    
    public function show_abonnement() {
        global $abonnement_view,$serial_id;
        global $msg;
        global $pmb_gestion_devise;
        
        $perio=new serial_display($this->num_notice,1);
        $r=$abonnement_view;
        $r=str_replace("!!view_id_abonnement!!","catalog.php?categ=serials&sub=abon&serial_id=$serial_id&abt_id=$this->abt_id",$r);
        $r=str_replace("!!id_abonnement!!",$this->abt_id,$r);
        $r=str_replace("!!abonnement_header!!",$this->abt_name,$r);
        $r=str_replace("!!statut!!",abts_status::get_display($this->abt_status),$r);
        
        $modele=0;
        $modele_list="";
        $requete="select modele_id from abts_abts_modeles where abt_id='$this->abt_id'";
        $resultat=pmb_mysql_query($requete);
        while ($r_a=pmb_mysql_fetch_object($resultat)) {
            $modele_id=$r_a->modele_id;
            $modele_name=pmb_sql_value("select modele_name from abts_modeles where modele_id='$modele_id'");
            $num_periodicite=pmb_sql_value("select num_periodicite from abts_modeles where modele_id='$modele_id'");
            $periodicite=pmb_sql_value("SELECT libelle from abts_periodicites where periodicite_id='".$num_periodicite."'");
            if ($modele_list) $modele_list.=",";
            $modele_list.=" $modele_name";
            if($periodicite) $modele_list.=" ($periodicite)";
        }
        $r=str_replace("!!modele_lie!!",$modele_list,$r);
        $r=str_replace("!!duree_abonnement!!",$this->duree_abonnement,$r);
        $r=str_replace("!!date_debut!!",format_date($this->date_debut),$r);
        $r=str_replace("!!date_fin!!",format_date($this->date_fin),$r);
        
        $nombre_series = pmb_sql_value("SELECT SUM(nombre) FROM abts_grille_abt WHERE num_abt='$this->abt_id' AND type ='1'") ?? '';
        $r = str_replace("!!nombre_de_series!!", $nombre_series, $r);
        
        $nombre_horsseries = pmb_sql_value("SELECT SUM(nombre) FROM abts_grille_abt WHERE num_abt='$this->abt_id' AND type ='2'") ?? '';
        $r = str_replace("!!nombre_de_horsseries!!", $nombre_horsseries, $r);
        
        $prix='';
        $prix=$this->prix.'&nbsp'.$pmb_gestion_devise;
        $r=str_replace("!!prix!!",$prix,$r);
        
        $fournisseur_name = '';
        if($this->fournisseur) $fournisseur_name=$msg["abonnements_fournisseur"].": ".pmb_sql_value("SELECT raison_sociale from entites where id_entite = '".$this->fournisseur."' ");
        $r=str_replace("!!fournisseur!!",$fournisseur_name,$r);
        
        $aff_destinataire="";
        if($this->destinataire){
            $aff_destinataire="<tr>
				<td colspan='2'>".$this->destinataire."</td>
			</tr>";
        }
        $r=str_replace("!!commentaire!!",$aff_destinataire,$r);
        
        //Liste des destinataires
        $tpl_empr_list = $this->get_tpl_empr_list();
        $aff_empr_list="";
        if($tpl_empr_list){
            $aff_empr_list="
			<tr>
				<td colspan='2'>
					<h3>".$msg["serialcirc_diff_empr_list_title"]."</h3>
					$tpl_empr_list
				</td>
			</tr>";
        }
        
        $r=str_replace("!!serial_id!!", $serial_id, $r);
        $r=str_replace("!!serialcirc_empr_list!!", $aff_empr_list, $r);
        $r=str_replace("!!serialcirc_export_list_bt!!", "<input type='button' class='bouton' value='".$msg["serialcirc_export_list"]."'
				onClick=\"document.location='./edit.php?dest=TABLEAU&categ=serialcirc_diff&sub=export_empr&&num_abt=".$this->abt_id."'\"/>&nbsp;", $r);
        
        return $r;
    }
    
    /**
     * Liste des formulaire de modèles (dépliables +,-)
     * @return string
     */
    protected function get_modele_list() {
        $modele_list="";
        $this->modele_list_dates = array();
        $requete="select a.modele_id,num,vol,tome,delais,critique, num_statut_general, date_debut, date_fin
				from abts_abts_modeles a join abts_modeles m on m.modele_id=a.modele_id
				where abt_id='$this->abt_id'";
        $resultat=pmb_mysql_query($requete);
        if (!$resultat) die($requete."<br /><br />".pmb_mysql_error());
        while ($r_a=pmb_mysql_fetch_object($resultat)) {
            $modele_id=$r_a->modele_id;
            $num=$r_a->num;
            $vol=$r_a->vol;
            $tome=$r_a->tome;
            $delais=$r_a->delais;
            $critique=$r_a->critique;
            $modele_name=pmb_sql_value("select modele_name from abts_modeles where modele_id='$modele_id'");
            $num_periodicite=pmb_sql_value("select num_periodicite from abts_modeles where modele_id='$modele_id'");
            $periodicite=pmb_sql_value("select libelle from abts_periodicites where periodicite_id ='".$num_periodicite."'");
            $num_statut=$r_a->num_statut_general;
            if($periodicite) $modele_name.=" ($periodicite)";
            // 				if(!$num_statut)$num_statut=$this->statut_id;
            $modele_list.=$this->gen_tpl_abt_modele($modele_id,$modele_name,$num,$vol,$tome,$delais,$critique,$num_statut);
            $this->modele_list_dates[] = array($r_a->date_debut,$r_a->date_fin);
        }
        return $modele_list;
    }
    
    protected function get_calendar() {
        global $msg, $charset;
        global $serial_id;
        
        $calend ="";
        if (pmb_sql_value("select sum(nombre) from abts_grille_abt where num_abt='$this->abt_id'")) {
            $calend= <<<ENDOFTEXT
				<script type="text/javascript">
				function ad_date(obj,e) {
					if(!e) e=window.event;
					var tgt = e.target || e.srcElement; // IE doesn't use .target
					var strid = tgt.id;
					var type = tgt.tagName;
					e.cancelBubble = true;
					if (e.stopPropagation) e.stopPropagation();
					var pos=findPos(obj);
					var url="./catalog/serials/abonnement/abonnement_parution_edition.php?abonnement_id=!!abonnement_id!!&date_parution="+obj+"&type_serie=1&numero=";
					var notice_view=document.createElement("iframe");
					notice_view.setAttribute('id','frame_abts');
					notice_view.setAttribute('name','periodique');
					notice_view.src=url;
					var att=document.getElementById("att");
					notice_view.style.visibility="hidden";
					notice_view.style.display="block";
					notice_view=att.appendChild(notice_view);
					w=notice_view.clientWidth;
					h=notice_view.clientHeight;
					notice_view.style.left=pos[0]+"px";
					notice_view.style.top=pos[1]+"px";
					notice_view.style.visibility="visible";
				}
				</script>
ENDOFTEXT;
            $calend=str_replace("!!serial_id!!",$serial_id,$calend);
            $calend=str_replace("!!abonnement_id!!",$this->abt_id,$calend);
            $base_url="./catalog.php?categ=serials&sub=abonnement&serial_id="."$serial_id&abonnement_id=$this->abt_id";
            $base_url_mois='';
            
            $calend.= "<div id='calendrier_tab' style='width:99%'>" ;
            $date = $this->date_debut;
            $calend.= "<A name='ancre_calendrier'></A>";
            
            $year=pmb_sql_value("SELECT YEAR('$date')");
            $cur_year=$year;
            //debut expand
            $calend.="
				<div class='row'>&nbsp;</div>
				<div id='abts_year_$year' class='notice-parent'>
					<img src='".get_url_icon('minus.gif')."' class='img_plus' name='imEx' id='abts_year_$year"."Img' title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' onClick=\"expandBase('abts_year_$year', true); return false;\" />
					<span class='notice-heada'>
						$year
		    		</span>
				</div>
				<div id='abts_year_$year"."Child' startOpen='Yes' class='notice-child' style='margin-bottom:6px;width:94%'>
				";
						
						$i=pmb_sql_value("SELECT MONTH('$date')");
						if($i==2 || $i==5 || $i==8 || $i==11) {
						    $calend.= "<div class='row' style='padding-top: 5px'><div class='colonne3'>&nbsp;";
						    $calend.= "</div>\n";
						}
						if($i==3 || $i==6 || $i==9 || $i==12) {
						    $calend.= "<div class='row' style='padding-top: 5px'><div class='colonne3'>&nbsp;";
						    $calend.= "</div>\n";
						    $calend.= "<div class='colonne3' style='padding-left: 3px'>&nbsp;";
						    $calend.= "</div>\n";
						}
						do{
						    $year=pmb_sql_value("SELECT YEAR('$date')");
						    if($year!=$cur_year){
						        $calend.= "
						</div>
						";
						        $calend.="
						<div class='row'></div>
						<div id='abts_year_$year' class='notice-parent'>
                            ".get_expandBase_button('abts_year_'.$year)."
							<span class='notice-heada'>
								$year
				    		</span>
						</div>
						<div id='abts_year_$year"."Child' class='notice-child' style='margin-bottom:6px;display:none;width:94%'>
						";
								$cur_year=$year;
						    }
						    $i=pmb_sql_value("SELECT MONTH('$date')");
						    
						    if ($i==1 || $i==4 || $i==7 || $i==10 ) $calend.= "<div class='row' style='padding-top: 5px'><div class='colonne3'>";
						    else
						        $calend.= "<div class='colonne3' style='padding-left: 3px'>";
						        $calend.= pmb_bidi(calendar_gestion(str_replace("-","",$date), 0, $base_url, $base_url_mois,0,0,$this->abt_id));
						        $calend.= "</div>\n";
						        if ($i==3 || $i==6 || $i==9 || $i==12 ) $calend.="</div>\n";
						        
						        $date=pmb_sql_value("SELECT DATE_ADD('$date', INTERVAL 1 MONTH)");
						        $diff=pmb_sql_value("SELECT DATEDIFF('".$this->get_date_fin()."','$date')");
						        $extracted_date = explode('-', $date);
						        $nb_days_in_month = date('t', mktime(0, 0, 0, $extracted_date[1], $extracted_date[2], $extracted_date[0]));
						}
						while($diff>=(-$nb_days_in_month));
						//fin expand
						$calend.= "	</div>";
						$calend.= "</div>\n";
						$calend.="<script type='text/javascript'>parent.location.href='#ancre_calendrier';</script>";
        }
        return $calend;
    }
    
    /**
     * Vérifications sur les dates
     * @return string
     */
    protected function get_test_liste_modele() {
        global $msg;
        global $serial_id;
        global $act;
        
        if (!$this->abt_id) {
            //Checkbox des modèles à associer à l'abonnement
            $resultat=pmb_mysql_query("select modele_id,modele_name from abts_modeles where num_notice='$serial_id'");
            //Confection du javascript pour tester au moins une sélection de modèle
            $test_liste_modele="if(";
            $cpt=0;
            while ($rp=pmb_mysql_fetch_object($resultat)) {
                if(	$cpt++ >0) {
                    $test_liste_modele.=" || ";
                }
                $test_liste_modele.=" (document.getElementById('modele[".$rp->modele_id."]').checked==true) ";
                
            }
            $test_liste_modele.=")
			{
				return true;
			}else {
				alert(\"$msg[abonnements_err_msg_select_model]\");
				return false;
			}";
        } else {
            $test_liste_modele = "
        	var d = form.date_debut.value.replace(/-/g,'');
        	var d_abo_debut = new Date(d.substr(0,4),d.substr(4,2),d.substr(6,2));
        	d = form.date_fin.value.replace(/-/g,'');
        	var d_abo_fin = new Date(d.substr(0,4),d.substr(4,2),d.substr(6,2));
        	var dates_modeles = new Array(";
            foreach($this->modele_list_dates as $mdates){
                $test_liste_modele .= "new Array('".$mdates[0]."','".$mdates[1]."'),";
            }
            $test_liste_modele = substr($test_liste_modele,0,strlen($test_liste_modele)-1);
            $test_liste_modele .= "
            );";
            
            $test_liste_modele .= "
            	for(var i= 0; i < dates_modeles.length; i++){
            		var t = dates_modeles[i][0].split(/[-]/);
            		var d_mod_debut = new Date(t[0],t[1],t[2]);
                
            		var t = dates_modeles[i][1].split(/[-]/);
            		var d_mod_fin = new Date(t[0],t[1],t[2]);
            ";
            if ($this->date_debut=='0000-00-00' && $this->date_fin=='0000-00-00') {
                $test_liste_modele .= "
                    if ((d_abo_debut < d_mod_debut)||(d_abo_fin > d_mod_fin)) {
            			alert(\"".$msg['abo_date_incorrecte']."\");
            			return false;
            		}
                ";
            } elseif($act == 'prolonge') {
                $test_liste_modele .= "
                    var d_prev = form.date_fin.value.replace(/-/g,'');
        			var d_abo_prev_fin = new Date(d_prev.substr(0,4),d_prev.substr(4,2),d_prev.substr(6,2));
        			d_abo_prev_fin.setMonth(d_abo_prev_fin.getMonth() + parseInt(document.getElementById('duree_abonnement').value,10));
        			if (d_abo_prev_fin > d_mod_fin) {
        				alert(\"".$msg['abo_date_prolonge_incorrecte']."\");
        				return false;
        			}
                ";
            } else {
                $test_liste_modele .= "
	                if (d_abo_fin > d_mod_fin) {
        				alert(\"".$msg['abo_date_fin_incorrecte']."\");
        				return false;
        			}
                ";
            }
            $test_liste_modele .= "
            }";
        }
        
        return $test_liste_modele;
    }
    
    public function get_date_debut() {
        if (!$this->date_debut || $this->date_debut == "0000-00-00") {
            return date("Y-m-d",time());
        } else {
            return $this->date_debut;
        }
    }
    public function get_date_fin($date_debut='') {
        if (empty($date_debut)) {
            $date_debut = $this->get_date_debut();
        }
        if (!$this->date_fin || $this->date_fin == "0000-00-00") {
            return pmb_sql_value("SELECT DATE_ADD('$date_debut', INTERVAL 1 YEAR)");
        } else {
            return $this->date_fin;
        }
    }
    
    public function get_expl_content_form() {
        global $msg, $charset;
        global $pmb_antivol;
        global $edition_abonnement_expl_content_form;
        
        $content_form = $edition_abonnement_expl_content_form;
        
        //Cote:
        $content_form = str_replace('!!cote!!', htmlentities($this->cote,ENT_QUOTES,$charset), $content_form);
        
        // select "type document"
        $content_form = str_replace('!!type_doc!!', do_selector('docs_type', 'typdoc_id', $this->typdoc_id), $content_form);
        
        $content_form = str_replace('!!exemplarisation_automatique!!',
            "<input type='checkbox' value='1' ".($this->exemp_auto ?"checked":"yes")." name='exemp_auto' id='exemp_auto'/>",
            $content_form);
        
        // select "localisation"
        $content_form = str_replace('!!localisation!!',
            gen_liste ("select distinct idlocation, location_libelle from docs_location, docsloc_section where num_location=idlocation order by 2 ", "idlocation", "location_libelle", 'location_id', "calcule_section(this);", $this->location_id, "", "","","",0),
            $content_form);
        
        // select "section"
        $content_form = str_replace('!!section!!', $this->do_selector(), $content_form);
        
        // select "owner"
        $content_form = str_replace('!!owner!!', do_selector('lenders', 'lender_id', $this->lender_id), $content_form);
        
        // select "statut"
        $content_form = str_replace('!!statut!!', do_selector('docs_statut', 'statut_id', $this->statut_id), $content_form);
        
        // select "code statistique"
        $content_form = str_replace('!!codestat!!', do_selector('docs_codestat', 'codestat_id', $this->codestat_id), $content_form);
        
        //Prix
        $content_form = str_replace('!!prix!!', htmlentities($this->prix,ENT_QUOTES,$charset), $content_form);
        
        $selector="";
        if($pmb_antivol>0) {// select "type_antivol"
            $selector = "<select name='type_antivol' id='type_antivol'>";
            $selector .= "<option value='0'";
            if($this->type_antivol ==0)$selector .= ' SELECTED';
            $selector .= '>';
            $selector .= $msg["type_antivol_aucun"].'</option>';
            $selector .= "<option value='1'";
            if($this->type_antivol ==1)$selector .= ' SELECTED';
            $selector .= '>';
            $selector .= $msg["type_antivol_magnetique"].'</option>';
            $selector .= "<option value='2'";
            if($this->type_antivol ==2)$selector .= ' SELECTED';
            $selector .= '>';
            $selector .= $msg["type_antivol_autre"].'</option>';
            $selector .= '</select>';
        }
        $content_form = str_replace('!!type_antivol!!', $selector, $content_form);
        return $content_form;
    }
    
    public function get_content_form() {
        global $charset;
        global $creation_abonnement_content_form, $edition_abonnement_content_form;
        global $serial_id;
        global $pmb_abt_label_perio;
        
        if (!$this->abt_id) {
            $content_form = $creation_abonnement_content_form;
            
            if($pmb_abt_label_perio){
                $serial = new serial($serial_id);
                $content_form = str_replace('!!abt_name!!', htmlentities($serial->tit1, ENT_QUOTES, $charset), $content_form);
            }
            //Checkbox des modèles à associer à l'abonnement
            $resultat=pmb_mysql_query("select modele_id,modele_name from abts_modeles where num_notice='$serial_id'");
            $liste_modele="<table>";
            while ($rp=pmb_mysql_fetch_object($resultat)) {
                $liste_modele.="<tr><td><input type='checkbox' value='$rp->modele_id' name='modele[$rp->modele_id]' id='modele[$rp->modele_id]'/>$rp->modele_name</td></tr>";
            }
            $liste_modele.="</table>";
            $content_form = str_replace("!!liste_modele!!",$liste_modele, $content_form);
            $content_form = str_replace("!!abonnement_form1!!","", $content_form);
        } else {
            $content_form = $edition_abonnement_content_form;
            
            //Durée d'abonnement
            if (!$this->duree_abonnement) {
                $this->duree_abonnement=12;
            }
            $content_form = str_replace("!!duree_abonnement!!",$this->duree_abonnement, $content_form);
            
            //Date de début
            $date_debut = $this->get_date_debut();
            $content_form = str_replace("!!date_debut!!", $date_debut, $content_form);
            
            //Date de fin
            $content_form = str_replace("!!date_fin!!", $this->get_date_fin($date_debut), $content_form);
            
            //Fournisseur
            $content_form = str_replace('!!lib_fou!!', htmlentities(pmb_sql_value("SELECT raison_sociale from entites where id_entite = '".$this->fournisseur."' "),ENT_QUOTES,$charset), $content_form);
            $content_form = str_replace('!!id_fou!!', $this->fournisseur, $content_form);
            
            //Destinataire:
            $content_form = str_replace('!!destinataire!!', $this->destinataire, $content_form);
            
            $content_form = str_replace('!!abt_numeric_checked!!',	($this->abt_numeric ?"checked":"yes"), $content_form);
            
            //Donnees exemplaire
            $content_form = str_replace('!!expl_content_form!!', $this->get_expl_content_form(), $content_form);
            
            $content_form = str_replace("!!modele_list!!", $this->get_modele_list(), $content_form);
            
            // calendrier de réception s'il y a des enregistrement présents dans la grille
            $content_form.=$this->get_calendar();
        }
        
        $content_form = str_replace("!!serial_id!!",$serial_id, $content_form);
        
        //Remplacement des valeurs
        $content_form = str_replace("!!abt_id!!",htmlentities($this->abt_id,ENT_QUOTES,$charset), $content_form);
        $content_form = str_replace("!!abt_name!!",htmlentities($this->abt_name,ENT_QUOTES,$charset), $content_form);
        $content_form = str_replace("!!abt_name_opac!!",htmlentities($this->abt_name_opac,ENT_QUOTES,$charset), $content_form);
        
        $content_form = str_replace("!!num_notice!!",$this->num_notice, $content_form);
        return $content_form;
    }
    
    public function get_form() {
        global $msg;
        global $creation_abonnement_js_form, $edition_abonnement_js_form;
        global $serial_id;
        
        $interface_form = new interface_catalog_abts_form('form_abonnement');
        $interface_form->set_serial_id($serial_id);
        //Notice mère
        $perio=new serial_display($this->num_notice,1);
        if (!$this->abt_id) {
            $interface_form->set_abt_status(1);
            $interface_form->set_label($perio->header." : ".$msg["abts_abonnements_modify_title"]);
            $js_script = $creation_abonnement_js_form;
            $js_script_end = "";
        }else{
            $interface_form->set_abt_status($this->abt_status);
            $interface_form->set_label($perio->header." : ".$msg["abts_abonnements_modify_title"]);
            $js_script = $edition_abonnement_js_form;
            $js_script_end = "<script type='text/javascript'>expl_part_display();</script>
	        <script type=\"text/javascript\" src='./javascript/select.js'></script>
    		<script type=\"text/javascript\" src='./javascript/ajax.js'></script>";
            $js= <<<ENDOFTEXT
			<script type="text/javascript">
			function duplique(obj,e) {
				if(!e) e=window.event;
				var tgt = e.target || e.srcElement; // IE doesn't use .target
				var strid = tgt.id;
				var type = tgt.tagName;
				e.cancelBubble = true;
				if (e.stopPropagation) e.stopPropagation();
				var pos=findPos(obj);
				var url="./catalog/serials/abonnement/abonnement_duplique.php?abonnement_id=!!abonnement_id!!&serial_id=!!serial_id!!";
				var notice_view=document.createElement("iframe");
				notice_view.setAttribute('id','frame_abts');
				notice_view.setAttribute('name','periodique');
				notice_view.src=url;
				var att=document.getElementById("att");
				notice_view.style.visibility="hidden";
				notice_view.style.display="block";
				notice_view=att.appendChild(notice_view);
				w=notice_view.clientWidth;
				h=notice_view.clientHeight;
				posx=(getWindowWidth()/2-(w/2))<0?0:(getWindowWidth()/2-(w/2))
				posy=(getWindowHeight()/2-(h/2))<0?0:(getWindowHeight()/2-(h/2));
				notice_view.style.left=posx+"px";
				notice_view.style.top=posy+"px";
				notice_view.style.visibility="visible";
			}
			
			function kill_frame_periodique() {
				var notice_view=document.getElementById("frame_abts");
				notice_view.parentNode.removeChild(notice_view);
			}
			</script>
ENDOFTEXT;
            $js=str_replace("!!serial_id!!",$serial_id,$js);
            $js=str_replace("!!abonnement_id!!",$this->abt_id,$js);
            $js_script_end .= $js;
            
            $interface_form->add_action_extension('gen', $msg['abonnement_generer_la_grille'], $interface_form->get_url_base().'&act=gen&abt_id='.$this->abt_id);
            $interface_form->add_action_extension('prolonge', $msg['abonnement_prolonger_abonnement'], $interface_form->get_url_base().'&act=prolonge&abt_id='.$this->abt_id);
            $interface_form->add_action_extension('raz', $msg['abonnement_raz_grille'], $interface_form->get_url_base().'&act=raz&abt_id='.$this->abt_id);
        }
        $interface_form->set_object_id($this->abt_id)
        ->set_confirm_delete_msg($msg['abonnements_confirm_suppr_abonnement'])
        ->set_content_form($this->get_content_form())
        ->set_table_name('abts_abts')
        ->set_field_focus('abt_name')
        ->set_duplicable(true);
        $form = $interface_form->get_display();
        
        $js_script = str_replace("!!test_liste_modele!!",$this->get_test_liste_modele(), $js_script);
        
        return $js_script.$form.$js_script_end;
    }
    
    public function show_form() {
        global $msg;
        global $serial_header;
        
        if ($this->abt_id) {
            $this->getData();
            $header = str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg["abts_abonnements_modify_title"], $serial_header);
        } else {
            $header = str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg["abts_abonnements_add_title"], $serial_header);
        }
        return $header.$this->get_form();
    }
    
    // ----------------------------------------------------------------------------
    //	fonction do_selector qui génère des combo_box avec tout ce qu'il faut
    // ----------------------------------------------------------------------------
    public function do_selector() {
        global $charset;
        global $deflt_docs_section;
        global $deflt_docs_location;
        
        if (!$this->section_id) $this->section_id=$deflt_docs_section ;
        if (!$this->location_id) $this->location_id=$deflt_docs_location;
        
        $rqtloc = "SELECT idlocation FROM docs_location order by location_libelle";
        $resloc = pmb_mysql_query($rqtloc);
        $selector = '';
        while ($loc=pmb_mysql_fetch_object($resloc)) {
            $requete = "SELECT idsection, section_libelle FROM docs_section, docsloc_section where idsection=num_section and num_location='$loc->idlocation' order by section_libelle";
            $result = pmb_mysql_query($requete);
            $nbr_lignes = pmb_mysql_num_rows($result);
            if ($nbr_lignes) {
                if ($loc->idlocation==$this->location_id) $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:block\">\r\n";
                else $selector .= "<div id=\"docloc_section".$loc->idlocation."\" style=\"display:none\">\r\n";
                $selector .= "<select name='f_ex_section".$loc->idlocation."' id='f_ex_section".$loc->idlocation."'>";
                while($line = pmb_mysql_fetch_row($result)) {
                    $selector .= "<option value='$line[0]'";
                    $line[0] == $this->section_id ? $selector .= ' SELECTED>' : $selector .= '>';
                    $selector .= htmlentities($line[1],ENT_QUOTES, $charset).'</option>';
                }
                $selector .= '</select></div>';
            }
        }
        return $selector;
    }
    
    public function gen_tpl_abt_modele($id,$titre,$num,$vol,$tome,$delais,$delais_critique,$change_statut_id){
        global $msg;
        
        $requete="select * from abts_modeles where modele_id='$id'";
        $resultat=pmb_mysql_query($requete);
        if ($r_a=pmb_mysql_fetch_object($resultat)) {
            $tom_actif=$r_a->tom_actif;
            $vol_actif=$r_a->vol_actif;
            $num_depart=$r_a->num_depart;
            $vol_depart=$r_a->vol_depart;
            $tom_depart=$r_a->tom_depart;
        }
        if(!$num)	$num=$num_depart;
        if(!$vol)	$vol=$vol_depart;
        if(!$tome)	$tome=$tom_depart;
        $contenu= "
		<div class='row'>
			<label for='num_periodicite' class='etiquette'>".$msg["abonnements_periodique_numero_depart"]."</label>
		</div>
		<div class='row'>
			<input type='text' size='4' name='num[$id]' id='num[$id]' value='$num'/>
		</div>
		";
        if($vol_actif)$contenu.= "
		<div class='colonne2'>
			<div class='row'>
				<label for='num_periodicite' class='etiquette'>".$msg["abonnements_volume_numero_depart"]."</label>
			</div>
			<div class='row'>
				<input type='text' size='4' name='vol[$id]' id='vol[$id]' value='$vol'/>
			</div>
		</div>
		";
        if($tom_actif)$contenu.= "
		<div class='colonne_suite'>
			<div class='row'>
				<label for='num_periodicite' class='etiquette'>".$msg["abonnements_tome_numero_depart"]."</label>
			</div>
			<div class='row'>
				<input type='text' size='4' name='tome[$id]' id='tome' value='$tome'/>
			</div>
		</div>
		";
        $contenu.= "
		<div class='row'></div>
		<div class='colonne2'>
			<div class='row'>
				<label for='num_periodicite' class='etiquette'>".$msg["abonnements_delais_avant_retard"]."</label>
			</div>
			<div class='row'>
				<input type='text' size='4' name='delais[$id]' id='delais[$id]' value='$delais'/>
			</div>
		</div>
		<div class='colonne_suite'>
			<div class='row'>
				<label for='num_periodicite' class='etiquette'>".$msg["abonnements_delais_critique"]."</label>
			</div>
			<div class='row'>
				<input type='text' size='4' name='delais_critique[$id]' id='delais_critique[$id]' value='$delais_critique'/>
			</div>
		</div>
		<div class='row'></div>
		";
        
        // select !!change_statut!!
        $statut_form=str_replace('!!statut_check!!',
            "<input type='checkbox' ".(!$id || $change_statut_id ? "checked='checked'" : '')." value='1' name='change_statut_check[".$id."]' id='change_statut[".$id."]_check' onclick=\"gere_statut('change_statut[".$id."]');\"/>",
            $msg['catalog_change_statut_form']);
        
        $statut_form=str_replace('!!statut_list!!',
            do_selector('docs_statut', "change_statut[".$id."]", $change_statut_id),
            $statut_form);
        
        $contenu.= "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			$statut_form
		</div>
		";
			
			return gen_plus_form($id,$titre,$contenu);
    }
    
    public function gen_date($garder=0){
        global $include_path;
        
        if($this->abt_id) {
            if (!$garder) {
                $dummy = "delete FROM abts_grille_abt WHERE num_abt='$this->abt_id' and state='0'";
                pmb_mysql_query($dummy);
            }
            
            $date=$date_debut = construitdateheuremysql($this->date_debut);
            $date_fin = construitdateheuremysql($this->date_fin);
            
            //Pour tous les modèles utilisé dans l'abonnement, on recopie les grilles modèles dans la grille abonnement
            $requete="select modele_id from abts_abts_modeles where abt_id='$this->abt_id'";
            $resultat_a=pmb_mysql_query($requete);
            while ($r_a=pmb_mysql_fetch_object($resultat_a)) {
                $modele_id=$r_a->modele_id;
                
                $requete="select * from abts_grille_modele where num_modele='$modele_id'";
                $resultat=pmb_mysql_query($requete);
                while ($r_g=pmb_mysql_fetch_object($resultat)) {
                    
                    //Ne garder les bulletins compris entre les dates de début et fin d'abonnement
                    if( ( pmb_sql_value("SELECT DATEDIFF('$date_fin','$r_g->date_parution')")>= 0 ) &&
                        ( pmb_sql_value("SELECT DATEDIFF('$date_debut','$r_g->date_parution')")<= 0 ) ) {
                            for($i=1;$i<=$r_g->nombre_recu;$i++){
                                $requete = "INSERT INTO abts_grille_abt SET num_abt='$this->abt_id',
								date_parution ='$r_g->date_parution',
								modele_id='$modele_id',
								type = '$r_g->type_serie',
								numero='$r_g->numero',
								nombre='1',
								ordre='$i' ";
                                pmb_mysql_query($requete);
                            }
                        }
                }
            }
        }
    }
    
    public function update() {
        global $msg;
        global $include_path;
        global $act,$modele,$num,$vol,$tome,$delais,$delais_critique,$change_statut,$change_statut_check;
        
        if(!$this->abt_name)	return false;
        // nettoyage des valeurs en entrée
        $this->abt_name = clean_string($this->abt_name);
        $this->abt_name_opac = clean_string($this->abt_name_opac);
        // construction de la requête
        $requete = "SET abt_name='".addslashes($this->abt_name)."', ";
        $requete .= "abt_name_opac='".addslashes($this->abt_name_opac)."', ";
        $requete .= "num_notice='$this->num_notice', ";
        $requete .= "duree_abonnement='$this->duree_abonnement', ";
        $requete .= "date_debut='$this->date_debut', ";
        $requete .= "date_fin='$this->date_fin', ";
        $requete .= "fournisseur='$this->fournisseur', ";
        $requete .= "destinataire='".addslashes($this->destinataire)."', ";
        $requete .= "cote='".addslashes($this->cote)."', ";
        $requete .= "typdoc_id='$this->typdoc_id', ";
        $requete .= "exemp_auto='$this->exemp_auto', ";
        $requete .= "location_id='$this->location_id', ";
        $requete .= "section_id='$this->section_id', ";
        $requete .= "lender_id='$this->lender_id', ";
        $requete .= "statut_id='$this->statut_id', ";
        $requete .= "codestat_id='$this->codestat_id', ";
        $requete .= "prix='$this->prix', ";
        $requete .= "type_antivol='$this->type_antivol', ";
        $requete .= "abt_numeric='$this->abt_numeric', ";
        $requete .= "abt_status='$this->abt_status' ";
        
        if($this->abt_id) {
            // Update: s'assurer que le nom d'abonnement n'existe pas déjà
            $dummy = "SELECT * FROM abts_abts WHERE abt_name='".addslashes($this->abt_name)."' and num_notice='$this->num_notice' and abt_id!=$this->abt_id";
            $check = pmb_mysql_query($dummy);
            if(pmb_mysql_num_rows($check)) {
                require_once("$include_path/user_error.inc.php");
                warning($msg["abonnements_titre_creation_edition_abonnement"], $msg["abonnements_erreur_creation_doublon_abonnement"]." ($this->abt_name).");
                return FALSE;
            }
            
            // update
            $requete = 'UPDATE abts_abts '.$requete;
            $requete .= ' WHERE abt_id='.$this->abt_id.' LIMIT 1;';
            
            if(pmb_mysql_query($requete) ) {
                if($act=="gen") $this->gen_date();
                $requete="select modele_id from abts_modeles where num_notice='$this->num_notice'";
                $resultat=pmb_mysql_query($requete);
                while ($r=pmb_mysql_fetch_object($resultat)) {
                    $modele_id=$r->modele_id;
                    if(isset($change_statut_check[$modele_id]) && $change_statut_check[$modele_id])$num_statut=$change_statut[$modele_id];
                    else $num_statut=0;
                    
                    $num_value = isset($num[$modele_id]) ? intval($num[$modele_id]) : 0;
                    $vol_value = isset($vol[$modele_id]) ? intval($vol[$modele_id]) : 0;
                    $tome_value = isset($tome[$modele_id]) ? intval($tome[$modele_id]) : 0;
                    $delais_value = isset($delais[$modele_id]) ? intval($delais[$modele_id]) : 0;
                    $critique_value = isset($delais_critique[$modele_id]) ? intval($delais_critique[$modele_id]) : 0;
                    
                    $requete = "UPDATE abts_abts_modeles
                                SET num=$num_value,
                                    vol=$vol_value,
                                    tome=$tome_value,
                                    delais=$delais_value,
                                    critique=$critique_value,
                                    num_statut_general=$num_statut
                                WHERE modele_id=$modele_id
                                AND abt_id=$this->abt_id";
                    
                    pmb_mysql_query($requete);
                }
                //Traductions
                $translation = new translation($this->abt_id, 'abts_abts');
                $translation->update_small_text('abt_name_opac');
                return TRUE;
            }
            else {
                echo pmb_mysql_error();
                require_once("$include_path/user_error.inc.php");
                warning($msg["abonnements_titre_creation_edition_abonnement"], $msg["abonnements_titre_creation_edition_modele_impossible"]);
                return FALSE;
            }
        }
        else {
            // Création: s'assurer que le modèle n'existe pas déjà
            $dummy = "SELECT * FROM abts_abts WHERE abt_name='".addslashes($this->abt_name)."' and num_notice='$this->num_notice'";
            $check = pmb_mysql_query($dummy);
            if(pmb_mysql_num_rows($check)) {
                require_once("$include_path/user_error.inc.php");
                warning($msg["abonnements_titre_creation_edition_abonnement"], $msg["abonnements_erreur_creation_doublon_abonnement"]." ($this->abt_name).");
                return FALSE;
            }
            $requete = 'INSERT INTO abts_abts '.$requete.';';
            if(pmb_mysql_query($requete)) {
                $this->abt_id=pmb_mysql_insert_id();
                $requete="select modele_id,num_periodicite from abts_modeles where num_notice='$this->num_notice'";
                $resultat=pmb_mysql_query($requete);
                while ($r=pmb_mysql_fetch_object($resultat)) {
                    $modele_id=$r->modele_id;
                    $num_periodicite=$r->num_periodicite;
                    if(isset($modele[$modele_id])){
                        $requete="select libelle, retard_periodicite,seuil_periodicite from abts_periodicites where periodicite_id ='".$num_periodicite."'";
                        $r_delais=pmb_mysql_query($requete);
                        if ($r_d=pmb_mysql_fetch_object($r_delais)) {
                            $periodicite=$r_d->libelle;
                            $delais=$r_d->seuil_periodicite;
                            $critique=$r_d->retard_periodicite;
                        }
                        if(!isset($critique)) $critique = 0; //retard_periodicite est a NULL par défaut
                        if(isset($change_statut_check[$modele_id]) && $change_statut_check[$modele_id]) $num_statut=$change_statut[$modele_id];
                        else $num_statut=0;
                        $requete = "INSERT INTO abts_abts_modeles SET modele_id='$modele_id', abt_id='$this->abt_id', delais='$delais', critique='$critique', num_statut_general='$num_statut' ";
                        pmb_mysql_query($requete);
                    }
                }
                //Traductions
                $translation = new translation($this->abt_id, 'abts_abts');
                $translation->update_small_text('abt_name_opac');
                
                if($act=="gen") $this->gen_date();
                return TRUE;
            }
            else {
                echo pmb_mysql_error();
                require_once("$include_path/user_error.inc.php");
                warning($msg["abonnements_titre_creation_edition_abonnement"], $msg["abonnements_titre_creation_edition_modele_impossible"]);
                return FALSE;
            }
        }
    }
    
    public function delete(){
        global $msg;
        global $include_path;
        
        $serialcirl_diff = new serialcirc_diff();
        // l'abonnement a encore au moins un expl en circulation
        if($serialcirl_diff->expl_in_circ($this->abt_id)){
            return $msg['serialcirc_error_delete_abt'];
        }
        $dummy = "delete FROM abts_abts WHERE abt_id='$this->abt_id' ";
        pmb_mysql_query($dummy);
        
        $dummy = "delete FROM abts_grille_abt WHERE num_abt='$this->abt_id' ";
        pmb_mysql_query($dummy);
        
        $dummy = "delete FROM abts_abts_modeles WHERE abt_id='$this->abt_id' ";
        pmb_mysql_query($dummy);
        
        abts_pointage::delete_retard($this->abt_id);
        
        $serialcirl_diff->delete($this->abt_id);
        
        translation::delete($this->abt_id, 'abts_abts');
        return "";
    }
    
    public function set_properties_from_form() {
        global $deflt_docs_location, $deflt_docs_section;
        global $abt_name, $abt_name_opac, $num_notice, $duree_abonnement;
        global $id_fou, $destinataire;
        global $cote, $typdoc_id, $exemp_auto, $location_id, $lender_id, $statut_id, $codestat_id, $prix, $type_antivol, $abt_numeric, $abts_status;
        
        $formlocid="f_ex_section".$location_id ;
        global ${$formlocid};
        $section_id=${$formlocid} ;
        
        if (!$section_id) $section_id=$deflt_docs_section ;
        if (!$location_id) $location_id=$deflt_docs_location;
        if(!$abts_status) $abts_status = 1;
        
        $this->abt_name= stripslashes($abt_name ?? '');
        $this->abt_name_opac= stripslashes($abt_name_opac ?? '');
        $this->num_notice= $num_notice;
        $this->duree_abonnement = $duree_abonnement;
        $this->fournisseur = $id_fou;
        $this->destinataire = stripslashes($destinataire ?? '');
        $this->cote=stripslashes($cote ?? '');
        $this->typdoc_id=$typdoc_id;
        $this->exemp_auto=$exemp_auto;
        $this->location_id=$location_id;
        $this->section_id=$section_id;
        $this->lender_id=$lender_id;
        $this->statut_id=$statut_id;
        $this->codestat_id=$codestat_id;
        $this->prix=stripslashes($prix ?? '');
        $this->type_antivol=$type_antivol;
        $this->abt_numeric=$abt_numeric;
        $this->abt_status=$abts_status;
    }
    
    public function proceed() {
        global $act, $current_module;
        global $serial_id,$msg,$date_debut,$date_fin,$days,$day_month,$week_month,$week_year,$month_year,$date_parution;
        global $duree_abonnement,$date_debut,$date_fin;
        global $abt_id;
        global $nb_duplication;
        
        switch ($act) {
            case 'update':
                // mise à jour modèle
                $this->set_properties_from_form();
                $this->date_debut= $date_debut;
                $this->date_fin= $date_fin;
                $this->update();
                print $this->show_form();
                break;
            case 'gen':
                // mise à jour modèle
                $this->set_properties_from_form();
                $this->date_debut= $date_debut;
                $this->date_fin= $date_fin;
                $this->update();
                print $this->show_form();
                break;
            case 'prolonge':
                // mise à jour modèle
                $this->set_properties_from_form();
                $this->date_debut= $date_fin; //Ce n'est pas une erreur mais cela sert pour $this->gen_date(1); qui suit... Date début est bien re-valorisé juste après
                $this->date_fin= pmb_sql_value("SELECT DATE_ADD('$date_fin',INTERVAL $duree_abonnement month)");
                $this->gen_date(1);
                $this->date_debut= $date_debut;
                $this->update();
                print $this->show_form();
                break;
            case 'copy':
                
                $this->getData();
                $abt_id=$this->abt_id;
                $this->abt_name.="_1";
                for($i=0;$i<$nb_duplication;$i++){
                    //Création nouvel abonnement
                    $this->abt_id='';
                    do {
                        $this->abt_name++;
                        $requete = "SELECT abt_name FROM abts_abts WHERE abt_name='".addslashes($this->abt_name)."' and num_notice='$this->num_notice'";
                        $resultat=pmb_mysql_query($requete);
                    }
                    while (pmb_mysql_fetch_object($resultat));
                    $this->update();
                    //recopie des modeles associés
                    $requete = "select * from abts_abts_modeles where abt_id='$abt_id'";
                    $resultat=pmb_mysql_query($requete);
                    while ($r_m=pmb_mysql_fetch_object($resultat)) {
                        $requete = "INSERT INTO abts_abts_modeles SET modele_id='$r_m->modele_id', abt_id='$this->abt_id',num='$r_m->num' ,vol='$r_m->vol',tome='$r_m->tome',delais='$r_m->delais', critique='$r_m->critique',num_statut_general='$r_m->num_statut_general'";
                        pmb_mysql_query($requete);
                    }
                    //recopie des infos du calendrier
                    $requete = "select * from abts_grille_abt where num_abt='$abt_id'";
                    $resultat=pmb_mysql_query($requete);
                    while ($r_g=pmb_mysql_fetch_object($resultat)) {
                        $requete = "INSERT INTO abts_grille_abt SET num_abt='$this->abt_id',
							date_parution ='$r_g->date_parution',
							modele_id='$r_g->modele_id',
							type = '$r_g->type',
							numero='$r_g->numero',
							nombre='$r_g->nombre',
							ordre='$r_g->ordre' ";
                        pmb_mysql_query($requete);
                    }
                }
                print "<div class='row'><div class='msg-perio'>".$msg['maj_encours']."</div></div>";
                $id_form = md5(microtime());
                $retour = "./catalog.php?categ=serials&sub=view&serial_id=$serial_id&view=abon";
                print "<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
					<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
					</form>
					<script type=\"text/javascript\">document.dummy.submit();</script>
					</div>";
                break;
            case 'raz':
                if($this->abt_id) {
                    $dummy = "delete FROM abts_grille_abt WHERE num_abt='".$this->abt_id."'";
                    pmb_mysql_query($dummy);
                }
                print $this->show_form();
                break;
            case 'del':
                if($msg_error=$this->delete())	{
                    $retour = "./circ.php?categ=serialcirc";
                    error_message('', $msg_error, 1, $retour);
                }else{
                    print "<div class='row'><div class='msg-perio'>".$msg['maj_encours']."</div></div>";
                    $id_form = md5(microtime());
                    $retour = "./catalog.php?categ=serials&sub=view&serial_id=$serial_id&view=abon";
                    print "<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
						<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
						</form>
						<script type=\"text/javascript\">document.dummy.submit();</script>
						</div>";
                }
                break;
            default:
                print $this->show_form();
                break;
        }
    }
}

class abts_abonnements {
    
    public $abonnements = array(); //Tableau des IDs des modèles
    
    public function __construct($id_perio,$localisation=0) {
        $where_localisation = '';
        $localisation = intval($localisation);
        if ($localisation > 0) {
            $where_localisation=" and location_id = $localisation ";
        }
        $requete="select abt_id from abts_abts where num_notice=$id_perio $where_localisation order by abt_name";
        $resultat=pmb_mysql_query($requete);
        while ($r=pmb_mysql_fetch_object($resultat)) {
            $abonnement=new abts_abonnement($r->abt_id);
            if (!$abonnement->error) {
                $this->abonnements[]=$abonnement;
            }
        }
    }
    
    public function show_list() {
        global $abonnement_list,$msg,$serial_id;
        
        $display=$abonnement_list;
        $abonnements="";
        if (count($this->abonnements)) {
            for ($i=0; $i<count($this->abonnements); $i++) {
                $abonnements.=$this->abonnements[$i]->show_abonnement();
            }
        }
        
        $result = pmb_mysql_query("select modele_id,modele_name from abts_modeles where num_notice='$serial_id'");
        $cpt = 0;
        if ($result) {
            $cpt = pmb_mysql_num_rows($result);
        }
        if($cpt) {
            $display=str_replace("!!abts_abonnements_add_button!!","<input type='button' class='bouton' value='".$msg["abts_abonnements_add_button"]."' onClick='document.location=\"catalog.php?categ=serials&sub=abon&serial_id=$serial_id\"'/>",$display);
        } else {
            $display=str_replace("!!abts_abonnements_add_button!!",$msg["abts_modeles_no_modele"],$display);
        }
        return str_replace("!!abonnement_list!!",$abonnements,$display);
    }
}

function gen_plus_form($id,$titre,$contenu) {
    return "
	<div class='row'></div>
	<div id='$id' class='notice-parent'>
        ".get_expandBase_button($id)."
		<span class='notice-heada'>
			$titre
		</span>
	</div>
	<div id='$id"."Child' class='notice-child' startOpen='Yes' style='margin-bottom:6px;display:none;width:94%'>
		$contenu
	</div>
	";
}
