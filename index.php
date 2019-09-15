<html>
    <head>
        <title>AndroidHive | Firebase Cloud Messaging</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="//www.gstatic.com/mobilesdk/160503_mobilesdk/logo/favicon.ico">
        <link rel="stylesheet" href="style.css">
 
        <style type="text/css">
            body{
            }
            div.container{
                width: 1000px;
                margin: 0 auto;
                position: relative;
            }
            legend{
                font-size: 30px;
                color: #555;
            }
            .btn_send{
                background: #00bcd4;
            }
            label{
                margin:10px 0px !important;
            }
            textarea{
                resize: none !important;
            }
            .fl_window{
                width: 400px;
                position: absolute;
                right: 0;
                top:100px;
            }
            pre, code {
                padding:10px 0px;
                box-sizing:border-box;
                -moz-box-sizing:border-box;
                webkit-box-sizing:border-box;
                display:block; 
                white-space: pre-wrap;  
                white-space: -moz-pre-wrap; 
                white-space: -pre-wrap; 
                white-space: -o-pre-wrap; 
                word-wrap: break-word; 
                width:100%; overflow-x:auto;
            }
 
        </style>

        <script type='text/javascript'>
        function preview_image(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('output_image');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
        </script>
    </head>
    <body>
        <?php
            // Enabling error reporting
            error_reporting(-1);
            ini_set('display_errors', 'On');
    
            require_once __DIR__ . '/firebase.php';
            require_once __DIR__ . '/push.php';
    
            $firebase = new Firebase();
            $push = new Push();
    
            // optional payload
            $payload = array();
            $payload['team'] = 'Indonesia';
            $payload['score'] = '1.0';
    
            if (isset($_POST['submit'])) {

                if (isset($_POST['title']) == 1 && isset($_POST['message']) == 1 && isset($_POST['regId']) == 1) {
                    // notification title
                    $title = isset($_POST['title']) ? $_POST['title'] : '';
                    
                    // notification message
                    $message = isset($_POST['message']) ? $_POST['message'] : '';
                    
                    // push type - single user / topic
                    $push_type = isset($_POST['push_type']) ? $_POST['push_type'] : '';
                    
                    // whether to include to image or not
                    $include_image = isset($_POST['include_image']) ? TRUE : FALSE;
            
                    $image = '';    
                    if ($include_image) {
                        
                        $filename = $_FILES["gambar"]["name"];
                        if ($filename != "") {
                            // upload images first 
                            $target_dir = "images/";
                            $target_file = $target_dir.date("YmdHis").basename($_FILES["gambar"]["name"]);
                            $uploadOK = 1;
                            $imagefiletype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                            // Check file size
                            if ($_FILES["gambar"]["size"] > 1024000) {
                                echo "images too large";
                                $uploadOK = 0;
                            }
    
                            // echo "ukuran gambar cocok";
                            // Allow certain file formats
                            if($imagefiletype != "jpg" && $imagefiletype != "png" && $imagefiletype != "jpeg" && $imagefiletype != "gif" ) {
                                echo "images type not support";
                                $uploadOK = 0;
                            }
                            // echo "format gambar sesuai";
                            // Check if $uploadOk is set to 0 by an error
                            if ($uploadOK == 0) {
                                echo "Sorry, your file was not uploaded.";
                            // if everything is ok, try to upload file
                            } else {
                                if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                                    $image = $_FILES["gambar"]["name"];
                                } else {
                                    echo "Sorry, there was an error uploading your file.";
                                }
                            }
                        } else {
                            $image = "https://api.androidhive.info/images/minion.jpg";
                        }
                    }
                    
                    $image = "http://rifky.meirusfandi.com/".$image;
    
                    $push->setTitle($title);
                    $push->setMessage($message);
                    $push->setImage($image);
                    $push->setIsBackground(FALSE);
                    $push->setPayload($payload);
            
                    $json = '';
                    $response = '';
                    $json = $push->getPush();
                    $regId = isset($_POST['regId']) ? $_POST['regId'] : '';
                    $response = $firebase->send($regId, $json);
                } else {
                    $json = '';
                    $response = '';
                }
            } else {
                $json = '';
                $response = '';
            }
        ?>

        <div class="container">
            <div class="fl_window">
                <div><img src="https://api.androidhive.info/images/firebase_logo.png" width="200" alt="Firebase"/></div>
                <br/>
                <?php if ($json != '') { ?>
                    <label><b>Request:</b></label>
                    <div class="json_preview">
                        <pre><?php echo json_encode($json) ?></pre>
                    </div>
                <?php } ?>
                <br/>
                <?php if ($response != '') { ?>
                    <label><b>Response:</b></label>
                    <div class="json_preview">
                        <pre><?php echo json_encode($response) ?></pre>
                    </div>
                <?php } ?>
 
            </div>

            <form class="pure-form pure-form-stacked" method="post" enctype="multipart/form-data">
                <fieldset>
                    <label for="redId">Firebase Reg Id</label>
                    <input type="text" id="redId" name="regId" class="pure-input-1-2" placeholder="Enter firebase registration id">
 
                    <label for="image">Upload Image</label>
                    <input type="file" name="gambar" id="gambar">

                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" class="pure-input-1-2" placeholder="Enter title">
 
                    <label for="message">Message</label>
                    <textarea class="pure-input-1-2" rows="5" name="message" id="message" placeholder="Notification message!"></textarea>
 
                    <label for="include_image" class="pure-checkbox">
                        <input name="include_image" id="include_image" type="checkbox"> Include image
                    </label>
                    <input type="hidden" name="push_type" value="individual"/>
                    <button type="submit" name="submit" class="pure-button pure-button-primary btn_send">Send</button>
                </fieldset>
            </form>
        </div>
    </body>
</html>