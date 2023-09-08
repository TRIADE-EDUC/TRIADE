$(document).ready(function(){
$("span.addCart a").append(" <i class='fa fa-shopping-basket'></i>");
$("span.affiner_recherche a").append(" <i class='fa fa-search-plus'></i>");
$("span.short_url a").append(" <i class='fa fa-rss'></i>");
$("span.search_bt_sugg a").append(" <i class='fa fa-pencil-square-o'></i>");
$("span.triSelector a").append(" <i class='fa fa-sort-alpha-asc'></i>");
$("span.printSearchResult a").append(" <i class='fa fa-print'></i>");
$("span.short_url_permalink a").append(" <i class='fa fa-clipboard' aria-hidden='true'></i>");
    $("span.open_visionneuse a").append(" <i class='fa fa-window-maximize' aria-hidden='true'></i>");
    $("span.search_bt_external a").append("<i class='fa'><svg width=\"18\" height=\"18\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"><g><path fill-rule=\"evenodd\" clip-rule=\"evenodd\" fill=\"#666666\" d=\"M18.295,18.583c-0.633,0.591-1.691,0.484-2.363-0.236 l-3.941-3.655c0,0-2.674,1.327-4.255,1.327c-4.138,0-7.493-3.353-7.493-7.491s3.355-7.493,7.493-7.493 c2.808,0,5.253,1.547,6.534,3.833h-2.385c-1.014-1.15-2.495-1.879-4.148-1.879c-3.06,0-5.539,2.479-5.539,5.538 s2.479,5.538,5.539,5.538c2.112,0,3.949-1.184,4.883-2.925h1.924c-0.279,0.661-0.541,1.141-0.541,1.141l4.225,3.927 C18.898,16.93,18.93,17.992,18.295,18.583z\"/> <path fill-rule=\"evenodd\" clip-rule=\"evenodd\" fill=\"#666666\" d=\"M15.971,11.836V9.919h-2.842V6.087h2.842V4.171l3.787,3.832 L15.971,11.836z M11.584,9.92V6.087h0.771V9.92H11.584z M9.655,9.92V6.087h1.157V9.92H9.655z\"/></g></svg></i>");
// add Filter ico
//$(".facette_tr_see_more a").append(" <i class='fa fa-plus-square-o'></i>");
// mes alertes remplacer la x
$(".expl-empr-retard center b").append("<i class='fa fa-exclamation-circle' aria-hidden='true'></i>");
    $("img[src='./images/orderby_az.gif']").remove();	
});