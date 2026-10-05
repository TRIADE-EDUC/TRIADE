<template>
    <div id="monthlyCalendar" class="">
        <div class="row">
            <div>
                <div class="row">
                    <label for="monthly-number" class='etiquette'>{{ msg.get('calendar', 'calendar_weekly_each') }}</label>
                    <input id="monthly-number" v-model="settings.nbMonth" type="number"  min="1" class='saisie-5em'/>
                    <label for="monthly-number" class='etiquette'>{{ msg.get('calendar', 'calendar_mothly_month') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="row monthly-calendar-frequency">
                    <input name="monthlyCalendarSelect" value="no" type="radio" v-model="settings.monthlyCalendarSelected"/>
                    <!-- <label class='etiquette'>{{ pmb.getMessage('calendar', 'calendar_mothly_month') }}</label> -->
					<select id="monthlyRepeatFrequency" v-model="settings.repeatDay.frequency">
					   <!-- je mets des valeurs arbitraire pour ne pas tomber sur de chiffre de jour -->
                        <option value="10">{{ msg.get('calendar', 'calendar_frequencyDay_evey') }}</option>
                        <option value="1">{{ msg.get('calendar', 'calendar_frequencyDay_first') }}</option>
                        <option value="2">{{ msg.get('calendar', 'calendar_frequencyDay_second') }}</option>
                        <option value="3">{{ msg.get('calendar', 'calendar_frequencyDay_third') }}</option>
                        <option value="4">{{ msg.get('calendar', 'calendar_frequencyDay_fourth') }}</option>
                        <option value="5">{{ msg.get('calendar', 'calendar_frequencyDay_fifth') }}</option>
                        <option value="11">{{ msg.get('calendar', 'calendar_frequencyDay_last') }}</option>
					</select> 

                    <!-- <label class='etiquette'>{{ pmb.getMessage('calendar', 'calendar_mothly_month') }}</label> -->
					<select id="monthlyRepeatDay" v-model="settings.repeatDay.day">
					  <!-- <option v-for="(dayName, index) in WeekDays" :key="index" value="index">{{ dayName }}</option> -->
                        <option value="1">{{ msg.get('calendar', 'calendar_repeatDay_mon') }}</option>
                        <option value="2">{{ msg.get('calendar', 'calendar_repeatDay_tue') }}</option>
                        <option value="3">{{ msg.get('calendar', 'calendar_repeatDay_wes') }}</option>
                        <option value="4">{{ msg.get('calendar', 'calendar_repeatDay_thu') }}</option>
                        <option value="5">{{ msg.get('calendar', 'calendar_repeatDay_fri') }}</option>
                        <option value="6">{{ msg.get('calendar', 'calendar_repeatDay_sat') }}</option>
                        <option value="0">{{ msg.get('calendar', 'calendar_repeatDay_sun') }}</option>
					</select> 
                </div>
                <div class="row">
                    <input name="monthlyCalendarSelect" value="yes" type="radio" v-model="settings.monthlyCalendarSelected"/>
                    <div class="repeater-calendar-box">
	                    <div v-for="number in numbers" :key="number">
		                     <input name="dailyList" type="checkbox" :value="number" v-model="settings.dayList"/>
		                     <label class='etiquette'>{{ number }}</label>
	                    </div>
	                    <div>
	                        <input name="dailyList" type="checkbox" value="last" v-model="settings.dayList"/>
	                        <label class='etiquette'>{{ msg.get('calendar', 'calendar_last_day') }}</label>
	                    </div>
                    </div>
                </div>
            </div>
                <previewcalendars :selected-days="selectedDays" :settings="settings"></previewcalendars>
            </div>
        </div>
    </div>
</template>



<script>
    import messages from "../../helper/Messages.js";
    import previewcalendars from "./previewCalendars.vue";

    export default {
    	props :  {
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
            // Correspond a la global $user_lang
            userLang: {
                type: String,
                default: function() {
                    return "fr_FR";
                }
            },
            settings: {
		        type: Object,
		        default: function() {
		            return {
                        repeatDay: {
                            "frequency": 10,
                            "day": 1
            		    },
                        dayList: [1],
            		    nbMonth: 1,
                        monthlyCalendarSelected: "no",
					};
		        }
		    }
        },
        components : {
        	previewcalendars
        },
        data: function () {
            return {
                numbers: 31,
                msg: messages
            }
        },
        created: function() {
			if(!this.settings.repeatDay) {
				this.$set(this.settings, "repeatDay", {
                    "frequency" : 10,
                    "day" : 1,
                });
			}

			if(!this.settings.dayList) {
				this.$set(this.settings, "dayList", [1]);
			}

			if(!this.settings.nbMonth) {
				this.$set(this.settings, "nbMonth", 1);
			}

            if(!this.settings.monthlyCalendarSelected) {
				this.$set(this.settings, "monthlyCalendarSelected", "no");
			}
		},
        computed: {
            selectedDays: function() {
                if(this.settings.monthlyCalendarSelected === "no") {
	                return this.repeatDay();
                }

                return this.dayList();
            },
            locale: function () {
                return this.userLang.replace("_", "-");
            },
            WeekDays: function(locale) {
            	let startDate = this.startdate;
                let date = new Date(startDate.getFullYear(), startDate.getMonth());

                // On va chercher le premier lundi
                while (date.getDay() !== 1) {
                    date.setDate(date.getDate() + 1);
                }

                let weekDays = [];
                for(let i = 0; i < 7; i++) {
                    weekDays.push(date.toLocaleDateString(this.locale, { weekday: 'long' }));
                    date.setDate(date.getDate() + 1);
                }

                return weekDays;
            },
        },
        methods: {
        	repeatDay: function() {
                let selectedDays = {};
                let currentDate = new Date(this.startdate.getTime());
                let endDate = new Date(this.enddate.getTime());
                let nbMonth = currentDate.getMonth();

                const selectedDay = parseInt(this.settings.repeatDay.day);
                const frequency = parseInt(this.settings.repeatDay.frequency);

                while(currentDate < endDate) {
                    let year = currentDate.getFullYear();
                    let month = currentDate.getMonth();

                    if('undefined' === typeof selectedDays[year]) {
                        selectedDays[year] = {};
                    }

                    if('undefined' === typeof selectedDays[year][month]) {
                        selectedDays[year][month] = [];
                    }
					if (currentDate.getDay() === selectedDay && month == nbMonth) {
						// Calcul pour savoir si le jour est bien le second par exemple
						const dayNumber = Math.floor((currentDate.getDate() - 1) / 7) + 1;

						if(11 == frequency) {
						     var lastChoiceDayMonth = this.lastChoiceDayMonth(selectedDay, currentDate);
						}

						switch (true) {
						  case dayNumber === frequency:
						  case 11 === frequency && (currentDate.getDate() == lastChoiceDayMonth.getDate()):
						  case 10 === frequency:
					    	selectedDays[year][month].push(new Date(currentDate.getTime()).getDate());
						    break;
						  default:
						}
						if(12 <= nbMonth) {
                            nbMonth = 0;
                        }
					}

					if(currentDate.getDate() === new Date(year, month + 1, 0).getDate() && month == nbMonth) {
						nbMonth += parseInt(this.settings.nbMonth);
						if(12 <= nbMonth) {
							nbMonth -= 12;
						}
				    }
			        currentDate.setDate(currentDate.getDate() + 1);
                }

                this.$emit("selectedDays", {"selectedDays" : selectedDays})
        		return selectedDays;
        	},

        	dayList: function() {
        		let selectedDays = [];
                let currentDate = new Date(this.startdate.getTime());
                let endDate = new Date(this.enddate.getTime());
                let nbMonth = currentDate.getMonth();


                while(currentDate < endDate) {
                    let year = currentDate.getFullYear();
                    let month = currentDate.getMonth();
                    let lastDateOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

                    if('undefined' === typeof selectedDays[year]) {
                        selectedDays[year] = [];
                    }

                    if('undefined' === typeof selectedDays[year][month]) {
                        selectedDays[year][month] = [];
                    }

					if (this.settings.dayList.includes(currentDate.getDate()) && month == nbMonth) {
						selectedDays[year][month].push(new Date(currentDate.getTime()).getDate());
					} else if (this.settings.dayList.includes("last") && month == nbMonth) {
						if(lastDateOfMonth.getDate() === currentDate.getDate()) {
						    selectedDays[year][month].push(new Date(currentDate.getTime()).getDate());
						}
					}

					if(12 <= nbMonth) {
                        nbMonth = 0;
                    }

                    if(currentDate.getDate() === new Date(year, month + 1, 0).getDate() && month == nbMonth) {
                        nbMonth += parseInt(this.settings.nbMonth);
                        if(12 <= nbMonth) {
                            nbMonth -= 12;
                        }
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }

                this.$emit("selectedDays", {"selectedDays" : selectedDays})
                return selectedDays;
        	},

        	lastChoiceDayMonth: function(day, date) {
        		let endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        		while(day != endDate.getDay()) {
        			endDate.setDate(endDate.getDate() - 1);
        		}
        		return endDate;
        	}
        },
    }
</script>
<style>

.repeater-calendar-box {
    display: flex;
    flex-wrap: wrap;
    width: 25%;
    column-gap: 15px;
}

.repeater-calendar-box > * {
    flex: 0 0 auto;
}</style>