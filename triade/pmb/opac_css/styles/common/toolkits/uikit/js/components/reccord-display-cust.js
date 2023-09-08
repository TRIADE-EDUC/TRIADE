$(document).ready(function(){	  
    if($("form[name='cart_form']").length != 0){
        $('.parentNotCourte').addClass("uk-grid uk-grid-small reccord-item in-cart").removeClass("parentNotCourte").attr("data-uk-grid-margin","");
        $('.vignetteimgNot').addClass("uk-width-large-1-6 uk-width-medium-1-4 uk-width-1-1 thumb-item").removeClass("vignetteimgNot");	
        $('.notice_corps').addClass("uk-width-large-3-6 uk-width-1-1 uk-width-medium-3-4 content-item").removeClass("notice_corps");	
        $('.panier_avis_notCourte').addClass("uk-width-large-2-10 uk-width-medium-1-1 uk-width-1-1 actions-item").removeClass("panier_avis_notCourte");
        $('.footer_notice').addClass("uk-width-large-1-1 uk-width-1-1 bottom-item").removeClass("footer_notice");
    }else{
        $('.parentNotCourte').addClass("uk-grid uk-grid-small reccord-item").removeClass("parentNotCourte").attr("data-uk-grid-margin","");
        $('.vignetteimgNot').addClass("uk-width-large-1-5 uk-width-medium-1-4 uk-width-1-1 thumb-item").removeClass("vignetteimgNot");	
        $('.notice_corps').addClass("uk-width-large-3-5 uk-width-1-1 uk-width-medium-3-4 content-item").removeClass("notice_corps");	
        $('.panier_avis_notCourte').addClass("uk-width-large-1-5 uk-width-medium-1-1 uk-width-1-1 actions-item").removeClass("panier_avis_notCourte");
        $('.footer_notice').addClass("uk-width-large-1-1 uk-width-1-1 bottom-item").removeClass("footer_notice");
    }		
    $('body').addClass("wyr");	    
});