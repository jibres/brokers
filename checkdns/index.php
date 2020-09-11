<?php

class broker
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

		if(!isset($_REQUEST['domain']))
		{
			self::boboom('Domain not set!');
		}

		$dns_record = [];

		try
		{
			$dns_record = @dns_get_record($_REQUEST['domain'], DNS_ALL);
			if($dns_record === false)
			{
				self::boboom('can not get dns record. Result is false!');
			}
		}
		catch (\Exception $e)
		{
			self::boboom("Can not get DNS record in Catch!");
			return null;
		}

		if(!is_array($dns_record))
		{
			$dns_record = [];
		}

		// $dns = array_column($dns_record, 'target');

		self::jsonBoom($dns_record);

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

\broker::run();

?>
