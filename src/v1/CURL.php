<?php
class CURL {
	public static function request($tree, $get = NULL, $post = NULL) {
		if(!is_null($get) && is_array($get)) $get = "?".http_build_query($get); else $get = "";
		if(!is_null($post) && is_array($post)) $post = http_build_query($post); else $post = "";

		if(is_array($tree))
			$tree = implode("/", $tree);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.spark.io/v1/".$tree.$get);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}
};
?>