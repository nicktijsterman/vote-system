<template>
    <div class="flex flex-col">
        <input
            autocomplete="off"
            class="input input--token"
            v-maska="'**** **** **** ****'"
            maxlength="19"
            name="token"
            placeholder="ABCD 2345 WXYZ 6789"
            type="text"
            required
            v-model="value"
            autofocus
        />
        <div v-for="error of errors" class="font-normal text-failure">
            {{ error }}
        </div>
    </div>
</template>

<script>
import { vMaska } from 'maska/vue'

export default {
    directives: {
        maska: vMaska,
    },

    data() {
        return {
            value: this.initialValue,
        }
    },

    props: {
        initialValue: {
            type: String,
            required: false,
            default: '',
        },
        errors: {
            type: Array,
            required: false,
            default: () => [],
        },
    },
}
</script>
