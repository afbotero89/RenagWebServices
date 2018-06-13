<?php
//include('functions.php');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


// Add new stolen bike, method post
$app->post('/bikes/add',function(Request $request, Response $response){

    $serialID = $request->getParam('serialID');
    $owner = $request->getParam('owner');
    $status = $request->getParam('status');
    $document = $request->getParam('document');

    $sql = "INSERT INTO `status` (`serialID`, `owner`, `status`, `document`) VALUES ('$serialID', '$owner', '$status', '$document');";
    //$sql = "INSERT INTO `usersBikes` (`userName`, `userID`,  `password`, `email`, `age`, `address`, `typePayment`) VALUES ('$userName', '$userID', '$password', '$email', '$age', '$address', '$typePayment');";
    ejecutarSQLCommand($sql);
    echo $sql;
});


// Edit alls user fiel by serialID, method post
$app->post('/bikes/editAll',function(Request $request, Response $response){
    
    $serialID = $request->getParam('serialID');
    $owner = $request->getParam('owner');
    $status = $request->getParam('status');
    $document = $request->getParam('document');
    
    $sql = "UPDATE `status` SET `owner`='$owner',`status`='$status',`document`='$document' WHERE `serialID` = '$serialID'";
    ejecutarSQLCommand($sql);
    echo $sql;
});

// Get all bikes, method get
$app->get('/bikes/all',function(Request $request, Response $response){
    
    $sql = "SELECT * FROM `status` WHERE 1";
   
    $users = getSQLResultSet($sql);
    
    $rows = array();
    
    while($r = mysqli_fetch_assoc($users)) {
        $rows[] = $r;
    }
    echo json_encode($rows);
});

// Delete 1 user by id (it is necesary check the catch exeption), method get
$app->get('/bikes/delete/{id}',function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM `status` WHERE `id`=$id";
    try{
        $sqlResult = getSQLResultSet($sql);
    }catch(PDOExeption $e){
        echo "error='.$e->getMessage().'";
    }
    
    echo $sqlResult;
});

// Get 1 user by userID OR password, method get
$app->get('/bikes/user/{serialID}',function(Request $request, Response $response){
    
    $userID = $request->getAttribute('serialID');

    $sql = "SELECT * FROM `status` WHERE `serialID`='$serialID'";

    $user = getSQLResultSet($sql);

    $rows = array();
    
    while($r = mysqli_fetch_assoc($user)) {
        $rows[] = $r;
    }
    echo json_encode($rows);
});
