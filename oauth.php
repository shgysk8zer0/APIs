<?php
/**
 * @author Chris Zuber <shgysk8zer0@gmail.com>
 * @package shgysk8zer0\TwitterAPI
 * @version 0.0.1
 * @copyright 2015, Chris Zuber
 * @license MIT
 */
namespace shgysk8zer0\TwitterAPI;

use \shgysk8zer0\Core_API as API;

/**
 * Makes a request for a Twitter API oAuth token.
 * Tokens may be requested at https://apps.twitter.com/
 * @see https://dev.twitter.com/oauth/overview
 */
final class oAuth extends Abstracts\Request
{
	/**
	 * The parsed response from api.twitter.com {"token_type": "bearer", "acces_token": ...}
	 *
	 * @var \stdClass
	 */
	private $_oAuth  = null;

	/**
	 * Creates a new Twitter oAuth token request.
	 *
	 * @param string $key    Consumer Key (API Key)
	 * @param string $secret Consumer Secret (API Secret)
	 */
	public function __construct($key, $secret)
	{
		$this->grant_type = 'client_credentials';
		$token = base64_encode(urlencode($key) . ':' . urlencode($secret));
		$this->curlInit(self::URI . 'oauth2/token');
		$this->curlSetOpt(CURLOPT_SSL_VERIFYPEER, true);
		$this->curlSetOpt(CURLOPT_HTTPHEADER, [
			'Authorization:' . sprintf('Basic "%s";charset=%s', $token, self::CHARSET),
			'Content-Type:application/x-www-form-urlencoded;charset=UTF-8'
		]);
		$this->curlSetOpt(CURLOPT_POST, TRUE);
		$this->curlSetOpt(CURLOPT_POSTFIELDS, http_build_query($this->{self::MAGIC_PROPERTY}));
		$this->curlSetOpt(CURLOPT_RETURNTRANSFER, true);
		$oAuth = json_decode($this->curlExec());
		if ($this->curlErrno() !== 0) {
			throw new \Exception($this->curlError(), $this->curlErrno());
		}
		$this->curlClose();
		if (! is_object($oAuth)) {
			throw new \Exception('Unable to parse oAuth response', 500);
		} elseif (isset($oAuth->errors) and is_array($oAuth->errors) and ! empty($oAuth->errors)) {
			$error = current($oAuth->errors);
			throw new \Exception($error->message, $error->code);
		} else {
			$this->_oAuth = $oAuth;
		}
	}

	/**
	 * Returns the oAuth token as a string to be used in request headers
	 *
	 * @param void
	 * @return string Bearer AAA...
	 */
	public function __toString()
	{
		return 'Bearer ' .  $this->_oAuth->access_token;
	}
}
