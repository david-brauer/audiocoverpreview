<?php

namespace OCA\AudioCoverPreview\PreviewProviders;


class FLAC extends AbstractAudioPreview 
{
    public function getMimeType(): string
    {
        return "/audio\/flac)/";
    }

}