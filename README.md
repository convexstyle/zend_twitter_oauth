This is the simple examples to retrieve user time line data (user_timeline) from Twitter Api 1.1 based on Zend Framework (I am using Zend Framework 1.12.3 here).

There are two types of examples.

**1. Widget Type**

It is inconvenient to redirect to Twitter page to authorise in certain applications like a widget. To skip the oAuth authorisation procedure, you can generate an access token and an access token secret in advance,
and access Twitter Api using [Zend_Service_Twitter](http://framework.zend.com/manual/1.12/ja/zend.service.twitter.html).

**2. Normal oAuth Type**

This is normal procedure. First, get a request token to Twitter, redirect a user to Twitter to authorise an application, and retrieve an access token. Based on the access token, retrieve user time line data using
[Zend_Oauth libraries](http://framework.zend.com/manual/1.12/en/zend.oauth.introduction.html).

## How to get started
* [Download Zend Framework](http://framework.zend.com/downloads/latest). I downloaded Zend Framework 1.12.3 Full.
* Read [Twitter Api Documentation](https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline).
* Read [Zend_Service_Twitter](http://framework.zend.com/manual/1.12/ja/zend.service.twitter.html).
* Read [Zend oAuth](http://framework.zend.com/manual/1.12/en/zend.oauth.introduction.html).

## Widget Type

### Sample File
* widget.php
* _application/configs/app.ini

### Before start
1. Log in [Twitter](https://dev.twitter.com/) and register your application.

2. Create your access token in Details tab. See [here](http://www.convexstyle.com/github/create_accessToken.png "The image of creating access token") in details.

3. Once you create access token and access token secret, rename _application/configs/app-sample.ini to _application/configs/app.ini and copy and paste **access token**, **access token secret**, **consumer key** and **consumer secret** to **oauth.accessToken**, **oauth.accessTokenSecret**, **oauth.consumerKey**, and **oauth.consumerSecret** in app.ini.

4. Get the **Twitter User ID** you want to get the data from. If you don't know what your Twitter User ID is, access [gettwitterid.com](http://gettwitterid.com/ "gettwitterid.com") and find it out.

5. Copy your Twitter User ID and paste it to **user_id** in app.ini. Also, if you want to manage the number of entries you want to get at once, change **count** value in app.ini.

### Example Usage
<pre>
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
$response    = $twitter->statusesUserTimeline(array('user_id' => $twitterIni->userId, 'count' => $twitterIni->userId));
$twitterData = $response->toValue();
</pre>

## Normal oAuth Type

### Sample File
* request_token.php
* callback.php
* _application/configs/app.ini

### Before start
1. Log in [Twitter](https://dev.twitter.com/) and register your application.

2. Copy and paste your **consumer key** and **consumer secret** to **oauth.consumerKey** and **oauth.consumerSecret** in app.ini. (_application/configs/app.ini).

3. Assign a callback URL in the Settings Tab and copy and paste it to **oauth.redirectUrl** in app.ini. See [here](http://www.convexstyle.com/github/create_callbackBack.png "The image of creating callback URL")

4. Get the **Twitter User ID** you want to get the data from. If you don't know what your Twitter User ID is, access [gettwitterid.com](http://gettwitterid.com/ "gettwitterid.com") and find it out.

5. Copy your Twitter User ID and paste it to **user_id** in app.ini. Also, if you want to manage the number of entries you want to get at once, change **count** value in app.ini.

### Example Usage (Request Token)
<pre>
// Request Token
$config = array(
    'callbackUrl'     => $twitterIni->oauth->redirectUrl,
    'requestScheme'   => Zend_Oauth::REQUEST_SCHEME_HEADER,
    'signatureMethod' => 'HMAC-SHA1',
    'siteUrl'         => $twitterIni->oauth->siteUrl,
    'consumerKey'     => $twitterIni->oauth->consumerKey,
    'consumerSecret'  => $twitterIni->oauth->consumerSecret
);
$consumer = new Zend_Oauth_Consumer($config);
$token    = $consumer->getRequestToken();
$_SESSION['TWITTER_REQUEST_TOKEN'] = serialize($token);
$consumer->redirect();
</pre>

### Example Usage (Request Data with Access Token)
<pre>
// Get Timeline Data
$config = array(
    'callbackUrl'     => $twitterIni->oauth->redirectUrl,
    'siteUrl'         => $twitterIni->oauth->siteUrl,
    'consumerKey'     => $twitterIni->oauth->consumerKey,
    'consumerSecret'  => $twitterIni->oauth->consumerSecret
);

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
</pre>

