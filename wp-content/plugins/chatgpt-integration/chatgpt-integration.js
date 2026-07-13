(function () {
    if ( typeof wp === 'undefined' || ! wp.apiFetch ) {
        return;
    }

    const messageContainer = document.getElementById( 'chatgpt-messages' );
    const messageInput = document.getElementById( 'chatgpt-input' );
    const submitButton = document.getElementById( 'chatgpt-submit' );

    if ( ! messageContainer || ! messageInput || ! submitButton ) {
        return;
    }

    function appendMessage( content, role ) {
        const messageEl = document.createElement( 'div' );
        messageEl.className = 'chatgpt-integration-message chatgpt-integration-message-' + role;
        messageEl.textContent = content;
        messageContainer.appendChild( messageEl );
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }

    function setLoading( isLoading ) {
        submitButton.disabled = isLoading;
        submitButton.textContent = isLoading ? 'Sending...' : 'Send';
    }

    submitButton.addEventListener( 'click', function () {
        const message = messageInput.value.trim();

        if ( ! message ) {
            return;
        }

        appendMessage( message, 'user' );
        messageInput.value = '';
        setLoading( true );

        wp.apiFetch({
            path: ChatGPTIntegration.restUrl,
            method: 'POST',
            headers: {
                'X-WP-Nonce': ChatGPTIntegration.nonce,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message }),
        }).then( function (response) {
            if ( response && response.reply ) {
                appendMessage( response.reply, 'assistant' );
            } else if ( response && response.error ) {
                appendMessage( 'Error: ' + response.error, 'assistant' );
            } else {
                appendMessage( 'Unexpected response from ChatGPT.', 'assistant' );
            }
        }).catch( function (error) {
            appendMessage( 'Request failed. ' + ( error.message || error ), 'assistant' );
        }).finally( function () {
            setLoading( false );
        } );
    } );
})();
