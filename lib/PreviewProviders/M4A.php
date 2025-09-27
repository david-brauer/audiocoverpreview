<?php

namespace OCA\AudioCoverPreview\PreviewProviders;


class M4A extends AbstractAudioPreview 
{
    public function getMimeType(): string
    {
        return "/audio\/mp4)/";
    }

}