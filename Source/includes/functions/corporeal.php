<?
function crux( $key ) {
	return db::fetch( "Select value From crux where `key` = '" . db::input( $key ) . "'", db::SCALAR );
}

function recrux( $key, $value ) {
	return db::perform('crux', array('key' => $key, 'value' => $value), true);
}

function addressFormat($data, $html=true) {
	if( nempty($data['address']) ) $str .= $data['address'] . "\n";
	if( nempty($data['address2']) ) $str .= $data['address2'] . "\n";
	if( nempty($data['city']) ) $str .= $data['city'] . ", ";
	if( nempty($data['state']) ) $str .= $data['state'];
	if( nempty($data['zip']) ) $str .= "\n" . $data['zip'];
	if( $html ) return nl2br($str);
	return $str;
}

function draw_category_tree($class_prefix = "nav_", $root_id = "nav", $cat_id = 0, $depth = 0, $colapse = false) {
	global $_id;
	$qry = db::query("select c.categories_id, c.name, c.parent_id, c.redirect, c.template,
				c2.categories_id > 0 as hasChildren
				from categories c
				left join categories c2 on c.categories_id = c2.parent_id
				where c.status = 1 And c.categories_id > 0 And c.list > 0
				And c.parent_id = " . (int)$cat_id . "
				group by c.categories_id
				order by c.sort, c.name");

	if(mysql_num_rows($qry) > 0) {
		$src = '';
		while($row = mysql_fetch_array($qry)) {

			$src .= "\n" . str_repeat("\t", $depth + 1); //pretty html formating
			$src .= '<li>';
			$link = '';

			if( $row['template'] == -1 ) {
				$link = '#';
			}elseif( $row['template'] == -2 ){
				$link = href( $row['redirect'] );
			}else{
				$link = href( $row['categories_id'] );
			}


			$src .= '<a href="' . $link .
				'" class="'. ( $row['categories_id'] == $_id ? 'selected' : '') . ($colapse ? ' colapse' . (int)$colapse : '' ) .
				' template'.(int)$row['template'].'">';
			$src .= htmlE($row['name']);
			$src .= '</a>';
			if($colapse > 1) { $src .= '</li>'; }
			if( $row['hasChildren'] ) {
			$src .= draw_category_tree($class_prefix, $root_id, $row['categories_id'], $depth+1, max(0, $colapse - 1));
			}
			if($colapse <= 1) { $src .= '</li>'; }
		}

		if( $colapse < 1 || $depth == 0 ) {
			$src = '<ul'.($depth == 0 ? ' id="'.$root_id.'"' : '').' class="'.$class_prefix. (int)$depth.'">' . $src . '</ul>';
		}
	}
	return $src;
}

function getParent( $id ) {
	$qry = db::query("Select parent_id From categories Where categories_id = " . (int)$id);
	$row = mysql_fetch_array($qry);
	if( $row['parent_id'] > 0 ) {
		return (int)$row['parent_id'];
	}
	return false;
}

function breadcrumb( $id, $seperator = ' &raquo; ' ) {
	//if( $id < 1 ) return false;
	global $_meta;
	if( !$_meta['hide_breadcrumb'] ) {
		$id2 = $id;
		if($id != 0) {
			do {
				$qry = db::query("Select parent_id, name, template From categories Where categories_id = " . (int)$id2);
				$row = mysql_fetch_array($qry);
				if($id2 == $id) {
					$data[] = '<strong>' . $row['name'] . '</strong>';
				}else{
					if($id2 > 0) {
						$data[] = '<a href="'.href($id2).'" class="template'.(int)$row['template'].'">' . $row['name'] . '</a>';
					}
				}
			} while( $id2 = $row['parent_id'] );
		}
		$data = firstNotEmpty($_meta['breadcrumbs'], $data, array('<strong>' . $_meta['title'] . '</strong>'));
		$data[] = '<a href="'.DWS_BASE.'">Home</a>';
		$data = array_reverse($data);
		$i = count($data);
		return '<div class="breadcrumb">' . implode( $seperator, $data ) . '</div>';
	}
}

function button( $text, $link = false, $linkParams = '', $buttonParams = '', $type = false ) {
	if( $type ) {
	}elseif( is_string($link) || $link === false ) {
		$type = 'button';
	}else{
		$type = 'submit';
	}

	$return = '<button type="' . $type . '" '.$buttonParams.'>'.htmlE( $text ).'</button>';
	if($link && $link !== true ) { $return = '<a title="' . htmlE($text) . '" href="'.href($link).'" '.$linkParams.'>' . $return . '</a>'; }
	return $return;
}

function keywordExpansion( $searchedFor ) {
	$inf = new Inflector();

	$search = trim( $searchedFor );
	$searchA = explode(' ',$search);
	$searchA2 = array();
	foreach($searchA as &$s) {
		if(strlen(trim($s))) {
			$searchA2[] = db::input($inf->pluralize($s));
			$searchA2[] = db::input($inf->singularize($s));
			$searchA2[] = db::input($s);
		}
	}
	return array_unique($searchA2);
}

/**
* Oxford style comma seperates
* 
* I climbed to Dharamsala
* I met the highest lama
* His accent sounded fine
* To me
* 
* @param array $texts array of texts to be oxford commafied
* @param string $conj the conjunction
* @param string $sep the separator
*/
function OxfordComma( $texts, $conj = 'and', $sep = ',' ) {
	$len = count( $texts );
	foreach( $texts as $fragment ) {
		$str .= $fragment;
		//echo $len;
		if( $len - 1 > ++$j ) {
			$str .= $sep .  ' ';
		}elseif( $len > $j ){
			if( $len > 2 ) { $str .= $sep; }
			$str .= ' ' . $conj .  ' ';
		}
	}
	return $str;
}