import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { REVERB_CONFIG } from './constants'

window.Pusher = Pusher

/** @type {?Echo} */
let instance = null

/**
 * @returns {Echo}
 */
const getConnection = () => {
    if (!instance) {
        const config = REVERB_CONFIG
        if (!config) {
            throw `Missing websocket configuration`
        }
        instance = new Echo({
            broadcaster: 'reverb',
            key: config.REVERB_APP_KEY,
            wsHost: config.REVERB_HOST,
            wsPort: config.REVERB_PORT,
            wssPort: config.REVERB_PORT,
            forceTLS: config.REVERB_SCHEME === 'https',
            enabledTransports: ['ws', 'wss'],
            authEndpoint: config.PUSHER_AUTH_ROUTE,
        })
    }
    return instance
}

export default getConnection
