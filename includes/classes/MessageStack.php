<?

/**
* MessageStack Class for Stacking Messages across
* time/space/sessions until which time they can be output
*
* @package CorpusPHP
* @subpackage Output
* @author Jesse G. Donat
* @version 1.5.1
*/
class MessageStack {
	var $iid;

	/**
	* Constructor
	*
	* @param string $id the stack to be used for this instance of the message stack
	* @return MessageStack
	*/
	function __construct($id = 'default') {
		$this->iid = $id;
		if(!isset( $_SESSION['corpus']['message_stack'][$this->iid] )) $_SESSION['corpus']['message_stack'][$this->iid] = array();
	}

	/**
	* Add messages to the stack
	*
	* @param string $message the message to be displayed
	* @param bool $classExt what to append to the class, true == 'error'
	*/
	function add($message, $classExt = false) { $_SESSION['corpus']['message_stack'][$this->iid][] = array($message,$classExt);  }

	/**
	* Draw
	* @return int the number of messages the stack drew
	*/
	function draw() {
		$x = count($_SESSION['corpus']['message_stack'][$this->iid]) > 0;
		if( $x > 0 ) {
			echo '<div class="messages">';
			foreach($_SESSION['corpus']['message_stack'][$this->iid] as $message) {
				echo '<div class="message message'.($message[1] === true?'error': firstNotEmpty( $message[1], 'default' )).' message_stack_'.$this->iid.'">' . $message[0] . '</div>';
			}
			echo '</div>';
		}
		$this->clear();
		return $x;
	}

	/**
	* Remove all messages from the stack
	*/
	function clear() { $_SESSION['corpus']['message_stack'][$this->iid] = array(); }

}