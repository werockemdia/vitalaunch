<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>



<div data-pacdora-ui="3d-preview" style="width:200px; height:200px;"></div>


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
<script>

Pacdora.$on( 'design: save', image: '',
            
            before: 'design', 
           
data =>
{
    
uploadBase64(data.screenshot)
},'test'
)
</script>
<div id ="designbtn" class="btn" data-pacdora-ui="design-btn" data-save-screenshot="true" data-screenshot-width="1000" >
            Design online
</div>

<script>
 alert(data.screenshot);
function uploadBase64(base64){
    alert('hi');
    const bytes = window.atob(base64.split(",")[1]);
    const ab = new ArrayBuffer(bytes.length);
    const ia = new Uint8Array(ab);
    for (let i = 0; i < bytes.length; i++) {
    ia[i] = bytes.charCodeAt(i);
    }
    const blob = new Blob([ab], {
    type: "image/png",
    });
    const formData = new FormData();
    formData.append("file", blob);
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
    if (xhr.readyState == 4) {
    if (xhr.status == 200) {
    // UPLOAD SUCCESS
    }
    }
    };
    xhr.open("POST", "public_html/_pacui/image", true);
    xhr.send(formData);
}
</script>


<?php
// Initiate curl session in a variable (resource)
        $curl_handle = curl_init();
        $url = "https://api.pacdora.com/open/v1/user/projects?userId=1";
        $crl = curl_init();
        $data = array("appid" => "8c5f9c28d30f5dbd",  "appkey" => "3acf8da0a0a7dde8");
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_FRESH_CONNECT, $data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_HTTPHEADER, array("appid: 8c5f9c28d30f5dbd", "appkey: 3acf8da0a0a7dde8"));
        $response = curl_exec($crl);
        //print_r($response);
        //die(); 
        
        curl_close($crl);
        $response_data = json_decode($response);
        $user_data = $response_data->data;

        // Extract only first 5 user data (or 5 array elements)
        $user_data = array_slice($user_data, 0, 4);
        $api_img_url =  $user_data[0]->screenshot;
           $api_project_id =  $user_data[0]->id;
         echo '</br>';
         
         $postParameter = array(
            'projectIds' => $api_project_id,
            
        );
        //echo $postParameter['projectIds'];
         $url = "https://api.pacdora.com/open/v1/user/projects/export/pdf/";
        $json = json_encode($postParameter);
        $crl = curl_init();
        $data = array("appid" => "8c5f9c28d30f5dbd",  "appkey" => "3acf8da0a0a7dde8");
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_FRESH_CONNECT, $data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_CUSTOMREQUEST, "POST");
        $data = "projectIds[0]=".$api_project_id;
        curl_setopt($crl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($crl, CURLOPT_HTTPHEADER, array("appid: 8c5f9c28d30f5dbd", "appkey: 3acf8da0a0a7dde8"));
        $response = curl_exec($crl);
        curl_close($crl);
        $response_data = json_decode($response);
        $user_data = $response_data->data; 
        $user_data = array_slice($user_data, 0, 4);
        $taskIdty =  $user_data[0]->taskId;
        
        $taskId =374971;
        $endpoint = "https://api.pacdora.com/open/v1/user/projects/export/pdf?taskId=".$taskId;
        
        // Your App ID and App Key
        $app_id = "8c5f9c28d30f5dbd";
        $app_key = "3acf8da0a0a7dde8";
        
        // Initialize cURL session
        $curl = curl_init();
        
        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "appId: $app_id",
                "appKey: $app_key",
                "Content-Type: application/json" // Adjust this according to the API's requirements
            )
        ));
        
        // Execute the request
        $response = curl_exec($curl);
        
        // Check for errors
        if ($response === false) {
            echo "Error: " . curl_error($curl);
            // Handle error as needed
        } else {
            // Parse JSON response
            $user_data = json_decode($response, true);
            $filePath =  $user_data['data']->filePath;
            $user_data_arr = $user_data['data'];
            echo $user_pdf_path = $user_data_arr['filePath'];
            
             $statusMsg = '';

            //file upload path
            echo dirname(__FILE__);
            $targetDir = "public_html/_pacui";
            $fileName = basename($_FILES["file"]["name"]);
            $targetFilePath = $user_pdf_path;
            $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
            
            //if(isset($_POST["submit"]) && !empty($_FILES["file"]["name"])) {
                //allow certain file formats
                $allowTypes = array('jpg','png','jpeg','gif','pdf');
                if(in_array($fileType, $allowTypes)){
                    //upload file to server
                    if(move_uploaded_file($user_pdf_path, $targetDir)){
                        $statusMsg = "The file ".$fileName. " has been uploaded.";
                    }else{
                        $statusMsg = "Sorry, there was an error uploading your file.";
                    }
                }else{
                    $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
                }
           
            
            //display status message
            echo $statusMsg;
            
            
            
           
    // destination of the file on the server
    $destination = '/uploads';

    // get the file extension
    $extension = pathinfo($user_pdf_path, PATHINFO_EXTENSION);

    // the physical file on a temporary uploads directory on the server
    $file = $user_pdf_path;
    $size = $user_pdf_path;

    if (!in_array($extension, ['zip', 'pdf', 'docx'])) {
        echo "You file extension must be .zip, .pdf or .docx";
    } else {
        // move the uploaded (temporary) file to the specified destination
        echo move_uploaded_file($user_pdf_path, $destination);
        if (move_uploaded_file($user_pdf_path, $destination)) {
            /*$sql = "INSERT INTO files (name, size, downloads) VALUES ('$filename', $size, 0)";
            if (mysqli_query($conn, $sql)) {
                echo "File uploaded successfully";
            }*/
        } else {
            echo "Failed to upload file.";
        }
    }
    
      $upload_dir = "uploads/"; // Directory where you want to save uploaded files
    $file_name = basename($user_pdf_path);
    $source_file = $user_pdf_path;
    $target_path = $upload_dir . $file_name;

    // Check if file already exists
    if (file_exists($target_path)) {
        echo "Sorry, file already exists.";
    } else {
        // Read the file content and save it using file_put_contents()
        $file_content = file_get_contents($source_file);
        if (file_put_contents($target_path, $file_content) !== false) {
            echo "The file " . $file_name . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
  
 ?>
