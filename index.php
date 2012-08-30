<?php
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_Photos');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');

# userinfos contains the desired Google Account's name and password on 2 separate lines
$userinfos = file("userinfos");

$serviceName = Zend_Gdata_Photos::AUTH_SERVICE_NAME;
$client = Zend_Gdata_ClientLogin::getHttpClient($userinfos[0], $userinfos[1], $serviceName);

// update the second argument to be CompanyName-ProductName-Version
$gp = new Zend_Gdata_Photos($client, "");

try {
    $userFeed = $gp->getUserFeed("default");
    foreach ($userFeed as $albumEntry) {
    	echo "<br/>";
        echo "Album name : " . $albumEntry->title->text ./* " ; id = " . $albumEntry->getGPhotoId() . */"<br />\n";
        $query = $gp->newAlbumQuery();

		$query->setUser("default");
		$query->setAlbumId($albumEntry->getGPhotoId());

		$albumFeed = $gp->getAlbumFeed($query);
		foreach ($albumFeed as $photoEntry) {
    		echo "--> " . $photoEntry->title->text /*. " ; id = " . $photoEntry->getGPhotoId()*/;
            $mediaContentArray = $photoEntry->getMediaGroup()->getContent();
            ?><a href="<?php echo $mediaContentArray[0]->getUrl(); ?>">(link)</a> <?php
            echo "<br/>";
		}
    }
} catch (Zend_Gdata_App_HttpException $e) {
    echo "Error: " . $e->getMessage() . "<br />\n";
    if ($e->getResponse() != null) {
        echo "Body: <br />\n" . $e->getResponse()->getBody() . 
             "<br />\n"; 
    }
    // In new versions of Zend Framework, you also have the option
    // to print out the request that was made.  As the request
    // includes Auth credentials, it's not advised to print out
    // this data unless doing debugging
    // echo "Request: <br />\n" . $e->getRequest() . "<br />\n";
} catch (Zend_Gdata_App_Exception $e) {
    echo "Error: " . $e->getMessage() . "<br />\n"; 
}

?>