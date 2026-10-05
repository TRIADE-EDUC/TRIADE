<template>
    <div class="filter">
        <div class="filter-group">
            <label :for="filterId" class="etiquette">{{ label }}</label>
            <select :id="filterId" class="saisie-20em" :name="filterId" v-model="selected" required @change="change"
                ref="select">
                <option value="">{{ messages.get('dsi', 'filter_empty_option') }}</option>
                <option v-for="(option, index) in contentList" :key="index" :value="option.value">{{ option.label }}
                </option>
            </select>
            <input type="input" class="saisie-80em" v-model="search" :placeholder="placeholder" @change="change"
                ref="input"  @input="change" />
        </div>
    </div>
</template>

<script>
let uid = 0;
export default {
    props: {
        contentList: {
            type: Array,
            required: true
        },
        label: {
            type: String,
            required: true
        },
        placeholder: {
            type: String,
            required: false,
            default: () => { return ""; }
        },
    },
    data: function () {
        return {
            selected: "",
            search: "",
            filterId: ""
        };
    },
    computed: {
        hasValues: function () {
            return (this.selected != "" || this.search != "");
        },
    },
    created: function () {
        this.filterId = `content_filter_${uid}`;
        uid++;
    },
    mounted: function () {
        if (this.$refs.select && this.$refs.select.form) {
            this.$refs.select.form.addEventListener('reset', () => this.reset())
        }
    },
    methods: {
        reset: function () {
            if (this.hasValues) {
                this.selected = "";
                this.search = "";
                this.change();
            }
        },
        change: function () {
            this.$emit('update', {
                contentType: this.selected,
                search: this.search
            })
        }
    }
}
</script>