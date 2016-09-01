<?php
/**
 * Elgg AgoraMap Maps Api plugin
 * @package amap_maps_api 
 */

class Geocoder
{
	//public static $url = 'http://maps.google.com/maps/geo';
	public static $url = 'http://maps.googleapis.com/maps/api/geocode/xml';

	const G_GEO_SUCCESS             = 'OK';
	const G_GEO_ZERO_RESULTS        = 'ZERO_RESULTS';
	const G_GEO_OVER_QUERY_LIMIT    = 'OVER_QUERY_LIMIT';
	const G_GEO_REQUEST_DENIED      = 'REQUEST_DENIED';
	const G_GEO_INVALID_REQUEST     = 'INVALID_REQUEST';
	const G_GEO_UNKNOWN_ADDRESS     = 'ZERO_RESULTS';
	const G_GEO_UNAVAILABLE_ADDRESS = 'ZERO_RESULTS';
	
	protected $_apiKey;

	public function __construct($key)   //obs
	{
		$this->_apiKey = $key;
	}

	public function performRequest($search, $output = 'xml')
	{
		/*
		$url = sprintf('%s?q=%s&output=%s&key=%s&oe=utf-8',
					   self::$url,
					   urlencode($search),
					   $output,
					   $this->_apiKey);
		 */
		$url = sprintf('%s?address=%s&sensor=false',
					   self::$url,
					   urlencode($search));            

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	public function lookup($search)
	{
		$response = $this->performRequest($search, 'xml');
		$xml = new SimpleXMLElement($response);
		//$status   = (int) $xml->Response->Status->code;
		$status   = $xml->status;
//print_r($status);

		switch ($status) {
			case self::G_GEO_SUCCESS:
				//require_once('Placemark.php');

				//$placemarks = array();
				//foreach ($xml->Response->Placemark as $placemark)
				//    $placemarks[] = Placemark::FromSimpleXml($placemark);

				foreach ($xml->result as $placemark)   {
					$placemarks[] = Placemark::FromSimpleXml($placemark);
				}

				return $placemarks;

			case self::G_GEO_ZERO_RESULTS:
				throw new Exception(sprintf('Unknown location on map', ''));
			case self::G_GEO_OVER_QUERY_LIMIT:
				throw new Exception(sprintf($status, ''));
			case self::G_GEO_REQUEST_DENIED:
				throw new Exception(sprintf($status, ''));
			case self::G_GEO_INVALID_REQUEST:
				throw new Exception(sprintf($status, ''));
			case self::G_GEO_UNKNOWN_ADDRESS:
				throw new Exception(sprintf($status, '')); 		
			case self::G_GEO_UNAVAILABLE_ADDRESS:
				throw new Exception(sprintf($status, ''));

			default:
				throw new Exception(sprintf('Google Geo error %d occurred', $status));
				//return array();
		}
	}
}

class Placemark
{
	const ACCURACY_UNKNOWN      = 0;
	const ACCURACY_COUNTRY      = 1;
	const ACCURACY_REGION       = 2;
	const ACCURACY_SUBREGION    = 3;
	const ACCURACY_TOWN         = 4;
	const ACCURACY_POSTCODE     = 5;
	const ACCURACY_STREET       = 6;
	const ACCURACY_INTERSECTION = 7;
	const ACCURACY_ADDRESS      = 8;

	protected $_point;
	protected $_address;
	protected $_accuracy;

	public function setAddress($address)
	{
		$this->_address = (string) $address;
	}

	public function getAddress()
	{
		return $this->_address;
	}

	public function __toString()
	{
		return $this->getAddress();
	}

	public function setPoint(Kanelggapoint $point)
	{
		$this->_point = $point;
	}

	public function getPoint()
	{
		return $this->_point;
	}

	public function setAccuracy($accuracy)
	{
		$this->_accuracy = (int) $accuracy;
	}

	public function getAccuracy()
	{
		return $this->_accuracy;
	}

	public static function FromSimpleXml($xml)
	{
		$point = Kanelggapoint::Create($xml->geometry->location->lng.','.$xml->geometry->location->lat);
		$placemark = new self;
		$placemark->setPoint($point);
		//$placemark->setAddress($xml->address);
		$placemark->setAddress($xml->formatted_address);
		//$placemark->setAccuracy($xml->AddressDetails['Accuracy']);

		return $placemark;
	}
}    

class Kanelggapoint
{
	protected $_lat;
	protected $_lng;

	public function __construct($latitude, $longitude)
	{
		$this->_lat = $latitude;
		$this->_lng = $longitude;
	}

	public function getLatitude()
	{
		return $this->_lat;
	}

	public function getLongitude()
	{
		return $this->_lng;
	}

	public static function Create($str)
	{
		list($longitude, $latitude, $elevation) = explode(',', $str, 3);

		return new self($latitude, $longitude);
	}
}    
?>
