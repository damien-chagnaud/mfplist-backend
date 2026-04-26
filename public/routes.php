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


/// Route for the clients page
// ##################################################
// GET route for clients
get($_SERVER['SITE_URL'].'/clients', '../views/clients/get_clients.php'); 
//GET route for clients synctime
get($_SERVER['SITE_URL'].'/clients/synctime', '../views/clients/get_clients_synctime.php');
// POST route for clients
post($_SERVER['SITE_URL'].'/clients', '../views/clients/post_clients.php');


/// Route for the contacts page
// ##################################################
// GET route for contacts
get($_SERVER['SITE_URL'].'/contacts', '../views/contacts/get_contacts.php'); 
//GET route for contacts synctime
get($_SERVER['SITE_URL'].'/contacts/synctime', '../views/contacts/get_contacts_synctime.php');
// POST route for contacts
post($_SERVER['SITE_URL'].'/contacts', '../views/contacts/post_contacts.php');

// Route for devices page
// ##################################################
// GET route for devices
get($_SERVER['SITE_URL'].'/devices', '../views/devices/get_devices.php');
// GET route for devices synctime
get($_SERVER['SITE_URL'].'/devices/synctime', '../views/devices/get_devices_synctime.php');
// POST route for devices
post($_SERVER['SITE_URL'].'/devices', '../views/devices/post_devices.php');


/*

// Dynamic GET. Example with 1 variable
// The $id will be available in user.php
get('/user/$id', '../views/user.php');

// Dynamic GET. Example with 2 variables
// The $name will be available in full_name.php
// The $last_name will be available in full_name.php
// In the browser point to: localhost/user/X/Y
get('/user/$name/$last_name', 'views/full_name.php');

// Dynamic GET. Example with 2 variables with static
// In the URL -> http://localhost/product/shoes/color/blue
// The $type will be available in product.php
// The $color will be available in product.php
get('/product/$type/color/$color', 'product.php');

// A route with a callback
get('/callback', function(){
  echo 'Callback executed';
});

// A route with a callback passing a variable
// To run this route, in the browser type:
// http://localhost/user/A
get('/callback/$name', function($name){
  echo "Callback executed. The name is $name";
});

// Route where the query string happends right after a forward slash
get('/product', '');

// A route with a callback passing 2 variables
// To run this route, in the browser type:
// http://localhost/callback/A/B
get('/callback/$name/$last_name', function($name, $last_name){
  echo "Callback executed. The full name is $name $last_name";
});

// ##################################################
// ##################################################
// ##################################################
// Route that will use POST data
post('/user', '/api/save_user');



// ##################################################
// ##################################################
// ##################################################
// any can be used for GETs or POSTs
*/


// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404','../views/pages/index.php');