<?php

defined('AWS_API_KEY') or define('AWS_API_KEY', get_option('aws_api_key'));
defined('AWS_API_SECRET_KEY') or define('AWS_API_SECRET_KEY', get_option('aws_api_secret_key'));
defined('AWS_ASSOCIATE_TAG') or define('AWS_ASSOCIATE_TAG', get_option('aws_associate_tag', 'devenews-20'));
#defined('AWS_ANOTHER_ASSOCIATE_TAG') or define('AWS_ANOTHER_ASSOCIATE_TAG', get_option('aws_another_associate_tag', ''));

require_once 'lib/AmazonECS.class.php';

try {
    // get a new object with your API Key and secret key. Lang is optional.
    $amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'US', AWS_ASSOCIATE_TAG);
    
    // ItemPage - Offset
    if(isset($_GET['itempage']) && $_GET['itempage'] != '1')
      $amazonEcs->optionalParameters(array('ItemPage' => $_GET['itempage']));

    // from now on you want to have pure arrays as response
    $amazonEcs->returnType(AmazonECS::RETURN_TYPE_ARRAY);
 
    $response = $amazonEcs->responseGroup('Large')->category($_GET['category'])->search($_GET['searchterm']);

    echo json_encode($response);      

}
catch(Exception $e) {
  echo $e->getMessage();
}

