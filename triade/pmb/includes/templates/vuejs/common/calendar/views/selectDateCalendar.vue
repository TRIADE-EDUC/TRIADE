<template>
    <div id="selectDateCalendar">
		<div class="row">
		    <div>
		        <div class="row uk-clearfix">
		            <label class='etiquette'>{{ msg.get('calendar', 'calendar_start_date') }}</label>
		        </div>
		        <div class="row uk-clearfix">
		            <input v-model="selectDate.start" type="date" @change="selectedDate"/>
		        </div>
		    </div>
		    <div>
		        <div class="row uk-clearfix">
		            <label class='etiquette'>{{ msg.get("calendar", "calendar_end_date") }}</label>
		        </div>
		        <div class="row uk-clearfix">
		            <input v-model="selectDate.end" type="date" @change="selectedDate"/>
		        </div>
		    </div>
		</div>
    </div>
</template>

<script>
import messages from "../../helper/Messages.js";

    export default {
        props : [],
        components : {
        },
        data: function () {
            return {
                selectDate: {
                	start: null,
                	end: null,
                },
                msg: messages
            }
        },
        created : function() {
        },
        computed : {
        },
        methods: {
        	selectedDate :function() {
                this.$emit("selectedDate", {"selectedDate" : this.selectDate})
            }
        },
    }
</script>