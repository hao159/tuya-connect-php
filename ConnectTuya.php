<?php

/**
 * Php connect tuya sample
 * Contact me haonh1502@gmail.com
 * @version 1.0
 */

class ConnectTuya
{
	private $_baseUrl;
	private $_deviceId;
	private $_clientId;
	private $_secret_key;
	private $_getToken;
	private $_fDevices;
	private $_t;
	
	public function __construct()
	{
		// code...
		$this->_baseUrl = ''; # url of your data center Ex: https://openapi.tuyaus.com/
		$this->_clientId  = ''; 
		$this->_secret_key = '';
		$this->_getToken = "v1.0/token?grant_type=1";
		$this->_t = round(microtime(true) * 1000);
		$this->_fDevices = 'v1.0/iot-03/devices/';
	}

	/**
	 * Get access token
	 * 
	 * @author hao.nguyen <haonh1502@gmail.com>
	 * @return Array response access_token of request
	 */ 

	public function get_token()
	{
		$headers = array(
		    'sign_method: HMAC-SHA256',
		    'client_id: '.$this->_clientId,
		    't: '.$this->_t,
		    'Content-Type: application/json',
		    'access_token:'
		);
		$url =  $this->_baseUrl . $this->_getToken;
		$sign = $this->build_sign('GET', $url, [], []);

		$headers[] = "sign: " . $sign;
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => $headers
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return json_decode($response, true);
	}

	/**
	 * Get device specification
	 *
	 * @param String   $device_id  device id
	 * @param String $access_token Access_token from get_token function
	 * 
	 * @author hao.nguyen <haonh1502@gmail.com>
	 * @return Array response specification of request
	 */ 

	public function get_specification($device_id,  $access_token)
	{
		$headers = array(
		    'sign_method: HMAC-SHA256',
		    'client_id: '.$this->_clientId,
		    't: '.$this->_t,
		    'Content-Type: application/json',
		    'access_token: ' . $access_token
		);
		$method = 'GET';
		$payload = array(
			'device_id' => $device_id
		);
		$url =  $this->_baseUrl . $this->_fDevices . $device_id . '/specification';
		$sign = $this->build_sign($method, $url, [], [], $access_token);

		$headers[] = "sign: " . $sign;
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => $headers
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return json_decode($response, true);
	}

	/**
	 * Send command by device
	 *
	 * @param String   $device_id  device id
	 * @param Array   $commands  array of commands 
	 * @param String $access_token Access_token from get_token function
	 * 
	 * @author hao.nguyen <haonh1502@gmail.com>
	 * @return Array response specification of request
	 */

	public function send_commands($device_id, $commands ,$access_token)
	{
		$headers = array(
		    'sign_method: HMAC-SHA256',
		    'client_id: '.$this->_clientId,
		    't: '.$this->_t,
		    'Content-Type: application/json',
		    'access_token: ' . $access_token
		);
		$method = 'POST';
		if (empty($commands)) {
			// code...
			return false;
		}
		$payload = array(
			"commands" => $commands
		);
		$url =  $this->_baseUrl . $this->_fDevices . $device_id . '/commands';
		$sign = $this->build_sign($method, $url, $payload, [], $access_token);

		$headers[] = "sign: " . $sign;
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS =>json_encode($payload),
			CURLOPT_HTTPHEADER => $headers
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return json_decode($response, true);
	}

	
	/**
	 * Genarator sign for request
	 *
	 * @param String   $method  POST or GET
	 * @param String $url url of request
	 * @param Array $payload body of request
	 * @param Array $headers headers of request, !!! if error put it empty array
	 * @param String $token access_token
	 * 
	 * @author hao.nguyen <haonh1502@gmail.com>
	 * @return hash sign
	 */ 

	private function build_sign($method, $url ,$payload, $headers, $token = "")
	{
		$str_header = "";
		if ($headers) {
			// code...
			$str_header = implode("\n", $headers);
		}
		$str_payload = "";
		if ($payload) {
			// code...
			$str_payload = json_encode($payload);
		}

		$content_SHA256 = hash('sha256', $str_payload);
		
		$parse_url = parse_url($url);
		$part_url = empty($parse_url['path']) ? "" : $parse_url['path'];
		$query_url = empty($parse_url['query']) ? "" : "?" . $parse_url['query'];
		$str_to_sign = $method . "\n" . $content_SHA256 . "\n" . $str_header . "\n" . $part_url . $query_url;

		$sign = strtoupper(hash_hmac('SHA256', $this->_clientId . $token . $this->_t . $str_to_sign, $this->_secret_key));
		return $sign;
	}

}