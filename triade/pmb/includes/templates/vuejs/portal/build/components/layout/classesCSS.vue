<template>
	<div class="classes-parameters">
		<div class="add-classes">
			<div class="add-classes-container-input" v-if="show">
				<form ref="form" @submit.prevent="addClasses">
					<input ref="input" 
						class="add-classes-input" 
						type="text" v-model.trim="newClasses" 
						v-on:keyup.enter="submit" required>
					<i class="add-classes-close fa fa-times" aria-hidden="true" @click="close"></i>
				</form>
			</div>
			<div v-else>
                <i v-if="originClassCss != undefined" class="fa fa-info-circle" :title="originClassCss"></i>
				<button class="bouton portal-badge-add" @click="open">
					{{ $cms.getMessage('add_classes_css') }} 
				</button>
			</div>
		</div>
		<div class="classes-list">					
			<div class="portal-badge" 
				v-for="(classe, index) in classesList" :key="`class_${index}`"
				:title="$cms.getMessage('classe_css').replace('%s', classe)">
				{{ classe }} 
				<i class="remove fa fa-trash" aria-hidden="true" @click="removeClass(index)"></i>
			</div>
		</div>
	</div>
</template>


<script>
	export default {
		name: "class_css",
		props: ["classes", "originClassCss"],
	    data: function () {
	        return {
	            show: false,
	            newClasses: "",
	            classesList: [],
	        }
	    },
	    mounted: function() {
	        this.classesList = this.classes;
	    },
	    watch: {
	        "classes" : function(newValue, oldValue) {
		        this.classesList = newValue;
	        }
	    },
	    methods: {
	        addClasses: function() {
	            var classesList = this.newClasses.split(' ');
	            for (let i = 0; i < classesList.length; i++) {
	                let classe = classesList[i];
	                if (classe[0] == ".") {
	                    classesList[i] = classe.slice(1, classe.length).trim()
	                }
	            }
	            
	            // On regroupe toutes les classes
	            classesList = [...this.classesList, ...classesList];
	            // [...new Set(classesList)] == array_unique
	            classesList = [...new Set(classesList)];
	            
	            if (classesList.join(",") != this.classesList.join(",")) {	                
		            this.classesList = classesList;
		            this.emitChange();
	            }
	            this.close();
	        },
	        close: function() {
	            this.newClasses = "";
	            this.show = false;
	        },
	        open: function() {
	            this.show = true;
	            this.$nextTick(() => {
		            this.$refs.input.focus()
				})
	        },
	        removeClass: function(index) {
	            this.classesList.splice(index, 1);
	            this.emitChange();
	        },
	        emitChange: function() {
	            this.$emit('change_classes', this.classesList);
	        },
	        submit: function() {
	            if (this.$refs.input.checkValidity()) {
	                this.$refs.form.submit()
	            }
	        }
	    }
	}
</script>