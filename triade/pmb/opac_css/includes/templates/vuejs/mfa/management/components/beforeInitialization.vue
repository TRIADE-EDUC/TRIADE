<template>
    <div class="mfa-before-init">
        <div class="row">
            <span>
                {{ pmb.getMessage('mfa', 'mfa_secret_code') }}
                <b>{{ pmb.getMessage('mfa', 'mfa_empty_code') }}</b>
            </span>
        </div>
        <div class="row">
            <input type="button"
                   class="bouton"
                   :value="pmb.getMessage('mfa', 'mfa_init_button')"
                   @click="startInit" />
        </div>
	</div>
</template>

<script>
    export default {
		props: [],
		methods: {
            startInit: function() {
                this.$parent.init = true;
            }
		}
	}
</script>