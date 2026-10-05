<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_view_concept.tpl.php,v 1.10 2019/05/29 11:23:32 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg;
global $skos_view_concept_concept_in_list_with_all_links;
global $skos_view_concept_concept_in_list;
global $skos_view_concept_concept;
global $skos_view_concept_detail_concept;
global $skos_view_concept_authorities_indexed_with_concept;

$skos_view_concept_concept_in_list_with_all_links = "
{% for element in concept.elements %}
	<a href='{{ element.link }}'>{{ element.label }}</a>{% if !loop.last %}{{ concept.separator }}{% endif %}
{% endfor %}";

$skos_view_concept_concept_in_list = "
<a href='{{ concept.link }}'>{{ concept.label }}</a>";

$skos_view_concept_concept = "
<h3>{{concept.label}}</h3>";

$skos_view_concept_detail_concept = "
<div class='details'>
	{% for subdivision, elements in concept.composed_concept_elements %}
		{% if loop.first %}
			".$msg['skos_view_concept_composition']."
		{% endif %}
		<br/>
		<strong>{{ subdivision }} : </strong>
		{% for element in elements %}
			{% if !loop.first %}{{ concept.composed_concept_separator }}{% endif %}
			<a href='{{ element.link }}'>{{ element.label }}</a>
		{% endfor %}
	{% endfor %}
	<table>
	{% for property, information in concept.properties %}
		{% if property != 'hidden' %}
		<tr>
			<td class='bg-grey'>
				{{information.label}}
			</td>
			<td>
				{% for value in information.values %}
					{% if value.length > 0 %}
						{% if value.id != 0 %}
							<a href='./index.php?lvl=concept_see&id={{value.id}}'>{{value.label}}</a>
						{% else %}
							<a href='{{value.uri}}' target='_blank'>{{value.uri}}</a>
						{% endif %}							
					{% else %}
		 				<span>{{value}}</span>
					{% endif %}
					{% if !loop.last %}
						<br>
					{% endif %}
				{% endfor %}
			</td>
		<tr>
		{% endif %}
	{% endfor %}
	</table>	
</div>";

$skos_view_concept_authorities_indexed_with_concept = "
{% for authority_type in concept.authorities %}
	{% for element in authority_type.elements %}
		{% if loop.first %}
			<br/><strong>{{ authority_type.type_name }} : </strong>
		{% else %}
			; 
		{% endif %}
		<a href='{{ element.link }}'>{{ element.label }}</a>
	{% endfor %}
{% endfor %}";