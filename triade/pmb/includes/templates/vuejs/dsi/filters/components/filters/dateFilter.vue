<template>
    <div class="filter">
        <div class="filter-group">
            <label :for="startLabel" class="etiquette">
                {{ messages.get('dsi', 'filter_date_start') }}
            </label>
            <input type="date" :id="startLabel" v-model="start" @change="change" ref="dateStart" />
            <label :for="endLabel" class="etiquette">
                {{ messages.get('dsi', 'filter_date_end') }}
            </label>
            <input type="date" :id="endLabel" v-model="end" @change="change" ref="dateEnd" />
            <button v-if="start != '' || end != ''" type="button" class="bouton" @click="reset">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</template>

<script>
let uid = 0;
export default {
    data: function () {
        return {
            start: "",
            end: ""
        };
    },
    computed: {
        startLabel: function () {
            return `filter_start_date_${uid}`;
        },
        endLabel: function () {
            return `filter_end_date_${uid}`;
        },
        hasValues: function () {
            return (this.start != "" || this.end != "");
        },
    },
    created: function () {
        uid++;
    },
    mounted: function () {
        if (this.$refs.dateStart && this.$refs.dateStart.form) {
            this.$refs.dateStart.form.addEventListener('reset', () => this.reset())
        }
    },
    methods: {
        reset: function () {
            if (this.hasValues) {
                this.start = "";
                this.end = "";
                this.change();
            }
        },
        change: function () {
            this.$emit("update", {
                start: this.start,
                end: this.end
            })
        }
    }
}
</script>