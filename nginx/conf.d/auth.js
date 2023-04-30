function makeSubAuthRequest(r) {
    r.subrequest(
        "/_auth_verify",
        "",
        function(reply) {
            if (reply.status != 200) {
                r.error("Unexpected response from authorization server (HTTP " + reply.status + "). " + reply.body);
                r.return(401);
            }

            // We have a response from authorization server, validate it has expected JSON schema
            try {
                r.log("Token response: " + reply.responseBody)
                var response = JSON.parse(reply.responseBody);

                if (!response || !response.data) {
                  r.error("Token introspection response is not the expected JSON: " + reply.responseBody);
                  r.return(401);
                  return;
                }

                var encodedAuthData = btoa(JSON.stringify(response.data));
                r.headersOut['x-auth-data'] = encodedAuthData;
                r.status = 204;
                r.sendHeader();
                r.finish();
            } catch (e) {
                r.error("Token introspection response is not JSON: " + reply.body);
                r.return(401);
            }
        }
    );
    r.return(401);
}

export default { makeSubAuthRequest }