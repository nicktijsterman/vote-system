@php
    $reverbConfig = [
        'REVERB_APP_KEY' => config('broadcasting.connections.reverb.key'),
        'REVERB_HOST' => config('reverb.public.host'),
        'REVERB_PORT' => config('reverb.public.port'),
        'REVERB_SCHEME' => config('reverb.public.scheme'),
        'PUSHER_AUTH_ROUTE' => action(\Illuminate\Broadcasting\BroadcastController::class . '@authenticate'),
    ];
@endphp
<script nonce="{{ csp_nonce('script') }}">
    window.__REVERB_CONFIG__ = @json($reverbConfig)
</script>
