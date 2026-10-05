<template>
    <div v-if="data" class="widget-counters">
        <div v-for="(counter, key) in enabledCounters" :key="key" class="widget-counter tile" :style="`background-color: ${counter.color};`">
            <div>
                <button 
                    v-show="editable"
                    type="button" 
                    class="dashboard-button" 
                    @click="decrease(key)" 
                    :title="messages.get('common', 'down')"
                    :style="`color: ${helper.calculateTextColor(counter.color)};`">

                    <i class="fa fa-minus" aria-hidden="true"></i>
                </button>

                <!-- <span class="widget-counter-value">
                    {{ data[key] }}
                </span> -->

                <input 
                    type="number"
                    min="0"
                    id="widget-counter-input"
                    class="widget-counter-input" 
                    v-model="data[key]"
                    @keydown="allowOnlyNumbers"
                    @input="checkInt(key)"
                    @change="changeCounter(key)"
                    :disabled="!editable"
                    :style="`color: ${helper.calculateTextColor(counter.color)};`">
                
                <button 
                    v-show="editable"
                    type="button" 
                    class="dashboard-button" 
                    @click="increase(key)"
                    :title="messages.get('common', 'up')"
                    :style="`color: ${helper.calculateTextColor(counter.color)};`">

                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
            </div>
            
            <label :style="`color: ${helper.calculateTextColor(counter.color)};`">
                {{ counter.name }}
            </label>	
        </div>
        <div v-show="!editMode" class="dashboard-item-actions">
            <button 
                type="button" 
                class="dashboard-button" 
                :title="messages.get('common', 'refresh')"
                @click="refresh(false, true)">

                <i class="fa fa-refresh" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["widget", "editMode", "current_user", "widget_type"],
        components: {},
        data: function() {
            return {
                data: null
            }
        },
        created: function() {
            this.fetchData(false);

            if(this.widget.dashboardWidgetSettings && this.widget.dashboardWidgetSettings.refresh.enabled) {
                const refresh = Math.max(this.$root.widget_refresh_time, this.widget.dashboardWidgetSettings.refresh.value);
                setTimeout(this.refresh, refresh * 1000);
            }
        },
        computed: {
            enabledCounters: function() {
                let enabledCounters = {};

                for(let key in this.widget.dashboardWidgetSettings.counters) {
                    if(this.widget.dashboardWidgetSettings.counters[key].enabled) {
                        enabledCounters[key] = this.widget.dashboardWidgetSettings.counters[key];
                    }
                }

                return enabledCounters;
            },

            /**
             * Retourne l'editabilite de la note
             *
             * @return {bool}
             */
             editable: function() {
                
                if(this.editMode) {
                    return false;
                }
                if(this.widget.numUser == this.current_user) {
                    return true;
                }
                if(this.widget.widgetEditable == 1) {
                    return true;
                }
                return false;
            }
        },
        methods: {
            fetchData: async function(loader = true) {
                let post = new URLSearchParams();
                post.append("data", JSON.stringify({ 
                    source: this.widget_type.source,
                    params: { location: this.widget.widgetSettings.location }
                }));

                if (loader) {
                    this.showLoader();
                }

                let response = await fetch(this.$root.url_webservice + "widget/getData", {
                    method: "POST",
                    cache: 'no-cache',
                    body: post
                });

                let result = await response.json();
                if (result.error) {
                    this.notif.error(this.messages.get('dashboard', result.errorMessage));
                    return;
                }

                this.data = {};
                const firstKey = Object.keys(result)[0];

                for(let key in this.widget.dashboardWidgetSettings.counters) {
                    if(result[firstKey] && result[firstKey][key]){
                        this.data[key] = parseInt(result[firstKey][key]);
                        continue;
                    }
                    this.data[key] = 0;
                }

                this.$forceUpdate();

                if (loader) {
                    this.hiddenLoader();
                }
            },
            increase: function(key) {
                this.updateCounter(key, "add");

                this.$set(this.data, key, parseInt(this.data[key]) + 1);
                this.$forceUpdate();
            },
            decrease: function(key) {
                this.updateCounter(key, "remove");

                if(this.data[key] == 0) {
                    return;
                }
                
                this.$set(this.data, key, parseInt(this.data[key]) - 1);
                this.$forceUpdate();
            },
            changeCounter: function(key) {
                this.$set(this.data, key, parseInt(this.data[key]));
                this.$forceUpdate();

                this.updateCounter(key, this.data[key]);
            },
            updateCounter: async function(key, value = "add") {
                let post = new URLSearchParams();
                post.append("data", JSON.stringify({
                    idWidget: this.widget.idWidget,
                    source: this.widget_type.source,
                    params: { 
                        location: this.widget.widgetSettings.location,
                        counter_type: key,
                        value: value,
                    }
                }));

                await fetch(this.$root.url_webservice + "widget/updateData", {
                    method: "POST",
                    cache: 'no-cache',
                    body: post
                });
            },
            allowOnlyNumbers: function(event) {
                if (event.key.length === 1 && /\D/.test(event.key)) {
                    event.preventDefault();
                }
            },
            checkInt: function(key) {
                this.$forceUpdate();
                this.$set(this.data, key, parseInt(this.data[key]));
            },
            refresh: function (loop = true, loader = false) {
                this.$root.$emit("refreshWidget", this.widget, loader);
                this.fetchData(loader);

                if(this.widget.dashboardWidgetSettings.refresh.enabled && loop) {
                    const refresh = Math.max(this.$root.widget_refresh_time, this.widget.dashboardWidgetSettings.refresh.value);
                    setTimeout(this.refresh, refresh * 1000);
                }
            }
        }
    };
</script>