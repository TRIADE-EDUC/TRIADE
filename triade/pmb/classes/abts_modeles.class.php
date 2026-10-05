<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts_modeles.class.php,v 1.50.4.2 2025/04/08 08:54:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/abts_modeles.tpl.php");
require_once($class_path."/serial_display.class.php");
require_once($include_path."/abts_func.inc.php");
require_once($class_path."/abts_abonnements.class.php");

class abts_modele {
    public $modele_id; //Numéro du modèle
    public $modele_name; //Nom du modèle
    public $num_notice; //numéro de la notice liée
    public $num_periodicite; //Identifiant de la périodicité
    public $duree_abonnement; //Durée de l'abonnement
    public $date_debut; //Date de début de validité du modèle
    public $date_fin; //Date de fin de validité du modèle
    public $days; //Jours de la semaine exclus
    public $day_month; //Jours du mois exclus
    public $week_month; //Semaine du mois exclue
    public $week_year; //Semaine de l'année exclue
    public $month_year; //Mois dans l'année exclu
    public $error; //Erreur
    public $error_message; //Message d'erreur
    public $num_cycle;
    public $num_combien;
    public $num_increment;
    public $num_date_unite;
    public $num_increment_date;
    public $num_depart;
    public $vol_actif;
    public $vol_increment;
    public $vol_date_unite;
    public $vol_increment_numero;
    public $vol_increment_date;
    public $vol_cycle;
    public $vol_combien;
    public $vol_depart;
    public $tom_actif;
    public $tom_increment;
    public $tom_date_unite;
    public $tom_increment_numero;
    public $tom_increment_date;
    public $tom_cycle;
    public $tom_combien;
    public $tom_depart;
    public $format_aff;
    public $format_periode;
    
    public function __construct($modele_id="") {
        $this->modele_id=intval($modele_id);
        $this->initData();
        $this->getData();
    }
    
    public function initData() {
        $this->modele_name = '';
        $this->num_notice = 0;
        $this->num_periodicite = '';
        $this->duree_abonnement = 12;
        $this->date_debut = '';
        $this->date_fin = '';
        $this->days = '';
        $this->day_month = '';
        $this->week_month = '';
        $this->week_year = '';
        $this->month_year = '';
        $this->num_cycle = 0;
        $this->num_combien = 12;
        $this->num_increment = 0;
        $this->num_date_unite = 1;
        $this->num_increment_date = 12;
        $this->num_depart = 1;
        $this->vol_actif = 0;
        $this->vol_increment = 0;
        $this->vol_date_unite = 1;
        $this->vol_increment_numero = 12;
        $this->vol_increment_date = 12;
        $this->vol_cycle = 0;
        $this->vol_combien = 1;
        $this->vol_depart = 1;
        $this->tom_actif = 0;
        $this->tom_increment = 10;
        $this->tom_date_unite = 2;
        $this->tom_increment_numero = 1;
        $this->tom_increment_date = 1;
        $this->tom_cycle = 0;
        $this->tom_combien = 1;
        $this->tom_depart = 1;
        $this->format_aff = '';
        $this->format_periode = '';
        $this->error = '';
        $this->error_message = '';
    }
    
    public function getData() {
        
        if ($this->modele_id) {
            $requete = "select * from abts_modeles where modele_id=".$this->modele_id;
            $resultat = pmb_mysql_query($requete);
            if (pmb_mysql_num_rows($resultat)) {
                $r = pmb_mysql_fetch_object($resultat);
                $this->modele_name = $r->modele_name;
                $this->num_notice = $r->num_notice;
                $this->num_periodicite = $r->num_periodicite;
                $this->duree_abonnement = $r->duree_abonnement;
                $this->date_debut = $r->date_debut;
                $this->date_fin = $r->date_fin;
                $this->days = $r->days;
                $this->day_month = $r->day_month;
                $this->week_month = $r->week_month;
                $this->week_year = $r->week_year;
                $this->month_year = $r->month_year;
                $this->num_cycle = $r->num_cycle;
                $this->num_combien = $r->num_combien;
                $this->num_increment = $r->num_increment;
                $this->num_date_unite = $r->num_date_unite;
                $this->num_increment_date = $r->num_increment_date;
                $this->num_depart = $r->num_depart;
                $this->vol_actif = $r->vol_actif;
                $this->vol_increment = $r->vol_increment;
                $this->vol_date_unite = $r->vol_date_unite;
                $this->vol_increment_numero = $r->vol_increment_numero;
                $this->vol_increment_date = $r->vol_increment_date;
                $this->vol_cycle = $r->vol_cycle;
                $this->vol_combien = $r->vol_combien;
                $this->vol_depart = $r->vol_depart;
                $this->tom_actif = $r->tom_actif;
                $this->tom_increment = $r->tom_increment;
                $this->tom_date_unite = $r->tom_date_unite;
                $this->tom_increment_numero = $r->tom_increment_numero;
                $this->tom_increment_date = $r->tom_increment_date;
                $this->tom_cycle = $r->tom_cycle;
                $this->tom_combien = $r->tom_combien;
                $this->tom_depart = $r->tom_depart;
                $this->format_aff = $r->format_aff;
                $this->format_periode = $r->format_periode;
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
    
    public function show_modele() {
        global $modele_view,$serial_id;
        $perio=new serial_display($this->num_notice,1);
        $r=$modele_view;
        $r=str_replace("!!view_id_modele!!","catalog.php?categ=serials&sub=modele&serial_id=".$serial_id."&modele_id=".$this->modele_id,$r);
        $r=str_replace("!!id_modele!!",$this->modele_id,$r);
        $r=str_replace("!!modele_header!!",$this->modele_name,$r);
        
        $num_periodicite = pmb_sql_value("SELECT libelle FROM abts_periodicites WHERE periodicite_id='".$this->num_periodicite."'") ?? '';
        $r = str_replace("!!num_periodicite!!", $num_periodicite, $r);
        
        $r=str_replace("!!duree_abonnement!!",$this->duree_abonnement,$r);
        $r=str_replace("!!date_debut!!",format_date($this->date_debut),$r);
        $r=str_replace("!!date_fin!!",format_date($this->date_fin),$r);
        
        $nombre_series = pmb_sql_value("SELECT SUM(nombre_recu) FROM abts_grille_modele WHERE num_modele='".$this->modele_id."' AND type_serie ='1'") ?? '';
        $r = str_replace("!!nombre_de_series!!", $nombre_series, $r);
        
        $nombre_horsseries = pmb_sql_value("SELECT SUM(nombre_recu) FROM abts_grille_modele WHERE num_modele='".$this->modele_id."' AND type_serie ='2'") ?? '';
        $r = str_replace("!!nombre_de_horsseries!!", $nombre_horsseries, $r);
        
        return $r;
    }
    
    protected function get_display_cell_label($for, $label) {
        global $charset;
        return "<td><label for='".$for."' style='all:unset'>".htmlentities($label, ENT_QUOTES, $charset)."</label></td>";
    }
    
    protected function get_display_cell_checkbox($name, $value, $included) {
        return "<td><input type='checkbox' value='$value' ".(!$included && $this->modele_id ? "checked='checked'" : "")." id='".$name."_".$value."' name='".$name."[$value]' /></td>";
    }
    
    /**
     * Jours de la semaine exclus
     * @return string
     */
    protected function get_days_content_form() {
        global $msg;
        
        $days = "
		<table>";
        $days_t = "<tr>";
        $days_v = "<tr>";
        for ($i=1; $i<8; $i++) {
            $tmp = '';
            if (substr($this->days, $i-1, 1)) {
                $tmp = substr($this->days, $i-1, 1);
            }
            $days_t .= $this->get_display_cell_label('days_'.$i, $msg["week_days_short_".$i]);
            $days_v .= $this->get_display_cell_checkbox('days', $i, $tmp);
        }
        $days_v.="</tr>";
        $days_t.="</tr>";
        $days.=$days_t."\n".$days_v."
		</table>";
        return $days;
    }
    
    /**
     * Jours du mois exclus
     * @return string
     */
    protected function get_days_month_content_form() {
        $day_month="
		<table>";
        for ($j=0;$j<3;$j++) {
            $days_t="<tr>";
            $days_v="<tr>";
            for ($i=0; $i<15; $i++) {
                if (($j*(14+1)+$i+1)>31) break;
                $tmp = '';
                if (substr($this->day_month, ($j*(15)+$i), 1)) {
                    $tmp = substr($this->day_month, ($j*(15)+$i), 1);
                }
                $days_t .= $this->get_display_cell_label('day_month_'.($j*(15)+$i+1), ($j*(14+1)+$i+1));
                $days_v .= $this->get_display_cell_checkbox('day_month', ($j*(15)+$i+1), $tmp);
            }
            $days_v.="</tr>";
            $days_t.="</tr>";
            $day_month.=$days_t."\n".$days_v."\n";
        }
        $day_month.="
		</table>";
        return $day_month;
    }
    
    /**
     * Semaines dans le mois exclues
     */
    protected function get_week_month_content_form() {
        $week_month="
		<table>";
        $days_t="<tr>";
        $days_v="<tr>";
        for ($i=1; $i<7; $i++) {
            $tmp = '';
            if (substr($this->week_month, $i-1, 1)) {
                $tmp = substr($this->week_month, $i-1, 1);
            }
            $days_t .= $this->get_display_cell_label('week_month_'.$i, $i);
            $days_v .= $this->get_display_cell_checkbox('week_month', $i, $tmp);
        }
        $days_v.="</tr>";
        $days_t.="</tr>";
        $week_month.=$days_t."\n".$days_v."
		</table>";
        return $week_month;
    }
    
    /**
     * Semaines dans l'année exclues
     */
    protected function get_week_year_content_form() {
        $week_year = "
		<table>";
        $nb_x=15;
        $nb=54;
        for ($j=0;$j<($nb/$nb_x);$j++) {
            $days_t = "<tr>";
            $days_v = "<tr>";
            for ($i=0; $i<$nb_x; $i++) {
                if (($j*($nb_x)+$i+1)>($nb)) break;
                $tmp = '';
                if (substr($this->week_year, ($j*($nb_x)+$i), 1)) {
                    $tmp = substr($this->week_year, ($j*($nb_x)+$i), 1);
                }
                $days_t .= $this->get_display_cell_label('week_year_'.($j*($nb_x)+$i+1), ($j*($nb_x)+$i+1));
                $days_v .= $this->get_display_cell_checkbox('week_year', ($j*($nb_x)+$i+1), $tmp);
            }
            $days_v .= "</tr>";
            $days_t .= "</tr>";
            $week_year .= $days_t."\n".$days_v."\n";
        }
        $week_year .= "
		</table>";
        return $week_year;
    }
    
    /**
     * Mois dans l'année exclus
     */
    protected function get_month_year_content_form() {
        global $msg;
        
        $month_year = "
		<table>";
        $nb_x=6;
        $nb=12;
        for ($j=0;$j<($nb/$nb_x);$j++) {
            $days_t = "<tr>";
            $days_v = "<tr>";
            for ($i=0; $i<$nb_x; $i++) {
                if (($j*($nb_x)+$i+1)>($nb)) break;
                $tmp = '';
                if (substr($this->month_year, ($j*($nb_x)+$i), 1)) {
                    $tmp = substr($this->month_year, ($j*($nb_x)+$i), 1);
                }
                $days_t .= $this->get_display_cell_label('month_year_'.($j*($nb_x)+$i+1), $msg[($j*($nb_x)+$i)+1006]);
                $days_v .= $this->get_display_cell_checkbox('month_year', ($j*($nb_x)+$i+1), $tmp);
            }
            $days_v .= "</tr>";
            $days_t .= "</tr>";
            $month_year.=$days_t."\n".$days_v."\n";
        }
        $month_year .= "
		</table>";
        return $month_year;
    }
    
    /**
     * Numérotation:  Numéro
     */
    protected function get_numero_content_form() {
        global $msg;
        
        $str_unite="
			<select id='num_date_unite' name='num_date_unite'>
				<option value='0' ".($this->num_date_unite == 0 ? "selected='selected'" : "").">".$msg['abonnements_periodicite_unite_jour']."</option>
				<option value='1' ".($this->num_date_unite == 1 ? "selected='selected'" : "").">".$msg['abonnements_periodicite_unite_mois']."</option>
				<option value='2' ".($this->num_date_unite == 2 ? "selected='selected'" : "").">".$msg['abonnements_periodicite_unite_annee']."</option>
			</select>";
        
        $num="<table>";
        
        $num_t="<tr>";
        $num_v="<tr>";
        $num_t.="<td>".$msg["abonnements_actif"]."</td>";
        $num_v.="<td><input type='checkbox' value='1' checked='checked'  disabled='disabled' name='facilite' id='facilite' /></td>";
        $num_t.="</tr>";
        $num_v.="</tr>";
        $num.=$num_t."\n".$num_v."\n";
        
        $num_t="<tr>";
        $num_v="<tr>";
        $num_t.="<td>".$msg["abonnements_cyclique"]."</td>";
        $num_v.="<td><input type='checkbox' value='1' ".($this->num_cycle ?"checked":"yes")." name='num_cycle[1]' id='num_cycle'  onClick='gere_num(\"num_cycle\");'/></td>";
        $num_t.="</tr>";
        $num_v.="</tr>";
        $num.=$num_t."\n".$num_v."\n";
        
        if(!isset($msg["abonnements_incrementation_selon_date_2"])) $msg["abonnements_incrementation_selon_date_2"]= '';
        $num_t="<tr>";
        $num_v="<tr>";
        $num_t.="<td></td>";
        $num_v.="<td></td>";
        $num_t.="<td><input type='radio' name='num_increment' id='num_increment' value='0' ".($this->num_increment==0 ?"checked":"yes").">".$msg["abonnements_incrementation_selon_numero_1"]."<input type='text' size='5' name='num_combien' id='num_combien' value='$this->num_combien'/>".$msg["abonnements_incrementation_selon_numero_2"]."</td>";
        $num_v.="<td><input type='radio' name='num_increment' id='num_increment1' value='1' ".($this->num_increment==1 ?"checked":"yes").">".$msg["abonnements_incrementation_selon_date_1"]."<input type='text' size='5' name='num_increment_date' id='num_increment_date' value='$this->num_increment_date'/>$str_unite".$msg["abonnements_incrementation_selon_date_2"]."</td>";
        $num_t.="<td>".$msg["abonnements_numero_depart"]."</td>";
        $num_v.="<td><input type='text' size='5' name='num_depart' id='num_depart' value='".$this->num_depart."' /></td>";
        $num_t.="</tr>";
        $num_v.="</tr>";
        $num.=$num_t."\n".$num_v."\n";
        
        $num.="</table>";
        return $num;
    }
    
    /**
     * Numérotation: Volume
     * @return string
     */
    protected function get_volume_content_form() {
        global $msg;
        
        $str_unite="
			<select id='vol_date_unite' name='vol_date_unite'>
				<option value='0' ".($this->vol_date_unite == 0 ? "selected='selected'" : "").">".$msg['abonnements_periodicite_unite_jour']."</option>
				<option value='1' ".($this->vol_date_unite == 1 ? "selected='selected'" : "").">".$msg['abonnements_periodicite_unite_mois']."</option>
				<option value='2' ".($this->vol_date_unite == 2 ? "selected='selected'" : "").">".$msg['abonnements_periodicite_unite_annee']."</option>
			</select>";
        
        $vol="<table>";
        
        $vol_t="<tr>";
        $vol_v="<tr>";
        $vol_t.="<td>".$msg["abonnements_actif"]."</td>";
        $vol_v.="<td><input type='checkbox' value='1' ".($this->vol_actif && $this->modele_id ?"checked":"yes")." name='vol_actif[1]' id='vol_actif'  onClick='gere_num(\"vol_actif\");'/></td>";
        $vol_t.="</tr>";
        $vol_v.="</tr>";
        $vol.=$vol_t."\n".$vol_v."\n";
        
        $vol_t="<tr>";
        $vol_v="<tr>";
        $vol_t.="<td>".$msg["abonnements_incrementation"]."</td>";
        $vol_t.="</tr>";
        $vol_v.="</tr>";
        $vol.=$vol_t."\n".$vol_v."\n";
        
        $vol_t="<tr>";
        $vol_v="<tr>";
        $vol_t.="<td></td>";
        $vol_v.="<td></td>";
        $vol_t.="<td><input type='radio' name='vol_increment' id='vol_increment' value='0' ".($this->vol_increment==0 ?"checked":"yes").">".$msg["abonnements_incrementation_selon_numero_1"]."<input type='text' size='5' name='vol_increment_numero' id='vol_increment_numero' value='$this->vol_increment_numero'/>".$msg["abonnements_incrementation_selon_numero_2"]."</td>";
        $vol_v.="<td><input type='radio' name='vol_increment' id='vol_increment1' value='1' ".($this->vol_increment==1 ?"checked":"yes").">".$msg["abonnements_incrementation_selon_date_1"]."<input type='text' size='5' name='vol_increment_date' id='vol_increment_date' value='$this->vol_increment_date'/>$str_unite".$msg["abonnements_incrementation_selon_date_2"]."</td>";
        $vol_t.="</tr>";
        $vol_v.="</tr>";
        $vol.=$vol_t."\n".$vol_v."\n";
        
        $vol_t="<tr>";
        $vol_v="<tr>";
        $vol_t.="<td>".$msg["abonnements_cyclique"]."</td>";
        $vol_v.="<td><input type='checkbox' value='1' ".($this->vol_cycle && $this->modele_id ?"checked":"yes")." name='vol_cycle[1]' id='vol_cycle'  onClick='gere_num(\"vol_cycle\");'/></td>";
        $vol_t.="<td>".$msg["abonnements_combien"]."</td>";
        $vol_v.="<td><input type='text' size='5' name='vol_combien' id='vol_combien' value='".$this->vol_combien."'/></td>";
        $vol_t.="<td>".$msg["abonnements_numero_depart"]."</td>";
        $vol_v.="<td><input type='text' size='5' name='vol_depart' id='vol_depart' value='".$this->vol_depart."'/></td>";
        $vol_t.="</tr>";
        $vol_v.="</tr>";
        $vol.=$vol_t."\n".$vol_v."\n";
        $vol.="</table>";
        return $vol;
    }
    
    /**
     * Numérotation: Tome
     * @return string
     */
    protected function get_tome_content_form() {
        global $msg;
        
        $str_unite="
			<select id='tom_date_unite' name='tom_date_unite'>
				<option value='0' ".($this->tom_date_unite == 0 ? "selected='selected'" : "").">".$msg['abonnements_periodicite_unite_jour']."</option>
				<option value='1' ".($this->tom_date_unite == 1 ? "selected='selected'" : "").">".$msg['abonnements_periodicite_unite_mois']."</option>
				<option value='2' ".($this->tom_date_unite == 2 ? "selected='selected'" : "").">".$msg['abonnements_periodicite_unite_annee']."</option>
			</select>";
        
        $tom="<table>";
        
        $tom_t="<tr>";
        $tom_v="<tr>";
        $tom_t.="<td>".$msg["abonnements_actif"]."</td>";
        $tom_v.="<td><input type='checkbox' value='1' ".($this->tom_actif && $this->modele_id ?"checked":"yes")." name='tom_actif[1]' id='tom_actif'   onClick='gere_num(\"tom_actif\");'/></td>";
        $tom_t.="</tr>";
        $tom_v.="</tr>";
        $tom.=$tom_t."\n".$tom_v."\n";
        
        $tom_t="<tr>";
        $tom_v="<tr>";
        $tom_t.="<td>".$msg["abonnements_incrementation"]."</td>";
        $tom_t.="</tr>";
        $tom_v.="</tr>";
        $tom.=$tom_t."\n".$tom_v."\n";
        
        $tom_t="<tr>";
        $tom_v="<tr>";
        $tom_t.="<td></td>";
        $tom_v.="<td></td>";
        $tom_t.="<td><input type='radio' name='tom_increment' id='tom_increment' value='0' ".($this->tom_increment==0 ?"checked":"yes").">".$msg["abonnements_incrementation_selon_volume_1"]."<input type='text' size='5' name='tom_increment_numero' id='tom_increment_numero' value='$this->tom_increment_numero'/>".$msg["abonnements_incrementation_selon_volume_2"]."</td>";
        $tom_v.="<td><input type='radio' name='tom_increment' id='tom_increment1'  value='1' ".($this->tom_increment==1 ?"checked":"yes").">".$msg["abonnements_incrementation_selon_date_1"]."<input type='text' size='5' name='tom_increment_date' id='tom_increment_date' value='$this->tom_increment_date'/>$str_unite".$msg["abonnements_incrementation_selon_date_2"]."</td>";
        $tom_t.="</tr>";
        $tom_v.="</tr>";
        $tom.=$tom_t."\n".$tom_v."\n";
        
        $tom_t="<tr>";
        $tom_v="<tr>";
        $tom_t.="<td>".$msg["abonnements_cyclique"]."</td>";
        $tom_v.="<td><input type='checkbox' value='1' ".($this->tom_cycle && $this->modele_id ?"checked":"yes")." name='tom_cycle[1]' id='tom_cycle' onClick='gere_num(\"tom_cycle\");'/></td>";
        $tom_t.="<td>".$msg["abonnements_combien"]."</td>";
        $tom_v.="<td><input type='text' size='5' name='tom_combien' id='tom_combien' value='".$this->tom_combien."'/></td>";
        $tom_t.="<td>".$msg["abonnements_numero_depart"]."</td>";
        $tom_v.="<td><input type='text' size='5' name='tom_depart' id='tom_depart' value='".$this->tom_depart."'/></td>";
        $tom_t.="</tr>";
        $tom_v.="</tr>";
        $tom.=$tom_t."\n".$tom_v."\n";
        
        $tom.="</table>";
        return $tom;
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
            return pmb_sql_value("SELECT DATE_ADD('".$date_debut."', INTERVAL 1 YEAR)");
        } else {
            return $this->date_fin;
        }
    }
    
    public function get_content_form() {
        global $msg, $charset;
        global $modele_content_form;
        
        $content_form = $modele_content_form;
        $content_form = str_replace("!!modele_name!!",htmlentities($this->modele_name,ENT_QUOTES,$charset), $content_form);
        
        //Périodicité
        $requete="select periodicite_id,libelle from abts_periodicites";
        $resultat=pmb_mysql_query($requete);
        $liste_perio="<select name='num_periodicite'>\n<option value='0'>".$msg['abonnements_periodicite_manuel']."</option>\n";
        while ($rp=pmb_mysql_fetch_object($resultat)) {
            $liste_perio.="<option value='".$rp->periodicite_id."' ".($this->num_periodicite==$rp->periodicite_id?"selected":"").">".htmlentities($rp->libelle,ENT_QUOTES,$charset)."</option>\n";
        }
        $liste_perio.="</select>";
        $content_form=str_replace("!!num_periodicite!!",$liste_perio,$content_form);
        
        //Durée d'abonnement
        $content_form=str_replace("!!duree_abonnement!!",$this->duree_abonnement,$content_form);
        
        //Date de début
        if (!$this->date_debut || $this->date_debut == "0000-00-00") {
            $date_debut=date("Y-m-d",time());
        } else {
            $date_debut=$this->date_debut;
        }
        $content_form=str_replace("!!date_debut!!", $date_debut,$content_form);
        
        //Date de fin
        
        $date_fin = $this->get_date_fin($date_debut);
        $content_form=str_replace("!!date_fin!!", $date_fin,$content_form);
        
        //Jours de la semaine exclus
        $content_form=str_replace("!!days!!",$this->get_days_content_form(),$content_form);
        
        //Jours du mois exclus
        $content_form=str_replace("!!days_month!!",$this->get_days_month_content_form(),$content_form);
        
        //Semaines dans le mois exclues
        $content_form=str_replace("!!week_month!!",$this->get_week_month_content_form(),$content_form);
        
        //Semaines dans l'année exclues
        $content_form=str_replace("!!week_year!!",$this->get_week_year_content_form(),$content_form);
        
        //Mois dans l'année exclus
        $content_form=str_replace("!!month_year!!",$this->get_month_year_content_form(),$content_form);
        
        //Numérotation:  Numéro
        $content_form=str_replace("!!numero!!",$this->get_numero_content_form(),$content_form);
        
        //Numérotation: Volume
        $content_form=str_replace("!!volume!!",$this->get_volume_content_form(),$content_form);
        
        //Numérotation: Tome
        $content_form=str_replace("!!tome!!",$this->get_tome_content_form(),$content_form);
        
        $format="<table>";
        
        $format_t="<tr>";
        $format_v="<tr>";
        $format_t.="<td>".$msg["abonnements_format_numero"]."</td>";
        $format_v.="<td><input type='text' size='100' name='format_aff' id='format_aff' value='".htmlentities($this->format_aff,ENT_QUOTES,$charset)."'/></td>";
        $format_t.="</tr>";
        $format_v.="</tr>";
        $format.=$format_t."\n".$format_v."\n";
        
        $content_form=str_replace("!!format!!",$format,$content_form);
        
        $format_t="<tr>";
        $format_v="<tr>";
        $format_t.="<td>".$msg["abonnements_format_periode"]."</td>";
        $format_v.="<td><input type='text' size='100' name='format_periode' id='format_periode' value='".htmlentities($this->format_periode,ENT_QUOTES,$charset)."'/></td>";
        $format_t.="</tr>";
        $format_v.="</tr>";
        $format=$format_t."\n".$format_v."\n";
        
        $format.="</table>";
        
        $content_form=str_replace("!!format_periode!!",$format,$content_form);
        
        return $content_form;
    }
    
    public function get_calendar() {
        global $msg, $charset;
        global $serial_id;
        
        $calend="
			<script type=\"text/javascript\" src='./javascript/select.js'></script>
			<script type=\"text/javascript\" src='./javascript/ajax.js'></script>";
        
        $calend.= <<<ENDOFTEXT
			<script type="text/javascript">
			function ad_date(obj,e) {
				if(!e) e=window.event;
				var tgt = e.target || e.srcElement; // IE doesn't use .target
				var strid = tgt.id;
				var type = tgt.tagName;
				e.cancelBubble = true;
				if (e.stopPropagation) e.stopPropagation();
				var id_obj=document.getElementById(obj);
				var pos=findPos(id_obj);
				if(type!='A'){
					var url="./catalog/serials/modele/modele_parution_edition.php?act=change&serial_id=!!serial_id!!&modele_id=!!modele_id!!&date_parution="+obj+"&type_serie=1&numero=";
				}
				else{
					var url="./catalog/serials/modele/modele_parution_edition.php?serial_id=!!serial_id!!&modele_id=!!modele_id!!&date_parution="+obj+"&type_serie=1&numero=";
				}
				var notice_view=document.createElement("iframe");
				notice_view.setAttribute('id','frame_periodique');
				notice_view.setAttribute('name','periodique');
				notice_view.src=url;
				
				var att=document.getElementById("att");
				notice_view.style.visibility="hidden";
				notice_view.style.display="block";
				notice_view=att.appendChild(notice_view);
				if(type=='A'){
					w=notice_view.clientWidth;
					h=notice_view.clientHeight;
					notice_view.style.left=pos[0]+"px";
					notice_view.style.top=pos[1]+"px";
					notice_view.style.visibility="visible";
					}
			}
			function kill_frame_periodique() {
				var notice_view=document.getElementById("frame_periodique");
				notice_view.parentNode.removeChild(notice_view);
			}
			</script>
ENDOFTEXT;
        $calend=str_replace("!!serial_id!!",$serial_id,$calend);
        $calend=str_replace("!!modele_id!!",$this->modele_id,$calend);
        
        $base_url="./catalog.php?categ=serials&sub=modele&serial_id=".$serial_id."&modele_id=".$this->modele_id;
        $base_url_mois='';
        
        $calend.= "<div id='calendrier_tab' style='width:99%'>" ;
        $date = $this->date_debut;
        $calend.= "<A name='ancre_calendrier'></A>";
        
        $year=pmb_sql_value("SELECT YEAR('".$date."')");
        $cur_year=$year;
        //debut expand
        $calend.="
		<div class='row'>&nbsp;</div>
		<div id='abts_year_$year' class='notice-parent'>
			<img src='".get_url_icon('minus.gif')."' class='img_plus' name='imEx' id='abts_year_".$year."Img' title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' onClick=\"expandBase('abts_year_".$year."', true); return false;\" />
			<span class='notice-heada'>
				".$year."
    		</span>
		</div>
		<div id='abts_year_$year"."Child' startOpen='Yes' class='notice-child' style='margin-bottom:6px;width:94%'> ";
        
        $i=pmb_sql_value("SELECT MONTH('".$date."')");
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
        do {
            $year=pmb_sql_value("SELECT YEAR('".$date."')");
            if($year!=$cur_year){
                $calend.= "</div>";
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
            $i=pmb_sql_value("SELECT MONTH('".$date."')");
            
            if ($i==1 || $i==4 || $i==7 || $i==10 ) $calend.= "<div class='row' style='padding-top: 5px'><div class='colonne3'>";
            else
                $calend.= "<div class='colonne3' style='padding-left: 3px'>";
                $calend.= pmb_bidi(calendar_gestion(str_replace("-","",$date), 0, $base_url, $base_url_mois,0,$this->modele_id,0));
                $calend.= "</div>\n";
                if ($i==3 || $i==6 || $i==9 || $i==12 ) $calend.="</div>\n";
                
                $date=pmb_sql_value("SELECT DATE_ADD('".$date."', INTERVAL 1 MONTH)");
                $diff=pmb_sql_value("SELECT DATEDIFF('".$this->get_date_fin()."','".$date."')");
        } while($diff>=0);
        //fin expand
        $calend.= "</div>";
        $calend.= "</div>\n";
        $calend.="<script type='text/javascript'>parent.location.href='#ancre_calendrier';</script>";
        return $calend;
    }
    
    public function show_form() {
        global $modele_form;
        global $serial_header;
        global $msg;
        global $charset;
        global $tpl_del_bouton,$tpl_copy_bouton;
        
        $r=$serial_header.$modele_form;
        
        if (!$this->modele_id) {
            $r=str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg["abts_modeles_add_title"], $r);
            $r=str_replace('!!libelle_form!!', $msg["abts_modeles_add_title"], $r);
            // 			// Valeur par défaut
            $this->format_aff = sprintf($msg['abts_no'],"\$NUM;");
            $this->format_periode = sprintf($msg['abts_periode'],"\$DATE;");
        } else {
            $r=str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg["abts_modeles_modify_title"], $r);
            $r=str_replace('!!libelle_form!!', $msg["abts_modeles_modify_title"], $r);
        }
        //Construction du formulaire
        if ($this->modele_id){
            $del_bouton=$tpl_del_bouton;
            $copy_bouton=$tpl_copy_bouton;
        }else{
            $copy_bouton=$del_bouton="";
        }
        $this->getData();
        $r=str_replace("!!del_button!!",$del_bouton,$r);
        $r=str_replace("!!copy_bouton!!",$copy_bouton,$r);
        
        //Remplacement des valeurs
        $r=str_replace("!!modele_id!!",htmlentities($this->modele_id,ENT_QUOTES,$charset),$r);
        
        //Notice mère
        $perio=new serial_display($this->num_notice,1);
        $r=str_replace("!!num_notice_libelle!!",$perio->header,$r);
        $r=str_replace("!!num_notice!!",$this->num_notice,$r);
        
        
        $r=str_replace("!!content_form!!",$this->get_content_form(),$r);
        
        
        global $serial_id;
        $r=str_replace("!!action!!","./catalog.php?categ=serials&sub=modele&serial_id=".$serial_id,$r);
        $r=str_replace("!!serial_id!!",$serial_id,$r);
        
        
        if ($this->modele_id) {
            $r .= $this->get_calendar();
        }
        return $r;
    }
    
    public function gen_date(){
        if($this->modele_id) {
            $dummy = "delete FROM abts_grille_modele WHERE num_modele='".$this->modele_id."' ";
            pmb_mysql_query($dummy);
            
            $date = construitdateheuremysql($this->date_debut);
            $date_fin = construitdateheuremysql($this->date_fin);
            
            //Lire la périodicté
            $duree=1;
            $requete="select duree,unite from abts_periodicites where periodicite_id='".$this->num_periodicite."'";
            $resultat=pmb_mysql_query($requete);
            if($r=pmb_mysql_fetch_object($resultat)) {
                $duree=$r->duree;
                $unite=$r->unite;
                do {
                    $dayofweek=pmb_sql_value("SELECT DAYOFWEEK('".$date."')");	//1 = Dimanche, 2 = Lundi, ... 7 = Samedi
                    if($dayofweek==1)$dayofweek=8;
                    $dayofweek--;
                    $day=pmb_sql_value("SELECT DAYOFMONTH('".$date."')");	// 1 à 31
                    $month=pmb_sql_value("SELECT MONTH('".$date."')");	//1 à 12
                    $week=pmb_sql_value("SELECT WEEK('".$date."',5)") + 1;//0 ... 53
                    
                    //calcul numero de semaine dans le mois
                    $weekofmonth=($day+7-$dayofweek)/7+1;
                    
                    //Mois dans l'année exclu
                    if (substr($this->month_year, $month-1, 1)) {
                        if (substr($this->week_year, $week-1, 1)) {
                            if (substr($this->week_month, $weekofmonth-1, 1)) {
                                if (substr($this->day_month, $day-1, 1)) {
                                    if (substr($this->days, $dayofweek-1, 1)) {
                                        //c'est un jour prévu de réception
                                        $requete = "INSERT INTO abts_grille_modele SET num_modele='".$this->modele_id."', date_parution ='".$date."', type_serie = '1'";
                                        pmb_mysql_query($requete);
                                    }
                                }
                            }
                        }
                    }
                    // Calcul de la date suivante à analyser et la sortie du while
                    if($unite==0) $sql_add="INTERVAL ".$duree." DAY";
                    if($unite==1) $sql_add="INTERVAL ".$duree." MONTH";
                    if($unite==2) $sql_add="INTERVAL ".$duree." YEAR";
                    $date=pmb_sql_value("SELECT DATE_ADD('$date', $sql_add)");
                    $diff=pmb_sql_value("SELECT DATEDIFF('$date_fin','$date')");
                }
                while(($diff>=0) && ($duree != 0));
            }
        }
    }
    
    public function update() {
        global $msg;
        global $include_path;
        global $act;
        
        if(!$this->modele_name)	return false;
        
        // nettoyage des valeurs en entrée
        $this->modele_name = clean_string($this->modele_name);
        
        // construction de la requête
        $requete = "SET modele_name='".addslashes($this->modele_name)."', ";
        $requete .= "num_notice='$this->num_notice', ";
        $requete .= "num_periodicite='$this->num_periodicite', ";
        $requete .= "duree_abonnement='$this->duree_abonnement', ";
        $requete .= "date_debut='$this->date_debut', ";
        $requete .= "date_fin='$this->date_fin', ";
        $requete .= "days='$this->days', ";
        $requete .= "day_month='$this->day_month', ";
        $requete .= "week_month='$this->week_month', ";
        $requete .= "week_year='$this->week_year', ";
        $requete .= "month_year='$this->month_year', ";
        
        $requete .= "num_cycle='$this->num_cycle', ";
        $requete .= "num_increment='$this->num_increment', ";
        $requete .= "num_combien='$this->num_combien', ";
        $requete .= "num_date_unite='$this->num_date_unite', ";
        $requete .= "num_increment_date='$this->num_increment_date', ";
        $requete .= "num_depart='$this->num_depart', ";
        $requete .= "vol_actif='$this->vol_actif', ";
        $requete .= "vol_increment='$this->vol_increment', ";
        $requete .= "vol_date_unite='$this->vol_date_unite', ";
        $requete .= "vol_increment_numero='$this->vol_increment_numero', ";
        $requete .= "vol_increment_date='$this->vol_increment_date', ";
        $requete .= "vol_cycle='$this->vol_cycle', ";
        $requete .= "vol_combien='$this->vol_combien', ";
        $requete .= "vol_depart='$this->vol_depart', ";
        $requete .= "tom_actif='$this->tom_actif', ";
        $requete .= "tom_increment='$this->tom_increment', ";
        $requete .= "tom_date_unite='$this->tom_date_unite', ";
        $requete .= "tom_increment_numero='$this->tom_increment_numero', ";
        $requete .= "tom_increment_date='$this->tom_increment_date', ";
        $requete .= "tom_cycle='$this->tom_cycle', ";
        $requete .= "tom_combien='$this->tom_combien', ";
        $requete .= "tom_depart='$this->tom_depart', ";
        $requete .= "format_aff='".addslashes($this->format_aff)."', ";
        $requete .= "format_periode='".addslashes($this->format_periode)."' ";
        
        if($this->modele_id) {
            // update: s'assurer que le nom de modèle n'existe pas déjà
            $dummy = "SELECT * FROM abts_modeles WHERE modele_name='".addslashes($this->modele_name)."' and num_notice='".$this->num_notice."' and modele_id!='".$this->modele_id."' ";
            $check = pmb_mysql_query($dummy);
            if(pmb_mysql_num_rows($check)) {
                require_once("$include_path/user_error.inc.php");
                warning($msg["abonnements_titre_creation_edition_modele"], $msg["abonnements_erreur_creation_doublon_modele"]." ($this->modele_name).");
                return FALSE;
            }
            $requete = 'UPDATE abts_modeles '.$requete;
            $requete .= ' WHERE modele_id='.$this->modele_id.' LIMIT 1;';
            if(pmb_mysql_query($requete)) {
                
                if($act=="gen") $this->gen_date();
                return TRUE;
            }else {
                require_once("$include_path/user_error.inc.php");
                warning($msg["abonnements_titre_creation_edition_modele"], $msg["abonnements_titre_creation_edition_modele_impossible"]);
                return FALSE;
            }
        } else {
            
            // s'assurer que le modèle n'existe pas déjà
            $dummy = "SELECT * FROM abts_modeles WHERE modele_name='".addslashes($this->modele_name)."' and num_notice='".$this->num_notice."'";
            $check = pmb_mysql_query($dummy);
            if(pmb_mysql_num_rows($check)) {
                require_once("$include_path/user_error.inc.php");
                warning($msg["abonnements_titre_creation_edition_modele"], $msg["abonnements_erreur_creation_doublon_modele"]." ($this->modele_name).");
                return FALSE;
            }
            $requete = 'INSERT INTO abts_modeles '.$requete.';';
            
            if(pmb_mysql_query($requete)) {
                $this->modele_id=pmb_mysql_insert_id();
                
                if($act=="gen") $this->gen_date();
                return TRUE;
            } else {
                require_once("$include_path/user_error.inc.php");
                warning($msg["abonnements_titre_creation_edition_modele"], $msg["abonnements_titre_creation_edition_modele_impossible"]);
                return FALSE;
            }
        }
    }
    
    public function delete() {
        //Je supprime les abonnements liés au modèle
        $requete="SELECT abt_id FROM abts_abts_modeles WHERE modele_id='".$this->modele_id."'";
        $res=pmb_mysql_query($requete);
        if(pmb_mysql_num_rows($res)){
            while ($ligne=pmb_mysql_fetch_object($res)) {
                $mon_abt= new abts_abonnement($ligne->abt_id);
                $mon_abt->delete();
            }
        }
        
        $dummy = "delete FROM abts_modeles WHERE modele_id='".$this->modele_id."' ";
        pmb_mysql_query($dummy);
        
        $dummy = "delete FROM abts_grille_modele WHERE num_modele='".$this->modele_id."' ";
        pmb_mysql_query($dummy);
    }
    
    public function set_properties_from_form() {
        global $modele_name, $num_notice, $num_periodicite, $duree_abonnement, $date_debut, $date_fin;
        global $days, $day_month, $week_month, $week_year, $month_year;
        global $num_cycle, $num_combien, $num_depart;
        global $vol_actif,$vol_increment,$vol_date_unite,$vol_increment_numero, $vol_increment_date,$vol_cycle,$vol_combien,$vol_depart;
        global $tom_actif,$tom_increment,$tom_date_unite,$tom_increment_numero, $tom_increment_date,$tom_cycle,$tom_combien,$tom_depart;
        global $format_aff, $format_periode;
        
        $this->modele_name= stripslashes($modele_name);
        $this->num_notice= $num_notice;
        $this->num_periodicite= $num_periodicite;
        $this->duree_abonnement= $duree_abonnement;
        $this->date_debut=$date_debut;
        $this->date_fin= $date_fin;
        $this->days=$this->calc_selection($days,7);
        $this->day_month=$this->calc_selection($day_month,31);
        $this->week_month=$this->calc_selection($week_month,6);
        $this->week_year= $this->calc_selection($week_year,54);
        $this->month_year=$this->calc_selection($month_year,12);
        $this->num_cycle = 1-$this->calc_selection($num_cycle,1);
        $this->num_combien = $num_combien;
        $this->num_depart = $num_depart;
        $this->vol_actif = 1-$this->calc_selection($vol_actif,1);
        $this->vol_increment = $vol_increment;
        $this->vol_date_unite = $vol_date_unite;
        $this->vol_increment_numero = $vol_increment_numero;
        $this->vol_increment_date = $vol_increment_date;
        $this->vol_cycle = 1-$this->calc_selection($vol_cycle,1);
        $this->vol_combien = $vol_combien;
        $this->vol_depart = $vol_depart;
        $this->tom_actif = 1-$this->calc_selection($tom_actif,1);
        $this->tom_increment = $tom_increment;
        $this->tom_date_unite = $tom_date_unite;
        $this->tom_increment_numero = $tom_increment_numero;
        $this->tom_increment_date = $tom_increment_date;
        $this->tom_cycle = 1-$this->calc_selection($tom_cycle,1);
        $this->tom_combien = $tom_combien;
        $this->tom_depart = $tom_depart;
        $this->format_aff = stripslashes($format_aff);
        $this->format_periode = stripslashes($format_periode);
    }
    
    public function proceed() {
        global $include_path,$charset, $current_module;
        global $act;
        global $serial_id,$msg,$date_parution;
        global $new_serial_id,$num_increment,$num_date_unite,$num_increment_date;
        global $include_path;
        
        switch ($act) {
            case 'update':
                // mise à jour modèle
                $this->set_properties_from_form();
                $this->num_increment=$num_increment;
                $this->num_date_unite=$num_date_unite;
                $this->num_increment_date=$num_increment_date;
                $this->update();
                print $this->show_form();
                break;
            case 'gen':
                // mise à jour modèle
                $this->set_properties_from_form();
                $this->update();
                print $this->show_form();
                break;
            case 'copy':
                // mise à jour modèle
                $requete = "select type_serie, numero,date_parution,type_serie,nombre_recu from abts_grille_modele where num_modele='".$this->modele_id."'";
                $resultat=pmb_mysql_query($requete);
                
                //Création du nouveau modèle
                $this->modele_id='';
                $this->num_notice=$new_serial_id;
                $this->modele_name= clean_string($this->modele_name);
                $serial_id=$new_serial_id;
                $this->update();
                $requete = "delete FROM abts_grille_modele WHERE num_modele='".$this->modele_id."'";
                pmb_mysql_query($requete);
                //recopie des infos du calendrier
                if(pmb_mysql_num_rows($resultat)) {
                    while(($r=pmb_mysql_fetch_object($resultat))){
                        $date_parution=$r->date_parution;
                        $type_serie=$r->type_serie;
                        $nombre_recu=$r->nombre_recu;
                        $numero=$r->numero;
                        
                        $requete = "INSERT INTO abts_grille_modele SET num_modele='".$this->modele_id."', date_parution ='".$date_parution."', type_serie = '".$type_serie."', nombre_recu= '".$nombre_recu."'";
                        pmb_mysql_query($requete);
                    }
                }
                print $this->show_form();
                break;
            case 'del':
                // Verif si abonnements associés
                $requete="select abt_name,abts_abts.abt_id as ab_id from abts_abts_modeles,abts_abts where modele_id='".$this->modele_id."' and abts_abts.abt_id=abts_abts_modeles.abt_id";
                $resultat=pmb_mysql_query($requete);
                if (pmb_mysql_num_rows($resultat)) {
                    while(($r=pmb_mysql_fetch_object($resultat))) {
                        $liste.="<a href=\"catalog.php?categ=serials&sub=abon&serial_id=".$this->num_notice."&abt_id=".$r->ab_id."\">".$r->abt_name."</a><br />";
                    }
                    require_once("$include_path/user_error.inc.php");
                    warning(htmlentities($msg["abonnements_titre_effacement_modele"],ENT_QUOTES,$charset), htmlentities($msg["abonnements_effacement_modele_erreur"],ENT_QUOTES,$charset)."<br /><strong>".$liste."</strong>");
                    print $this->show_form();
                    return;
                }
                $this->delete();
                print "<div class='row'><div class='msg-perio'>".$msg['maj_encours']."</div></div>";
                $id_form = md5(microtime());
                $retour = "./catalog.php?categ=serials&sub=view&serial_id=".$serial_id."&view=modele";
                print "<form class='form-".$current_module."' name=\"dummy\" method=\"post\" action=\"".$retour."\" style=\"display:none\">
					<input type=\"hidden\" name=\"id_form\" value=\"".$id_form."\">
					</form>
					<script type=\"text/javascript\">document.dummy.submit();</script>
					</div>";
                break;
            default:
                print $this->show_form();
                break;
        }
    }
    
    public function calc_selection($val,$size){
        $ret='';
        for ($i=0; $i<$size; $i++) {
            if(!isset($val[$i+1])) $ret .='1'; else $ret .='0';
        }
        return $ret;
    }
}

class abts_modeles {
    
    public $modeles = array(); //Tableau des IDs des modèles
    
    public function __construct($id_perio) {
        $id_perio = intval($id_perio);
        $requete="select modele_id from abts_modeles where num_notice=$id_perio";
        $resultat=pmb_mysql_query($requete);
        while ($r=pmb_mysql_fetch_object($resultat)) {
            $modele=new abts_modele($r->modele_id);
            if (!$modele->error) $this->modeles[]=$modele;
        }
    }
    
    public function show_list() {
        global $modele_list,$msg;
        $r=$modele_list;
        $modeles="";
        if (count($this->modeles)) {
            for ($i=0; $i<count($this->modeles); $i++) {
                $modeles.=$this->modeles[$i]->show_modele();
            }
        } else $modeles=$msg["abts_modeles_no_modele"];
        return str_replace("!!modele_list!!",$modeles,$r);
    }
}