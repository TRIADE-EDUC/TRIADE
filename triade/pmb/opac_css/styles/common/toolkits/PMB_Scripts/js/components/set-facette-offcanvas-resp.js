$(document).ready(function () {
    if ($('#facette').is(':visible') == true && $('#facette_wrapper').length) {
          // Ajout class au main pour détecter la page
          $("#container").addClass("pmb_page_w_facette");


        if ($(window).width() < 960) {
            $("#facette").attr( "uk-offcanvas", "mode: slide; overlay: true" );
            $("#facette_wrapper").addClass("uk-offcanvas-bar");
            $("#lvl1").addClass("uk-hidden");
            $("*[data-pmb-offcanvas='pmb_offcanvas_toggle']").removeClass("uk-hidden");
        }

        // Specific event size
        $(window).resize(function () {
            if ($(window).width() < 960) {
								$("#facette").attr( "uk-offcanvas", "mode: slide; overlay: true" );
		            $("#facette_wrapper").addClass("uk-offcanvas-bar");
		            $("#lvl1").addClass("uk-hidden");
		            $("*[data-pmb-offcanvas='pmb_offcanvas_toggle']").removeClass("uk-hidden");
            } else {
	              $("#facette").removeAttr("uk-offcanvas").removeClass("uk-offcanvas");;
	              $("#facette_wrapper").removeClass("uk-offcanvas-bar");
	              $("#lvl1").removeClass("uk-hidden");
								$("*[data-pmb-offcanvas='pmb_offcanvas_toggle']").addClass("uk-hidden");
            }
        });
    }
});
