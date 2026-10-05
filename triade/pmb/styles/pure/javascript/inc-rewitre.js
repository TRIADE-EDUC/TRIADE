// $Id: inc-rewitre.js,v 1.8 2022/12/21 13:39:15 qvarin Exp $ 
$(document).ready(function() {
	// ==========================================================================
	// generic content
	// ==========================================================================
	$("html").addClass("pure add-before");
	$(".row").addClass(function(index) {
		if ($(this).children().length > 0) {
			return "uk-clearfix"
		}
	})
	// ==========================================================================
	// mainnav bar
	// ==========================================================================
	var selectorFinalUrl = '.' + window.location.href.substring(window.location.href.lastIndexOf('/'));
	var selectorSetterMenu = document.getElementById('menu');
	if (selectorSetterMenu) {
		var selectorSetterLinks = selectorSetterMenu.querySelectorAll('a[href]');
		var selectorSetterLink = selectorSetterMenu.querySelector('a[href="' + selectorFinalUrl + '"]');
		if (!selectorSetterLink) {
			var selectedMenu = selectorSetterMenu.querySelector('li[class*="active"]');
			if (selectedMenu) {
				selectedMenu.classList.remove('active');
				selectedMenu.classList.add('uk-active');
			} else {
				selectorSetterLinks = Array.prototype.slice.call(selectorSetterLinks);
				selectorSetterLinks.some(function(link) {
					if (link.href && selectorFinalUrl.indexOf(link.getAttribute('href')) != -1) {
						//Faire ton traitement ici (mettre la classe selected ou je ne sais quoi avec jQuery)
						link.parentElement.classList.add('uk-active');
						return true;
					}
				});
			}
		} else {
			//Faire ton traitement ici (mettre la classe selected ou je ne sais quoi avec jQuery)
			selectorSetterLink.parentElement.classList.add('uk-active');
		}
	}
	$("#navbar").addClass("uk-navbar-container uk-navbar-left");
	$("#navbar>ul").addClass("uk-navbar-nav");
	$("#extra").addClass("uk-iconnav");
	$("#extra2").addClass("uk-iconnav");

	// ==========================================================================
	// Upper Nav notif dashboard
	// ==========================================================================
	$("a[title='Tableau de Bord']").addClass("dashboard");
	$("a[title='Tableau de Bord']").html("<span uk-icon='icon: dashboard; ratio: 1'></span>");
	$('#notification').append("<span uk-icon='icon: info; ratio: 1'></span>");

	// ==========================================================================
	// Upper Nav notif dashboard
	// ==========================================================================
	$("#div_alert>*>ul").prepend("<span uk-icon='icon: info; ratio: 1'></span>");
	$("#div_alert>*>ul").addClass("alert-nav");
	$(".icon_history").html("<span uk-icon='icon: historywyr; ratio: 1'></span>");
	$(".icon_help").html("<span uk-icon='icon: question; ratio: 1'></span>");
	$(".icon_param").html("<span uk-icon='icon: cog; ratio: 1'></span>");
	$(".icon_opac").html("<i class='fa fa-globe' aria-hidden='true'></i>");
	$(".icon_sauv").html("<i class='fa fa-floppy-o' aria-hidden='true'></i>");
	$(".icon_quit").html("<span uk-icon='icon: quit; ratio: 1'></span>");
	
	
	// ==========================================================================
	// side nav
	// ==========================================================================
	$("#menu>ul").addClass("uk-nav");
	$("#menu>ul>li").addClass("nav-item");
	$("#menu>ul>li>ul").addClass("uk-nav-sub");
	$("#menu>h3").prepend("<span class='uk-margin-small-right uk-icon'><i class='fa fa-caret-down' aria-hidden='true'></i></span>");
	$("#menu .uk-nav>li>a").prepend("<span class='uk-margin-small-right uk-icon'><i class='fa fa-circle-o' aria-hidden='true'></i></span>");

	// ==========================================================================
	// Sticky part
	// ==========================================================================
	if ($('#extra').length == 1) {
		var stickyDelay = 1;
		var widthInitExtra = window.getComputedStyle(document.getElementById('extra')).width;
		var divInitWidthExtra = document.createElement('div');
		divInitWidthExtra.setAttribute('style', 'width:' + widthInitExtra);
		divInitWidthExtra.setAttribute('id', 'initW');
		var extra = document.getElementById('extra');
		extra.insertBefore(divInitWidthExtra, extra.childNodes[0]);
		document.getElementById("navbar").setAttribute('style', 'padding-right:' + widthInitExtra);

		// add event
		UIkit.sticky('#navbar', {
			top: stickyDelay,
			offset: 0,
			showOnUp: true,
			//animation: "uk-animation-slide-top",
		});
		UIkit.sticky('#extra', {
			top: stickyDelay,
			offset: 0,
			showOnUp: true,
			widthElement: '#initW',

		});
		UIkit.sticky('#extra2', {
			top: stickyDelay,
			offset: 0,
			showOnUp: true
		});
	}
	
	// ==========================================================================
	// Glyphico
	// ==========================================================================


	// ==========================================================================
	// Table
	// ==========================================================================
	incRewitreTable();

	// recherche_ajax_mode actif : application du style table aux notice-child
	$(".notice-child").each(function() {
		var target = this;
		var observer = new MutationObserver(function(mutations) {
			mutations.forEach(function(mutation) {
				$('.notice-child table').addClass("uk-table uk-table-small uk-table-striped uk-table-middle");
			});
		});
		observer.observe(target, { childList: true });
	});


	// ==========================================================================
	// Tableau cree en div fixe a la taille de la plus grande cellule
	// ==========================================================================
	var cells = document.getElementsByClassName("dom_cell2");
	var size = 0;
	for (var i = 0; i < cells.length; i++) {
		if (parseInt(window.getComputedStyle(cells[i]).height.replace('px', '')) > size) {
			size = window.getComputedStyle(cells[i]).height.replace('px', '');
		}
	}
	var rows = document.getElementsByClassName("dom_row2");
	for (var i = 0; i < rows.length; i++) {
		rows[i].style.setProperty('height', size + 'px');
	}

	// ==========================================================================
	// hmenu // tab
	// ==========================================================================
	$(".hmenu").addClass(function(index) {
		if ($(".hmenu").children().length === 0)
			return "empty-node"
	});
	$(".sel_navbar,#content_onglet_perio").addClass('uk-tab uk-margin-remove-bottom');
	$(".hmenu>span").addClass('uk-button wui-button uk-margin-remove-bottom');

	$(".hmenu .selected, .sel_navbar_current, .onglet-perio-selected").addClass('uk-active');


	// ==========================================================================
	// main title
	// ==========================================================================
	$("#contenu>h1,#contenu>.row>h1,#make_mul_sugg>h1,#import_sug>h1").first().addClass("section-title");
	$("#contenu>h1").not(".section-title").addClass("section-sub-title");
	$("#contenu>h1, #contenu>div>h1, #contenu>.row>h1, #make_mul_sugg>h1, #import_sug>h1, #contenu>div>div>.section-title").first().prepend("<span class='uk-margin-small-right uk-icon'><i class='fa fa-circle' aria-hidden='true'></i></span>");

	// ==========================================================================
	// Sub title
	// ==========================================================================
	$("div#contenu>h2").addClass("bkg-white section-sub-title article-title");//titre de type contenu>h2 sans row
	$("div#contenu>.row>h2").addClass("section-sub-title article-title");//titre de type h2 contenu dans une row>contenu  
	$("#contenu>h1").not(".section-title").addClass("section-sub-title");
	$("#contenu>*>h3").addClass("h2-like section-sub-title");
	$("#contenu>h3").addClass("h2-like section-sub-title");

	// ==========================================================================
	// auto margin
	// ==========================================================================
	var ukMarginMenu = $(".hmenu");
	UIkit.margin(ukMarginMenu, {
		margin: 'uk-margin-small-top',
	});
	var ukMargin = $(".left").has("input");
	UIkit.margin(ukMargin, {
		margin: 'uk-margin-small-top',
	});
	var ukMarginTrInput = $("tr[id^='relance_empr']>td").has("input");
	UIkit.margin(ukMarginTrInput, {
		margin: 'uk-margin-small-top',
	});
	
	// ==========================================================================
	// Dashboard
	// ==========================================================================
	var dashboardgrid = $("#dashboards>div");
	UIkit.grid(dashboardgrid, {});

	// ==========================================================================
	// Page ready
	// ==========================================================================
	$("body").addClass("pure ready");
});

function domUpdated() {
	// ==========================================================================
	// Table
	// ==========================================================================
	incRewitreTable()
}

function incRewitreTable() {
	$('#contenu>table').addClass("table-bkg");
	$('#contenu table').addClass("uk-table uk-table-small uk-table-striped uk-table-middle");
	$(".stat-child>table").addClass("uk-table uk-table-small uk-table-striped uk-table-middle");
	$('#cms_dragable_cadre').parents("table").addClass("ui-table-Xsmall");
	$("table a").parents("tr").attr({
		'onmouseover': null,
		'onmouseout': null
	});
	$('table h3').parents('tr').addClass('actions-thead');
}