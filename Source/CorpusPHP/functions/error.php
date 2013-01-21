<?

function _errorHandler( $errno, $errstr, $errfile, $errline) {
	echo '<p><strong>Error ' . $errno . '<br /><small>' . $errstr . '</small></strong><small>';
	echo _errorTracer() . '</small></p>';
}

function _errorTracer($waitForFunct = false, $startAtFile = false) {
	if( function_exists('debug_backtrace') ) {
		$traceback = debug_backtrace();
		$trace_str = '';
		$start     = false;
		if(is_array($traceback)) {
			
			foreach($traceback as $trace) {
				if($start) {
					$trace_str .= '<br /><br />Called Via ' . ( $trace['class'] ? $trace['class'] . $trace['type'] : '' ) . $trace['function']  . 
						" <strong>at line {$trace['line']}</strong><br /><small>{$trace['file']}</small>";
				}
				
				if(nempty($waitForFunct) && $trace['function'] == $waitForFunct) {
					$trace_str .= '<big><strong>'.ucwords(str_replace("_"," ",$waitForFunct)).' Error</strong> in ' . ( $trace['class'] ? $trace['class'] . $trace['type'] : '' ) . $trace['function'] . 
					" <strong>at line {$trace['line']}</strong><br /><small>{$trace['file']}</small></big>";
					$start = true;
				}elseif( strpos($trace['file'], DWS_FUNC . 'error.php') ) {
					$start = true;
				}
				
				//print_r($trace);
			}
		}
		return $trace_str;
	}
	return false;
}

function drop() {
	call_user_func_array('see', func_get_args());
	die();
}

function see() {
	$data = $args = "";
	$eol = "\n\r";
	$arguments = func_get_args();
	if (is_array($arguments) && !empty($arguments)) {
		foreach ($arguments as $i => $argument) {
			$args .= ">>>>>>>>>>>>> Arg No. {$i} >>>>>>>>>>>>>" . $eol . $eol;
			$argument = is_null($argument) ? "(null)null" : $argument;
			$argument = $argument === false ? "(bool)false" : $argument;
			$argument = $argument === true ? "(bool)true" : $argument;
			$args .= print_r($argument, true) . $eol . $eol;
		} $args .= ">>>>>>>>>>>>>>> EOF >>>>>>>>>>>>>>>>>";
		$final = "<pre>{$eol}{$args}{$eol}</pre>";
		die($final);
	} return "";
}
