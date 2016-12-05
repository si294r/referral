<?php

defined('IS_DEVELOPMENT') OR exit('No direct script access allowed');

$swrve_user_id = isset($params[1]) ? $params[1] : "";

if (trim($swrve_user_id) == "") {
    return array(
        "status" => FALSE,
        "message" => "Error: swrve_user_id is empty"
    );
}

include("/var/www/redshift-config2.php");
$connection = new PDO(
    "pgsql:dbname=$rdatabase;host=$rhost;port=$rport",
    $ruser, $rpass
);

// create shorten_id if not exists
$sql1 = "INSERT INTO referral_almighty_ios (swrve_user_id)
SELECT :user_id1 WHERE NOT EXISTS (
    SELECT 1 FROM referral_almighty_ios 
    WHERE swrve_user_id = :user_id2
  )";
$statement1 = $connection->prepare($sql1);
$statement1->bindParam(":user_id1", $swrve_user_id);
$statement1->bindParam(":user_id2", $swrve_user_id);
$statement1->execute();

// get shorten_id
$sql2 = "SELECT shorten_id, swrve_user_id FROM referral_almighty_ios WHERE swrve_user_id = :user_id";
$statement2 = $connection->prepare($sql2);
$statement2->execute(array(':user_id' => $swrve_user_id));
$row = $statement2->fetch(PDO::FETCH_ASSOC);

return array(
    'shorten_id' => $row['shorten_id'],
    'swrve_user_id' => $row['swrve_user_id']
);