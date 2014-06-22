<?php
class PushWeather {
	private $greenCodes = [800, 801, 802, 803, 804, 903, 904, 905, 950, 951, 952, 953, 954, 955];
	private $lBlueCodes = [300, 301, 302, 310, 311, 312, 313, 314, 321, 500];
	private $blueCodes = [501, 502, 503, 504, 511, 520, 521, 522, 531, 906];
	private $yellowCodes = [200, 201, 202, 210, 211, 212, 221, 230, 231, 232];
	private $redCodes = [701, 711, 721, 731, 741, 751, 761, 762, 771, 781, 900, 901, 902, 960, 961, 962, 956, 957, 958,
								959, 600, 601, 602, 611, 612, 615, 616, 620, 621, 622, 623];

	public function pushAll() {
		$cubes = Cube::all();
		foreach ($cubes as $cube) {
			$this->pushOne($cube);
		}
	}

	public function pushOne($cube){
		$location = $cube->getLocation();
		if ($location->ok) {
			$weather = $this->getWeather($location->name);
			$light = $this->checkColour($weather);
			$this->weatherMode($light, $cube);
			$cube->setLED($light);
		}
	}

	public function checkColour($userWeather){
		$colour;
		if ($this->searchIn($userWeather, $this->greenCodes)){
			$colour = "00ff00";
		} elseif ($this->searchIn($userWeather, $this->lBlueCodes)) {
			$colour = "5cceff";
		} elseif ($this->searchIn($userWeather, $this->blueCodes)) {
			$colour = "0000ff";
		} elseif ($this->searchIn($userWeather, $this->yellowCodes)) {
			$colour = "ffff00";
		} elseif ($this->searchIn($userWeather, $this->redCodes)) {
			$colour = "ff0000";
		}
		return $colour;
	}

	public function getWeather($city) {
		$URL = sprintf("http://api.openweathermap.org/data/2.5/weather?q=%s&mode=xml&APPID=c64efae3aac67e9b8398f501e8b25f69", $city);
		$xml = $this -> loadFile($URL);
		if (isset($xml -> weather)) {
			$weather = $xml->weather['number'];
			return $weather;
		} else {
			return 1;
		}
	}

	public function lightOff($access_token, $deviceID) {
		$cube = new Cube($deviceID);
		if($cube->getPublicAccessToken() == $access_token) {
			$cube->setLED("000");
			return TRUE;
		} else return FALSE;
	}

	public function weatherMode($light, $cube) {
		if ($light == "#0000ff"){
			$cube->setMode(Light_Mode::SOLID);
			//$cube->setMode("breathing");
		}
		if ($light == "#ffff00"){
			$cube->setMode(Light_Mode::FLASH);
		} else {
			$cube->setMode(Light_Mode::SOLID);
		}
	}

	public function loadFile($URL) {
		$xml = new SimpleXMLElement(file_get_contents($URL));
		if ($xml == false) {
			exit ;
		} else {
			return $xml;
		}
	}

	public function searchIn($elem, $array){
	   $top = sizeof($array) -1;
	   $bot = 0;
	   while($top >= $bot)
	   {
	      $p = floor(($top + $bot) / 2);
	      if ($array[$p] < $elem) $bot = $p + 1;
	      elseif ($array[$p] > $elem) $top = $p - 1;
	      else return TRUE;
	   }
	   return FALSE;
	}
};
?>