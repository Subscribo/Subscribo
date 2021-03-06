<html>
<head>
    <title>API Checker</title>
</head>
<body onload="loaded()">
<h1>API Checker</h1>

<form onsubmit="makeRequest(); return false;">
    <label for="access_token">Access token:</label>
    <input type="text" id="access_token" style="width:50em">
    <label for="access_token">Locale:</label>
    <input type="text" id="locale">
    <br>
    <label for="select_verb">Select:</label>
    <select id="select_verb" onchange="document.getElementById('verb').value=this.value">
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
    <br>
    <label for="verb">Verb:</label>
    <input type="text" value="GET" id="verb" style="width: 10em">
    <label for="url">URL:</label>
    <input type="text" id="url" value="/api/v1/" style="width: 40em">
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

    function requestLoaded() {
        document.getElementById('response').value = this.responseText;


    }
    function loaded()
    {


    }
    function makeRequest()
    {
        var myRequest = new XMLHttpRequest();
        myRequest.onload = requestLoaded;
        var verb = document.getElementById('verb').value;
        var url =  document.getElementById('url').value;
        var requestBody = document.getElementById('request_body').value;
        var accessToken = document.getElementById('access_token').value;
        var locale = document.getElementById('locale').value;
        myRequest.open(verb, url, true);
        myRequest.setRequestHeader('Content-Type', 'text/json');
        if (accessToken) {
            myRequest.setRequestHeader(<?php echo json_encode(\Subscribo\RestCommon\RestCommon::ACCESS_TOKEN_HEADER_FIELD_NAME); ?>, accessToken);
        }
        if (locale) {
            myRequest.setRequestHeader('Accept-Language', locale);
        }


        myRequest.send(requestBody);
        return false;

    }
    function display()
    {
        document.getElementById('output').contentDocument.body.innerHTML = document.getElementById('response').value
    }

</script>

</body>
</html>
