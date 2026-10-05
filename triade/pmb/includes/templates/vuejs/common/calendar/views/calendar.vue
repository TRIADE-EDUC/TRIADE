<template>
	<div class="calendar">
		<div class="calendar-wrapper">
			<div class="header-background">
				<div class="calendar-header">
					<div class="row header-title">
						<div class="header-text">
							<h3 class="month-name">{{ monthName }}</h3>
						</div>
					</div>
				</div>
			</div>

			<div class="calendar-content">
				<div class="calendar-table calendar-cells">
					<div class="table-header">
						<div class="table-row">
							<div class="table-col" v-for="(dayName, index) in WeekDays" :key="index">
                                {{ dayName }}
                            </div>
						</div>
					</div>

					<div class="table-body">
						<div v-for="(days, week) in weeks" :key="week" class="table-row">
							<div v-for="(day, index) in days"
                                :key="index"
                                :class="day.class" 
                                @click="addCustomDate(year, month, day.name, day.class)">

                                {{ day.name }}
                            </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
    props: {
        // Correspond a la global $user_lang
        userLang: {
            type: String,
            default: function() {
                return "fr_FR";
            }
        },
        month: {
            default: function() {
                return (new Date()).getMonth();
            }
        },
        year: {
            default: function() {
                return (new Date()).getFullYear();
            }
        },
        selectedDays: {
            type: Array,
            default: function() {
                return [];
            }
        },
        settings: {
            type: Object,
            default: function() {
                return {};
            }
        }
    },
    computed: {
        locale: function () {
            return this.userLang.replace("_", "-");
        },
        currentDate: function () {
            return new Date();
        },
        startDate: function () {
            return new Date(this.year, this.month, 1);
        },
        endDate: function () {
            return new Date(this.startDate.getFullYear(), this.startDate.getMonth() + 1, 0);
        },
        WeekDays: function() {
            let date = new Date(this.startDate.getFullYear(), this.startDate.getMonth());

            // On va chercher le premier lundi
            while (date.getDay() !== 1) {
                date.setDate(date.getDate() + 1);
            }

            let weekDays = [];
            for(let i = 0; i < 7; i++) {
                weekDays.push(date.toLocaleDateString(this.locale, { weekday: 'short' }));
                date.setDate(date.getDate() + 1);
            }

            return weekDays;
        },
        monthName: function () {
            let monthName = this.startDate.toLocaleString(this.locale, {
                month: "long"
            });
            let yearNum = this.startDate.toLocaleString(this.locale, {
                year: "numeric"
            });

            return `${monthName} ${yearNum}`;
        },
        weeks: function () {
            let weeks = [];
            let days = [];

            for (let i = 1; i < (this.startDate.getDay() || 7); i++) {
                days.push({
                    "class": "table-col empty-day",
                    "name": ""
                });
            }

            for (let i = 1; i <= this.endDate.getDate(); i++) {
                if (days.length >= 7) {
                    weeks.push(days);
                    days = [];
                }

                let classCss = "table-col";
                if (this.selectedDays.includes(i)) {
                    classCss += " selected";
                }

                if (
                	this.currentDate.getFullYear() == this.endDate.getFullYear() &&
                	this.currentDate.getMonth() == this.endDate.getMonth() &&
                	this.currentDate.getDate() == i
                ) {
                	classCss += " current-date";
                }

                if(this.settings.custom_dates) {
                    const dateString = this.dateToString(new Date(this.year, this.month, i));
                    if(this.settings.custom_dates.added_dates.includes(dateString)) {
                        classCss += " added-custom-day";
                    }

                    if(this.settings.custom_dates.removed_dates.includes(dateString)) {
                        classCss = classCss.replace("selected", "removed-custom-day")
                    }
                }

                days.push({
                    "class": classCss,
                    "name": i
                });
            }

            for (let i = days.length; i < 7; i++) {
                days.push({
                    "class": "table-col empty-day",
                    "name": ""
                });
            }
            weeks.push(days);

            return weeks;
        }
    },
    methods: {
        addCustomDate: function(year, month, day, classes) {
            if(this.settings && !this.settings.custom_dates) {
                return;
            }

            let date = new Date(year, month, day);
            let now = new Date();
            now.setHours(0, 0, 0, 0);

            if(date < now) {
                return;
            }

            let dateString = this.dateToString(date);

            if(classes.includes("selected")) {
                if(this.settings.custom_dates.added_dates.includes(dateString)) {
                    this.$delete(this.settings.custom_dates.added_dates, this.settings.custom_dates.added_dates.indexOf(dateString));

                    this.$forceUpdate();
                    return;
                }

                if(this.settings.custom_dates.removed_dates.includes(dateString)) {
                    this.$delete(this.settings.custom_dates.removed_dates, this.settings.custom_dates.removed_dates.indexOf(dateString));

                    this.$forceUpdate();
                    return;
                }

                this.$set(this.settings.custom_dates.removed_dates, this.settings.custom_dates.removed_dates.length, dateString);

            } else {
                if(this.settings.custom_dates.removed_dates.includes(dateString)) {
                    this.$delete(this.settings.custom_dates.removed_dates, this.settings.custom_dates.removed_dates.indexOf(dateString));

                    this.$forceUpdate();
                    return;
                }

                if(this.settings.custom_dates.added_dates.includes(dateString)) {
                    this.$delete(this.settings.custom_dates.added_dates, this.settings.custom_dates.added_dates.indexOf(dateString));

                    this.$forceUpdate();
                    return;
                }

                this.$set(this.settings.custom_dates.added_dates, this.settings.custom_dates.added_dates.length, dateString);
            }

            this.$forceUpdate();

            return;
        },
        dateToString: function(date) {
            let year = date.getFullYear();
            let month = (date.getMonth() + 1).toString().padStart(2, '0');
            let day = date.getDate().toString().padStart(2, '0');

            return year + '-' + month + '-' + day;
        }
    }
}
</script>
