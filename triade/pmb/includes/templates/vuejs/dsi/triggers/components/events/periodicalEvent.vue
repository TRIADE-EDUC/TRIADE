<template>
    <div class="periodical-calendar">
        <div class="dsi-form-group">
            <label class="etiquette" for="periodicalType">{{ messages.get('dsi', 'event_form_periodical') }} :</label>
            <div class="dsi-form-group-content">
                <select id="periodicalType" name="periodicalType" v-model.trim="event.settings.periodical">
                    <option value="" disabled>{{ messages.get('dsi', 'event_form_periodical_choice') }}</option>
                    <option value="daily">{{ messages.get('dsi', 'event_form_periodical_daily') }}</option>
                    <option value="weekly">{{ messages.get('dsi', 'event_form_periodical_weekly') }}</option>
                    <option value="monthly">{{ messages.get('dsi', 'event_form_periodical_monthly') }}</option>
                    <!-- <option value="yearly">{{ messages.get('dsi', 'event_form_periodical_yearly') }}</option> -->
                </select>
            </div>
        </div>
        <div v-if="event.settings.periodical">
            <fieldset class="dsi-fieldset-setting dsi-periodical-event-period">
                <legend class="dsi-legend-setting">{{ messages.get('dsi', 'event_form_settings') }}</legend>

                <div class="dsi-fieldset-dates">
                    <label class="etiquette" for="periodicalStartDate">{{ messages.get('dsi', 'event_form_periodical_start_date') }} :</label>
                    <input type="date" name="periodicalStartDate" id="periodicalEndDate" v-model="event.settings.periodical_start" required>
                    
                    <template v-if="event.settings.periodical_start">
                        <label class="etiquette" for="periodicalEndDate">{{ messages.get('dsi', 'event_form_periodical_end_date') }} ({{ messages.get('dsi', 'dsi_form_optional') }}) :</label>
                        <input type="date" name="periodicalEndDate" id="periodicalEndDate" v-model="event.settings.periodical_end">
                    </template>
                </div>

                <div v-if="event.settings.periodical_start">
                    <label class="etiquette" for="periodicalTime">{{ messages.get('dsi', 'event_form_periodical_time') }} :</label>
                    <input type="time" id="periodicalTime" name="periodicalTime" v-model="event.settings.periodical_time" required />
                </div>
                
                <template v-if="event.settings.periodical_start">
                    <dailycalendar v-if="'daily' === event.settings.periodical"
                        :startdate="startDate"
                        :enddate="endDate"
                        :settings="event.settings.periodical_data">
                    </dailycalendar>
                    <weeklycalendar v-if="'weekly' === event.settings.periodical"
                        :startdate="startDate"
                        :enddate="endDate"
                        :settings="event.settings.periodical_data">
                    </weeklycalendar>
                    <monthlycalendar v-if="'monthly' === event.settings.periodical"
                        :startdate="startDate"
                        :enddate="endDate"
                        :settings="event.settings.periodical_data">
                    </monthlycalendar>
                </template>

                <template v-if="!event.settings.periodical_end && event.settings.periodical_start">
                    <button type="button"
                        class="bouton"
                        @click="decreasePreview" 
                        :disabled="previewStartPlusN == 0" 
                        :title="messages.get('dsi', 'event_form_periodical_minus_calendar')">

                        <i class="fa fa-calendar-minus-o" aria-hidden="true"></i>
                    </button>
                    <button type="button"
                        class="bouton" 
                        @click="increasePreview"
                        :title="messages.get('dsi', 'event_form_periodical_plus_calendar')">

                        <i class="fa fa-calendar-plus-o" aria-hidden="true"></i>
                    </button>
                </template>

                <button v-if="displayResetCustomDates"
                    type="button"
                    class="bouton"
                    @click="resetCustomDates"
                    :title="messages.get('dsi', 'event_form_periodical_reset_calendar')">
                    
                    <i class="fa fa-undo" aria-hidden="true"></i>
                </button>
            </fieldset>
        </div>
    </div>
</template>
<script>
	import dailycalendar from "../../../../common/calendar/views/dailyCalendar.vue";
	import weeklycalendar from "../../../../common/calendar/views/weeklyCalendar.vue";
	import monthlycalendar from "../../../../common/calendar/views/monthlyCalendar.vue";
	
    export default {
        props : {
        	event: {
        		type: Object,
        	}
        },
        components : {
        	dailycalendar,
        	weeklycalendar,
        	monthlycalendar,
        },
        data: function () {
            return {
                previewStartPlusN: 0
            }
        },
        created: function() {
            if(!this.event.settings.periodical) {
                this.$set(this.event.settings, "periodical", "");
                this.$set(this.event.settings, "periodical_time", "09:00");
                this.$set(this.event.settings, "periodical_data", {});
                this.$set(this.event.settings.periodical_data, "custom_dates", { added_dates: [], removed_dates: [] });
            }
        },
        watch: {
            "event.settings.periodical": function() {
                this.$set(this.event.settings.periodical_data, "custom_dates", { added_dates: [], removed_dates: [] });
            }
        },
        computed: {
            startDate: function() {
                if (this.event.settings.periodical_start) {
                    let startDate = new Date(this.event.settings.periodical_start);

                    const currentDate = new Date();
                    
                    // Normaliser l'heure pour comparaison
                    currentDate.setHours(0, 0, 0, 0);

                    if(!this.event.settings.periodical_end && startDate.getTime() < currentDate.getTime()) {
                        switch (this.event.settings.periodical) {
                            case "daily":
                                // Récupérer le nombre de jours d'intervalle
                                const intervalDays = this.event.settings.periodical_data?.nbDays || 0;

                                // Différence en jours entre la date actuelle et la date de début
                                const diffDays = Math.ceil((currentDate - startDate) / (1000 * 60 * 60 * 24));
        
                                // Calculer le nombre de jours à ajouter à la date de début pour qu'elle soit après aujourd'hui
                                const daysToAdd = Math.ceil(diffDays / intervalDays) * intervalDays;
        
                                // Ajouter les jours nécessaires
                                startDate = new Date(startDate.getTime() + daysToAdd * 24 * 60 * 60 * 1000);
                                break;

                            case "weekly":
                            case "monthly":
                                // TODO Gérer les autres types (weekly, monthly)
                                startDate = currentDate;
                                break;
                        }
                    }

                    // Normaliser l'heure de la date retournée
                    startDate.setHours(0, 0, 0, 0); 
                    return startDate;
                }

                return new Date();
            },

        	endDate: function() {
        		if(this.event.settings.periodical_end) {
        		    return new Date(this.event.settings.periodical_end);
        		}

                if(this.event.settings.periodical_start) {
                    const date = new Date(this.startDate.getFullYear(), this.startDate.getMonth() + this.previewStartPlusN + 1, 0);

                    // Creez une nouvelle date avec l'annee et le mois de newDate et definissez le jour sur le dernier jour du mois
                    const lastDayOfMonth = new Date(date.getFullYear(), date.getMonth(), date.getDate());

                    return lastDayOfMonth;
                }
                
        		return new Date();
        	},
            displayResetCustomDates: function() {
                return this.event.settings.periodical_data.custom_dates && this.event.settings.periodical_data.custom_dates.added_dates.length || this.event.settings.periodical_data.custom_dates.removed_dates.length;
            }
        },
        methods: {
            increasePreview: function() {
                this.previewStartPlusN = this.previewStartPlusN + 1;
            },
            decreasePreview: function() {
                if(this.previewStartPlusN > 0) {
                    this.previewStartPlusN = this.previewStartPlusN - 1;
                }
            },
            resetCustomDates: function() {
                this.$set(this.event.settings.periodical_data.custom_dates, "removed_dates", []);
                this.$set(this.event.settings.periodical_data.custom_dates, "added_dates", []);
            }
        }
    }
</script>