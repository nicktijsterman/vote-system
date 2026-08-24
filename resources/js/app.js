import { createApp, defineAsyncComponent } from 'vue'
import { createI18n } from 'vue-i18n'
import { APP_LOCALE } from './shared/constants'

const locale = APP_LOCALE
const i18n = createI18n({ legacy: false, locale, messages: {} })

import(`../lang/${locale}.json`).then((messages) => {
    i18n.global.setLocaleMessage(locale, messages.default ?? messages)
})

const app = createApp({
    components: {
        LiveApplicationControls: defineAsyncComponent(() =>
            import('./admin/LiveApplicationControls.vue'),
        ),
        PropositionOptionEditor: defineAsyncComponent(() =>
            import('./admin/PropositionOptionEditor.vue'),
        ),
        VoterManagementPage: defineAsyncComponent(() =>
            import('./admin/VoterManagementPage.vue'),
        ),
        ListResultOption: defineAsyncComponent(() =>
            import('./admin/ListResultOption.vue'),
        ),

        TokenInput: defineAsyncComponent(() =>
            import('./voter/TokenInput.vue'),
        ),
        VoterVotingPage: defineAsyncComponent(() =>
            import('./voter/VoterVotingPage.vue'),
        ),

        IllVote: defineAsyncComponent(() => import('./shared/IllVote.vue')),
    },
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]')?.content,
            token: document.querySelector('meta[name="auth-token"]')?.content,
        }
    },
})

app.use(i18n)
app.mount('#app')
