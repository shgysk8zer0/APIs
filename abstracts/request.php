<?php
/**
 * @author Chris Zuber <shgysk8zer0@gmail.com>
 * @package shgysk8zer0\TwitterAPI
 * @subpackage Abstracts
 * @version 0.0.1
 * @copyright 2015, Chris Zuber
 * @license MIT
 */
namespace shgysk8zer0\TwitterAPI\Abstracts;
use \shgysk8zer0\Core_API as API;

abstract class Request implements API\Interfaces\cURL
{
	use API\Traits\cURL;
	use API\Traits\Magic_Methods;
	use API\Traits\Magic\Call_Setter;

	protected $_data = array();

	const URI            = 'https://api.twitter.com/';
	const VERSION        = 1.1;
	const CHARSET        = 'UTF-8';
	const UA_STRING      = 'shgysk8zer0/APIs/Twitter';
	const MAGIC_PROPERTY = '_data';
}
