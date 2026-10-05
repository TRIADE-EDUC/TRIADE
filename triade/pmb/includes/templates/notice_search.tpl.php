<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_search.tpl.php,v 1.29 2021/05/03 08:28:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $layout_begin, $layout_end, $msg;
global $other_search_form, $current_module, $nb_per_page_a_search;

// en-tête et pied de page
$layout_begin = "";
$layout_end = "";
 
//    ----------------------------
//    Form: Other Search
//    ----------------------------
$other_search_form ="
<script type='text/javascript'>
      function test_form(form)
      {
     // on checke si le champ de saisie est renseigné
            if(form.other_query.value.length == 0)
            {
                alert(\"$msg[414]\");
                document.forms['other_search_form'].elements['other_query'].focus();
                return false;
            }
                return true;
      }
</script>

<form class='form-$current_module' id='other_search_form' name='other_search_form' method='post' action='./catalog.php?categ=search&mode=4'>
<h3>".$msg[4053]."</h3>
<div class='form-contenu'>
    <!--    Termes de recherche    -->
    <div class='row'>
        <label class='etiquette' for='other_query'>$msg[label_search_terms]</label>
        </div>
    <div class='row'>
        <input class='saisie-80em' id='other_query' type='text' value='!!other_query!!' name='other_query' />
        </div>
    <div class='row'>
        <span class='astuce'>$msg[155]
            <a class='aide' title='$msg[1900]$msg[1901]$msg[1902]' href='./help.php?whatis=regex' onclick='aide_regex();return false;'>$msg[1550]</a>
            </span>
        </div>
    <br />
    <!--    Chercher tous les mots    -->
    <div class='row'>
        <input type='radio' id='search_type' name='search_type' value='1' checked='checked' />$msg[905]
        <input type='radio' id='search_type' name='search_type' value='0' />$msg[906]
        </div>

    <div class='row'>
        <label class='etiquette' for='bla'>$msg[where_to_search]</label>
        </div>
    <div class='row'>
        <div class='saisie-contenu'>
            <input type='checkbox' id='n_resume_flag' name='n_resume_flag' checked='checked' value='1' />$msg[1903] / $msg[1904]
            </div>
        </div>
    <div class='row'>
        <div class='saisie-contenu'>
            <input type='checkbox' id='n_gen_flag' name='n_gen_flag' checked='checked' value='1' />$msg[1912]
            </div>
        </div>
    <div class='row'>
        <div class='saisie-contenu'>
            <input type='checkbox' id='n_titres_flag' name='n_titres_flag' checked='checked' value='1' />$msg[1910]
            </div>
        </div>
    <div class='row'>
        <div class='saisie-contenu'>
            <input type='checkbox' id='n_matieres_flag' name='n_matieres_flag' checked='checked' value='1' />$msg[1911]
            </div>
        </div>
    <!--    Formes fléchies
    <div class='row'>
        <label for='etiquette'>$msg[1906]$msg[1907]</label>
        </div>
    <div class='row'>
        <input type='radio' id='accept_subset' name='accept_subset' value='1' checked='checked' />$msg[1906]
        </div>
    <div class='row'>
        <input type='radio' id='accept_subset' name='accept_subset' value='0' />$msg[1909]
        </div>
    <hr class='spacer' />
    -->

    <br />
    <!--    Résultats par page    -->
    <div class='row'>
        <label class='etiquette' for='res_per_page'>$msg[1905]$msg[1901]</label>
        <select id='res_per_page' name='res_per_page'>
        <option value='5'>5</option>
        <option value='10'>10</option>
        <option value='15'>15</option>
        <option value='20'>20</option>
        <option value='25'>25</option>
        <--<option value='$nb_per_page_a_search' selected='selected'>$nb_per_page_a_search</option>-->
        </select>
        </div>
    </div>

<!--    Bouton d'envoi    -->
<div class='row'>
    <input class='bouton' type='submit' value='$msg[142]' onClick=\"return test_form(this.form)\" />
    </div>
</form>
<script type='text/javascript'>
      document.forms['other_search_form'].elements['other_query'].focus();
</script>
";