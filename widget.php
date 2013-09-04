<?php

/**
 * widget.php
 *
 * @author:    Hiroshi Tazawa
 * @version:   1.0
 * @copyright: 2013,convexstyle.com
 * @license:   convexstyle
 * @version:   Release: @1.0@
 * @since:     Class available Since Release 1.0
 */

// Set Path Separater
if(!defined('PATH_SEPARATOR')) {
    if(substr(strtoupper(PHP_OS), 0, 3) == 'WIN') {
        define('PATH_SEPARATOR', ';');
    } else {
        define('PATH_SEPARATOR', ':');
    }
}

// Set the Application Path
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/_application/'));


// Set the global Variables
define('CONFIG_BASE', APPLICATION_PATH . '/configs/');
define('LIB_BASE', APPLICATION_PATH . '/libs/');


// Set the include Path
$paths = array(
    LIB_BASE,
    get_include_path()
);
set_include_path(implode(PATH_SEPARATOR, $paths));


// AutomateLoader
require_once('Zend/Loader/Autoloader.php');
$_autoLoader = Zend_Loader_Autoloader::getInstance();
$_autoLoader->setFallbackAutoloader(true)->pushAutoloader(NULL, 'Smarty_');


// Load Twitter Config Data
$twitterIni = new Zend_Config_Ini(CONFIG_BASE . "app.ini", 'twitter_widget');


// Get Twitter Timeline Data
$accessToken = new Zend_Oauth_Token_Access();
$accessToken->setToken($twitterIni->oauth->accessToken);
$accessToken->setTokenSecret($twitterIni->oauth->accessTokenSecret);

$twitter = new Zend_Service_Twitter(array(
    'accessToken'  => $accessToken,
    'oauthOptions' => array(
        'consumerKey'    => $twitterIni->oauth->consumerKey,
        'consumerSecret' => $twitterIni->oauth->consumerSecret
    )
));
$response    = $twitter->statusesUserTimeline(array('user_id' => $twitterIni->userId, 'count' => $twitterIni->count));
// If you would like to use screen_name instead of user_id, please use the following syntax
//$response    = $twitter->statusesUserTimeline(array('screen_name' => $twitterIni->screen_name, 'count' => $twitterIni->userId));
$twitterData = $response->toValue();


// Assign this variable to view or filter for something.
Zend_Debug::dump($twitterData);exit;