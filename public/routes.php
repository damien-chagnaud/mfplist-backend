<?php

// load Route Library
require_once '../lib/router.php';
// ##################################################
require_once 'bootstrap.php';
require_once 'head.php';

get('/', '../views/pages/index.php');


// Route for the login page
// ##################################################
// GET route for login
get('/login', '../views/login/get_login.php');
// POST route for login
post('/login', '../views/login/post_login.php');


/// Route for the machines page
// ##################################################
// GET route for machines
get('/machines', '../views/machines/get_machines.php');
// POST route for machines
post('/machines', '../views/machines/post_machines.php');
// PUT route for machines
put('/machines', '../views/machines/put_machines.php');
// GET route for machine service info page
get('/machines/infos', '../views/machines/infos.php');


// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404','../views/pages/index.php');