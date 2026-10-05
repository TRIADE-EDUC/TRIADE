<template>
    <div>
        <span v-for="(entity, key) in filteredEntities" :key="key" >
            <input type="checkbox" @input="setEntity(key)" :value="key" :id="key" :checked="entity.checked" />
            <label :for="key">{{entity.value}}</label>
        </span>
    </div>
</template>

<script>
export default {
    name : "entitiesselector",
    props : ["entities", "value"],
    computed : {
        filteredEntities : function() {
            let result = {};
            for(let key in this.entities) {
                if(! this.entities[key]) {
                    continue;
                }
                result[key] = this.entities[key];
            }
            return result;
        }
    },
    methods : {
        setEntity : function(key) {
            let i = this.value.indexOf(key)
            if(i == -1) {
                this.value.push(key);
            } else  {
                this.value.splice(i, 1);
            }
            this.$emit("input", this.value);
        }
    }
}
</script>