<?php

use Flarum\Database\Migration;

return Migration::addColumns('users', [
    'last_ip_address' => ['string', 'length' => 64, 'nullable' => true, 'index' => true],
]);