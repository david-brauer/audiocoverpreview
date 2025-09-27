<?php

namespace OCA\AudioCoverPreview\PreviewProviders;


class OGG extends AbstractAudioPreview 
{
    public function getMimeType(): string
    {
        return "/audio\/ogg)/";
    }

}