"use strict";

;(function () {
    const sendButtons = document.getElementsByClassName('ss-button');
    for (let i = 0; i < sendButtons.length; i++) {
        sendButtons[i].addEventListener('click', (e) => {
            e.stopPropagation();
            const textAreaName = (e.target).dataset.name;
            const text = document.getElementById(`ss_ta_${textAreaName}`).value;

            let parsedData = null;
            try {
                parsedData = JSON.parse(text);
            } catch (e) {
                alert('The data in string is not a JSON.');
                return;
            }

            const jsonData = JSON.stringify({
                serviceName: textAreaName,
                config: parsedData
            });

            updateServiceSettings({
                method: 'PUT',
                url: 'service-settings',
                jsonData: jsonData,
            });
        });
    }

    function updateServiceSettings(requestParams) {
        const xhr = new XMLHttpRequest();
        xhr.open(requestParams.method, requestParams.url, true);
        xhr.setRequestHeader('Content-type', 'application/json');

        xhr.onreadystatechange = function () {
            if (XMLHttpRequest.DONE === xhr.readyState && 200 === xhr.status) {
                let response = {};
                try {
                    response = JSON.parse(xhr.response);
                } catch (e) {
                    handleErrorResponse(e)
                    return;
                }

                if (200 !== response.code) {
                    handleErrorResponse(response.message)
                    return;
                }

                handleSuccessResponse();
            }
        }

        xhr.send(requestParams.jsonData);
    }

    function handleSuccessResponse() {
        alert(`The request for updating is successful.`);
    }

    function handleErrorResponse(message = null) {
        if (message) {
            alert(message);
            return;
        }

        alert(`The request for updating is failed.`);
    }
})();