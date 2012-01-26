<?php

// change the following paths if necessary
require(dirname(__FILE__).'/../../../../../framework/yiit.php');

set_include_path(get_include_path() .
	PATH_SEPARATOR . dirname(__FILE__) . "/../" .
	PATH_SEPARATOR . dirname(__FILE__) . "/fixtures/models/"
);


//require(dirname(__FILE__).'/../WForm.php');
//require(dirname(__FILE__).'/../WFormBehavior.php');
//require(dirname(__FILE__).'/../WFormBehavior.php');
//$config=dirname(__FILE__).'/../config/test.php';
//
Yii::createWebApplication(array(
	'basePath' => dirname(__FILE__) . '/fixtures/'
));
