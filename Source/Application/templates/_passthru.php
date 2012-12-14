<?php

# Passthru Layout - a page that should never be reached - redirects to its parent

redirect( href( getParent( self::$id ) ), 301 );