<?php

namespace Tridi\Cekmutasi;

use Tridi\Cekmutasi\Support\Constant;

class Container
{
	protected $apiKey;
	protected $apiSignature;

    public function __construct()
    {
        $this->apiKey = config('cekmutasi.api_key', '');
        $this->apiSignature = config('cekmutasi.api_signature', '');
    }

    /**
	*	Make HTTP request
	*
	*	@param String $endpoint
	*
	*	@param Int Tridi\Cekmutasi\Support\Constant $method
	*
	*	@param Array Request Parameters $params
	*
	*	@return Object
	*
	**/

	protected function curl($endpoint, $method = Constant::HTTP_GET, $params = [])
    {
    	$url = Constant::API_BASEURL . '/'.ltrim($endpoint, '/');

    	$ch = curl_init();

    	if( $method == Constant::HTTP_GET )
    	{
    		$url .= '?'.http_build_query($params);
    	}
    	else
    	{
    		curl_setopt($ch, CURLOPT_POST, true);
    		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    	}

        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            	'Api-Key: ' . $this->apiKey,
            	'Accept: ' . Constant::FORMAT_JSON,
                'User-Agent: Cekmutasi Laravel/' . Constant::VERSION
            ]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if( $errno )
        {
            $result = json_encode([
                'success'     	=> false,
                'error_message'	=> $error
            ]);
        }

        return json_decode($result);
    }
}

?>