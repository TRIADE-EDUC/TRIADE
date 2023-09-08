        //move facette to filter
    $(document).ready(function(){        
        if( $("div[id^='cms_build']").length == 0){
            $("#main").append("<div class='filter uk-width-large-1-4'><div class='uk-panel'></div></div>");
            if( $("#facette").length == 1){
                $("#main").append("<div class='filter uk-width-large-1-4'><div class='uk-panel'></div></div>");
                $("#main").addClass("uk-grid uk-grid-collapse");
                $("#main_hors_footer").addClass("uk-width-large-3-4");
                $(".filter>.uk-panel").append($("#lvl1,#facette"));
            };	
        };
    });