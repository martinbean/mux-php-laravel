<?php

namespace MartinBean\Laravel\Mux\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Player extends Component
{
    /**
     * The Mux playback ID value.
     */
    public string $playbackId;

    /**
     * Indicate if the Mux Player script should be not loaded via CDN.
     */
    public bool $noScript;

    /**
     * Create a new component instance.
     */
    public function __construct(string $playbackId, bool $noScript = false)
    {
        $this->playbackId = $playbackId;
        $this->noScript = $noScript;
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('mux::player');
    }
}
