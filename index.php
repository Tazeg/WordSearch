<?php
//----------------------------------------------------------------------
//  AUTHOR	: Jean-Francois GAZET
//  WEB		: http://www.jeffprod.com
//  TWITTER	: @JeffProd
//  MAIL	: jeffgazet@gmail.com
//  LICENCE	: GNU GENERAL PUBLIC LICENSE Version 2, June 1991
//----------------------------------------------------------------------

require_once 'class.grid.php';
require_once 'class.word.php';

$grid=new Grid();
$grid->gen();
echo $grid->render();
echo "Words to find (".$grid->getNbWords().") :\n";
echo $grid->getWordsList("\n");
?>
