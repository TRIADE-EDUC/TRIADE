<template>
	<div :class="accordionClass">
		<h4>
			<button
				type="button"
				:aria-expanded="opened ? 'true' : 'false'"
				:class="accordionTriggerClass"
				:aria-controls="sectionId"
				:id="accordionId"
				:disabled="disabled"
				@click="onButtonClick">
				<span class="accordion-title">
					{{ title }}
					<span :class="iconClass"></span>
				</span>
			</button>
		</h4>
		<div :id="sectionId"
			role="region"
			:aria-labelledby="accordionId"
			class="accordion-panel"
			v-show="opened">
			<div>
				<fieldset>
					<slot><!-- contenu de la zone --></slot>
				</fieldset>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["title", "index", "expanded", "disabled"],
		data: function () {
			return {
				opened : false,
			}
		},
		created: function() {
			if (this.expanded) {
			    this.open();
			}    
		},
		computed : {
			accordionId : function() {
				return "accordion" + this.index;	
			},
			sectionId : function() {
				return "section" + this.index;	
			},
			accordionClass : function() {
				return this.disabled ? "accordion disabled" : "accordion";
			},
			accordionTriggerClass : function() {
				return this.disabled ? "accordion-trigger disabled" : "accordion-trigger";
			},
			iconClass : function() {
				return this.disabled ? "accordion-icon-disabled" : "accordion-icon";
			}
		},
		methods:{
			onButtonClick : function() {
				this.toggle(!this.opened);
			},
			
			toggle : function(open) {
				if (open === this.opened) {
					return;
				}
				this.opened = open;
			},
			
			open : function() {
				this.toggle(true);
			},
			
			close : function() {
				this.toggle(false);
			}
		}
	}
</script>