<?php
trait Entity {
	protected $data = NULL;

	protected function load($id, $pk = "id") {
		$this->data = DB::queryFirstRow("SELECT * FROM ".get_class()." WHERE ".$pk." = ".(is_numeric($id) ? "%d" : "%s"), $id);

		if(DB::count() == 0) $this->data = NULL;
	}

	public function delete($id, $pk = "id") {
		DB::delete(get_class(), $pk." = ".(is_numeric($id) ? "%d" : "%s"), $id);
	}

	public static function create() {
		$params = func_get_arg(0);

		try {
			DB::insert(get_class(), $params);
			return TRUE;
		}catch(MeekroDBException $e) {
			return FALSE;
		}
	}

	protected function commit() {
		DB::update(get_class(), $data, $pk." = ".(is_numeric($id) ? "%d" : "%s"), $id);
	}
};
?>