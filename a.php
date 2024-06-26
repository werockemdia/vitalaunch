<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<div class="row">
    <div class="container">
        <div class="col-sm-6">
            <div data-pacdora-ui="3d" style="width:400px; height:400px;"></div>
        </div>
        <div class="col-sm-6">
            <div data-pacdora-ui="3d-preview" style="width:400px; height:400px;"></div>
        </div>
    </div>
</div>



<!--<script src=https://cdn.pacdora.com/Pacdora-v1.0.0.js></script>-->
<script src="https://cdn.pacdora.com/Pacdora-v1.0.1.js"></script>
<script>

      (

        async () => {

          await Pacdora.init({

            userId: '1',

            appId: '8c5f9c28d30f5dbd',
            appKey: '3acf8da0a0a7dde8',

            modelId:'500390',

            theme:'#339999',

            doneBtn: 'Save and Quote',

            isDelay: false,
            
            image: '',
            
            before: 'design', 
            
            design:'save',

          }).then((res)=>{
              console.log(res.src,"demo");
          });
           

          const btn = document.getElementById('designbtn');

          btn.innerHTML = 'Design Online';

        })();
        var parsed = JSON.parse(data);

        alert(parsed);

</script>

<div class="row">
    <div class="container">
        <div class="col-sm-12">
            <center>
                <div id ="designbtn" class="btn" data-pacdora-ui="design-btn" data-save-screenshot="true" data-screenshot-width="1000" >
            Design online
</div>
            </center>
        </div>
    </div>
</div>
 
<?php
// Initialize an empty array to store request names
$requestNames = [];

// Capture the incoming request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Capture the incoming request URL
$requestUrl = $_SERVER['REQUEST_URI'];

// Capture the request name from the URL
$requestName = explode('/', $requestUrl);
$requestName = end($requestName);

// Add the request name to the array if it's not empty
if (!empty($requestName)) {
    $requestNames[] = $requestName;
}

// Log the request details (optional)
$logMessage = "Request Method: $requestMethod\n";
$logMessage .= "Request URL: $requestUrl\n";
$logMessage .= "Request Name: $requestName\n\n";
file_put_contents('request.log', $logMessage, FILE_APPEND);

// Example response
$response = "This is a response from the proxy server.";

// Set response headers
header("Content-Type: text/plain");

// Send the response back to the client
echo $response;

// Output the request names as JSON array (for testing)
echo json_encode($requestNames);
?>


<style>
    div#designbtn {
    background: #9ae0e8;
    border-radius: 0px;
}
</style>