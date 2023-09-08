$(document).ready(function(){
	
	$('#noticeNot').attr("id","noticeNote");
	$('#resa_notice').attr("id","store-record");
	$('#store-record a').addClass("uk-button").prepend("<i class='fa fa-level-down' aria-hidden='true'></i>");
	$('#noticeNote>div>div').removeAttr("class");
	$('#noticeNote>div').first().addClass("uk-grid uk-grid-small uk-grid-divider record-content");
	$("#noticeNote>.record-content>div").first().addClass("uk-width-3-4 record-content-data");
	$("#noticeNote>.record-content>div").last().addClass("uk-width-1-4");
	$(".record-content-data>div").first().addClass("uk-flex uk-flex-middle");
	$(".notice_corps").addClass("record-content").removeClass('notice_corps');
	$(".asideContentNot").addClass("aside-record-content").removeClass('asideContentNot');
	$(".onglet_basketNot").addClass("cart-add").removeClass("onglet_basketNot");
	$(".icon_basketNot").addClass("wyr-icon-button");
	$(".lienP").addClass("link-record").removeClass("lienP");
	$(".img_permalink:not('img')").addClass("pic-link-record wyr-icon-button").removeClass("img_permalink");
	$(".listeLectureN").addClass("loan-record").removeClass("listeLectureN");
	$(".imgListeLecture:not('img')").addClass("pic-link-record wyr-icon-button").removeClass("imgListeLecture");	
	$(".avisN").addClass("comment-record").removeClass("avisN");
	$(".imgComment:not('img')").addClass("pic-comment-record wyr-icon-button").removeClass("imgComment");
	$(".tagsN").addClass("tag-record").removeClass("tagsN");
	$(".imgTag:not('img')").addClass("pic-tag-record wyr-icon-button").removeClass("imgTag");
	$('#zone_exemplaires>table.exemplaires').removeAttr("class").wrap("<div class='uk-overflow-container'></div>").addClass("uk-table uk-table-striped uk-table-condensed");
	$('.notice_contenu').removeAttr("class").addClass("record-data");
	$(".record_nb_resas").addClass("uk-alert-warning uk-alert");
	$("#zone_depouillements a[target='cart_info']").addClass("uk-button");
	$("#zone_depouillements a[id^='record_container']").addClass("uk-grid uk-grid-small");
	$("#zone_depouillements div[id^='record_container']").addClass("uk-grid uk-grid-small");
	$(".panier_avis_notCourte").addClass("cart-small-display").removeClass("panier_avis_notCourte");
	$('.infoTitle_notCourte>h3>span').wrapAll("<div class='uk-flex uk-flex-middle'></div>");
	
	// Move element
	var exemplaires = $( "#zone_exemplaires" ).detach();
	$(exemplaires).insertAfter(".notice-header");	
	
	$('body').addClass("wyr");
	
	
});