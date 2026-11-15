<div id="audiocoversettings">
    <div class="toast-container hidden" data-toast-container>

    </div>
    <div class="settings-section" id="basic-compatibility">
        <h2>System Compatibility</h2>
        <p>
            This App relies on some 3rd Party Tools that need to be installed on the server. Even having all checks green does not always
            mean that there is no problem. However if you have any problems and see failed checks here you might investigate those first.
        </p>
        <h3>ffmpeg</h3>
        <p>ffmpeg must be installed and usable by the app. If you get an error here the app probably will not work.</p>
        <div data-id="ffmpeg-box" class="hidden"></div>

        <h3>Imagemagick</h3>
        <p>
            Imagemagick is used to fix the some image files that ffmpeg output. Sometimes the files are not readible by the PHP GD Library.
            This only happens sometimes. If you don't have any issues you can ignore the warning. Otherwise you might have a problem that
            could be solved by processing the file with imagemagick after ffmpeg to fix marker issues.
        </p>
        <div data-id="imagemagick-box" class="hidden"></div>
    </div>

    <div class="settings-section hidden" id="imagemagick-compaibility">

        <div data-id="re-encode-settings">
            <h3>Supported Imagemagick Formats</h3>
            <p> As Imagemagick is installed we can re-encode the image to use a different format before it gets loaded into gd.
                Your Imagemagick has to support reading of jpg and writing of your desired format for that. Also the
                App and php-gd have to support the format Below are the supported formats. If your desired format is not listed or
                is listed as not supported by your Imagemagick it might not work and the App will try to fall back to jpg.
            </p>
            <div class="notecard-warning">
                <span class="material-design-icon alert-icon">
                    <svg fill="var(--note-theme)" width="20" height="20" viewBox="0 0 24 24" class="material-design-icon__svg">
                        <path d="M13 14H11V9H13M13 18H11V16H13M1 21H23L12 2L1 21Z">
                        </path>
                    </svg>
                </span>
                <div>
                    This will only affect newly generate previews. Your existing previews will not be changed.<br />
                    You need to remove the existing previews to apply the changes everywhere.
                </div>
            </div>
            <label>Format:</label>
            <select id="settingFormats">
            </select>
            <p>
                The command for changing that is below (replace "format" with one of the supported formats below:)<br />
                <code>occ config:app:set --value="format" audiocoverpreview image_format</code>
            </p>
        </div>
        <h4>JPG (Default format)</h4>
        <p>
            <strong>The default format that needs to be supported</strong>
        </p>
        <div id="imagemagick-format-jpg"></div>

        <h4>PNG</h4>
        <p> If supported you can re-encode your previews to the png format</p>
        <div id="imagemagick-format-png"></div>

        <h4>WEBP</h4>
        <p> If supported you can re-encode your previews to the webp format. This has the benefit of smaller image sizes.</p>
        <div id="imagemagick-format-webp"></div>
    </div>

    <div class="settings-section" id="advanced-section">
        <h3>Advanced Usage</h2>
            <p>If you have set up the dependencies correctly and everything is working.
                You may consider setting this setting to true. This settings skips most environment checks for ffmpeg and imagemagick.
                This will disable the checks as long as the setting is set. The main reason for this is to get a bit more performance by
                not checking everything with every image that is fetched for isAvailable() and also every generation.
            </p>
            <div class="notecard-warning">
                <span class="material-design-icon alert-icon">
                    <svg fill="var(--note-theme)" width="20" height="20" viewBox="0 0 24 24" class="material-design-icon__svg">
                        <path d="M13 14H11V9H13M13 18H11V16H13M1 21H23L12 2L1 21Z">
                        </path>
                    </svg>
                </span>
                <div>This may cause problems if your system is not configured correctly.</div>
            </div>
            <span class="checkbox-container">
                <input class="checkbox-input" type="checkbox" id="skipChecks">
                <span class="checkbox-sub-container">
                    <span class="checkbox-svg-container hidden" data-checkbox-icon="checked">
                        <svg fill="#00679E" class="material-design-icon__svg" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M10,17L5,12L6.41,10.58L10,14.17L17.59,6.58L19,8M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3Z"></path>
                        </svg>
                    </span>
                    <span class="checkbox-svg-container" data-checkbox-icon="unchecked">
                        <svg fill="#00679E" class="material-design-icon__svg" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,5V19H5V5H19Z"></path>
                        </svg>
                    </span>
                    <span>Skip checks</span>
                </span>
            </span>
            </p>The command for changing that is below (replace "skipChecks" with true to enable or false to disable)
            <code>occ config:app:set --value=skipChecks audiocoverpreview skip_checks</code>
            </p>
    </div>
</div>