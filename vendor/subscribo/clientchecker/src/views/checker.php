<html>
<head>
    <title>Client Checker</title>
</head>
<body onload="loaded()">
<h1>Client Checker</h1>

<form onsubmit="makeRequest(); return false;">
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
        <?php
        $uriBase = Subscribo\RestProxy::uriBase();
        $uriStubs = Subscribo\ModelFactory::listUriStubs();
        asort($uriStubs);
        foreach ($uriStubs as $uriStub) {
            echo '            <option value="'.$uriBase.'/model/'.$uriStub.'">'.$uriStub.'</option>'."\n";
        }
        ?>
    </select>
    <br>
    <label for="verb">Verb:</label>
    <input type="text" value="GET" id="verb" style="width: 10em">
    <label for="url">URL:</label>
    <input type="text" id="url" value="/api/v0/model/languages" style="width: 40em">
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
        myRequest.open(verb, url, true);
        myRequest.setRequestHeader('Content-Type', 'text/json');


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
