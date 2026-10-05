// JavaScript Document
$(document).ready(function(){
	$(".localisation").attr("data", function(){ return $(".collstate_header_location_libelle").text() });
	$(".emplacement_libelle").attr("data", function(){ return $(".collstate_header_emplacement_libelle").text() });
	$(".cote").attr("data", function(){ return $(".collstate_header_cote").text() });	
	$(".type_libelle").attr("data", function(){ return $(".collstate_header_type_libelle").text() });
	$(".statut_opac_libelle").attr("data", function(){ return $(".collstate_header_statut_opac_libelle").text() });	
	$(".state_collections").attr("data", function(){ return $(".collstate_header_state_collections").text() });
	$(".origine").attr("data", function(){ return $(".collstate_header_origine").text() });
	$(".archive").attr("data", function(){ return $(".collstate_header_archive").text() });
	$(".lacune").attr("data", function(){ return $(".collstate_header_lacune").text() });	
	
	$(".location_libelle").attr("data", function(){ return $(".expl_header_location_libelle").text() });	
	$(".section_libelle").attr("data", function(){ return $(".expl_header_section_libelle").text() });
	$(".tdoc_libelle").attr("data", function(){ return $(".expl_header_tdoc_libelle").text() });	
	$(".expl_cote").attr("data", function(){ return $(".expl_header_expl_cote").text() });	
	
	
	$(".Localisation").attr("data", function(){ return $(".expl_header_location_libelle").text() });	
	$(".Section").attr("data", function(){ return $(".expl_header_section_libelle").text() });
	$(".Support").attr("data", function(){ return $(".expl_header_tdoc_libelle").text() });	
	$(".Cote").attr("data", function(){ return $(".expl_header_expl_cote").text() });	
	$(".Disponibilité").attr("data", function(){ return $(".expl_header_statut").text() });	
	
});










