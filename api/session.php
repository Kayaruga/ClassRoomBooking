<?php
require_once __DIR__ . '/config.php';
$user = currentUser();
respond(200, ['loggedIn' => (bool) $user, 'user' => $user]);
