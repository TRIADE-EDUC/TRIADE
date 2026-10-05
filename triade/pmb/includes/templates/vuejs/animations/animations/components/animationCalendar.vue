<template>
    <div id="animationCalendar">
        <div class="row">
            <div>
                <input id="repeatEventToAnimation" type="checkbox" v-model="repeatEvent.repeatEventToAnimation"/>
                <label for="repeatEventToAnimation" class='etiquette'>{{ pmb.getMessage("animation", "calendar_repetition_animation_parent") }}</label>
            </div>
	        <div id="el1Child" class="child" v-if="!repeatEvent.repeatEventToAnimation">
	            <div id="el1Child_0">
	                <label for="repeatEventAnimationName" class='etiquette'>{{ pmb.getMessage("animation", "calendar_repetition_animation_name") }}</label>
	                <input id="repeatEventAnimationName" type="text" v-model="repeatEvent.animationName"/>
	            </div>
            </div>
	        <div id="el1Child_2" class="child" v-if="!repeatEvent.repeatEventToAnimation">
	            <div id="el1Child_2">
	                <div id="el1Child_2a" class="row uk-clearfix">
	                    <label class='etiquette'>{{ pmb.getMessage('animation', 'update_add_animation_startDate') }}</label>
	                </div>
	                <div id="el1Child_2b" class="row uk-clearfix">
	                    <input v-model="repeatEvent.event.startDate" type="date"/>
	                    <input class="button" type="button" @click="clentEventDate('start')" value="X"/>
	                </div>
	                <div id="el1Child_2c" class="row uk-clearfix">
	                    <label class='etiquette'>{{ pmb.getMessage('animation', 'update_add_animation_endDate') }}</label>
	                </div>
	                <div id="el1Child_2d" class="row uk-clearfix">
	                    <input v-model="repeatEvent.event.endDate" type="date"/>
	                    <input class="button" type="button" @click="clentEventDate('end')" value="X"/>
	                </div>
	                <div id="el1Child_2">
	                    <input id="noEndDate" type="checkbox" v-model="repeatEvent.event.DuringDay"/>
	                    <label for="noEndDate" class='etiquette'>{{ pmb.getMessage("animation", "calendar_repetition_during_day") }}</label>
                    </div>
	            </div>
	            <div id="el1Child_3" v-if="!repeatEvent.event.DuringDay">
	                <div id="el1Child_3a" class="row uk-clearfix">
	                    <label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_startHour") }}</label>
	                </div>
	                <div id="el1Child_3b" class="row uk-clearfix">
	                    <input v-model="repeatEvent.event.startHour" type="time"/>
	                    <input class="button" type="button" @click="clentEventTime('start')" value="X"/>
	                </div>
	                <div id="el1Child_3c" class="row uk-clearfix">
	                    <label class='etiquette'>{{ pmb.getMessage("animation", "update_add_animation_endHour") }}</label>
	                </div>
	                <div id="el1Child_3d" class="row uk-clearfix">
                        <input v-model="repeatEvent.event.endHour" type="time"/>
	                    <input class="button" type="button" @click="clentEventTime('end')" value="X"/>
	                </div>
	            </div>
                <div id="el1Child_4" v-if="!repeatEvent.event.DuringDay">
	                <div id="el1Child_4a" class="row uk-clearfix">
	                    <label class='etiquette' >nombre de jours pour l'animation</label>
                        <input id="repeatEventnbDayAnimation" type="text" v-model="repeatEvent.event.nbDayAnimation"/>
	                </div>
	            </div>
	            <div id="el1Child_5">
		           <span>{{ pmb.getMessage("animation", "calendar_repetition_choice_localisation") }}</span>
		           <select id="repeaLocation" name="repeaLocation" v-model="repeatEvent.location">
		               <option v-for="(location, index) in locations" :key="index" :value="location.idlocation">{{ location.location_libelle }}</option>
		           </select>
	            </div>
	       </div>
        </div>

        <div class="row">
            <span>{{ pmb.getMessage("animation", "calendar_repetition_animation_choice") }}</span>
            <select id="repeatType" name="repeatType" v-model="repeatType">
                <option value="0">{{ pmb.getMessage("animation", "calendar_repetition_animation_repeat") }}</option>
                <option value="daily">{{ pmb.getMessage("animation", "calendar_repetition_animation_daily") }}</option>
                <option value="weekly">{{ pmb.getMessage("animation", "calendar_repetition_animation_weekly") }}</option>
                <option value="monthly">{{ pmb.getMessage("animation", "calendar_repetition_animation_monthly") }}</option>
            </select>
        </div>

        <div class="row">
            <dailycalendar v-if="'daily' == repeatType" :startdate="startDate" :enddate="endDate" @selectedDays="selectedDays"></dailycalendar>
            <weeklycalendar v-if="'weekly' == repeatType" :startdate="startDate" :enddate="endDate" @selectedDays="selectedDays"></weeklycalendar>
            <monthlycalendar v-if="'monthly' == repeatType" :startdate="startDate" :enddate="endDate" @selectedDays="selectedDays"></monthlycalendar>
        </div>
		<div class="row">
		    <input @click="repeatingAnimation" class="bouton" type="button" :value="pmb.getMessage('animation', 'animation_repeating_animation')"/>
		</div>
    </div>
</template>



<script>
	import dailycalendar from "../../../common/calendar/views/dailyCalendar.vue";
	import weeklycalendar from "../../../common/calendar/views/weeklyCalendar.vue";
	import monthlycalendar from "../../../common/calendar/views/monthlyCalendar.vue";
	
    export default {
        props : {
        	pmb: {
        		type: Object,
	        	default: function() {
	        		return "pmb";
	            }
        	},
        	startdate: {
        		type: String,
	        	default: function() {
	        		return "";
	            }
        	},
        	enddate: {
        		type: String,
	        	default: function() {
	        		return "";
	            }
        	},
        	locations: {
        		type: Array,
	        	default: function() {
	        		return [];
	            }

        	},
        	idanimation: {
        		type: Number,

        	}
        },
        components : {
        	dailycalendar,
        	weeklycalendar,
        	monthlycalendar,
        },
        data: function () {
            return {
            	repeatType: 0,
            	repeatEvent: {
            		location: 1,
            		repeatEventToAnimation: true,
            		event: {
	            		DuringDay: true,
	            		nbDayAnimation: 1
            		},
            	}
            }
        },
        computed: {
        	startDate: function() {
        		if(this.repeatEvent.event.startDate && !this.repeatEvent.repeatEventToAnimation) {
        		    return new Date(this.repeatEvent.event.startDate);
        		}
        		return new Date(this.startdate);
        	},
        	endDate: function() {
        		if(this.repeatEvent.event.endDate && !this.repeatEvent.repeatEventToAnimation) {
                    return new Date(this.repeatEvent.event.endDate);
                }
                return new Date(this.enddate);
        	}
        },
        methods: {
        	clentEventDate: function(event) {
        		if("start" == event) {
        			this.repeatEvent.event.startDate = "";
        		} else {
        			this.repeatEvent.event.endDate = "";
        		}
        	},
        	clentEventTime: function(event) {
        		if("start" == event) {
        			this.repeatEvent.event.startHour = "";
        		} else {
        			this.repeatEvent.event.endHour = "";
        		}
        	},

        	selectedDays: function(event) {
        		this.repeatEvent.selectedDays = event;
        	},

            repeatingAnimation: function(){
                if(!this.repeatType) {
                	alert(this.pmb.getMessage('animation', 'calendar_repetition_animation_no_repeat'));
                	return;
                }

                this.repeatEvent.idanimation = this.idanimation

                let url = "./ajax.php?module=animations&categ=animations&action=repeatEventAnimation";
                var data = new FormData();
                data.append('data', JSON.stringify(this.repeatEvent));

                fetch(url, {
                    method: 'POST',
                    body: data
                }).then(function(response) {
                    if (response.ok) {
                        response.text().then(function(idanimation) {
                        	document.location = './animations.php?categ=animations&action=view&id=' + idanimation;
                        });
                    } else {
                        console.log(this.pmb.getMessage('animation', 'admin_animation_no_response'));
                    }
                }).catch(function(error) {
                    console.log(this.pmb.getMessage('animation', 'admin_animation_error_fetch') + error.message);
                });
             }
        },
    }
</script>