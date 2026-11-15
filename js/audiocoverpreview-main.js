// Init and Setup
(function () {

    let settingsUrl = OC.generateUrl('apps/audiocoverpreview/getSettings');
    let changeFormatUrl = OC.generateUrl('apps/audiocoverpreview/changeFormat');
    let changeSkipCheckUrl = OC.generateUrl('apps/audiocoverpreview/changeSkipCheck');
    let getCapabilitiesUrl = OC.generateUrl('apps/audiocoverpreview/getCapabilities');
    let systemCapabilities;
    let imageFormatSelect;
    let skipSettingsCehck;
    document.addEventListener('DOMContentLoaded', function () {

        imageFormatSelect = document.getElementById('settingFormats');
        skipSettingsCehck = document.getElementById('skipChecks');

        ajaxRequest(getCapabilitiesUrl, 'GET', initSystemCapabilities);


        imageFormatSelect.addEventListener('change', function () {
            imageFormatSelect.disabled = true;
            let payload = { 'value': imageFormatSelect.value };
            ajaxRequest(changeFormatUrl, 'POST', changeFormat, true, payload);
        });

        skipSettingsCehck.addEventListener('change', function () {
            skipSettingsCehck.disabled = true;
            changeCheckboxSvg(skipSettingsCehck,skipSettingsCehck.checked)
            let payload = { 'value': skipSettingsCehck.checked };
            ajaxRequest(changeSkipCheckUrl, 'POST', changeSkipChecks, true, payload);

        });
    });


    //Callbacks
    //System capabilites

    function initSystemCapabilities() {
        if (this.readyState !== 4) {
            return;
        }

        let responseText = getResponseJson(this);
        let error = false;
        systemCapabilities = responseText;

        if (responseText === null) {
            error = true;
        }
        showSystemStatus(error);
        if (systemCapabilities['imagemagick']['isAvailable'] === 'false') {
            return;
        }

        let imagemagickCapbilitiySection = document.getElementById('imagemagick-compaibility');
        if (imagemagickCapbilitiySection === null) {
            return;
        }
        ajaxRequest(settingsUrl, 'GET', initSettings);
        imagemagickCapbilitiySection.classList.remove('hidden');

        document.getElementById('imagemagick-format-jpg').appendChild(showImagemagickFormatStatus('JPG', true, true));
        document.getElementById('imagemagick-format-png').appendChild(showImagemagickFormatStatus('PNG', false, true));
        document.getElementById('imagemagick-format-webp').appendChild(showImagemagickFormatStatus('WEBP', false, true));
    }

    // Settings
    function initSettings() {
        if (this.readyState !== 4) {
            return;
        }

        let responseText = getResponseJson(this);
        if (responseText === null) {
            //TODO: handle error
        }
        for (let i = 0; i < responseText.imageFormat.allowedValues.length; i++) {
            let value = responseText.imageFormat.allowedValues[i];
            if (
                !(value in systemCapabilities['imagemagick']['supportedFormats']) ||
                !systemCapabilities['imagemagick']['supportedFormats'][value]['fullSupport']
            ) {
                imageFormatSelect.add(new Option(value + ' (The format is not fully supported. It might use jpg as fallback.)'));
                continue;
            }
            imageFormatSelect.add(new Option(value));
        }
        imageFormatSelect.value = responseText.imageFormat.selectedValue;
        imageFormatSelect.dataset.value = responseText.imageFormat.selectedValue;

        if (responseText.skipChecks.selectedValue) {
            skipSettingsCehck.checked = true;
            changeCheckboxSvg(skipSettingsCehck,true)

        }
    };

    function changeFormat() {
        if (this.readyState !== 4) {
            return;
        }

        let responseText = getResponseJson(this);
        if (responseText === null) {
            createToastMessage("Something went wrong updating the value", 10000);
            imageFormatSelect.value = imageFormatSelect.dataset.value;
            imageFormatSelect.disabled = false;
            return;
        }
        imageFormatSelect.dataset.value = imageFormatSelect.value;
        imageFormatSelect.disabled = false;
    }


    function changeSkipChecks() {
        if (this.readyState !== 4) {
            return;
        }

        let responseText = getResponseJson(this);
        if (responseText === null) {
            createToastMessage("Something went wrong updating the value", 10000);
            skipSettingsCehck.checked = !skipSettingsCehck.checked;
            changeCheckboxSvg(skipSettingsCehck,!skipSettingsCehck.checked)
            skipSettingsCehck.disabled = false;
            return;
        }
        skipSettingsCehck.disabled = false;
    }



    // Helper functions
    // AJAX
    function ajaxRequest(url, method, callback, useCsrfToken = false, jsonData = null) {
        let request = new XMLHttpRequest();
        request.onreadystatechange = callback;
        request.open(method, url);

        if (useCsrfToken === true) {
            let csrfToken = OC.requestToken;
            request.setRequestHeader('requesttoken', csrfToken);
        }

        if (jsonData !== null) {
            request.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
            request.send(JSON.stringify(jsonData));
            return request;
        }

        request.send();
        return request;
    }

    function getResponseJson(that) {
        if (that.status === 200) {
            let response = JSON.parse(that.responseText);
            return response;
        }
        return null;
    }

    // System capability logic
    function showSystemStatus(error) {
        showFFmpegStatus(systemCapabilities['ffmpeg'], error);
        showImagemagickStatus(systemCapabilities['imagemagick'], error);
    }

    function showFFmpegStatus(ffmpegStatus, error) {
        let ffmpegBox = document.querySelector('div[data-id="ffmpeg-box"]');
        if (ffmpegBox === null) {
            return;
        }
        ffmpegBox.classList.remove('hidden');
        if (error === true) {
            ffmpegBox.appendChild(createNotecard('info', '', 'Error fetching Information. The current status is unknown'));
            return;
        }
        if (ffmpegStatus['isAvailable'] === true) {
            ffmpegBox.appendChild(createNotecard('success', 'ffmpeg is installed and detected', ffmpegStatus['version']));
            return;
        }

        ffmpegBox.appendChild(createNotecard('error', 'fmpeg is not installed or not found.', 'This will likely cause errors'));
    }

    function showImagemagickStatus(imagemagickStatus, error) {

        let imagemagickBox = document.querySelector('div[data-id="imagemagick-box"]');
        if (imagemagickBox === null) {
            return;
        }
        imagemagickBox.classList.remove('hidden');
        if (error === true) {
            imagemagickBox.appendChild(createNotecard('info', '', 'Error fetching Information. The current status is unknown'));
            return;
        }

        if (imagemagickStatus['isAvailable'] === true) {
            imagemagickBox.appendChild(createNotecard('success', 'Imagemagick ' + imagemagickStatus['shortVersion'] + ' is installed and detected', imagemagickStatus['version']));
            return;
        }

        imagemagickBox.appendChild(createNotecard('warning', 'Imagemagick is not installed or detected', 'This might cause errors on some album covers'));
    }

    function showImagemagickFormatStatus(format, needsRead, needsWrite) {
        let supportedFormats = systemCapabilities['imagemagick']['supportedFormats'];
        // No support in installed IM
        if (!(format.toLowerCase() in supportedFormats)) {
            return createNotecard('error', format + " is not supported. This likely will not work.");
        }
        let supportedFormat = supportedFormats[format.toLowerCase()];
        // Full support
        if (supportedFormat["fullSupport"] === true) {
            return createNotecard('success', format + ' is fully supported', supportedFormat['description']);
        }
        // No read and no write support
        if (!supportedFormat['read'] && !supportedFormat['write']) {
            return createNotecard('error', format + ' has no read and no write support. This likely will not work');
        }
        // Check if the missing one is read and we need read
        if (!supportedFormat['read'] && needsRead) {
            return createNotecard('warning', format + ' has no read support. This likely will not work', supportedFormat['description']);
        }
        // Check if the missing one is write and we need write
        if (!supportedFormat['write'] && needsWrite) {
            return createNotecard('warning', format + ' has no write support. This likely will not work', supportedFormat['description']);
        }

        // The one we need is supported. Still show this with warning status
        return createNotecard('warning', format + ' has no read or write support. It might work but is not optimal', supportedFormat['description']);
    }

    // Toast
    function createToastMessage(message, timeout) {
        let toastContainer = document.querySelector("div[data-toast-container]");
        let toast = document.createElement('div');
        toast.classList.add('toast');

        let toastMessage = document.createElement('span');
        toastMessage.innerText = message;
        toast.appendChild(toastMessage);

        let closeBtn = document.createElement('button');
        closeBtn.classList.add('close-button');
        closeBtn.innerHTML = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"></path></svg>';
        closeBtn.addEventListener('click', clearNotification.bind(null, toast));
        setTimeout(function () {
            clearNotification(toast);
        }, timeout);
        toast.appendChild(closeBtn);

        toastContainer.classList.remove('hidden');
        toastContainer.appendChild(toast);
    }

    function clearNotification(toast) {
        toast.remove();
    }


    // Notecard
    function createNotecard(type, heading = '', text = '', withIcon = true) {
        let notecard = document.createElement('div');
        notecard.classList.add('notecard-' + type);
        if (withIcon === true) {
            switch (type) {
                case 'warning':
                    notecard.innerHTML =
                        `<span class="material-design-icon alert-icon">
                <svg fill="var(--note-theme)" width="20" height="20" viewBox="0 0 24 24" class="material-design-icon__svg">
                    <path d="M13 14H11V9H13M13 18H11V16H13M1 21H23L12 2L1 21Z">
                    </path>
                </svg>
            </span>`;
                    break;
                case 'error':
                    notecard.innerHTML =
                        `<span class="material-design-icon">
                <svg fill="var(--note-theme)" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path  d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M13,17H11V15H13V17M13,13H11V7H13V13Z">
                    </path>
                </svg>
            </span>`;
                    break;
                case 'success':
                    notecard.innerHTML =
                        `<span class="material-design-icon">
                <svg fill="var(--note-theme)" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10,17L5,12L6.41,10.58L10,14.17L17.59,6.58L19,8M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z">
                    </path>
                </svg>
            </span>`;
                    break;
                case 'info':
                default:
                    notecard.innerHTML =
                        `<span class="material-design-icon">
                <svg fill="var(--note-theme)" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13,9H11V7H13M13,17H11V11H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z">
                    </path>
                </svg>
            </span>`;
            }
        }

        let content = document.createElement('div');
        if (heading !== '') {
            let headingElement = document.createElement('p')
            headingElement.classList.add('notecard-header');
            headingElement.innerText = heading;
            content.appendChild(headingElement);
        }

        if (text !== '') {
            let textElement = document.createElement('p');
            textElement.classList.add('notecard-text');
            textElement.innerText = text;
            content.appendChild(textElement);
        }


        notecard.appendChild(content);
        return notecard;
    }

})();

function changeCheckboxSvg(element, checked) {
    let checkedSvg = element.parentElement.querySelector('span[data-checkbox-icon="checked"]');
    let uncheckedSvg = element.parentElement.querySelector('span[data-checkbox-icon="unchecked"]');

    if (checked) {
        checkedSvg.classList.remove('hidden');
        uncheckedSvg.classList.add('hidden');
        return;
    }

    checkedSvg.classList.add('hidden');
    uncheckedSvg.classList.remove('hidden');

}