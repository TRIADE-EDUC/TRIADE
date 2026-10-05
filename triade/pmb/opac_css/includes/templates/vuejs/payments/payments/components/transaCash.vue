<template>
    <table v-show="show" :id="id" :aria-labelledby="parentId" class="accordion-content">
        <caption>{{ messages.get("payments", "transactions") }}</caption>
        <thead class="account-table-head">
            <tr>
                <td class="transacash-title-registration-date">{{ messages.get("payments", "registration_date") }}</td>
                <td class="transacash-title-comment">{{ messages.get("payments", "comment") }}</td>
                <td class="transacash-title-credits">{{ messages.get("payments", "credits") }}</td>
                <td class="transacash-title-cash-flow">{{ messages.get("payments", "cash_flow") }}</td>
            </tr>
        </thead>
	    <tbody>
			<tr v-for="(transa, key) in transacash" :key="key" :class="lineClass(key)" class="account-transacash-line" @mouseover="focus(key)" @mouseout="blur()">
			    <td class="transacash-registration-date">
	                {{ transa.date_enrgt }}
			    </td>
			    <td class="transacash-comment">
	                {{ transa.label }}
			    </td>
			    <td class="transacash-credits">
	                <span v-if=(transa.credit)>
	                	{{ transa.sold }}
	                	<span class="devise" v-html="devise"></span>
                	</span>
                	<span v-else> - </span>
			    </td>
			    <td :class="soldClass(transa.sold)">
	                 <span v-if=(!transa.credit)>
	                	{{ transa.sold }}
	                	<span class="devise" v-html="devise"></span>
                	</span>
                	<span v-else> - </span>
			    </td>
			</tr>
		</tbody>
	</table>
</template>

<script>
	export default {
		props : ["transacash", "id", "parentId", "show", "devise"],

		data: function () {
            return {
                hover: -1
            }
		},
        methods: {
            lineClass: function(index) {
                let classCss = this.hover == index ? 'surbrillance' : '';
                classCss += ' account-line transacash ';
                classCss += index % 2 ? 'even' : 'odd';

                return classCss;
            },
            soldClass: function(sold) {
                let classCss = "transacash-cash-flow";
                if (0 > sold) {
                    classCss += " loss-price";
                }
                return classCss;
            },
            focus: function (index) {
                this.hover = index;
            },
            blur: function () {
                this.hover = -1;
            }
        }
	}
</script>