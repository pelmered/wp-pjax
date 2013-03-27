<?php

include_once('phpconsole.php');

$phpconsole = new Phpconsole();
$phpconsole->set_backtrace_depth(1);

/*
==============================================
USER'S SETTINGS
==============================================
*/

$phpconsole->set_domain('.elmered.com');  // don't forget to use leading dot, like so: .your-domain.com
$phpconsole->add_user('peter', 'EzmlMeachLn9YLaFQ7G4dLwla6gAIIHoVlYBdoLW7l8aLk3zQ68N09WJqGkJBH37', 'fb0J1MLfhXKN2XqEjceI3P96OG8wB6iIbw6AxoywOR2Jx6PDrdrWvKGVN5nrqBrV'); // you can add more developers, just execute another add_user()




function phpconsole($data_sent, $user = false) {
    global $phpconsole;
    return $phpconsole->send($data_sent, $user);
}

function phpcounter($number = 1, $user = false) {
    global $phpconsole;
    $phpconsole->count($number, $user);
}

function phpconsole_cookie($name) {
    global $phpconsole;
    $phpconsole->set_user_cookie($name);
}

function phpconsole_destroy_cookie($name) {
    global $phpconsole;
    $phpconsole->destroy_user_cookie($name);
}
