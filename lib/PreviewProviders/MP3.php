<?php

namespace OCA\AudioCoverPreview\PreviewProviders;


class MP3 extends AbstractAudioPreview 
{
    public function getMimeType(): string
    {
        return "/audio\/mpeg)/";
    }

}