<?php
session_start();

/**
 * Created by JetBrains PhpStorm.
 * User: convexstyle
 * Date: 20/07/13
 * Time: 2:25 AM
 * To change this template use File | Settings | File Templates.
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
$twitterIni = new Zend_Config_Ini(CONFIG_BASE . "app.ini", 'twitter_oauth');


$config = array(
    'callbackUrl'     => $twitterIni->oauth->redirectUrl,
    'siteUrl'         => $twitterIni->oauth->siteUrl,
    'consumerKey'     => $twitterIni->oauth->consumerKey,
    'consumerSecret'  => $twitterIni->oauth->consumerSecret
);
$consumer = new Zend_Oauth_Consumer($config);


// Get AccessToken
if (!empty($_GET) && isset($_SESSION['TWITTER_REQUEST_TOKEN'])) {

    $token = $consumer->getAccessToken(
        $_GET,
        unserialize($_SESSION['TWITTER_REQUEST_TOKEN'])
    );
    // Ideally, this access token should be saved in a database
    $_SESSION['TWITTER_ACCESS_TOKEN'] = serialize($token);

    // Discard the Request Token
    $_SESSION['TWITTER_REQUEST_TOKEN'] = null;


    // Get Timeline Data
    $config = array(
        'callbackUrl'     => $twitterIni->oauth->redirectUrl,
        'siteUrl'         => $twitterIni->oauth->siteUrl,
        'consumerKey'     => $twitterIni->oauth->consumerKey,
        'consumerSecret'  => $twitterIni->oauth->consumerSecret
    );

    // Ideally, this access token should be retrieved from a database
    $token = unserialize($_SESSION['TWITTER_ACCESS_TOKEN']);
    $client = $token->getHttpClient($config);
    $client->setMethod(Zend_Http_Client::GET);
    $client->setUri('https://api.twitter.com/1.1/statuses/user_timeline.json');
    $client->setParameterGet('user_id', $twitterIni->userId);
    $response = $client->request();

    if($response->isSuccessful()) {
        if(strlen($response->getBody()) > 0) {
            $twitterData = Zend_Json::decode($response->getBody());

            // Assign this variable to view or filter for something.
            Zend_Debug::dump($twitterData);exit;
        }
    }

} else {
    // Invalid access to this page
    exit('Invalid callback request.');
}