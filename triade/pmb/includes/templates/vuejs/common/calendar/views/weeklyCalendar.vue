<template>
    <div id="weeklyCalendar" class="weeklyCalendar">
        <div class="row">
            <div>
                <div class="row">
                    <label for="weekly-number" class='etiquette'>{{ msg.get('calendar', 'calendar_weekly_each') }}</label>
                    <input id="weekly-number" v-model="settings.nbWeeks" type="number" min="1" class='saisie-5em'/>
                    <label for="weekly-number" class='etiquette'>{{ msg.get('calendar', 'calendar_weekly_week') }}</label>
                </div>
            </div>
            <div>
                <div class="row">
                    <label class='etiquette'>{{ msg.get('calendar', 'calendar_weekly_day') }}</label>
                </div>
                <div class="row">
	                <template v-for="(day, index) in Days">
	                    <input :id="day.label + index" class="dayList" name="dayList" type="checkbox" v-model="settings.dayList" :value="day.value"/>
	                    <label :for="day.label + index" :key="index">{{ day.label }}</label>
	                </template>
                </div>
            </div>
            <previewcalendars :selected-days="selectedDays" :settings="settings"></previewcalendars>
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
						nbWeeks: 1,
                        dayList: []
					};
		        }
		    },
        },
        components : {
        	previewcalendars,
        },
        data: function () {
            return {
                msg: messages
            }
        },
        created: function() {
			if(!this.settings.nbWeeks) {
				this.$set(this.settings, "nbWeeks", 1);
			}

			if(!this.settings.dayList) {
				this.$set(this.settings, "dayList", []);
			}
		},
        computed: {
            selectedDays: function() {
                let selectedDays = {};
                let nbweek = 0;

                let currentDate = new Date(this.startdate.getTime());
                let endDate = new Date(this.enddate.getTime());

                while(currentDate < endDate) {
                    let year = currentDate.getFullYear();
                    let month = currentDate.getMonth();

                    if('undefined' === typeof selectedDays[year]) {
                        selectedDays[year] = {};
                    }

                    if('undefined' === typeof selectedDays[year][month]) {
                        selectedDays[year][month] = [];
                    }

					// si le jour courant est dans la liste des jours a inclure, l'ajouter au tableau
					if (this.settings.dayList.includes(currentDate.getDay()) && nbweek == 0) {
						selectedDays[year][month].push(new Date(currentDate.getTime()).getDate());
					}

				    // on est dimanche, on sort grand-mere mais demain c'est lundi donc +1 au nombre de semaine passe
					if (0 === currentDate.getDay()){
						nbweek++;

                        if(nbweek >= this.settings.nbWeeks) {
                        	nbweek = 0;
					    }
					}
					currentDate.setDate(currentDate.getDate() + 1)
                }

                this.$emit("selectedDays", {"selectedDays" : selectedDays})
                return selectedDays;
            },
            Days: function() {
                let date = new Date(this.startdate.getFullYear(), this.startdate.getMonth());

                // On va chercher le premier lundi
                while (date.getDay() !== 1) {
                    date.setDate(date.getDate() + 1);
                }

                let days = [];
                for(let i = 0; i < 7; i++) {
                	let label = date.toLocaleDateString(this.locale, { weekday: 'short' });
                	days.push({
                	   label: label[0].toLocaleUpperCase(),
                	   value: date.getDay()
                	});
                    date.setDate(date.getDate() + 1);
                }

                return days;
            },
            locale: function () {
                return this.userLang.replace("_", "-");
            },
        }
    }
</script>