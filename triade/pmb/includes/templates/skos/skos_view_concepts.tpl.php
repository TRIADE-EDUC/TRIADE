<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_view_concepts.tpl.php,v 1.5 2019/05/27 10:24:30 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");
global $skos_view_concepts_list_in_notice;
global $skos_view_concepts_list_in_authority;
global $skos_view_concepts_narrowers_list;
global $skos_view_concepts_broaders_list;
global $skos_view_concepts_composed_concepts_list;
global $skos_view_concepts_related_list, $skos_view_concepts_schemes_list;


$skos_view_concepts_list_in_notice = "
{% for scheme, concepts in concepts_list.elements %}
	{% if parameters.concepts_in_line %}
		{% if !loop.first %}<br />{% endif %}<b>{{ scheme }}</b><br />
	{% endif %}
	{% for concept in concepts %}
		{% if !parameters.concepts_in_line %}[{{ scheme }}]{% endif %}
		{{ concept }}
		{% if !loop.last %}
			{% if parameters.concepts_in_line %} ; {% else %}<br />{% endif %}
		{% endif %}
	{% endfor %}
{% endfor %}";

$skos_view_concepts_list_in_authority = "
{% for scheme, concepts in concepts_list.elements %}
	{% if loop.first %}
			<h3>{{ concepts_list.title }}</h3>
	{% endif %}
	{% if parameters.concepts_in_line %}
		{% if !loop.first %}<br />{% endif %}<b>{{ scheme }}</b><br />
	{% endif %}
	{% for concept in concepts %}
		{% if !parameters.concepts_in_line %}[{{ scheme }}]{% endif %}
		{{ concept }}
		{% if !loop.last %}
			{% if parameters.concepts_in_line %} ; {% else %}<br />{% endif %}
		{% endif %}
	{% endfor %}
{% endfor %}";

$skos_view_concepts_schemes_list = "
{% for scheme in concepts_list %}
	{% if loop.first %}
		<b>{{ msg.onto_common_inscheme }} :</b><br />
	{% endif %}
	{{ scheme }}<br />
{% endfor %}";

$skos_view_concepts_narrowers_list = "
{% for scheme, concepts in concepts_list.elements %}
	{% if loop.first %}
		<b>{{ concepts_list.title }}</b><br />
	{% endif %}
	{% if parameters.concepts_in_line %}
		{% if !loop.first %}<br />{% endif %}<b>{{ scheme }}</b><br />
	{% endif %}
	{% for concept in concepts %}
		{% if !parameters.concepts_in_line %}[{{ scheme }}]{% endif %}
		{{ concept }}
		{% if !loop.last %}
			{% if parameters.concepts_in_line %} ; {% else %}<br />{% endif %}
		{% endif %}
	{% endfor %}
{% endfor %}";

$skos_view_concepts_broaders_list = "
{% for scheme, concepts in concepts_list.elements %}
	{% if loop.first %}
		<b>{{ concepts_list.title }}</b><br />
	{% endif %}	
	<h4>{{scheme}}</h4>
	{% for concept in concepts %}
		<p>{{concept}}</p>
	{% endfor %}
{% endfor %}";

$skos_view_concepts_composed_concepts_list = "
{% for scheme, concepts in concepts_list.elements %}
	{% if loop.first %}
		<h3>{{ concepts_list.title }}</h3>
	{% endif %}
	{% if parameters.concepts_in_line %}
		{% if !loop.first %}<br />{% endif %}<b>{{ scheme }}</b><br />
	{% endif %}
	{% for concept in concepts %}
		{% if !parameters.concepts_in_line %}[{{ scheme }}]{% endif %}
		{{ concept }}
		{% if !loop.last %}
			{% if parameters.concepts_in_line %} ; {% else %}<br />{% endif %}
		{% endif %}
	{% endfor %}
{% endfor %}";

$skos_view_concepts_related_list = "
{% for scheme, concepts in concepts_list.elements %}
	{% if loop.first %}
		{{ concepts_list.title }}<br />
	{% endif %}
	<h4>{{scheme}}</h4>
	{% for concept in concepts %}
		<p>{{concept}}</p>
	{% endfor %}
{% endfor %}";