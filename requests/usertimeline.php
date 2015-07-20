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
	const ENDPOINT = '/statuses/user_timeline.json';

	/**
	 * The Twitter oAuth token object
	 *
	 * @var \shgysk8zer0\TwitterAPI\oAuth
	 */
	private $_oAuth = null;

	/**
	 * Create a new UserTimeline instance from a Twitter oAuth token object
	 *
	 * @param shgysk8zer0\TwitterAPI\oAuth $oAuth Parsed response from 'oauth2/token'
	 */
	public function __construct(TwitterAPI\oAuth $oAuth)
	{
		$this->_oAuth = $oAuth;
	}

	/**
	 * Make the API request and parse the response
	 *
	 * @param  array  $data Optional array of data to use for the request ['screen_name' => ...]
	 * @return \stdClass    JSON decoded Twiter response
	 */
	public function __invoke(array $data = array())
	{
		array_map([$this, '__set'], array_keys($data), array_values($data));
		if (! (isset($this->screen_name) or isset($this->user_id))) {
			throw new \Exception(sprintf('%s requires screen_name or user_id to be set', __METHOD__), 400);
		}
		$url = self::URI . self::VERSION . self::ENDPOINT;
		$url .= '?' . http_build_query($this->{self::MAGIC_PROPERTY});
		$this->curlInit($url);
		$this->curlSetOpt(CURLOPT_RETURNTRANSFER, true);
		$this->curlSetOpt(CURLOPT_HTTPHEADER, [
			"Authorization: {$this->_oAuth}",
			'User-Agent' => self::UA_STRING
		]);
		$resp = json_decode($this->curlExec());
		if (empty($resp)) {
			throw new \Exception('Unable to parse Twitter API response', 500);
		} elseif (isset($resp->errors) and is_array($resp->errors) and ! empty($resp->errors)) {
			$error = current($resp->errors);
			throw new \Exception($error->message, $error->code);
		} else {
			$this->curlClose();
			return $resp;
		}
	}
}
