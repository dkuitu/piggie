<?php

// These three lines would be executed by *all* scripts that use the Redis server.

// Create a new Redis instance
$redis = new Redis();

// Connect new Redis instance to IP and port
$redis->connect('10.10.10.3', 6379);
$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);

?>
