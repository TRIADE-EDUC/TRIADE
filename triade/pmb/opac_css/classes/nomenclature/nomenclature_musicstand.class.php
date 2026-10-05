<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: nomenclature_musicstand.class.php,v 1.7 2024/04/25 12:30:32 rtigero Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path . "/nomenclature/nomenclature_instrument.class.php");

/**
 * class nomenclature_musicstand
 * Représente un pupitre
 */
class nomenclature_musicstand
{

    /**
     * Aggregations:
     */

    /**
     * Compositions:
     */

    /**
     * * Attributes: **
     */
    protected $id;

    protected $family_num;

    /**
     * Famille auquel appartient le pupitre
     *
     * @access protected
     */
    protected $family;

    /**
     * Nom du pupitre
     *
     * @access protected
     */
    protected $name;

    /**
     * Effectif pour le pupitre
     *
     * @access protected
     */
    protected $effective;

    /**
     * Instrument standard du pupitre
     *
     * @access protected
     */
    protected $standard_instrument;

    /**
     * Liste des instruments composants le pupitre
     *
     * @access protected
     */
    protected $instruments;

    /**
     * Le pupitre est il divisable en partie
     *
     * @access protected
     */
    protected $divisable;

    /**
     * Booléen qui indique si le pupitre est valide
     *
     * @access protected
     */
    protected $valid = false;

    /**
     * Nomenclature du pupitre abrégée
     *
     * @access protected
     */
    protected $abbreviation;

    /**
     * Flag pour savoir si le pupitre est lié aux ateliers
     *
     * @access protected
     */
    protected $used_by_workshops;

    /**
     * Ordre du musicstand
     *
     * @access protected
     */
    protected $order;

    /**
     * Tableau d'instances
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * Constructueur
     *
     * @return void
     * @access public
     */
    public function __construct($id = 0)
    {
        $this->id = intval($id);
        $this->fetch_datas();
    }

    // end of member function __construct
    protected function fetch_datas()
    {
        $this->name = '';

        if ($this->id) {
            $query = "select nomenclature_musicstands.musicstand_order, nomenclature_musicstands.musicstand_name, nomenclature_musicstands.musicstand_famille_num, nomenclature_musicstands.musicstand_division, nomenclature_musicstands.musicstand_workshop, nomenclature_instruments.id_instrument, nomenclature_instruments.instrument_code, nomenclature_instruments.instrument_name from nomenclature_musicstands left join nomenclature_instruments on nomenclature_musicstands.id_musicstand = nomenclature_instruments.instrument_musicstand_num and instrument_standard = 1 where nomenclature_musicstands.id_musicstand = " . $this->id;
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    $this->set_name($row->musicstand_name);
                    if ($row->id_instrument) {
                        $this->set_standard_instrument(nomenclature_instrument::get_instance($row->id_instrument));
                    }
                    $this->order = $row->musicstand_order;
                    $this->set_divisable($row->musicstand_division);
                    $this->set_used_by_workshops($row->musicstand_workshop);
                    $this->family_num = $row->musicstand_famille_num;
                }
            }
        }
    }

    public function get_content_form()
    {
        global $msg;

        $interface_content_form = new interface_content_form(static::class);
        $interface_content_form->add_element('name', 'admin_nomenclature_family_musicstand_form_name')->add_input_node('text', $this->name);
        $interface_content_form->add_element('division', 'admin_nomenclature_family_musicstand_form_division')->add_input_node('boolean', $this->divisable);
        $interface_content_form->add_element('workshop', 'admin_nomenclature_family_musicstand_form_workshop')->add_input_node('boolean', $this->used_by_workshops);

        $tpl_instruments = "";
        if ($this->id) {
            $tpl_instruments = "
			<script type='text/javascript' src='./javascript/sorttable.js'></script>
			<table class='sortable'>
				<tr>
					<th>" . $msg["admin_nomenclature_instrument_code"] . "
					</th>
					<th>" . $msg["admin_nomenclature_instrument_name"] . "
					</th>
					<th>" . $msg["admin_nomenclature_instrument_standard"] . "
					  (<input type='radio' name='standard' value='0' !!checked!! /> " . $msg["admin_nomenclature_instrument_standard_no"] . " )
					</th>
				</tr>
			";
            $flag_checked = 0;
            $instruments = $this->get_instruments();
            if (is_array($instruments)) {
                foreach ($instruments as $instrument) {
                    if ($instrument->get_standard()) {
                        $checked = "checked";
                        $flag_checked = 1;
                    } else
                        $checked = "";

                    $standard = "<input type='radio' name='standard' value='" . $instrument->get_id() . "' $checked />";
                    $tpl_instrument = "
					<tr>
						<td style=\"cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=instrument&sub=instrument&action=form&id=" . $instrument->get_id() . "';\">
						" . $instrument->get_code() . "
						</td>
						<td style=\"cursor: pointer\" onmousedown=\"document.location='./admin.php?categ=instrument&sub=instrument&action=form&id=" . $instrument->get_id() . "';\">
						" . $instrument->get_name() . "
						</td>
						<td>
						" . $standard . "
						</td>
					</tr>
					";
                    $tpl_instruments .= $tpl_instrument;
                }
            }
            $tpl_instruments .= "
			</table>";
			if (! $flag_checked) $checked = "checked";
            else
                $checked = "";
            $tpl_instruments = str_replace('!!checked!!', $checked, $tpl_instruments);
        }
        $interface_content_form->add_element('instruments', 'admin_nomenclature_family_musicstand_form_instruments')->add_html_node($tpl_instruments);

        return $interface_content_form->get_display();
    }

    public function get_form()
    {
        global $msg;

        $interface_form = new interface_admin_nomenclature_form('nomenclature_family_musicstand_form');
        $family_name = "<a href='./admin.php?categ=family&sub=family&action=form&id=" . $this->get_family()->get_id() . "'>" . $this->get_family()->get_name() . "</a>";
        if (! $this->id) {
            $interface_form->set_label(str_replace('!!famille_name!!', $family_name, $msg['admin_nomenclature_family_musicstand_form_add']));
        } else {
            $interface_form->set_label(str_replace('!!famille_name!!', $family_name, $msg['admin_nomenclature_family_musicstand_form_edit']));
        }
        $interface_form->set_object_id($this->id)
            ->set_object_type('family_musicstand')
            ->set_confirm_delete_msg($msg['confirm_suppr_de'] . " " . $this->name . " ?")
            ->set_content_form($this->get_content_form())
            ->set_table_name('nomenclature_musicstands')
            ->set_field_focus('name');
        return $interface_form->get_display();
    }

    public function set_properties_from_form()
    {
        global $name;
        global $division;
        global $workshop;
        global $standard;

        $this->name = stripslashes($name);
        $this->divisable = stripslashes($division);
        $this->used_by_workshops = stripslashes($workshop);
        $this->standard_instrument = stripslashes($standard);
    }

    public function save()
    {
        if (! $this->family_num) {
            return false;
        }
        $fields = "
		musicstand_famille_num='" . $this->family_num . "',
		musicstand_name='" . addslashes($this->name) . "',
		musicstand_division='" . addslashes($this->divisable) . "',
		musicstand_workshop='" . addslashes($this->used_by_workshops) . "'
		";
        if (! $this->id) { // Ajout
            $requete = "select max(musicstand_order) as ordre from nomenclature_musicstands where musicstand_famille_num=" . $this->family_num;
            $resultat = pmb_mysql_query($requete);
            $ordre_max = pmb_mysql_result($resultat, 0, 0);
            $req = "INSERT INTO nomenclature_musicstands SET $fields, musicstand_order=" . ($ordre_max + 1);
            pmb_mysql_query($req);
            $this->id = pmb_mysql_insert_id();
        } else {
            $req = "UPDATE nomenclature_musicstands SET $fields where id_musicstand=" . $this->id;
            pmb_mysql_query($req);
        }

        $req = "UPDATE nomenclature_instruments SET instrument_standard=0 where instrument_musicstand_num=" . $this->id;
        pmb_mysql_query($req);
        if ($this->standard_instrument) {
            $req = "UPDATE nomenclature_instruments SET instrument_standard=1 where id_instrument=" . $this->standard_instrument;
            pmb_mysql_query($req);
        }
    }

    public static function delete($id)
    {
        $id = intval($id);
        if ($id) {
            $req = "UPDATE nomenclature_instruments SET instrument_musicstand_num=0 where instrument_musicstand_num=" . $id;
            pmb_mysql_query($req);

            $req = "DELETE from nomenclature_musicstands WHERE id_musicstand=" . $id;
            pmb_mysql_query($req);
        }
    }

    public function set_used_by_workshops($used_by_workshop)
    {
        if ($used_by_workshop) {
            $this->used_by_workshops = true;
        } else {
            $this->used_by_workshops = false;
        }
    }

    public function get_used_by_workshops()
    {
        return $this->used_by_workshops;
    }

    public function set_divisable($divisable)
    {
        if ($divisable) {
            $this->divisable = true;
        } else {
            $this->divisable = false;
        }
    }

    public function get_divisable()
    {
        return $this->divisable;
    }

    public function set_family_num($family_num)
    {
        $this->family_num = $family_num;
    }

    public function get_family_num()
    {
        return $this->family_num;
    }

    /**
     * Setter
     *
     * @param
     *            nomenclature_family family Famille à associer
     *            
     * @return void
     * @access public
     */
    public function set_family($family)
    {
        $this->family = $family;
    }

    // end of member function set_family

    /**
     * Getter
     *
     * @return nomenclature_family
     * @access public
     */
    public function get_family()
    {
        if (! isset($this->family) && $this->family_num) {
            $this->family = nomenclature_family::get_instance($this->family_num);
        }
        return $this->family;
    }

    // end of member function get_family

    /**
     * Getter
     *
     * @return string
     * @access public
     */
    public function get_name()
    {
        return $this->name;
    }

    // end of member function get_name

    /**
     * Setter
     *
     * @param
     *            string name Nom du pupitre
     *            
     * @return void
     * @access public
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    // end of member function set_name

    /**
     * Getter
     *
     * @return integer
     * @access public
     */
    public function get_effective()
    {
        if($this->effective == 0) {
            $this->calc_effective();
        }
        return $this->effective;
    }
    
    // end of member function get_effective
    
    /**
     * Setter
     *
     * @param
     *            integer effective Effectif du pupitre
     *            
     * @return void
     * @access public
     */
    public function set_effective($effective)
    {
        $this->effective = $effective;
    }

    // end of member function set_effective

    /**
     * Getter
     *
     * @return nomenclature_instrument
     * @access public
     */
    public function get_standard_instrument()
    {
        return $this->standard_instrument;
    }

    // end of member function get_standard_instrument

    /**
     * Setter
     *
     * @param
     *            nomenclature_instrument standard_instrument Instrument standard du pupitre
     *            
     * @return void
     * @access public
     */
    public function set_standard_instrument($standard_instrument)
    {
        $this->standard_instrument = $standard_instrument;
    }

    // end of member function set_standard_instrument

    /**
     * Getter
     *
     * @return nomenclature_instrument
     * @access public
     */
    public function get_instruments($keepEmpty = false)
    {
        if (! isset($this->instruments) && ! $keepEmpty) {
            $query = "select * from nomenclature_instruments where instrument_musicstand_num=" . $this->id . " order by instrument_code";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    $this->add_instrument(nomenclature_instrument::get_instance($row->id_instrument, true));
                }
            }
        }
        return $this->instruments;
    }

    // end of member function get_instruments
    public function get_instruments_display()
    {
        $display = '';
        $instruments = $this->get_instruments();
        if (is_array($instruments)) {
            foreach ($instruments as $instrument) {
                if ($display) {
                    $display .= "<br />";
                }
                $display .= "<a href='./admin.php?categ=instrument&sub=instrument&action=form&id=" . $instrument->get_id() . "'>" . $instrument->get_code() . " ( " . $instrument->get_name() . " )</a>";
				if ($instrument->get_standard()) $display .= " *";
            }
        }
        return $display;
    }

    /**
     * Setter
     *
     * @param
     *            nomenclature_instrument instruments Tableau des instruments du pupitre
     *            
     * @return void
     * @access public
     */
    public function set_instruments($instruments)
    {
        $this->instruments = $instruments;
    }

    // end of member function set_instruments

    /**
     * Methode d'ajout d'instrument à la liste des instruments du pupitre
     *
     * @param
     *            nomenclature_instrument instrument Instrument du pupitre
     *            
     * @param
     *            bool reorder Forcer l'insertion à l'ordre de l'instrument (décale les autres)
     *            
     * @return void
     * @access public
     */
    public function add_instrument($instrument, $reorder = false)
    {
        $instrument->set_musicstand($this);
        $this->instruments[] = $instrument;
        if ($reorder) {
            $this->reorder();
        }
    }

    // end of member function add_instrument

    /**
     *
     * @param
     *            integer order Numéro d'ordre de l'instrument à supprimer de la liste
     *            
     * @param
     *            bool reorder Booléen pour forcer le recalcul de l'ordre de chaque instrument
     *            
     * @return void
     * @access public
     */
    public function delete_instrument($order, $reorder = false)
    {
        for ($i = 0; $i < count($this->instruments); $i ++) {
            $instrument = $this->instruments[$i];
            if ($instrument->get_order() == $order) {
                array_splice($this->instruments, $i, 1);
                break;
            }
        }
        if ($reorder) {
            $this->reorder();
        }
    }

    // end of member function delete_instrument

    /**
     * Méthode qui ré-ordonne la liste d'instrument
     *
     *
     * @return void
     * @access protected
     */
    protected function reorder()
    {
        $orders = array();
        for ($i = 0; $i < count($this->instruments); $i ++) {
            $instrument = $this->instruments[$i];
            $orders[$instrument->get_order()] = $i;
        }
        foreach ($orders as $i) {
            $instrument = $this->instruments[$i];
            $instrument->set_order($i + 1);
        }
    }

    // end of member function reorder

    /**
     * Méthode qui indique si un pupitre est bon
     *
     * @return bool
     * @access public
     */
    public function check()
    {}

    // end of member function check

    /**
     * Méthode qui calcule l'effectif du pupitre en fonction des effectifs de chaque
     * intrument
     *
     * @return void
     * @access protected
     */
    protected function calc_effective()
    {
        $this->effective = 0;
        foreach ($this->instruments as $instrument) {
            $this->effective += $instrument->get_effective();
        }
    }

    // end of member function calc_effective
    public function get_tree_informations()
    {
        $tree = array(
            'id' => $this->get_id(),
            'name' => $this->get_name(),
            'divisable' => $this->get_divisable(),
            'used_by_workshops' => $this->get_used_by_workshops()
        );
        if (is_object($this->get_standard_instrument())) {
            $tree['std_instrument'] = array(
                'id' => $this->get_standard_instrument()->get_id(),
                'code' => $this->get_standard_instrument()->get_code(),
                'name' => $this->get_standard_instrument()->get_name()
            );
        }
        return $tree;
    }

    public function get_id()
    {
        return $this->id;
    }

    /**
     * Setter
     *
     * @param
     *            string abbreviation Nomenclature abrégée
     *            
     * @return void
     * @access public
     */
    public function set_abbreviation($abbreviation)
    {
        $this->abbreviation = pmb_preg_replace('/\s+/', '', $abbreviation);
    }

    // end of member function set_abbreviation

    /**
     * Getter
     *
     * @return string
     * @access public
     */
    public function get_abbreviation()
    {
        return pmb_preg_replace('/\s+/', '', $this->abbreviation);
    }

    // end of member function get_abbreviation
    public function get_order()
    {
        return $this->order;
    }

    /**
     * Calcule et affecte la nomenclature abrégée à  partir de l'arbre
     *
     * @return void
     * @access public
     */
    public function calc_abbreviation()
    {
        $tinstruments = array();
        $musicstand_standard_all = true;
        if (is_array($this->instruments)) {
            foreach ($this->instruments as $instrument) {
                if (count($instrument->get_others_instruments())) {
                    $musicstand_standard_all = false;
                }
                $nomenclature_instrument = new nomenclature_instrument($instrument->get_id(), $instrument->get_code(), $instrument->get_name());
                $nomenclature_instrument->calc_abbreviation();
                if ($instrument->is_standard()) {
                    if ($instrument->get_part()) {
                        $tinstruments[$instrument->get_part()] = $nomenclature_instrument->get_abbreviation();
                    } else {
                        $tinstruments[$instrument->get_order()] = $nomenclature_instrument->get_abbreviation();
                    }
                } else {
                    $tinstruments[$instrument->get_order()] = $nomenclature_instrument->get_abbreviation();
                    $musicstand_standard_all = false;
                }
            }
            if ($musicstand_standard_all) {
                $this->set_abbreviation($this->effective);
            } else {
                ksort($tinstruments);
                $this->set_abbreviation($this->effective . "[" . implode(".", $tinstruments) . "]");
            }
        } else {
            $this->set_abbreviation("0");
        }
    }

    // end of member function calc_abbreviation
    public static function get_instance($id, $force_new_instance = false)
    {
    	if ($force_new_instance && isset(static::$instances[$id])) {
            unset(static::$instances[$id]);
        }
        if (! isset(static::$instances[$id])) {
            static::$instances[$id] = new nomenclature_musicstand($id);
        }
        return static::$instances[$id];
    }
} // end of nomenclature_musicstand

