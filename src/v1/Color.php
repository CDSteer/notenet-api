<?php
class Color {	
	private $r = 0, $g = 0, $b = 0;

	public function Color() {
		// Get arguments
		$argc = func_num_args();
		$argv = func_get_args();

		if($argc == 1) {
			if(is_string($argv[0])) {
				$hex = $argv[0];

				if(substr($hex, 0, 1) == "#") {
					$hex = substr($hex, 1);
				}

				if(strlen($hex) == 3) {
					$hex = sprintf(str_repeat("%s", 6), $hex[0], $hex[0], $hex[1], $hex[1], $hex[2], $hex[2]);
				}

				if(strlen($hex) == 6) {
					$this->r = hexdec(substr($hex, 0, 2));
					$this->g = hexdec(substr($hex, 2, 2));
					$this->b = hexdec(substr($hex, 4, 2));
				}
			}
			return new Color($r, $g, $b);
		} else if($argc == 3) {
			$this->r = intval($argv[0]);
			$this->g = intval($argv[1]);
			$this->b = intval($argv[2]);
		}
	}

	public function darken($delta = 0.5) {
		$this->changeBrightness(-$delta);
	}

	public function lighten($delta = 0.5) {
		$this->changeBrightness($delta);
	}

	private function changeBrightness($delta = 0.5) {
		$r = $this->r;
		$g = $this->g;
		$b = $this->b;

		if($delta > 0) {
			$r = round($r * $delta) + round(255 * (1 - $delta));
			$g = round($g * $delta) + round(255 * (1 - $delta));
			$b = round($b * $delta) + round(255 * (1 - $delta));
		} else {
			$delta = $delta - ($delta * 2);
			$r = round($r * $delta);
			$g = round($g * $delta);
			$b = round($b * $delta);
		}

		if($r > 255) $r = 255;
		if($g > 255) $g = 255;
		if($b > 255) $b = 255;

		if($r < 0) $r = 0;
		if($g < 0) $g = 0;
		if($b < 0) $b = 0;

		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
	}

	public function invert() {
		$this->r = 255 - $this->r;
		$this->g = 255 - $this->g;
		$this->b = 255 - $this->b;
	}

	public function getRed($float = FALSE) {
		return $float ? $this->r / 255.0 : $this->r;
	}

	public function getGreen($float = FALSE) {
		return $float ? $this->g / 255.0 : $this->g;
	}

	public function getBlue($float = FALSE) {
		return $float ? $this->b / 255.0 : $this->b;
	}

	public function getHex($prefix = "#") {
		$r = dechex($this->r);
		$g = dechex($this->g);
		$b = dechex($this->b);

		if(strlen($r) < 2) $r = "0{$r}";
		if(strlen($g) < 2) $g = "0{$g}";
		if(strlen($b) < 2) $b = "0{$b}";

		$hex = strtoupper(sprintf("%s%s%s", $prefix, $r, $g, $b));
		return $hex;
	}

	public function copy($color) {
		return new Color($color->getHex());
	}

	public static function init() {
		// Full colors
		Color::$WHITE   = new Color(255 , 255 , 255);
		Color::$YELLOW  = new Color(255 , 255 , 0  );
		Color::$MAGENTA = new Color(255 , 0   , 255);
		Color::$CYAN    = new Color(0   , 255 , 255);
		Color::$RED     = new Color(255 , 0   , 0  );
		Color::$GREEN   = new Color(0   , 255 , 0  );
		Color::$BLUE    = new Color(0   , 0   , 255);
		Color::$OFF     = new Color(0   , 0   , 0  );

		// Half-colors
		Color::$ORANGE  = new Color(255 , 128 , 0  );

		// Duplicate names
		Color::$FUSCHIA = Color::$FUSCHIA;
		Color::$AQUA    = Color::$CYAN;
	}

	// Colors
	public static $RED;
	public static $GREEN;
	public static $BLUE;
	public static $YELLOW;
	public static $ORANGE;
	public static $MAGENTA;
	public static $FUSCHIA;
	public static $CYAN;
	public static $AQUA;
	public static $WHITE;
	public static $OFF;

};
?>