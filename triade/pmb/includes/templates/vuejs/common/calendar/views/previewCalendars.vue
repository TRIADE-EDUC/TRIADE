<template>
    <div>
	    <div class="calendars-container" v-for="(years, year) in selectedDays" v-if="years" :key="year">
	        <div class="row" v-for="(month, index) in years" v-if="month" :key="index">
	            <calendar :selectedDays="month" :month="index" :year="year" :settings="settings"></calendar>
	        </div>
	    </div>
    </div>
</template>

<script>
    import calendar from "./calendar.vue";

    export default {
    	props :  ["selectedDays", "settings"],
        components : {
        	calendar,
        }
    }
</script>