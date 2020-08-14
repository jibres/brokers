<?php
require 'vendor/autoload.php';

ini_set('display_startup_errors', 'On');
ini_set('error_reporting'       , 'E_ALL | E_STRICT');
ini_set('track_errors'          , 'On');
ini_set('display_errors'        , 1);
error_reporting(E_ALL);

class sendgridbroker
{
	public static function run()
	{
		$token = __DIR__.'/token.conf';

		if(!is_file($token))
		{
			self::boboom('Token file not found');
		}


		if(isset($_REQUEST['broker_token']) && $_REQUEST['broker_token'] == trim(file_get_contents($token)))
		{
			// it's ok
		}
		else
		{
			self::boboom('Hi!');
		}

		if(isset($_REQUEST['apikey']) && $_REQUEST['apikey'])
		{
			$apikey = $_REQUEST['apikey'];
			// it's ok
		}
		else
		{
			self::boboom('Hi!!!!');
		}


		self::send(self::my_data(), $apikey);
	}


	public static function send($_data, $_apikey)
	{

		$subject = 'Jibres';
		if(isset($_data['subject']) && $_data['subject'])
		{
			$subject = $_data['subject'];
		}

		$to = null;
		if(isset($_data['to']) && $_data['to'])
		{
			$to = $_data['to'];
		}
		if(!$to)
		{
			self::boboom('Please set email to parameter');
		}

		$to_name = null;
		if(isset($_data['to_name']) && $_data['to_name'])
		{
			$to_name = $_data['to_name'];
		}


		$template = 'text';
		if(isset($_data['template']) && $_data['template'])
		{
			$template = $_data['template'];
		}

		$body = null;
		if(isset($_data['body']) && $_data['body'])
		{
			$body = $_data['body'];
		}

		if(!$body)
		{
			self::boboom('Please set email body');
		}


		$email = new \SendGrid\Mail\Mail();

		$email->setFrom("info@jibres.com", "Jibres");

		$email->setSubject($subject);
		$email->addTo($to, $to_name);

		if($template === 'text')
		{
			$email->addContent("text/plain", $body);
		}
		else
		{
			$email->addContent("text/html", $body);
		}


		$sendgrid = new \SendGrid($_apikey);
		try
		{
		    $response = $sendgrid->send($email);
			$result =
			[
				$response->statusCode(),
				$response->headers(),
				$response->body(),
			];

			// $result = ['ok' => true];

		}
		catch (\Exception $e)
		{
			self::boboom('Caught exception: '. $e->getMessage());
		}

		// show result with jsonBoom
		self::jsonBoom($result);
	}



	public static function my_data()
	{
		// get all
		$allData = $_REQUEST;
		unset($allData['broker_token']);
		unset($allData['apikey']);

		// send all
		return $allData;
	}


	public static function boboom($_string = null, $_error = false)
	{
		if($_error)
		{
			@header("HTTP/1.1 504 Gateway Timeout", true, 504);
		}
		else
		{
			@header("HTTP/1.1 418 I\'m a teapot", true, 418);
		}
		// change header
		exit($_string);
	}

	public static function jsonBoom($_result = null)
	{
		if(is_array($_result))
		{
			$_result = json_encode($_result, JSON_UNESCAPED_UNICODE);
		}

		if(substr($_result, 0, 1) === "{")
		{
			@header("Content-Type: application/json; charset=utf-8");
		}
		echo $_result;
		exit();
	}
}

\sendgridbroker::run();

?>
