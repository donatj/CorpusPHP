<?
/**
* Elements
* 
* An html writing Object
* @package CorpusPHP
* @subpackage Output
* @author Jesse G. Donat
* @version .1
* @todo finish, perfect, extend, etc
*/
class elm extends Elements {} class Elements {

	static function elm( $tag, $attributes = false, $innerHTML = false ) {

		if( is_array($attributes) ) {
			foreach( $attributes as $column => $value){

				if ( is_array($value) && $value[0] === true ) {
					$values .= $column . '="' . $value[1] . '"';
				}else{
					$values .= $column . '="' . htmlE($value[1]) . '"';
				}
				
			}
		}
		
		return "<{$tag} {$values} " . ( $innerHTML === false ? ' />' : "{$innerHTML}</{$tag}>" );
	}

}