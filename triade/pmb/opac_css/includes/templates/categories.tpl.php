<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categories.tpl.php,v 1.20 2023/08/03 12:51:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $msg;
global $tpl_div_categories;
global $tpl_div_category;
global $tpl_subcategory;

// template for PMB OPAC

// éléments pour la recherche simple

// tpl_div_categories : le bloc qui contient toutes les catégories, présentées correctement
//   !!root_categories!! : sera remplacé par autant de blocs $tpl_div_category qu'ils y a
//                         de catégories de niveau 0.
$tpl_div_categories = "
<div id='categories'>
<!-- title -->
<!-- liens_thesaurus -->
<div id='categories-container'>
!!root_categories!!
</div>
<div style='clear: both; visibility: hidden; display: none;' id='category_bloc_sep'>&nbsp;</div>
</div>
";

// tpl_div_category : le bloc qui contient une catégorie et ses fils de premier niveau.
//   !!categoryname!! : sera remplacé par le nom de la catégorie de niveau 0
//   !!subcategories!! : sera rempalcé par autant de blocs $tpl_subcategory qu'il y a
//                       de fils de premiers niveau
$tpl_div_category =
"<div class='category' >
<h2><img src='".get_url_icon('folder.gif')."' alt='folder'>!!category_name!!</h2>
<ul>
!!sub_categories!!
</ul>
<div class='clear'></div>
</div>
";

// tpl_subcategory : le petit bloc qui contient le fils de premier niveau d'une catégorie
//   !!subcategory!! : sera remplacé par le nom du fils de premier niveau de la catégorie.
$tpl_subcategory =
"<li>!!sub_category!!</li>
";

?>
