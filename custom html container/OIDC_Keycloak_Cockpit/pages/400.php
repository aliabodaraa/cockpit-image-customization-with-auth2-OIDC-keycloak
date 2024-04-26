<?php
   include_once(__DIR__ . "/../constant_variables_oauthe2.php");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Success authentication with Keycloak ldp</title>
    <link rel="css" src"<?php echo CLIENT_URL.'/assets/app/css/style.css';?>">
    <style>
    #error-image{
        display: block;
        width: 30%;
        height: 30%;
        margin: auto;
    }
  #main-auth{
        text-align: center;
        margin-top: 9%;
  }
  .continue-btn{
    display: inline-flex;
    width: 100px;
    text-decoration: none;
    justify-content: center;
    padding: 5px;
    color: white;
    border-radius: 4px;
    background-color: #989898;
  }
 #auth-text{
      margin-top: 3%;
 }
  </style>
</head>
<body class="uk-height-viewport uk-flex uk-flex-middle">
    <div id="main-auth"  class="uk-container uk-container-center uk-text-center uk-animation-slide-bottom">
	<img src="<?php echo CLIENT_URL.'/assets/app/media/icons/lighthouse.svg';?>" id="error-image">
	<?php echo isset($_GET["error_description"])?'<h2>'.$_GET["error_description"].'</h2>':'';?>
        <p><a class="continue-btn" href="../oauth2_keycloak.php">re-authenticate</a></p>
    </div>
</body>
</html>
