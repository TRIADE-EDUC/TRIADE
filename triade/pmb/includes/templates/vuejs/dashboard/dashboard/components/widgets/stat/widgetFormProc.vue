<template>
    <div>
        <!-- Choix classement -->
        <div class="mb-s">
            <label class="widget-form-label" for="widget-classements">
                {{ messages.get('dashboard', 'form_classement_label') }}
            </label>
            <select id="widget-classements" v-model="selectedClassement">
                <option value="" disabled>
                    {{ messages.get('dashboard', 'form_classement_select') }}
                </option>
                <option 
                    v-for="(classement, key) in module.methods" 
                    :key="key"
                    :value="key">

                    {{ classement.label }}
                </option>
            </select>
        </div>

        <!-- Choix méthode -->
        <div class="mb-s" v-if="selectedClassement !== ''">
            <label class="widget-form-label" for="widget-stat-type">
                {{ messages.get('dashboard', 'form_stat_label') }}
            </label>
            <select id="widget-stat-type" v-model="selectedProc">
                <option value="" disabled>
                    {{ messages.get('dashboard', 'form_stat_select') }}
                </option>
                <option 
                    v-for="(proc, key) in module.methods[selectedClassement].procs" 
                    :key="key"
                    :value="key">

                    {{ proc.label }}
                </option>
            </select>

            <!-- Bouton ajout methode -->
            <button 
                v-if="selectedProc !== ''" 
                type="button" 
                class="bouton" 
                @click="emitMethod" 
                :title="messages.get('common', 'more_label')">

                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["module"],
        components: {},
        data: function() {
            return {
                selectedClassement: "",
                selectedProc: ""
            }
        },
        methods: {
            emitMethod: function() {
                let method = this.module.methods[this.selectedClassement].procs[this.selectedProc];
                if(method) {

                    method.module = "proc";
                    this.$emit('addMethod', method);
                }
            }
        }
    };
</script>