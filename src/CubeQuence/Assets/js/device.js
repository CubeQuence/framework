const callbackTimer = setInterval(() => {
    apiUse2('post', '/auth/callback/device', {})
        .then(data => {
            if (data.message !== "") {
                clearInterval(callbackTimer);
            }

            if (!data.success) {
                document.querySelector('h4#message').textContent = data.message;
                document.querySelector('img#qr').style = "filter: blur(8px)";
                document.querySelector('div#tryAgain').style = "";
            }
        })
}, 2000);
