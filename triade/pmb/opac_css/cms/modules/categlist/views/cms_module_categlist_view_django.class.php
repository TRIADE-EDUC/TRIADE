<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_categlist_view_django.class.php,v 1.2 2015/09/22 13:34:21 vtouchard Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class cms_module_categlist_view_django extends cms_module_common_view_django
{

    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->default_template = "
		{% for term in terms %}
    		{% if loop.first %}
                <h3>Synonymes</h3>
        		<span>Les synonymes des catégories suivantes, ont été pris en compte pendant la recherche:</span>
        		<ul>
    		{% endif %}
        		  <li><a href='./index.php?lvl=categ_see&id={{term.id_retenue}}'>{{term.libelle_retenue}}</a> ({{term.libelle_rejetee}}) </li>
    		{% if loop.last %}
        		</ul>
    		{% endif %}
		{% endfor %}";
    }

    /**
     *
     * @see cms_module_common_view_django::get_format_data_structure()
     */
    public function get_format_data_structure()
    {
        $datasource = new cms_module_categlist_datasource_categs();
        $datas = $datasource->get_format_data_structure();
        $format_datas = array_merge($datas, parent::get_format_data_structure());
        return $format_datas;
    }
}