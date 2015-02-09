<?php

$uriBase = Subscribo\RestProxy::getUriBase();
$remoteUriBase = Subscribo\RestProxy::getRemoteUriBase();
$uriParameters = Subscribo\RestProxy::getUriParameters();


?>
<!DOCTYPE html>
<html>
<head>
    <title>Client Checker</title>
</head>
<body onload="loaded()">
<h1>Client Checker</h1>

<form onsubmit="makeRequest(); return false;">
    <label for="select_verb">Select:</label>
    <select id="select_verb" onchange="document.getElementById('verb').value=this.value; document.getElementById('add_csrf_token').checked=( ! arrayContain(this.value, ['GET','OPTIONS','HEAD']))">
        <option value="GET">GET</option>
        <option value="POST">POST</option>
        <option value="PUT">PUT</option>
        <option value="DELETE">DELETE</option>
        <option value="PATCH">PATCH</option>
        <option value="OPTIONS">OPTIONS</option>
        <option value="HEAD">HEAD</option>
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp;
    <label for="select_url">Select:</label>
    <select id="select_url" onchange="document.getElementById('url').value=this.value">
    </select>
    <input type="button" onclick="initialize()" value="Initialize">

    <label for="add_csrf_token">Add CSRF Token</label>
    <input id="add_csrf_token" type="checkbox" checked="checked">
    <br>
    <label for="verb">Verb:</label>
    <input type="text" value="GET" id="verb" style="width: 10em">
    <label for="url">URL:</label>
    <input type="text" id="url" value="<?php echo $uriBase; ?>/" style="width: 40em">
    <input type="submit" value="RELOAD">
    <br>
    <label for="request_body">Request Body:</label>
    <br>
    <textarea id="request_body" cols="100" rows="10"></textarea>
    <br>
    <label for="response">Response:</label>
    <br>
    <textarea id="response"  cols="100" rows="10"></textarea>
    <input type="button" onclick="display()" value="Display">

</form>
<iframe id="output" style="width: 100%; height: 40%"></iframe>
<script type="text/javascript">

    function arrayContain(needle, haystack) {
        for (var i = 0; i < haystack.length; i++) {
            if (haystack[i] === needle) {
                return true;
            }
        }
        return false;
    }

    function requestLoaded() {
        document.getElementById('response').value = this.responseText;


    }

    function loaded()
    {

    }

    function initialize()
    {
        processRequest('GET', '<?php echo $uriBase; ?>', '', initializeFinish);
    }

    function initializeFinish()
    {
        var content = JSON.parse(this.responseText);
        var selectUrl = document.getElementById('select_url');
        var endpoints = content.endpoints;
        var uriBase = String('<?php echo $uriBase; ?>');
        if (uriBase) {
            uriBase = '/' + uriBase + '/';
        }
        var remoteUriBase = String('<?php echo $remoteUriBase; ?>');
        var parameters = [<?php
$first = true;
foreach ($uriParameters as $key => $value) {
    if ( ! $first) {
        echo ", ";
    }
    $first = false;
    echo '{key:"'.$key.'", value:"'.$value.'"}';
}
?>];
        for (var i = 0; i < endpoints.length; i++) {
            var endpoint = endpoints[i];
            console.log(endpoint);
            if (endpoint.sameServer && (remoteUriBase === endpoint.prefix )) {
                var option = document.createElement('option');
                option.innerHTML = endpoint.name;
                option.value = '';
                if (endpoint.partialSimpleUri) {
                    option.value =  uriBase + String(endpoint.partialSimpleUri);
                }
                if (endpoint.partialParametrizedUri) {
                    option.value =  uriBase + exchangeParameters(endpoint.partialParametrizedUri, parameters);
                }
                if ('/' === endpoint.partialSimpleUri) {
                    option.value =  uriBase;
                }

                selectUrl.appendChild(option);
            }
        }
    }

    function exchangeParameters(source, parameters)
    {
        var result = source;
        for (var i = 0; i < parameters.length; i++) {
            var parameter = parameters[i];
            var needle = '{'+parameter.key+'}';
            var replacement = parameter.value;
            result = result.replace(needle, replacement)
        }
        return result;
    }

    function makeRequest()
    {
        var verb = document.getElementById('verb').value;
        var url =  document.getElementById('url').value;
        var requestBody = document.getElementById('request_body').value;
        processRequest(verb, url, requestBody, requestLoaded);
        return false;
    }

    function processRequest(verb, url, requestBody, loaded)
    {
        var myRequest = new XMLHttpRequest();
        myRequest.onload = loaded;
        myRequest.open(verb, url, true);
        myRequest.setRequestHeader('Content-Type', 'text/json');
        if (document.getElementById('add_csrf_token').checked) {
            myRequest.setRequestHeader('X-XSRF-TOKEN', <?php echo json_encode(Crypt::encrypt(csrf_token())); ?>);
        }
        myRequest.send(requestBody);
    }

    function display()
    {
        document.getElementById('output').contentDocument.body.innerHTML = document.getElementById('response').value
    }

</script>

</body>
</html>
