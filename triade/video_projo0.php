<html>
<body onload="init();">
<script type="text/javascript" src="./librairie_js/wz_dragdrop.js"></script>
<img src="video-proj-moyen.php?saisie_eleve=<?php print $_GET["saisie_eleve"]?>&saisie_classe=<?php print $_GET["saisie_classe"]?>" name="main" width="430" height="280" /><br><br><i>Double-cliquer sur l'image pour la redimensionner.</i>
<img name="lefttop" src="image/commun/marker_rect.gif" width="16" height="16" style="visibility:hidden;">
<img name="righttop" src="image/commun/marker_rect.gif" width="16" height="16" style="visibility:hidden;">
<img name="rightbottom" src="image/commun/marker_rect.gif" width="16" height="16" style="visibility:hidden;">
<img name="leftbottom" src="image/commun/marker_rect.gif" width="16" height="16" style="visibility:hidden;">
<script type="text/javascript">
<!--
SET_DHTML("main"+CURSOR_MOVE, "lefttop"+CURSOR_NW_RESIZE, "righttop"+CURSOR_NE_RESIZE, "rightbottom"+CURSOR_SE_RESIZE, "leftbottom"+CURSOR_SW_RESIZE);

var main = dd.elements.main;
var lt = dd.elements.lefttop;
var rt = dd.elements.righttop;
var rb = dd.elements.rightbottom;
var lb = dd.elements.leftbottom;
var grips = [lt, rt, rb, lb];

function init()
{
    hideGrips();
    main.setZ(main.z+1);
    main.div.ondblclick = showGrips;
}

function my_PickFunc()
{
    if (dd.obj.name == "main")
        hideGrips();
        
    else
    {
        var i = 4; while (i--)
        {
            if (grips[i] != dd.obj)
                grips[i].hide();
        }
    }
}

function my_DropFunc()
{
    hideGrips();
}

function my_DragFunc()
{
    if (dd.obj == rb)
    {
        main.resizeTo(rb.x-lb.x, rb.y-rt.y);
    }
    else if (dd.obj == rt)
    {
        main.resizeTo(rt.x-lt.x, rb.y-rt.y);
        main.moveTo(rt.x-main.w+rt.w/2, rt.y+rt.h/2);
    }
    else if (dd.obj == lb)
    {
        main.moveTo(lb.x+lb.w/2, lt.y+lt.h/2);
        main.resizeTo(rb.x-lb.x, lb.y-lt.y);
    }
    else if (dd.obj == lt)
    {
        main.moveTo(lt.x+lt.w/2, lt.y+lt.h/2);
        main.resizeTo(rt.x-lt.x, lb.y-lt.y);
    }
}

function showGrips()
{
    moveGripsToCorners();
    var i = 4; while(i--)
    {
        grips[i].setZ(main.z+1);
        grips[i].show();
    }
}

function hideGrips()
{
    var i = 4; while(i--)
        grips[i].hide();
}

function moveGripsToCorners()
{
    lt.moveTo(main.x-lt.w/2, main.y-lt.h/2);
    rt.moveTo(main.x+main.w-lt.w/2, main.y-lt.h/2);
    rb.moveTo(main.x+main.w-lt.w/2, main.y+main.h-lt.h/2);
    lb.moveTo(main.x-lt.w/2, main.y+main.h-lt.h/2);
}
//-->
</script>

</body>
</html>
