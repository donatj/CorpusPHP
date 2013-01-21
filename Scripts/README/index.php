<?php

include('markdown.php');

echo Markdown(

file_get_contents('../../README.md')

);