<?

Class User {
	
	public $info;
	var $id;
	
	function __construct( $user_id ) {
		$this->id = (int)$user_id;
		$this->refresh();
	}
	
	function refresh() {
		$this->info = db::fetch("Select * From users Where user_id = " . $this->id, 'row');
	}
	
}
