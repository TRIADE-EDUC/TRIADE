        //move facette to filter
    $(document).ready(function(){
        if( $("div[id^='cms_build']").length == 0){
            $("#main").append("<div class='filter uk-width-1-4@l@l'><div class='uk-panel'></div></div>");
            if( $("#facette").length == 1){
                $("#main").append("<div class='filter uk-width-1-4@l'><div class='uk-panel'></div></div>");
                $("#main").addClass("uk-grid uk-grid-collapse");
                $("#main_hors_footer").addClass("uk-width-3-4@l");
                $(".filter>.uk-panel").append($("#lvl1,#facette"));
            };
        };
    });
