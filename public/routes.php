<?php

// load Route Library
require_once '../lib/router.php';
// ##################################################
require_once 'bootstrap.php';
require_once 'head.php';

get($_SERVER['SITE_URL'], '../views/pages/index.php');


// Route for the login page
// ##################################################
// GET route for login
get($_SERVER['SITE_URL'].'/login', '../views/login/get_login.php');
// POST route for login
post($_SERVER['SITE_URL'].'/login', '../views/login/post_login.php');


/// Route for the machines page
// ##################################################
// GET route for machines
get($_SERVER['SITE_URL'].'/machines', '../views/machines/get_machines.php');
// POST route for machines
post($_SERVER['SITE_URL'].'/machines', '../views/machines/post_machines.php');
// PUT route for machines
put($_SERVER['SITE_URL'].'/machines', '../views/machines/put_machines.php');


// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404','../views/pages/index.php');