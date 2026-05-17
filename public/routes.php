<?php

// load Route Library
require_once '../lib/router.php';
// ##################################################
require_once 'bootstrap.php';
require_once 'head.php';

// ROUTES:

// Route for the home page
// ##################################################
// GET route for home page
get('/', '../views/pages/index.php');


// Route for the login page
// ##################################################
// GET route for login
get('/login', '../views/login/get_login.php');
// POST route for login
post('/login', '../views/login/post_login.php');
// POST route for token verification
post('/login/verify', '../views/login/post_verify.php');




/// Route for the machines page
// ##################################################
// GET route for machines
get('/machines', '../views/machines/get_machines.php');
// POST route for machines
post('/machines', '../views/machines/post_machines.php');
// PUT route for machines
put('/machines', '../views/machines/put_machines.php');
//if debug mode is enabled, add the infos endpoint for machines
if (getenv('DEBUG_MODE') === 'true') {
    get('/machines/infos', '../views/machines/get_machines_infos.php');
}


/// Route for the users page
// ##################################################
// GET route for users
get('/users', '../views/users/get_users.php');
// GET route for a single user by uuid
get('/users/$uuid', '../views/users/get_users_by_uuid.php');
// POST route for users
post('/users', '../views/users/post_users.php');
// PUT route for users
put('/users', '../views/users/put_users.php');
// DELETE route for users by uuid
delete('/users/$uuid', '../views/users/delete_users.php');


// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404','../views/pages/index.php');