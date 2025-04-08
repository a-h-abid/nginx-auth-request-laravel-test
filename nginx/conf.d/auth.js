function makeSubAuthRequest(r) {
    r.subrequest(
        "/_auth_verify",
        "",
        function(reply) {
            if (reply.status != 200) {
                r.error("Unexpected response from AuthMS: HTTP " + reply.status + " | " + reply.responseText);
                r.return(401);
                return;
            }

            // We have a response from authorization server, validate it has expected JSON schema
            try {
                r.log("Token response: " + reply.responseText)
                var response = JSON.parse(reply.responseText);

                if (!response || !response.data) {
                  r.error("Token introspection response is not the expected JSON: " + reply.responseText);
                  r.return(401);
                  return;
                }

                // // Calculate cache time based on expires timestamp
                // var replyExpiresTs = reply.headersOut['x-token-expires-ts'];
                // if (replyExpiresTs) {
                //     var expiresTimestamp = parseInt(replyExpiresTs);
                //     var currentTimestamp = Math.floor(Date.now() / 1000); // Current time in seconds
                //     var secondsRemaining = expiresTimestamp - currentTimestamp;

                //     // Ensure we don't set negative cache times
                //     cacheTime = Math.min(secondsRemaining > 0 ? secondsRemaining : 0, MAX_CACHE_TIME);
                //     r.log("Using dynamic cache time based on timestamp: " + cacheTime + " seconds");
                //     r.headersOut['x-token-expires-in'] = cacheTime + 's';
                // }

                var encodedAuthData = btoa(JSON.stringify(response.data));
                r.headersOut['x-auth-data'] = encodedAuthData;
                r.status = 204;
                r.sendHeader();
                r.finish();
            } catch (e) {
                r.error("Auth.JS error: " + e.message + " | HTTP Status: " + reply.status + " | " + reply.responseText);
                r.return(500);
            }
        }
    );
}

export default { makeSubAuthRequest }