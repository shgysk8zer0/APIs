<?php
/**
 * @author Chris Zuber <shgysk8zer0@gmail.com>
 * @package shgysk8zer0\TwitterAPI
 * @subpackage Requests
 * @version 0.0.1
 * @copyright 2015, Chris Zuber
 * @license MIT
 */
namespace shgysk8zer0\TwitterAPI\Requests;

use \shgysk8zer0\TwitterAPI as TwitterAPI;
use \shgysk8zer0\Core_API as API;

/**
 * @see https://dev.twitter.com/rest/reference/get/statuses/user_timeline
 */
final class UserTimeline extends TwitterAPI\Abstracts\Request implements API\Interfaces\cURL
{
	private $_oAuth = '';

	public function __construct($key, $secret)
	{
		$this->_oAuth = new TwitterAPI\oAuth($key, $secret);
	}

	public function __invoke()
	{
		if (! (isset($this->screen_name) or isset($this->user_id))) {
			throw new \Exception(sprintf('%s requires screen_name or user_id to be set', __METHOD__), 400);
		}
		$url = self::URI . self::VERSION . "/statuses/user_timeline.json";
		$url .= '?' . http_build_query($this->{self::MAGIC_PROPERTY});
		$this->curlInit($url);
		$this->curlSetOpt(CURLOPT_RETURNTRANSFER, true);
		$this->curlSetOpt(CURLOPT_HTTPHEADER, [
			"Authorization: {$this->_oAuth}",
			'User-Agent' => self::UA_STRING
		]);
		$resp = $this->curlExec();
		$this->curlClose();
		return json_decode($resp);
	}
}
