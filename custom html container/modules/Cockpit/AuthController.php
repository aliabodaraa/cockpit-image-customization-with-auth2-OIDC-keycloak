<?php
/**
 * This file is part of the Cockpit project.
 *
 * (c) Artur Heinze - 🅰🅶🅴🅽🆃🅴🅹🅾, http://agentejo.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cockpit;
class AuthController extends \LimeExtra\Controller {
   protected $layout = 'cockpit:views/layouts/app.php';
   protected $user;

   public function __construct($app) {
      $user = $app->module('cockpit')->getUser();
      $mapper=$_SESSION['u_name']??$_SESSION['u_email'];
      $mapper_type = ($user && $user["mapper_type"])?$user["mapper_type"]:null; //added once in Auth.php check() else section (you can use isset :(
      if($mapper_type && empty($_COOKIE) && $_SERVER['REQUEST_METHOD'] === 'POST'){ //first enter to app - here you definitely has mapper_type
	  $_GET['mapper_type'] = $mapper_type;
	  $_GET['is_new_account'] = $user['is_new_account'];
          include_once($_SERVER['DOCUMENT_ROOT'] . '/OIDC_Keycloak_Cockpit/pages/success_authentication.php');
	  //$this->app->reroute($this['cockpit.start']);return $this->app->invoke('Cockpit\\Controller\\Base', 'dashboard');
      }

      if (!$user && $_SERVER['REQUEST_URI']!=="/auth/check" && $mapper){ //when click continue button the $user will becomes null
        //if(isset($_SESSION['access_token']))
    	//    var_dump($_SESSION['access_token']);
	//query user have this mail
	$user1 = $app->storage->findOne('cockpit/accounts', ['user' => $_SESSION['u_name']]);
	$user2=$app->storage->findOne('cockpit/accounts', ['email' => $_SESSION['u_email']]);
	$user=$user1??$user2;
	$app->module('cockpit')->setUser($user);
        $app->module('cockpit')->authenticateViaOIDC($user);
      }elseif (!$user && !$mapper){//Cockpit credemtials failed (normal login)
        $app->reroute('/auth/login?to='.$app->retrieve('route'));
        $app->stop();
      }

        parent::__construct($app);

        $this->user  = $user;
        $app['user'] = $user;

        $controller = \strtolower(\str_replace('\\', '.', \get_class($this)));

        $app->trigger("app.{$controller}.init", [$this]);

    }

}
