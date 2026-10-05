<template>
	<div id="registrationSaved" class="row registration_saved">
		<h1 class="registration_intro" v-if="formdata.globals.opac_rgaa_active">
            {{ pmb.getMessage("animation", "animation_registration_saved_success") }}
        </h1>
		<h2 class="registration_intro" v-else>
            {{ pmb.getMessage("animation", "animation_registration_saved_success") }}
        </h2>

		<p>{{ pmb.getMessage("animation", "animation_registration_recap") }}</p>

		<div class="row registration_saved_registration_list" v-for="(registration, index) in formdata.registrationList" :key="index">

			<!-- contact -->
			<hr />
			<div class='row registration_saved_contact'>
		        <h2 v-if="formdata.globals.opac_rgaa_active">
		            {{ pmb.getMessage("animation", "animation_registration_contact") }}
		        </h2>
		        <h3 v-else>
		            {{ pmb.getMessage("animation", "animation_registration_contact") }}
		        </h3>
			</div>
			<table class="fiche-inscription registration_saved_tab_registration">
				<tr>
					<td class="bg-grey align_right">
						<span class="etiq_champ">{{ pmb.getMessage("animation", "animation_registration_barcode") }}</span>
					</td>
					<td v-if="registration.barcode">{{ registration.barcode}}</td>
					<td v-else><em>{{ pmb.getMessage("animation", "animation_registration_not_set") }}</em></td>
				</tr>
				<tr>
					<td class="bg-grey align_right">
						<span class="etiq_champ">{{ pmb.getMessage("animation", "animation_registration_name") }}</span>
					</td>
					<td>{{ registration.name}}</td>
				</tr>
				<tr>
					<td class="bg-grey align_right">
						<span class="etiq_champ">{{ pmb.getMessage("animation", "animation_registration_email") }}</span>
					</td>
					<td v-if="registration.email"><a :href="'mailto:'+registration.email">{{ registration.email }}</a></td>
					<td v-else><em>{{ pmb.getMessage("animation", "animation_registration_not_set") }}</em></td>
				</tr>
				<tr>
					<td class="bg-grey align_right">
						<span class="etiq_champ">{{ pmb.getMessage("animation", "animation_registration_phone") }}</span>
					</td>
					<td>{{ registration.phoneNumber }}</td>
				</tr>
			</table>

			<!-- personnes inscrites -->
			<hr />
			<div class='row registration_saved_list_registration'>
                <h2 v-if="formdata.globals.opac_rgaa_active">
                    {{ pmb.getMessage("animation", "animation_registration_persons") }} {{ registration.registrationListPerson.length }}
                </h2>
                <h3 v-else>
                    {{ pmb.getMessage("animation", "animation_registration_persons") }} {{ registration.registrationListPerson.length }}
                </h3>
			</div>
			<br>
			<table class='registration_saved_tab_list_person'>
				<thead>
					<tr>
						<th>{{ pmb.getMessage("animation", "animation_registration_barcode") }}</th>
						<th>{{ pmb.getMessage("animation", "animation_registration_name") }}</th>
						<th v-if="showPricesRegistrationList">
							{{ pmb.getMessage("animation", "animation_registration_price_type") }}
						</th>
						<th>{{ pmb.getMessage("animation", "animation_empr_registration_status") }}</th>
					</tr>
				</thead>
				<tbody>
					<template v-for="(person, indexPerson) in registration.registrationListPerson">
						<tr>
							<td v-if="person.barcode">{{ person.barcode }}</td>
							<td v-else><em>{{ pmb.getMessage("animation", "animation_registration_not_set") }}</em></td>
							<td>{{ person.name }}</td>
							<td v-if="showPricesRegistrationList">
								{{ getPrices(registration.animation, person.numPrice) }}
							</td>
							<td>{{ registration.registrationStatus.name }}</td>
						</tr>
						<tr>
							<td colspan="3">
								<div class="row">
									<customfields :customfields="person.personCustomsFields" customprefixe="price_type" :img="formdata.img" :pmb="pmb"></customfields>
								</div>
							</td>
						</tr>
					</template>
				</tbody>
			</table>

			<!-- Animation -->
			<hr />
			<div class="row registration_saved_animation">
                <h2 v-if="formdata.globals.opac_rgaa_active">
                    {{ pmb.getMessage("animation", "animation") }} : <a :href="'./index.php?lvl=animation_see&id='+registration.animation.idAnimation">{{ registration.animation.name }}</a>
                </h2>
                <h3 v-else>
                    {{ pmb.getMessage("animation", "animation") }} : <a :href="'./index.php?lvl=animation_see&id='+registration.animation.idAnimation">{{ registration.animation.name }}</a>
                </h3>
			</div>
			<div class="row registration_saved_animation_date">
				<p>
					{{ pmb.getMessage("animation", "animation_registration_date") }} :
					<strong>{{ registration.animation.event.startDate }}</strong>
					<template v-if="!registration.animation.event.duringDay">
						 {{ pmb.getMessage("animation", "animation_registration_date_to") }}
						<strong> {{ registration.animation.event.endDate }}</strong>
					</template>

				</p>
				<div class="resume" v-html="registration.animation.description"></div>
			</div>

		</div>
	</div>
</template>

<script>
import customfields from "../../../common/customFields/view/customFields.vue";

export default {
	props : [
		"pmb",
		"formdata"
	],
	components : {
		customfields,
	},
	computed: {
		showPricesRegistrationList: function () {
			for (const registration of this.formdata.registrationList) {
				for (const price of registration.animation.prices) {
					if (price.value > 0) {
						return true;
					}
				}
			}
			return false;
		}
	},
	methods : {
		getPrices: function (animation, numPrice) {
			for (let price of animation.prices) {
				if (price.idPrice == numPrice) {
					return price.name + " : " + price.value + " " + this.formdata.globals.pmbDevise;
				}
			}
		}
	}
}
</script>