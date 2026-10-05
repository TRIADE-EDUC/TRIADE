<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: classementGen.tpl.php,v 1.4.2.1 2024/05/03 07:40:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $classementGen_selector, $classementGen_datalist;
global $classementGen_list_table_header, $classementGen_list_table_row, $classementGen_list_table_footer;

// templates pour la gestion des classements

//Template du selecteur
$classementGen_selector = "<div data-dojo-type='dijit/form/DropDownButton' style='float:right;'>
				<span></span>
			    <div data-dojo-type='dijit/TooltipDialog' id='classementGen_Dialog_!!object_id!!'>
			    	<label class='etiquette'>!!msg_object_classement!!</label>
			   		<br />
					<select data-dojo-type='dijit/form/ComboBox' id='classementGen_!!object_type!!_!!object_id!!' name='classementGen_!!object_type!!_!!object_id!!'>
						!!classements_liste!!
					</select>
			        <br />
			 		<button data-dojo-type='dijit/form/Button' onclick=\"classementGen_save('!!object_type!!','!!object_id!!','!!url_callback!!');return false;\" type='button'>!!msg_object_classement_save!!</button>
			    </div>
			</div>";


$classementGen_datalist = "
			    <span id='classementGen_!!object_id!!' class='classementGen'>
                    <span class='datalist'>
                        <label class='visually-hidden'>!!msg_object_classement!!</label>
                        <input list='classementGenListe_!!object_type!!_!!object_id!!' type='text' id='classementGen_!!object_type!!_!!object_id!!' value='' class='datalist-input' placeholder='!!msg_object_classement!!' style='width:80%' autocomplete='off' />
    					<i id='datalist-icon' class='fa fa-caret-down'></i>
                        <datalist id='classementGenListe_!!object_type!!_!!object_id!!' class='datalist-options'>
    						!!classements_liste!!
    					</datalist>
                    </span>
                    <i class='fa fa-save' onclick=\"classementGen_save('!!object_type!!','!!object_id!!','!!url_callback!!');return false;\" style='cursor:pointer' title='!!msg_object_classement_save!!'></i>
			    </span>";

//Table pour gérer les classements
$classementGen_list_table_header = "<table>
	<tr>
		<th>!!title!!</th>
	</tr>";

$classementGen_list_table_row = "<tr class='!!tr_class!!' !!tr_js!! style='cursor: pointer'>
									<td><strong>!!td_lib!!</strong></td>
								</tr>";
$classementGen_list_table_footer = "</table>";
