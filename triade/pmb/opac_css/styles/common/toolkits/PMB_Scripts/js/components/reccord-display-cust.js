$(document).ready(function(){
    if($("form[name='cart_form']").length != 0){
        $('.parentNotCourte').addClass("uk-grid uk-grid-small reccord-item in-cart").removeClass("parentNotCourte").attr("data-uk-grid-margin","");
        $('.vignetteimgNot').addClass("uk-width-1-6@l uk-width-1-4@m uk-width-1-1 thumb-item").removeClass("vignetteimgNot");
        $('.notice_corps').addClass("uk-width-3-6@l uk-width-1-1 uk-width-3-4@m content-item").removeClass("notice_corps");
        $('.panier_avis_notCourte').addClass("uk-width-2-10@l uk-width-medium-1-1 uk-width-1-1 actions-item").removeClass("panier_avis_notCourte");
        $('.footer_notice').addClass("uk-width-1-10@l uk-width-1-1 bottom-item").removeClass("footer_notice");
    }else{
        $('.parentNotCourte').addClass("uk-grid uk-grid-small reccord-item").removeClass("parentNotCourte").attr("data-uk-grid-margin","");
        $('.vignetteimgNot').addClass("uk-width-1-5@l uk-width-1-4@m uk-width-1-1 thumb-item").removeClass("vignetteimgNot");
        $('.notice_corps').addClass("uk-width-3-5@l uk-width-1-1 uk-width-3-4@m content-item").removeClass("notice_corps");
        $('.panier_avis_notCourte').addClass("uk-width-1-5@l uk-width-medium-1-1 uk-width-1-1 actions-item").removeClass("panier_avis_notCourte");
        $('.footer_notice').addClass("uk-width-1-10@l uk-width-1-1 bottom-item").removeClass("footer_notice");
    }
    $('body').addClass("wyr");
});
