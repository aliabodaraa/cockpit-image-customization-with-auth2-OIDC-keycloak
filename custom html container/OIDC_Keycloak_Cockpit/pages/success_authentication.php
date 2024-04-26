<?php
   include_once(__DIR__ . "/../constant_variables_oauthe2.php");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Success authentication with Keycloak ldp</title>
    <link rel="icon" href="@base('/favicon.ico')" type="image/x-icon">
    <link rel="css" src"<?php echo CLIENT_URL.'/assets/app/css/style.css';?>">
    <style>
    #auth-image{
	display: block;
	width: 56%;
	height: 10%;
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
    <!--<img src="<?php echo CLIENT_URL.'/assets/app/media/icons/lighthouse.svg';?>" width="150" height="1050">-->
    <div id="main-auth"  class="uk-container uk-container-center uk-text-center uk-animation-slide-bottom">
	<img id="auth-image" src="<?php echo CLIENT_URL.'/OIDC_Keycloak_Cockpit/images/logo-keycloak-cockpit-oauth2.jpg';?>">
        <p id="auth-text">Now you are authenticated from Keycloak IDP
			<?php
			 if($_GET['is_new_account'])
			   echo "(matching failed ... a new account has just created with same email and name in keycloak)";
			 elseif($_GET['mapper_type'] == "u_name")
			   echo "(matching done with name)";
			 elseif($_GET['mapper_type'] == "u_email")
			   echo "(matching done with email)";
			 ?></p>
        <p><a class="continue-btn" href="http://localhost:8089/accounts/account">Continue</a></p>
	<p id="counter">5</p>
	<span>wait .. you will redirect to your cockpit account</span></br></br></br></br>
	<b id="loading"></b>
  </div>
  <script>
        //console.log("AsasasLi");
        let counter=5;
	let counter_content=document.getElementById('counter');
        let interval = setInterval(()=>{
          counter_content.innerHTML=--counter;
          if(counter < 2)
            document.getElementById("loading").innerHTML="loading ...";
          if(counter === 0){
            document.getElementsByClassName("continue-btn")[0].click();
            clearInterval(interval);
          }
        },900);
   </script>
</body>
</html>
