<template>
    <div class="dsi-form-selector">
        <div class="dsi-form-group-m">
            <div class="dsi-form-group-content">
                <label for="exact_date">{{ msg.exact_date }}</label>
                <input name="exact_date" type="radio" v-model="data.dateRange" value="exact" id="exact_date" />
            </div>
            <div class="dsi-form-group-content">
                <label for="range_date">{{ msg.range_date }}</label>
                <input name="range_date" type="radio" v-model="data.dateRange" value="range" id="range_date" />
            </div>
        </div>
        <div class="dsi-form-group-m">
            <div class="dsi-form-group-content">
                <label for="date_from">{{ msg.from_date }}</label>
                <input name="date_from" type="date" v-model="data.dateFrom" id="date_from" />
            </div>
        </div>
        <div class="dsi-form-group-m">
            <div v-if="data.dateRange == 'range'" class="dsi-form-group-content">
                <label for="date_until">{{ msg.until_date }}</label>
                <input name="date_until" type="date" v-model="data.dateUntil" id="date_until" />
            </div>
        </div>
        <div>
            <slot name="trySearch"></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        data: {
            type: Object,
            default: () => {
                return {
                    dateRange: "exact",
                    dateFrom: "",
                    dateUntil: ""
                }
            }
        },
        msg: {
            type: Object
        }
    }
}
</script>
