@unless($noScript)
    @once
        <script src="https://cdn.jsdelivr.net/npm/@mux/mux-player" defer></script>
    @endonce
@endunless
<mux-player {{ $attributes->merge(['playback-id' => $playbackId]) }}></mux-player>
