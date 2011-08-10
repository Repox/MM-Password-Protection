<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	/**
	* Simple password protection addon for MojoMotor
	* 
	* Usage:
	* Create/edit a layout and put this as the first line:
	* {mojo:password:protect username="myuser" password="mypassword"}
	* This will initiate a basic HTTP authentication.
	* Failing to login, will show the general error page.
	*
	* You can add more users by seperating usernames and passwords with a comma.
	* Like this:
	* {mojo:password:protect username="myuser,user2" password="mypassword,user2pass"}
	* Just make sure you have the same amount of usernames and passwords.
	*
	* If you need to change the basic realm, just add an attribute named realm:
	* {mojo:password:protect username="myuser" password="mypassword" realm="Customer Site"}
	*
	* If you'd like to change the text in the error message, just use the heading and message attributes
	* {mojo:password:protect username="myuser" password="mypassword" realm="Customer Site" heading="Stay Away!" message="You are not allowed here!"}
	*
	* Author: Dan Storm
	* E-mail: storm@catalystcode.net
	* Website: http://catalystcode.net
	* 
	* Please support me - send me feedback wether it's good or bad.
	*/
	
	class Password
	{
		private $addon;
		
		public function __construct()
		{
			$this->addon =& get_instance();
		}
		
		public function protect( $input = array() )
		{

			if(isset($_SERVER['REDIRECT_REMOTE_USER']))
				list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['REDIRECT_REMOTE_USER'], 6)));
			
			if(!isset($input["parameters"]["username"]))
				show_error('<strong>Simple Password Protection:</strong> You didn\'t set a username');

			if(!isset($input["parameters"]["password"]))
				show_error('<strong>Simple Password Protection:</strong> You didn\'t set a password');

			
			$usernames = explode(",", $input["parameters"]["username"]);
			$passwords = explode(",", $input["parameters"]["password"]);

			if(count($usernames) != count($passwords))
				show_error('<strong>Simple Password Protection:</strong> Number of usernames and passwords doesn\'t match');
				
			
			$user_array = array();
			for($i = 0; $i < count($usernames); $i++)
				$user_array[trim($usernames[$i])] = trim($passwords[$i]);
							
			$realm = "My Realm";
			if(isset($input["parameters"]["realm"]))
				$realm = $input["parameters"]["realm"];
				
			$heading = "Unauthorized";
			if(isset($input["parameters"]["heading"]))
				$heading = $input["parameters"]["heading"];
				
			$message = "You need to login to see this";
			if(isset($input["parameters"]["message"]))
				$message = $input["parameters"]["message"];						
				
			if( isset($user_array[$_SERVER['PHP_AUTH_USER']]) && $user_array[$_SERVER['PHP_AUTH_USER']] == $_SERVER['PHP_AUTH_PW'] )
				return;
								
			ob_start();
			include(APPPATH.'errors/error_general'.EXT);
			$buffer = ob_get_contents();
			ob_end_clean();

			header("WWW-Authenticate: Basic realm=\"".$realm."\"");
			header("HTTP/1.1 401 Unauthorized");			
			exit($buffer);
		}
		
	}
