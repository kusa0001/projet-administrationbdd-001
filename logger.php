<?php
require 'vendor/autoload.php';
use MongoDB\Client;
function log_action($user_id, $action, $target, $status = "success") {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $timestamp = new DateTime();
    $log = [
        "user_id"   => $user_id,
        "action"    => $action,
        "target"    => $target,
        "timestamp" => $timestamp->format(DateTime::ATOM),
        "ip"        => $ip,
        "status"    => $status
    ];
    $log_file = __DIR__ . "/logs/actions.json";
    if (!is_dir(dirname($log_file))) mkdir(dirname($log_file), 0777, true);
    file_put_contents($log_file, json_encode($log, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
    try {
        $client = new Client("mongodb://localhost:27017");
        $collection = $client->projet_logs->activity_logs;
        $log_mongo = $log;
        $log_mongo['timestamp'] = new MongoDB\BSON\UTCDateTime($timestamp->getTimestamp()*1000);
        $collection->insertOne($log_mongo);
    } catch (Exception $e) {
        error_log("MongoDB log error: " . $e->getMessage());
    }
}
?>