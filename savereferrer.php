<?php

defined('IS_DEVELOPMENT') OR exit('No direct script access allowed');

$json = json_decode($input);

$data['swrve_user_id'] = isset($json->swrve_user_id) ? $json->swrve_user_id : "";
$data['referrer'] = isset($json->referrer) ? $json->referrer : "";

if (trim($data['swrve_user_id']) == "") {
    return array(
        "status" => FALSE,
        "message" => "Error: swrve_user_id is empty"
    );
}
if (trim($data['referrer']) == "") {
    return array(
        "status" => FALSE,
        "message" => "Error: referrer is empty"
    );
}

include("/var/www/redshift-config2.php");
$connection = new PDO(
    "pgsql:dbname=$rdatabase;host=$rhost;port=$rport",
    $ruser, $rpass
);

// create record if not exists
$sql1 = "INSERT INTO referral_almighty_ios (swrve_user_id)
SELECT :user_id1 WHERE NOT EXISTS (
    SELECT 1 FROM referral_almighty_ios 
    WHERE swrve_user_id = :user_id2
  )";
$statement1 = $connection->prepare($sql1);
$statement1->bindParam(":user_id1", $data['swrve_user_id']);
$statement1->bindParam(":user_id2", $data['swrve_user_id']);
$statement1->execute();

// save referrer
$sql2 = "UPDATE referral_almighty_ios "
        . "SET referrer = :referrer "
        . "WHERE swrve_user_id = :user_id";
$statement2 = $connection->prepare($sql2);
$statement2->bindParam(":referrer", $data['referrer']);
$statement2->bindParam(":user_id", $data['swrve_user_id']);
$statement2->execute();
$row = $statement2->fetch(PDO::FETCH_ASSOC);

return $data;