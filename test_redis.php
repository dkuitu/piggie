<?php
// These three lines would be executed by *all* scripts that use the Redis server.

// Create new instance of Redis server
$redis = new Redis();

// Connect to IP and Port
$redis->connect('10.10.10.3', 6379);
$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);

// These lines demonstrate the three main Redis functions you will be using.

$redis->set('testKey1', 100);

if ($redis->exists('testKey1')) {
    $val = $redis->get('testKey1');
    echo $val;
} else {
    echo 'Key did not exist.';
}

?>
