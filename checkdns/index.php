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
			if(!isset($_REQUEST['type']))
			{
				$_REQUEST['type'] = null;
			}

			$dns_record = null;

			switch ($_REQUEST['type'])
			{

				case 'DNS_A':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_A);
					break;

				case 'DNS_CNAME':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_CNAME);
					break;

				case 'DNS_HINFO':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_HINFO);
					break;

				case 'DNS_CAA':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_CAA);
					break;

				case 'DNS_MX':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_MX);
					break;

				case 'DNS_NS':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_NS);
					break;

				case 'DNS_PTR':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_PTR);
					break;

				case 'DNS_SOA':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_SOA);
					break;

				case 'DNS_TXT':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_TXT);
					break;

				case 'DNS_AAAA':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_AAAA);
					break;

				case 'DNS_SRV':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_SRV);
					break;

				case 'DNS_NAPTR':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_NAPTR);
					break;

				case 'DNS_A6':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_A6);
					break;

				case 'DNS_ALL':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_ALL);
					break;

				case 'DNS_ANY':
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_ANY);
					break;

				default:
					$dns_record = @dns_get_record($_REQUEST['domain'], DNS_NS);
					break;
			}

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
