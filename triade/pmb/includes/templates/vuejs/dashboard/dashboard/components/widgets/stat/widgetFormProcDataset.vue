<template>
    <div>
        <div class="mb-s" v-if="conditions && method.conditions">
            <div v-for="condition, keyCondition in conditions" :key="keyCondition" class="mb-s">
                <div v-if="condition.type === 'query_list' || condition.type === 'list'">

                    <label class="widget-form-label" :for="`${condition.name}-${keyMethod}-${keyCondition}`">
                        {{ condition.label }}
                    </label>
                    <select 
                        :id="`${condition.name}-${keyMethod}-${keyCondition}`"
                        :multiple="condition.options.multiple == 1"
                        :required="condition.mandatory == 1"
                        :size="condition.options.multiple == 1 ? 4 : 1"
                        v-model="method.conditions[condition.name]">

                        <option v-if="condition.options.default" :value="condition.options.default[0]">
                            {{ condition.options.default[1] }}
                        </option>
                        <option v-for="option in condition.options.values" :value="option[0]">
                            {{ option[1] }}
                        </option>
                    </select>

                </div>

                <div v-if="condition.type === 'text'">

                    <label class="widget-form-label" :for="`${condition.name}-${keyMethod}-${keyCondition}`">
                        {{ condition.label }}
                    </label>
                    <input 
                        type="text" 
                        :id="`${condition.name}-${keyMethod}-${keyCondition}`"
                        :maxlength="condition.options.maxsize" 
                        :required="condition.mandatory == 1"
                        v-model="method.conditions[condition.name]">

                </div>

                <div v-if="condition.type === 'date_box'">

                    <label class="widget-form-label" :for="`${condition.name}-${keyMethod}-${keyCondition}`">
                        {{ condition.label }}
                    </label>
                    <input 
                        type="date" 
                        :id="`${condition.name}-${keyMethod}-${keyCondition}`"
                        :required="condition.mandatory == 1"
                        v-model="method.conditions[condition.name]">

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["method", "keyMethod", "source"],
        data: function() {
            return {
                conditions: [],
            }
        },
        created: async function() {
            await this.fetchConditions();

            if(!this.method.conditions) {
                this.initMethodConditions();
            }
        },
        methods: {
            fetchConditions: async function() {
                let response = await this.ws.post('widget', 'getConditions', { 
                        source: this.source, 
                        params: {id: this.method.id} 
                    }
                );

                if (response.error) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                    return;
                }

                this.$set(this, "conditions", response);
            },
            initMethodConditions: function() {
                let conditions = {};

                for(let condition of this.conditions) {
                    if(condition.options && condition.options.multiple) {
                        conditions[condition.name] = [];
                        continue;
                    }

                    conditions[condition.name] = "";

                    if(condition.options.default && condition.options.default.length > 0) {
                        conditions[condition.name] = condition.options.default[0];
                    }
                }

                this.$set(this.method, "conditions", conditions);
            }
        }
    };
</script>