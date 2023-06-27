(function(){
    var tags;
    var defaults = {
        height: 400,
        width: 400,
        radius: 150,
        speed: 3,
        slower: 0.99,
        timer: 5,
        fontMultiplier: 15,
        tagCSSOver: {
            border: '',
            color: ''
        },
        tagCSSOut: {
            border: '',
            color: ''
        }
    }
    var forCalcs = {
        halfHeight: null,
        halfWidth: null,
        hwratio: null,
        dtr: null,
        diametr: null,
        speedX: null,
        speedY: null,
        tLength: null
    }
    var curState = {
        mouseOver: null,
        lastFy: null,
        lastFx: null,
        sy: null,
        cy: null,
        sx: null,
        cx: null,
        mouseX: null,
        mouseY: null
    }
    var options = {};
    jQuery.fn.tagSphere = function(opt){
        options = jQuery.extend(defaults, opt);
        initContainer(this);
        initTags(this);
        initCalcs();
        deployTags();
        setInterval(updateTags, options.timer);
        return this;
    };
    
    function initCalcs(){
        forCalcs.halfHeight = options.height / 2;
        forCalcs.halfWidth = options.width / 2;
        forCalcs.speedX = options.speed / forCalcs.halfWidth;
        forCalcs.speedY = options.speed / forCalcs.halfHeight;
        forCalcs.dtr = Math.PI / 180;
        forCalcs.diametr = options.radius * 2;
        forCalcs.hwratio = options.height / options.width;
        forCalcs.whratio = options.width / options.height;
        forCalcs.tLength = tags.length - 1;
        curState.mouseOver = false;
        curState.lastFx = options.speed;
        curState.lastFy = options.speed;
    }
    
    function initContainer(tagCont){
        tagCont.height(options.height);
        tagCont.width(options.width);
        tagCont.css({
            'overflow': 'hidden',
            'position': 'relative'
        });    
        var tagContPosition = tagCont.offset();
        tagCont.mousemove(function(e){
        curState.mouseX = e.pageX - this.offsetLeft - tagContPosition.left;
        curState.mouseY = e.pageY - this.offsetTop - tagContPosition.top;

        });
        tagCont.hover(function(){
            curState.mouseOver = true;
        }, function(){
            curState.mouseOver = false;
        });
    }
    
    function initTags(tagCont){
        tags = tagCont.children('ul').children();
        tags.css({
            'position': 'absolute',
            'list-style-type': 'none',
            'list-style-position': 'outside',
            'list-style-image': 'none'
        });
        for (var i = 0; i < tags.length; i++) {
            var jTag = jQuery(tags[i]);
            tags[i] = jTag;
            jTag.hover(function(){
                jQuery(this).css(options.tagCSSOver);
            }, function(){
                jQuery(this).css(options.tagCSSOut);
            })
        }
    }
    
    function deployTags(){
        var phi = 0;
        var theta = 0;
        var max = forCalcs.tLength + 1;
        var i = 0;
        while (i++ < max) {
            phi = Math.acos(-1 + (2 * i - 1) / max);
            theta = Math.sqrt(max * Math.PI) * phi;
            tags[i - 1].cx = options.radius * Math.cos(theta) * Math.sin(phi);
            tags[i - 1].cy = options.radius * Math.sin(theta) * Math.sin(phi);
            tags[i - 1].cz = options.radius * Math.cos(phi);
            tags[i - 1].h = jQuery(tags[i - 1]).height() / 4;
            tags[i - 1].w = jQuery(tags[i - 1]).width() / 4;
        }
    }
    
    function calcRotation(fy, fx){
        curState.sy = Math.sin(fy * forCalcs.dtr);
        curState.cy = Math.cos(fy * forCalcs.dtr);
        curState.sx = Math.sin(fx * forCalcs.dtr);
        curState.cx = Math.cos(fx * forCalcs.dtr);
    }
    
    function updateTags(){
        var fy;
        var fx;
        if (curState.mouseOver) {
            fy = options.speed - forCalcs.speedY * curState.mouseY;
            fx = forCalcs.speedX * curState.mouseX - options.speed;
        }
        else {
            fy = curState.lastFy * options.slower;
            fx = curState.lastFx * options.slower;
        }
        curState.lastFy = fy;
        curState.lastFx = fx;
        if (Math.abs(fy) > 0.01 || Math.abs(fx) > 0.01) {
            calcRotation(fy, fx);
            j = -1;
            while (j++ < forCalcs.tLength) {
                rx1 = tags[j].cx;
                ry1 = tags[j].cy * curState.cy + tags[j].cz * -curState.sy;
                rz1 = tags[j].cy * curState.sy + tags[j].cz * curState.cy;
                tags[j].cx = rx1 * curState.cx + rz1 * curState.sx;
                tags[j].cy = tags[j].cy * curState.cy + tags[j].cz * -curState.sy;
                tags[j].cz = rx1 * -curState.sx + rz1 * curState.cx;
                var per = forCalcs.diametr / (forCalcs.diametr + tags[j].cz);
                tags[j].x = tags[j].cx * per;
                tags[j].y = tags[j].cy * per;
                tags[j].alpha = per / 2;
                tags[j].css({
                    'left': forCalcs.whratio * (tags[j].x - tags[j].w * per) + forCalcs.halfWidth,
                    'top': forCalcs.hwratio * (tags[j].y - tags[j].h * per) + forCalcs.halfHeight,
                    'opacity': tags[j].alpha,
                    'font-size': options.fontMultiplier * tags[j].alpha + 'px',
                    'z-index': Math.round(-tags[j].cz)+1000
                });
            }
        }
    }
})()
