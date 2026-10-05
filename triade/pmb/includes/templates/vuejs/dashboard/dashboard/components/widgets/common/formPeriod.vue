<template>
<div v-if="period">
    <div class="mb-s">
        <label class="widget-form-label" for="periodSelector">
            {{ messages.get('dashboard', 'form_period_period_label') }}
        </label>
        <select 
            id="periodSelector" 
            name="periodSelector" 
            v-model="period.periodSelector">
            <option value="undefined">{{ messages.get('dashboard', 'form_period_undefined') }}</option>
            <option value="today">{{ messages.get('dashboard', 'form_period_today') }}</option>
            <option value="thisWeek">{{ messages.get('dashboard', 'form_period_this_week') }}</option>
            <option value="lastWeek">{{ messages.get('dashboard', 'form_period_last_week') }}</option>
            <option value="thisMonth">{{ messages.get('dashboard', 'form_period_this_month') }}</option>
            <option value="lastMonth">{{ messages.get('dashboard', 'form_period_last_month') }}</option>
            <option value="thisYear">{{ messages.get('dashboard', 'form_period_this_year') }}</option>
            <option value="lastYear">{{ messages.get('dashboard', 'form_period_last_year') }}</option>
            <option value="dates">{{ messages.get('dashboard', 'form_period_dates') }}</option>
            <option value="since">{{ messages.get('dashboard', 'form_period_since') }}</option>
            <option value="to">{{ messages.get('dashboard', 'form_period_to') }}</option>
        </select>
    </div>

    <div v-if="period.periodSelector == 'dates'" id="datesDiv" class="mb-s">
        <label class="widget-form-label" for="datesSince">
            {{ messages.get('dashboard', 'form_period_since_label') }}
        </label>
        <input type="date" id="datesSince" name="datesSince" v-model="period.datesSince" />

        <label for="datesTo">
            {{ messages.get('dashboard', 'form_period_to_label') }}
        </label>
        <input type="date" id="datesTo" name="datesTo" v-model="period.datesTo" />
    </div>

    <div id="sinceDiv" v-if="period.periodSelector == 'since'">
        <div class="mb-s">
            <label class="widget-form-label" for="sinceSelector">
                {{ messages.get('dashboard', 'form_period_since_label') }}
            </label>
            <select id="sinceSelector" name="sinceSelector" v-model="period.sinceSelector" >
                <option value="today">{{ messages.get('dashboard', 'form_period_today') }}</option>
                <option value="thisWeek">{{ messages.get('dashboard', 'form_period_this_week') }}</option>
                <option value="thisMonth">{{ messages.get('dashboard', 'form_period_this_month') }}</option>
                <option value="lastMonth">{{ messages.get('dashboard', 'form_period_last_month') }}</option>
                <option value="thisYear">{{ messages.get('dashboard', 'form_period_this_year') }}</option>
                <option value="lastYear">{{ messages.get('dashboard', 'form_period_last_year') }}</option>
                <option value="aDate">{{ messages.get('dashboard', 'form_period_a_date') }}</option>
            </select>
            <input 
                v-if="period.sinceSelector == 'aDate'"
                type="date" 
                id="sinceStartDate" 
                name="sinceStartDate" 
                v-model="period.sinceStartDate">
        </div>
        <div class="mb-s">
            <label class="widget-form-label" for="sinceDuration">
                {{ messages.get('dashboard', 'form_period_duration_label') }}
            </label>
            <select id="sinceUnit" name="sinceUnit" v-model="period.sinceUnit" >
                <option value="undefined">{{ messages.get('dashboard', 'form_period_undefined') }}</option>
                <option value="days">{{ messages.get('dashboard', 'form_period_days') }}</option>
                <option value="weeks">{{ messages.get('dashboard', 'form_period_weeks') }}</option>
                <option value="months">{{ messages.get('dashboard', 'form_period_months') }}</option>
                <option value="years">{{ messages.get('dashboard', 'form_period_years') }}</option>
            </select>
            <input 
                v-if="period.sinceUnit != 'undefined'"
                type="number" 
                size="5" 
                id="sinceDuration" 
                name="sinceDuration" 
                min="1" 
                v-model="period.sinceDuration">
        </div>
    </div>

    <div v-if="period.periodSelector == 'to'" id="toDiv">
        <div class="mb-s">
            <label class="widget-form-label" for="toDuration">
                {{ messages.get('dashboard', 'form_period_duration_label') }}
            </label>
            <select id="toUnit" name="toUnit" v-model="period.toUnit">
                <option value="undefined">{{ messages.get('dashboard', 'form_period_undefined') }}</option>
                <option value="days">{{ messages.get('dashboard', 'form_period_days') }}</option>
                <option value="weeks">{{ messages.get('dashboard', 'form_period_weeks') }}</option>
                <option value="months">{{ messages.get('dashboard', 'form_period_months') }}</option>
                <option value="years">{{ messages.get('dashboard', 'form_period_years') }}</option>
            </select>
            <input 
                v-if="period.toUnit != 'undefined'"
                type="number" 
                size="5" 
                id="toDuration" 
                name="toDuration" 
                min="1" 
                v-model="period.toDuration">
        </div>
        <div class="mb-s">
            <label class="widget-form-label" for="toSelector">
                {{ messages.get('dashboard', 'form_period_to_label') }}
            </label>
            <select name="toSelector" id="toSelector" v-model="period.toSelector" >
                <option value="today">{{ messages.get('dashboard', 'form_period_today') }}</option>
                <option value="thisWeek">{{ messages.get('dashboard', 'form_period_this_week') }}</option>
                <option value="thisMonth">{{ messages.get('dashboard', 'form_period_this_month') }}</option>
                <option value="lastMonth">{{ messages.get('dashboard', 'form_period_last_month') }}</option>
                <option value="thisYear">{{ messages.get('dashboard', 'form_period_this_year') }}</option>
                <option value="lastYear">{{ messages.get('dashboard', 'form_period_last_year') }}</option>
                <option value="aDate">{{ messages.get('dashboard', 'form_period_a_date') }}</option>
            </select>
            <input 
                v-if="period.toSelector == 'aDate'"
                type="date" 
                id="toEndDate" 
                name="toEndDate" 
                v-model="period.toEndDate">
        </div>
    </div>
</div>
</template>

<script>
    export default {
        props: ["period"],
        created: function() {
            if(!this.period.periodSelector) {
                this.$set(this.period, 'periodSelector', 'today');
                this.$set(this.period, 'datesSince', '');
                this.$set(this.period, 'datesTo', '');
                this.$set(this.period, 'sinceSelector', 'today');
                this.$set(this.period, 'sinceStartDate', '');
                this.$set(this.period, 'sinceDuration', '1');
                this.$set(this.period, 'sinceUnit', 'undefined');
                this.$set(this.period, 'toSelector', 'today');
                this.$set(this.period, 'toEndDate', '');
                this.$set(this.period, 'toDuration', '1');
                this.$set(this.period, 'toUnit', 'undefined');
            }
        }
    }
</script>
