<?php
include('functions.php');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


// Add new users, method post
$app->post('/api/add',function(Request $request, Response $response){

    $userName = $request->getParam('userName');
    $userID = $request->getParam('userID');
    $password = $request->getParam('password');
    $email = $request->getParam('email');
    $age = $request->getParam('age');
    $address = $request->getParam('address');
    $typePayment = $request->getParam('typePayment');
    $sql = "INSERT INTO `usersBikes` (`id`, `userName`, `userID`, `password`, `email`, `age`, `address`, `typePayment`, `latitude`, `longitude`) VALUES (NULL, 'user2', 'id2', 'pass2', 'email2', 'age2', 'add2', 'pay2', 'lat2', 'long2');";
    //$sql = "INSERT INTO `usersBikes` (`userName`, `userID`,  `password`, `email`, `age`, `address`, `typePayment`) VALUES ('$userName', '$userID', '$password', '$email', '$age', '$address', '$typePayment');";
    ejecutarSQLCommand($sql);
    echo $sql;
});

// Edit one user fiel by id, method post
$app->post('/api/edit',function(Request $request, Response $response){
    
    $userID = $request->getParam('userID');
    $field = $request->getParam('field');
    $value = $request->getParam('value');
    $sql = "UPDATE `usersBikes` SET `$field`= '$value' WHERE `userID` = '$userID'";
    ejecutarSQLCommand($sql);
    echo $sql;
});

// Edit alls user fiel by id, method post
$app->post('/api/editAll',function(Request $request, Response $response){
    
    $userName = $request->getParam('userName');
    $userID = $request->getParam('userID');
    $password = $request->getParam('password');
    $email = $request->getParam('email');
    $age = $request->getParam('age');
    $address = $request->getParam('address');
    $typePayment = $request->getParam('typePayment');
    
    $sql = "UPDATE `usersBikes` SET `userName`='$userName',`userID`='$userID',`password`='$password',`email`='$email',`age`='$age',`address`='$address',`typePayment`='$typePayment',`latitude`='',`longitude`='' WHERE `userID` = '$userID'";
    ejecutarSQLCommand($sql);
    echo $sql;
});

// Get all users, method get
$app->get('/api/all',function(Request $request, Response $response){
    
    $sql = "SELECT * FROM `usersBikes` WHERE 1";
   
    $users = getSQLResultSet($sql);
    
    $rows = array();
    
    while($r = mysqli_fetch_assoc($users)) {
        $rows[] = $r;
    }
    echo json_encode($rows);
});

// Delete 1 user by id (it is necesary check the catch exeption), method get
$app->get('/api/delete/{id}',function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM `usersBikes` WHERE `id`=$id";
    try{
        $sqlResult = getSQLResultSet($sql);
    }catch(PDOExeption $e){
        echo "error='.$e->getMessage().'";
    }
    
    echo $sqlResult;
});

// Get 1 user by userID OR password, method get
$app->get('/api/user/{userID}',function(Request $request, Response $response){
    
    $userID = $request->getAttribute('userID');

    $sql = "SELECT * FROM `usersBikes` WHERE `userID`='$userID'";

    $user = getSQLResultSet($sql);

    $rows = array();
    
    while($r = mysqli_fetch_assoc($user)) {
        $rows[] = $r;
    }
    echo json_encode($rows);
});


// Get 1 user by userID AND password, method get, this query is for check the credentials in the start session (user login)
$app->get('/api/userLogin/{userName}/{password}',function(Request $request, Response $response){
    
    $userName = $request->getAttribute('userName');

    $userPassword = $request->getAttribute('password');

    $sql = "SELECT * FROM `usersBikes` WHERE `userName`='$userName' AND `password`='$userPassword'";

    $user = getSQLResultSet($sql);

    $rows = array();
    
    while($r = mysqli_fetch_assoc($user)) {
        $rows[] = $r;
    }
    echo json_encode($rows);
});


// Login user by phone number and password
$app->post('/api/userLoginByPhoneAndPass',function(Request $request, Response $response){

    $phoneNumber = $request->getParam('phoneNumber');
    $password = $request->getParam('password');

    $sql = "SELECT * FROM `users` WHERE `phoneNumber`='$phoneNumber' AND `password`='$password'";

    $sqlResult = getSQLResultSet($sql);

    $rows = array();
    
    while($r = mysqli_fetch_assoc($sqlResult)) {
        $rows[] = $r;
    }
    $jsonValue = json_encode($rows);

    if(empty($rows)){
        $arr = array('response' => 'nill');
        echo json_encode($arr);;
    }else{
        echo $jsonValue;
    }
});


// Latitude and longitude: post method
$app->post('/api/latitudeAndLongitude',function(Request $request, Response $response){

    $IMEI = $request->getParam('IMEI');  //The IMEI is the unique identifier from the lock
    $sql = "SELECT `longitude`, `latitude`, `lastHourOfConnection` FROM `electricBikes` WHERE `IMEI`='$IMEI'";

    $sqlResult = getSQLResultSet($sql);

    $rows = array();
    
    while($r = mysqli_fetch_assoc($sqlResult)) {
        $rows[] = $r;
    }
    $jsonValue = json_encode($rows);

    if(empty($rows)){
        $arr = array('response' => 'nill');
        echo json_encode($arr);;
    }else{
        echo $jsonValue;
    }
});