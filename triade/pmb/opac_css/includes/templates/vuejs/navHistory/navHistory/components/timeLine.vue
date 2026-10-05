<template>
    <g id="timeLine" :transform="translate" v-if="svg.advanceMode">
        <template v-for="(timeStamp, index) in timeLine">
            <g class="time" :key="`timeScale_${index}`" @mouseenter="showIcon(timeStamp)" @mouseleave="showIcon(timeStamp)"> 
				<g :transform="computedPositionFav(timeStamp)" v-if="activeBookmarkIcon(timeStamp)" @click="openPopup(timeStamp)">
					<rect class="background" x="0" y="0" width="16" height="16"/>
					<title v-if="isBookmark(timeStamp)">{{ getTitleBookmark(timeStamp) }}</title>
					<path :class="['favoris', isBookmark(timeStamp) ? 'active' : '']" d="M 0 5.87 L 6.13 5.87 L 8 0 L 9.87 5.87 L 16 5.87 L 11.12 9.8 L 13.05 16 L 8 12.16 L 2.95 16 L 4.88 9.8 Z"/>
				</g>
                <line class="line" :x1="timeStamp.x" :x2="timeStamp.x" :y1="computedLineY" :y2="svg.height" :stroke-width="strokeWidthLine"></line>
                <text v-if="timeStamp.dateLabel" class="date" :x="timeStamp.x" :y="computedDateY" :font-size="fontSize" :stroke-width="strokeWidth">{{ timeStamp.dateLabel }}</text>
                <text class="time" :x="timeStamp.x" :y="computedTimeY" :font-size="fontSize" :stroke-width="strokeWidth">{{ timeStamp.timeLabel }}</text>
            </g>
        </template>
		<foreignObject id="timeLinePopup" :x="svg.viewBox.x" :y="popupPositionY" v-if="showPopup">
			<body class="container" xmlns="http://www.w3.org/1999/xhtml" @click="hiddenPopup">
		        <div class="content-popup" :style="stylePopup">
		        	<input type="text" :placeholder="pmbmessages.getMessage('nav_history', 'nav_history_timeline_popup_placeholder')" v-model.trim="bookmark.title">
		        	<button type="button" class="submit-popup" @click="setFavoris()">
		        		{{ pmbmessages.getMessage('nav_history', 'nav_history_timeline_popup_submit') }}
		        	</button> 
		        </div>
			</body>
		</foreignObject>
    </g>
</template>

<script>
export default {
    name: "timeLine",
    props: [
        "svg", 
        "firstitem",
        "pmbmessages"
	],
    data: function () {
        return {
            bookmark: {
                time: 0,
                title: ''
            },
            modeMove: null,
            listIconsBookmarks: {},
            showPopup: false,
            update: false, // Force la mise a jour de la timeLine
            textBold: 1,
            lineBold: 1,
            textSize: 15,
            maxLine: 10,
            optionDate: { year: '2-digit', month: '2-digit', day: '2-digit' },
            optionTime: { hour: '2-digit', minute: '2-digit', second: '2-digit'}
        }
    },
    computed: {
        strokeWidthLine: function () {
			return (this.svg.scale > 0) ? this.lineBold/this.svg.scale : this.lineBold*this.svg.scale;
        },
        strokeWidth: function () {
			return (this.svg.scale > 0) ? this.textBold/this.svg.scale : this.textBold*this.svg.scale;
        },
        fontSize: function () {
			return (this.svg.scale > 0) ? this.textSize/this.svg.scale : this.textSize*this.svg.scale;
        },
        stylePopup: function () {
			return `font-size: ${this.fontSize}px;`;
        },
        computedLineY: function () {
            return this.svg.height-this.fontSize;
        },
        computedDateY: function () {
            return this.svg.height-(this.fontSize*2);
        },
        computedTimeY: function () {
            return this.svg.height-this.fontSize;
        },
        groupPositionY: function () {
            return  this.svg.viewBox.y + (this.svg.viewBox.height - this.svg.viewBox._height);
        },
        translate: function () {
            return `translate(0, ${this.groupPositionY})`;
        },
        popupPositionY: function () {
            return this.svg.viewBox.y -(this.groupPositionY);
        },
        timeLine: function () {
            let timeLine = new Array();
            
            if (!this.firstitem.timestamp || this.update) {
                return timeLine
            }
            
            let startX = this.svg.viewBox.x
            let endX = this.svg.viewBox.x + this.svg.viewBox.width
            
            let distance = endX-startX;
            distance = distance/this.maxLine;
            
            let x = startX + distance;
            for (let i = 0; i < this.maxLine-1; i++) {
                
                let time = this.computedTimeFromX(x);
                let timestamp = 0;
                if (x < this.firstitem.x) {
                    if (x < 0) {
		                timestamp = time + this.firstitem.timestamp ;
                    } else {
		                timestamp = this.firstitem.timestamp - time;
                    }
                } else {
	                timestamp = time+this.firstitem.timestamp;
                }
                
                
				let date = new Date();
				date.setTime(timestamp);
				date.setMilliseconds(0);
				let dateLabel = date.toLocaleDateString('fr-FR', this.optionDate);
				timestamp = date.getTime();
				
                if (!this.listIconsBookmarks[timestamp]) {
                    this.listIconsBookmarks[timestamp] = false;
                }
                
                
				if (i > 0 && i < this.maxLine-2) {
				    let previousDate = new Date(timeLine[i-1].time);
				    if (previousDate.getDay() === date.getDay()) {
						dateLabel = "";
				    }
 				}
				
                timeLine.push({
                    time: date.getTime(),
                    dateLabel: dateLabel,
                    timeLabel: date.toLocaleTimeString('fr-FR', this.optionTime),
                    x: x
                });
                
                x += distance;
            }
            return timeLine;
        }
    },
    methods: {
        computedPositionFav: function (timeStamp) {
            let y = this.svg.height-(this.fontSize*4);
            if (!timeStamp.dateLabel) {
	            y = this.svg.height-(this.fontSize*3);
            }
            
            let scale = (this.svg.scale > 0) ? 1/this.svg.scale : 1*this.svg.scale;
            let distance = (this.svg.scale > 0) ? 8/this.svg.scale : 8*this.svg.scale;
            let x = timeStamp.x - distance;
            return `translate(${x}, ${y}) scale(${scale})`;
        },
        showIcon: function (timeStamp) {
			if (this.svg.moveOptions.enableBookmarks || (!this.svg.moving && !this.svg.moveMode)) {
	            this.update = true;
	            this.listIconsBookmarks[timeStamp.time] = !this.listIconsBookmarks[timeStamp.time];
	            this.update = false;
			}
        },
        openPopup: function (timeStamp) {
			if (this.svg.moveOptions.enableBookmarks || (!this.svg.moving && !this.svg.moveMode)) {
				// On masque le tooltip de l'item
	            this.svg.itemHover.itemId = 0;
				// Désactive le déplacement
				this.modeMove = this.svg.moveMode;
				this.svg.moveMode = false;
				// Mon désactive l'accès aux boutons
				this.svg.accesControls = false;
				
	            this.showPopup = true;
	            this.bookmark.time = timeStamp.time;
	            if (this.isBookmark(timeStamp)) {
		            this.bookmark.title = this.getTitleBookmark(timeStamp);
	            }
			}
        },
        hiddenPopup: function (e) {
		    if(e && e.target !== e.currentTarget) return;
            this.showPopup = false;
            this.resetBookmark();
			this.svg.accesControls = true;
			this.svg.moveMode = this.modeMove;
			this.modeMove = null;
        },
        checkBookmark: function () {
            
            // Aucun Créneaux choisie
            if (this.bookmark.time == 0) {
                return false;
            }
            
            // Aucun label
            if (this.bookmark.title.length == 0) {
                return false;
            }
            
			return true;
        },
        setFavoris: function () {
            if (this.checkBookmark()) {
	            // On retire la timeline
	            this.update = true;
	            
	            // On sauvegarde le favori
	            let bootmark = {
					time: this.bookmark.time, 
					title: this.bookmark.title, 
	            };
	            
				this.svg.bookmarksList[this.bookmark.time] = bootmark;
	            this.saveBookmark(bootmark);
	            
	            this.resetBookmark();
                this.hiddenPopup();
                
	            // On recalcule la timeline
	            this.update = false;
            } else {
                this.hiddenPopup();
            }
        },
        activeBookmarkIcon: function (timeStamp) {
            if (this.bookmark.time == timeStamp.time) {
                return true;
            }
            if (this.isBookmark(timeStamp)) {
                return true;
            }
            return this.listIconsBookmarks[timeStamp.time];
        },
        isBookmark: function (timeStamp) {
            return this.svg.bookmarksList[timeStamp.time] ? true : false;
        },
        getTitleBookmark: function (timeStamp) {
            return this.svg.bookmarksList[timeStamp.time].title;
        },
        computedTimeFromX: function (x) {
            let min = x/100;
            let second = min*60;
            let time = second*1000;
            return time
        },
        resetBookmark: function () {
            // On reset la variable
			this.bookmark.time = 0;
			this.bookmark.title = '';
        },
        saveBookmark: function(bookmark) {
            let postData = "session_data=" + JSON.stringify({
				"bookmark": bookmark,
			});

			var xhttp = new XMLHttpRequest();
			xhttp.open("POST", "./ajax.php?module=ajax&categ=session&action=save_bookmark_nav_history", true);
			xhttp.withCredentials = true;
			xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhttp.send(postData);
        }
    }
} 
</script>