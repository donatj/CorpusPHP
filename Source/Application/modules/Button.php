<?php

$_meta['name'] = 'Button';
$_meta['callable'] = true;

if( !$shutup ) :

echo call_user_func_array('button', $this->data);

endif;