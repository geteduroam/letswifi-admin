let element = document.getElementById("realm_type_realm_refreshKey");

element.addEventListener("click", function() {
    let element = document.getElementById('realm_type_realm_key');
    element.value = base64urlencode(generateRandomString());
});

/**
 * Base64-urlencode a string
 * @param  {String} str The unencoded string
 * @return {String}     The encoded string
 */
function base64urlencode (str) {
    // Convert the ArrayBuffer to string using Uint8 array to convert to what btoa accepts.
    // btoa accepts chars only within ascii 0-255 and base64 encodes them.
    // Then convert the base64 encoded to base64url encoded
    // (replace + with -, replace / with _, trim trailing =)
    return btoa(String.fromCharCode.apply(null, stringToArrayBuffer(str)))
        .replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}

function stringToArrayBuffer(str) {
    const buffer = new Uint8Array(new ArrayBuffer(str.length));
    let i = 0, strLen = str.length;
    for (; i<strLen; i++) {
        buffer[i] = str.charCodeAt(i);
    }
    return buffer
}

/**
 * Generate a secure random string using the browser crypto functions
 * @return {String} A random string
 */
function generateRandomString () {
    const array = new Uint8Array(12);
    window.crypto.getRandomValues(array);
    return Array.from(array, dec => ('0' + dec.toString(16)).substr(-2)).join('');
}
