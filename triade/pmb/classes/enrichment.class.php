<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: enrichment.class.php,v 1.13.4.1 2025/03/28 12:57:11 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path, $include_path;

require_once $class_path . "/connecteurs.class.php";
require_once $class_path . "/marc_table.class.php";
require_once $include_path . "/templates/enrichment.tpl.php";
require_once $class_path . "/search.class.php";

class enrichment
{
    public $enhancer = array();

    public $active = array();

    public $catalog;

    protected $params = array();

    protected $types_names = array();

    protected $enrichmentsTabHeaders = null;

    public function __construct()
    {
        $this->fetch_sources();
        $this->fetch_data();
    }

    /**
     * Recupere la liste des sources d'enrichissement
     */
    public function fetch_sources()
    {
        global $base_path, $msg;

        $this->parseType();

        $connectors = connecteurs::get_instance();
        $this->catalog = $connectors->catalog;

        // Recherche des sources pours lesquelles l'enrichissement est actif
        foreach ($this->catalog as $prop) {

            if ($prop['ENRICHMENT'] == "yes") {

                $conn_path = $base_path . "/admin/connecteurs/in/" . $prop['PATH'];
                $conn_class = $prop['NAME'];

                if (file_exists($conn_path . "/" . $conn_class . ".class.php")) {

                    require_once $conn_path . "/" . $conn_class . ".class.php";
                    $conn = new $conn_class($conn_path);

                    $conn->get_sources();

                    foreach ($conn->sources as $source_id => $s) {

                        if ($s['ENRICHMENT'] == 1) {
                            $enrichment_types = array();

                            $info = $conn->getTypeOfEnrichment($source_id);

                            for ($i = 0; $i < count($info['type']); $i ++) {
                                if (! is_array($info['type'][$i])) {
                                    $info['type'][$i] = array(
                                        'code' => $info['type'][$i],
                                        'label' => $msg[substr($this->types_names[$info['type'][$i]], 4)]
                                    );
                                } elseif (! $info['type'][$i]['label']) {
                                    $info['type'][$i]['label'] = $msg[substr($this->types_names[$info['type'][$i]], 4)];
                                }
                                if (! empty($s['TYPE_ENRICHEMENT_ALLOWED'])) {
                                    $enrichment_types[] = $info['type'][$i];
                                }
                            }

                            $this->enhancer[] = array(
                                'id' => $s['SOURCE_ID'],
                                'name' => $s['NAME'],
                                'enrichment_types' => $enrichment_types
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     *  Récupération des données existantes
     */
    public function fetch_data()
    {
        $rqt = "select * from sources_enrichment";
        $res = pmb_mysql_query($rqt);
        if (pmb_mysql_num_rows($res)) {
            while ($r = pmb_mysql_fetch_object($res)) {
                $this->active[$r->source_enrichment_typnotice . $r->source_enrichment_typdoc][] = $r->source_enrichment_num;
                $this->params[$r->source_enrichment_typnotice . $r->source_enrichment_typdoc][$r->source_enrichment_num] = unserialize($r->source_enrichment_params);
            }
        }
    }


    /**
     * Affichage du formulaire
     */
    public function show_form()
    {
        global $msg;

        $content_form = '';
        if (count($this->enhancer)) {
            // création du sélecteur...
            $select = "<select name='enrichment_select_source[!!key!!][]' id='enrichment_select_source_!!key!!' multiple>";
            foreach ($this->enhancer as $source) {
                $select .= "<option value='" . $source['id'] . "'>" . $source['name'] . "</option>";
            }

            $typnoti = array(
                'm' => $msg['type_mono'],
                's' => $msg['type_serial'],
                'a' => $msg['type_art'],
                'b' => $msg['type_bull']
            );
            // pour chaque type de document...
            $typdoc = new marc_list("doctype");
            $content_form = $this->generateSelectorScript();
            foreach ($typnoti as $tnoti => $notice) {
                $content = "
				<div class='row'>
					<table class='quadrille'>
						<tr>
							<th colspan='2'>" . $msg['admin_connecteurs_enrichment_default_value_form'] . "</th>
						</tr>
						<tr>
							<td colspan='2'>
							 " . $this->generateSelector($tnoti) . "
							</td>
						</tr>
						<tr><td colspan=2>&nbsp;</td></tr>
						<tr>
							<th>" . $msg['admin_connecteurs_enrichment_type_form'] . "</th>
							<th>" . $msg['admin_connecteurs_enrichment_enhancer_form'] . "</th>
						</tr>";
                $parity_source = 0;
                foreach ($typdoc->table as $tdoc => $document) {
                    if ($parity_source % 2) {
                        $pair_impair_type = "even";
                    } else {
                        $pair_impair_type = "odd";
                    }
                    $parity_source ++;
                    $content .= "
    				<tr class='$pair_impair_type'>
							<td>" . $typdoc->table[$tdoc] . "</td>
							<td>" . $this->generateSelector($tnoti . $tdoc, 1) . "</td>
						</tr>";
                }
                $content .= "
					</table>
				</div>";
                $content_form .= gen_plus("enrichment_" . $tnoti, $typnoti[$tnoti], $content);
            }
        } else {
            $content_form .= $msg['admin_connecteurs_enrichment_no_sources'];
        }
        $interface_form = new interface_admin_form('enrichment');
        $interface_form->set_label($msg["admin_connecteurs_enrichment_def"]);

        $interface_form->set_content_form($content_form);
        print $interface_form->get_display_parameters();
    }

    /**
     * Sauvegarde dans la BDD
     */
    public function update()
    {
        global $msg;
        global $enrichment_select_source;

        $typnoti = array(
            'm' => $msg['type_mono'],
            's' => $msg['type_serial'],
            'a' => $msg['type_art'],
            'b' => $msg['type_bull']
        );
        $typdoc = new marc_list("doctype");
        // on commence par vider la table...
        pmb_mysql_query("truncate table sources_enrichment");
        $this->active = array();
        $this->params = array();
        // et on remet tout...
        foreach ($typnoti as $tnoti => $notice) {
            // les valeurs par défaut
            if ( isset($enrichment_select_source[$tnoti]['sources']) && is_array($enrichment_select_source[$tnoti]['sources'])) {
                foreach ($enrichment_select_source[$tnoti]['sources'] as $source) {
                    $serialized_params = serialize($enrichment_select_source[$tnoti][$source]);
                    $rqt = "insert into sources_enrichment set source_enrichment_num = '$source', source_enrichment_typnotice = '$tnoti', source_enrichment_params = '" . $serialized_params . "'";
                    pmb_mysql_query($rqt);
                    $this->active[$tnoti][] = $source;
                    $this->params[$tnoti][$source] = $enrichment_select_source[$tnoti][$source];
                }
            }
            foreach ($typdoc->table as $tdoc => $document) {
                // les spécifiques
                if ($enrichment_select_source[$tnoti . $tdoc]['sources']) {
                    if (! in_array(0, $enrichment_select_source[$tnoti . $tdoc]['sources'])) {
                        foreach ($enrichment_select_source[$tnoti . $tdoc]['sources'] as $source) {
                            $serialized_params = serialize($enrichment_select_source[$tnoti . $tdoc][$source]);
                            $rqt = "insert into sources_enrichment set source_enrichment_num = '$source', source_enrichment_typnotice = '$tnoti', source_enrichment_typdoc = '$tdoc', source_enrichment_params = '" . $serialized_params . "'";
                            pmb_mysql_query($rqt);
                            $this->active[$tnoti . $tdoc][] = $source;
                            $this->params[$tnoti . $tdoc][$source] = $enrichment_select_source[$tnoti . $tdoc][$source];
                        }
                    }
                }
            }
        }
        $this->generateHeaders();
    }


    protected function generateSelector($type, $default_value = 0)
    {
        global $msg;

        $selector = "<div style='width:200px;float: left;'><h3>" . $msg['admin_connecteurs_enrichment_available_sources'] . "</h3>";
        $selector .= "<select name='enrichment_select_source[$type][sources][]' id='enrichment_select_source_$type' multiple type='" . $type . "' size='5'>";
        if ($default_value) {
            $selector .= "<option id='enrichment_select_source_" . $type . "_0' value='0' " . (! isset($this->active[$type]) ? "selected" : "") . ">" . $msg["admin_connecteurs_enrichment_enhancer_default"] . "</option>
				<script>
					document.getElementById('enrichment_select_source_" . $type . "_0').addEventListener('click', update_selected_sources_default, true);
				</script>";
        }
        foreach ($this->enhancer as $source) {
            $selector .= "<option id='enrichment_select_source_" . $type . "_" . $source['id'] . "' value='" . $source['id'] . "' " . (isset($this->active[$type]) && in_array($source['id'], $this->active[$type]) ? "selected" : "") . ">" . $source['name'] . "</option>
				<script>
					document.getElementById('enrichment_select_source_" . $type . "_" . $source['id'] . "').addEventListener('click', update_selected_sources, true);
				</script>";
        }
        $selector .= "</select></div>";

        $selector .= "<div style='display: inline-block;'>
				<h3>" . $msg['admin_connecteurs_enrichment_default_display'] . "</h3>";
        $selector .= "<ul id='enrichment_selected_sources_" . $type . "'>";

        if (! $default_value || isset($this->active[$type])) { // Si défaut est sélectionné, on n'affiche rien
            $order = 0;
            $sorted_enrichments = $this->get_sorted_enrichments($type);
            foreach ($sorted_enrichments as $enrichment_type) {
                $selector .= "<li id='enrichment_selected_sources_" . $type . "_" . $enrichment_type['source'] . "_" . $enrichment_type['code'] . "'>
						<span id='enrichment_selected_sources_" . $type . "_" . $enrichment_type['source'] . "_" . $enrichment_type['code'] . "_up' style='cursor:pointer;'>&and;</span>
						<span id='enrichment_selected_sources_" . $type . "_" . $enrichment_type['source'] . "_" . $enrichment_type['code'] . "_down' style='cursor:pointer;'>&or;</span>
						<input type='checkbox' id='enrichment_selected_sources_" . $type . "_" . $enrichment_type['source'] . "_" . $enrichment_type['code'] . "_default_display' name='enrichment_select_source[" . $type . "][" . $enrichment_type['source'] . "][" . $enrichment_type['code'] . "][default_display]' value='1' " .
						((isset($this->params[$type][$enrichment_type['source']][$enrichment_type['code']]['default_display']) && $this->params[$type][$enrichment_type['source']][$enrichment_type['code']]['default_display'] ) ? "checked='checked'" : "") . "/>
						<label for='enrichment_selected_sources_" . $type . "_" . $enrichment_type['source'] . "_" . $enrichment_type['code'] . "_default_display'>" . $enrichment_type['label'] . "</label>
						<input type='hidden' id='enrichment_selected_sources_" . $type . "_" . $enrichment_type['source'] . "_" . $enrichment_type['code'] . "_order' name='enrichment_select_source[" . $type . "][" . $enrichment_type['source'] . "][" . $enrichment_type['code'] . "][order]' value='" . $order . "' />
					</li>
					<script>
						document.getElementById('enrichment_selected_sources_" . $type . "_" . $enrichment_type['source'] . "_" . $enrichment_type['code'] . "_up').addEventListener('click', function() {order_selected_source('up', this);}, true);
						document.getElementById('enrichment_selected_sources_" . $type . "_" . $enrichment_type['source'] . "_" . $enrichment_type['code'] . "_down').addEventListener('click', function() {order_selected_source('down', this);}, true);
					</script>";
						$order ++;
            }
        }
        $selector .= "</ul>
				</div>";

        return $selector;
    }

    protected function generateSelectorScript()
    {
        global $msg, $charset;

        $script = "<script>";

        // On transmet les type et leur label en parametre
        $enrichment_types = array();
        foreach ($this->enhancer as $source) {
            foreach ($source['enrichment_types'] as $enrichment_type) {
                $enrichment_types[$source['id']][$enrichment_type['code']] = ($charset == "utf-8" ? $enrichment_type['label'] : encoding_normalize::utf8_normalize($enrichment_type['label']));
            }
        }
        $script .= "
				var enrichment_types = " . json_encode($enrichment_types) . ";";

        // Fonction d'ajout d'une source sélectionnée
        $script .= "
				function add_selected_source(parent_id, source_id) {
					for (var code in enrichment_types[source_id]) {
						if (!document.getElementById(parent_id + '_' + source_id + '_' + code)) {
							var li = document.createElement('li');
							li.setAttribute('id', parent_id + '_' + source_id + '_' + code);

							var span_up = document.createElement('span');
							span_up.setAttribute('id', parent_id + '_' + source_id + '_' + code + '_up');
							span_up.setAttribute('style', 'cursor:pointer;');
							span_up.innerHTML = '&and;';
							span_up.addEventListener('click', function() {order_selected_source('up', this);}, true);

							var span_down = document.createElement('span');
							span_down.setAttribute('id', parent_id + '_' + source_id + '_' + code + '_down');
							span_down.setAttribute('style', 'cursor:pointer;');
							span_down.innerHTML = '&or;';
							span_down.addEventListener('click', function() {order_selected_source('down', this);}, true);

							var label = document.createElement('label');
							label.setAttribute('for', parent_id + '_' + source_id + '_' + code + '_default_display');
							label.innerHTML = enrichment_types[source_id][code];

							var input_display = document.createElement('input');
							input_display.setAttribute('id', parent_id + '_' + source_id + '_' + code + '_default_display');
							input_display.setAttribute('type', 'checkbox');
							input_display.setAttribute('value', '1');
							input_display.setAttribute('name', 'enrichment_select_source[' + parent_id.replace('enrichment_selected_sources_', '') + '][' + source_id + '][' + code + '][default_display]');

							var input_order = document.createElement('input');
							input_order.setAttribute('id', parent_id + '_' + source_id + '_' + code + '_order');
							input_order.setAttribute('type', 'hidden');
							input_order.setAttribute('value', '0');
							input_order.setAttribute('name', 'enrichment_select_source[' + parent_id.replace('enrichment_selected_sources_', '') + '][' + source_id + '][' + code + '][order]');

							li.appendChild(span_up);
							li.appendChild(document.createTextNode(' '));
							li.appendChild(span_down);
							li.appendChild(document.createTextNode(' '));
							li.appendChild(input_display);
							li.appendChild(document.createTextNode(' '));
							li.appendChild(label);
							li.appendChild(input_order);

							document.getElementById(parent_id).appendChild(li);
						}
					}
				}";

        // Fonction de suppression d'une source sélectionnée
        $script .= "
				function delete_selected_source(parent_id, source_id) {
					for (var code in enrichment_types[source_id]) {
						if (document.getElementById(parent_id + '_' + source_id + '_' + code)) {
							document.getElementById(parent_id).removeChild(document.getElementById(parent_id + '_' + source_id + '_' + code));
						}
					}
				}";

        // Fonction de mise à jour de la liste des sources sélectionnées au clic sur défaut
        $script .= "
				function update_selected_sources_default(e) {
					e.stopPropagation();
					var available_select = this.parentNode;

					for (var i = 0; i < available_select.options.length; i++) {
						var current_option = available_select.options[i];
						if ((current_option.getAttribute('id') != this.getAttribute('id'))) {
							delete_selected_source('enrichment_selected_sources_' + available_select.getAttribute('type'), current_option.getAttribute('value'));
							current_option.selected = false;
						}
					}
				}";

        // Fonction de mise à jour de la liste des sources sélectionnées
        $script .= "
				function update_selected_sources(e) {
					e.stopPropagation();
					var available_select = this.parentNode;

					for (var i = 0; i < available_select.options.length; i++) {
						var current_option = available_select.options[i];
						if (current_option.selected) {
							if (current_option.getAttribute('value')*1 == 0) { // On déselectionne le défaut
								current_option.selected = false;
							} else {
								add_selected_source('enrichment_selected_sources_' + available_select.getAttribute('type'), current_option.getAttribute('value'));
							}
						} else {
							delete_selected_source('enrichment_selected_sources_' + available_select.getAttribute('type'), current_option.getAttribute('value'));
						}
					}
					update_order_selected_sources(document.getElementById('enrichment_selected_sources_' + available_select.getAttribute('type')));
				}";

        // Fonction de changement d'ordre d'une source sélectionnée
        $script .= "
				function order_selected_source(direction, object) {
					var node_to_move = object.parentNode;
					var parent = node_to_move.parentNode;
					var current_order = document.getElementById(node_to_move.getAttribute('id') + '_order').getAttribute('value')*1;

					if ((direction == 'up') && (current_order == 0)) {
						return false;
					}

					var need_update = false;
					for (var i = 0; i < parent.children.length; i++) {
						if (parent.children[i].nodeName == 'LI') {
							if((direction == 'up') && (document.getElementById(parent.children[i].getAttribute('id') + '_order').getAttribute('value')*1 == (current_order - 1))) {
								parent.insertBefore(node_to_move, parent.children[i]);
								need_update = true;
								break;
							} else if((direction == 'down') && (document.getElementById(parent.children[i].getAttribute('id') + '_order').getAttribute('value')*1 == (current_order + 1))) {
								if (parent.children[i].nextSibling) {
									parent.insertBefore(node_to_move, parent.children[i].nextSibling);
								} else {
									parent.appendChild(node_to_move);
								}
								need_update = true;
								break;
							}
						}
					}
					if (need_update) update_order_selected_sources(parent);
				}";

        // Fonction de mise à jour de l'ordre des sources sélectionnées
        $script .= "
				function update_order_selected_sources(parent) {
					var count = 0;
					for (var i = 0; i < parent.children.length; i++) {
						if (parent.children[i].nodeName == 'LI') {
							document.getElementById(parent.children[i].getAttribute('id') + '_order').setAttribute('value', count);
							count++;
						}
					}
				}";

        $script .= "</script>";

        return $script;
    }

    private function get_sorted_enrichments($type)
    {
        $sorted_enrichments = array();
        foreach ($this->enhancer as $source) {
            if (isset($this->active[$type]) && in_array($source['id'], $this->active[$type])) {
                foreach ($source['enrichment_types'] as $enrichment_type) {
                    $order = (isset($this->params[$type][$source['id']][$enrichment_type['code']]['order']) ? $this->params[$type][$source['id']][$enrichment_type['code']]['order'] : 0);
                    $sorted_enrichments[$order] = array(
                        'source' => $source['id'],
                        'code' => $enrichment_type['code'],
                        'label' => $enrichment_type['label']
                    );
                }
            }
        }
        ksort($sorted_enrichments);
        return $sorted_enrichments;
    }

    // retourne les éléments à rajouter dans le head, les calculs aux besoins;
    public function getHeaders()
    {
        if (is_null($this->enrichmentsTabHeaders)) {
            $this->generateHeaders();
        }
        return implode("\n", $this->enrichmentsTabHeaders);
    }

    // Méthode qui génère les éléments à insérer dans le header pour le bon fonctionnement des enrichissements
    public function generateHeaders()
    {
        global $base_path;

        $this->enrichmentsTabHeaders = array();
        $alreadyIncluded = array();
        foreach ($this->active as $type => $sources) {
            foreach ($sources as $source_id) {
                if (! in_array($source_id, $alreadyIncluded)) {
                    // on récupère les infos de la source nécessaires pour l'instancier
                    $name = connecteurs::get_class_name($source_id);
                    foreach ($this->catalog as $connector) {
                        if ($connector['NAME'] == $name) {
                            if (is_file($base_path . "/admin/connecteurs/in/" . $connector['PATH'] . "/" . $name . ".class.php")) {
                                require_once ($base_path . "/admin/connecteurs/in/" . $connector['PATH'] . "/" . $name . ".class.php");
                                $conn = new $name($base_path . "/admin/connecteurs/in/" . $connector['PATH']);
                                $this->enrichmentsTabHeaders = array_merge($this->enrichmentsTabHeaders, $conn->getEnrichmentHeader());
                                $this->enrichmentsTabHeaders = array_unique($this->enrichmentsTabHeaders);
                            }
                        }
                    }
                    $alreadyIncluded[] = $source_id;
                }
            }
        }
    }

    public function getEnrichment($notice_id, $tnoti, $tdoc)
    {
        global $base_path;
        $infos = array();
        if ($this->active[$tnoti . $tdoc]) {
            $type = $tnoti . $tdoc;
        } else {
            $type = $tnoti;
        }
        if (isset($this->active[$type])) {
            foreach ($this->active[$type] as $source_id) {
                // on récupère les infos de la source nécessaires pour l'instancier
                $name = connecteurs::get_class_name($source_id);
                foreach ($this->catalog as $connector) {
                    if ($connector['NAME'] == $name) {
                        if (is_file($base_path . "/admin/connecteurs/in/" . $connector['PATH'] . "/" . $name . ".class.php")) {
                            require_once ($base_path . "/admin/connecteurs/in/" . $connector['PATH'] . "/" . $name . ".class.php");
                            $conn = new $name($base_path . "/admin/connecteurs/in/" . $connector['PATH']);
                            $infos[] = $conn->getEnrichment($notice_id);
                        }
                    }
                }
            }
        }
        return $infos;
    }

    protected function parseType()
    {
        global $include_path;

        $file = $include_path . "/enrichment/categories.xml";
        $xml = file_get_contents($file);
        $types = _parser_text_no_function_($xml, "XMLLIST");
        foreach ($types['ENTRY'] as $type) {
            $this->types_names[$type['CODE']] = $type['value'];
        }
    }
}
