<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.0
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2021 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\modules\addons\modules\twilio\services;

class TwilioService
{

    public $verifyApiBaseUrl = "https://verify.twilio.com/v2/";

	/**
	 * TwilioService constructor.
	 *
	 * @param $apiKey string API Key
	 * @param $apiSecret string API Secret
	 */
	function __construct($apiKey, $apiSecret) {
		$this->apiKey = $apiKey;
		$this->apiSecret = $apiSecret;
	}

	/**
	 * Prepare new text message.
	 *
	 * If $unicode is not provided we will try to detect the
	 * message type. Otherwise set to TRUE if you require
	 * unicode characters.
	 *
	 * @param $to
	 * @param $from
	 * @param $message
	 * @return mixed
	 * @throws \Exception
	 */
	public function sendSms($to, $from, $message)
	{
		// Making sure strings are UTF-8 encoded
		if (!is_numeric($from) && !mb_check_encoding($from, 'UTF-8')) {
			throw new \Exception("$from needs to be a valid UTF-8 encoded string.");
		}

		if (!mb_check_encoding($message, 'UTF-8')) {
			throw new \Exception("$message needs to be a valid UTF-8 encoded string.");
		}

		// Convert and Validate numbers
		// $from = $this->validateNumber($this->convert($from));
		// $to = $this->validateNumber($this->convert($to));

		// Send away!
		$post =
			'&To=' .  urlencode( $to ) .
			'&From=' . urlencode( $from ) .
			'&Body=' . urlencode( $message );

		return $this->sendRequest($post);
	}

	/**
	 * Convert Persian/Arabic numbers to English numbers
	 *
	 * @param $phoneNumber
	 * @return mixed
	 */
	protected function convert($phoneNumber) {
		$persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
		$arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١','٠'];

		$numbers = range(0, 9);
		$convertedPersianNumbers = str_replace($persian, $numbers, $phoneNumber);
		$englishNumbersOnly = str_replace($arabic, $numbers, $convertedPersianNumbers);

		return $englishNumbersOnly;
	}

	/**
	 * Validate an originator string
	 *
	 * If the originator ('from' field) is invalid, some networks may reject the network
	 * whilst stinging you with the financial cost! While this cannot correct them, it
	 * will try its best to correctly format them.
	 *
	 * @param $from
	 * @return string
	 */
	protected function validateNumber($from)
	{
		// Remove any invalid characters
		$ret = preg_replace('/[^a-zA-Z0-9]/', '', (string) $from);

		if(preg_match('/[a-zA-Z]/', $from)){
			// Alphanumeric format so make sure it's < 11 chars
			$ret = substr($ret, 0, 11);
		} else {
			// Numerical, remove any prepending '00'
			if(substr($ret, 0, 2) == '00'){
				$ret = substr($ret, 2);
				$ret = substr($ret, 0, 15);
			}
		}

		return (string) $ret;
	}

	/**
	 * Prepare and send a new message.
	 *
	 * @param $data
	 * @return mixed
	 */
	protected function sendRequest($data)
	{
		// Send SMS
		$url = 'https://api.twilio.com/2010-04-01/Accounts/'.$this->apiKey.'/Messages.json';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 3);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":" . $this->apiSecret);
		$response = curl_exec( $ch );
		curl_close($ch);

		return json_decode($response, true);
	}

	public function createVerificationService($serviceName)
    {
        $url = $this->verifyApiBaseUrl . "Services";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'FriendlyName' => $serviceName,
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":" . $this->apiSecret);
        $response = curl_exec( $ch );
        curl_close($ch);

        return json_decode($response, true);
    }

    public function sendVerificationToken($serviceID, $to)
    {
        $url = $this->verifyApiBaseUrl. "Services/{$serviceID}/Verifications";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'To' => $to,
            'Channel' => 'sms',
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":" . $this->apiSecret);
        $response = curl_exec( $ch );
        curl_close($ch);

        return json_decode($response, true);
    }

    public function checkVerificationToken($serviceID, $to, $code)
    {
        $url = $this->verifyApiBaseUrl. "Services/{$serviceID}/VerificationCheck";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'To' => $to,
            'Code' => $code,
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey . ":" . $this->apiSecret);
        $response = curl_exec( $ch );
        curl_close($ch);

        return json_decode($response, true);
    }
}
