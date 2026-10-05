<template>
    <div id="dailyCalendar" class="dailyCalendar">
        <div class="row">
            <div>
                <div class="row uk-clearfix">
                    <label for="daily-number" class='etiquette'>{{ msg.get('calendar', 'calendar_weekly_each') }}</label>
                    <input id="daily-number" v-model="settings.nbDays" type="number" min="1" class='saisie-5em'/>
                    <label for="daily-number" class='etiquette'>{{ msg.get('calendar', 'calendar_daily_day') }}</label>
                </div>
            </div>
        </div>
        <previewcalendars :selected-days="selectedDays" :settings="settings"></previewcalendars>
    </div>
</template>

<script>
    import messages from "../../helper/Messages.js";
    import previewcalendars from "./previewCalendars.vue";

    export default {
        props : {
        	startdate: {
	            type: Date,
		        default: function() {
		            return new Date();
		        }
		    },
		    enddate: {
		        type: Date,
		        default: function() {
		            return new Date(this.startdate.getFullYear(), this.startdate.getMonth() + 1, 0);
		        }
		    },
			settings: {
		        type: Object,
		        default: function() {
		            return {
						nbDays: 1
					};
		        }
		    }
        },
        components : {
        	previewcalendars
        },
        data: function () {
            return {
                msg: messages
            }
        },
		created: function() {
			if(!this.settings.nbDays) {
				this.$set(this.settings, "nbDays", 1);
			}
		},
        computed: {
        	selectedDays: function() {
        		if(0 == this.settings.nbDays){
        			this.settings.nbDays = 1;
        		}

        		let selectedDays = {};

                let currentDate = new Date(this.startdate.getTime());
                let endDate = new Date(this.enddate.getTime());

        		while(currentDate <= endDate) {
	       			let month = currentDate.getMonth();
       		    	let year = currentDate.getFullYear();

        			if('undefined' === typeof selectedDays[year]) {
        				selectedDays[year] = {};
        			}

        			if('undefined' === typeof selectedDays[year][month]) {
        				selectedDays[year][month] = [];
        			}

        			selectedDays[year][month].push(new Date(currentDate.getTime()).getDate());
        			currentDate.setDate(currentDate.getDate() + parseInt(this.settings.nbDays));
        		}

       		    this.$emit("selectedDays", {"selectedDays" : selectedDays})
        		return selectedDays;
        	}
        },
    }
</script>