<?

class User {
	
	protected static $_table = 'users';
	protected static $_pk    = 'user_id';
	
	function __construct($id) {
		$this->id = (int)$id;
	}
	
}
