<template>
	<tbody class="account-table-line account-line">
		<tr :class="lineClass" @mouseover="focus()" @mouseout="blur()">
			<td class="account-line-title">
                <button 
                    v-if="account.transacash && account.transacash.length"
                    type="button"
                    class="bouton"
                    :id="accordionId"
                    :aria-expanded="showAccordion ? 'true' : 'false'"
                    :aria-controls="sectionId"
                    @click="toggleAccordion()">
                    <span :class="toggleAccordionClass"></span>
                </button>
                {{ account.label }}
            </td>
			<td :class="soldClass">
                {{ account.sold }} <span class="devise" v-html="devise"></span>
			</td>
		</tr>
		<transacash :transacash="account.transacash" :id="sectionId" :parentId="accordionId" :show="showAccordion" :devise="devise"/>
	</tbody>
</template>

<script>
	import transacash from "./transaCash.vue";
	
	export default {
		props : ["account", "index", "id", "devise"],
		
		data: function() {
		    return {
		        hover: false,
		        showAccordion: false,
		    }
		},
		computed: {
            toggleAccordionClass: function() {
                return this.showAccordion ? "fa fa-angle-down" : "fa fa-angle-right";
            },
		    paymentsAccountLineId: function() {
		        return `paymentsAccountLine${this.index}`;
		    },
		    accordionId: function() {
		        return `${this.paymentsAccountLineId}Accordion`;
		    },
		    sectionId: function() {
		        return `${this.paymentsAccountLineId}Transacash`;
		    },
		    soldClass: function() {
		        let classCss = "center";
                classCss += 0 > this.account.sold ? ' loss-price' : '';
		        return classCss;
		    },
		    lineClass: function() {
		        let classCss = this.hover ? 'surbrillance' : '';
		        classCss += ' account ';
		        classCss += this.index % 2 ? 'even' : 'odd';

		        return classCss;
		    }
		},
		methods: {
		    toggleAccordion: function () {
		        this.showAccordion = !this.showAccordion;
		    },
		    focus: function () {
		        this.hover = true;
		    },
		    blur: function () {
		        this.hover = false;
		    },
		},
		components : {
			transacash,
		}
	}
</script>