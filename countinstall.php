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

// get count install
$sql2 = "SELECT count(*) as count_install FROM referral_almighty_ios WHERE referrer = :user_id";
$statement2 = $connection->prepare($sql2);
$statement2->execute(array(':user_id' => $swrve_user_id));
$row = $statement2->fetch(PDO::FETCH_ASSOC);

return array(
    'swrve_user_id' => $swrve_user_id,
    'count_install' => $row['count_install'],
    'error' => 0,
    'message' => 'Success'
);
