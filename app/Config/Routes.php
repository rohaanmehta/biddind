<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('/marketplace', 'Home::marketplace');
$routes->get('/marketplace/load-more', 'Home::loadMoreProducts');
$routes->get('/sitemap.xml', 'Home::sitemap');
$routes->get('/robots.txt', 'Home::robotsTxt');
$routes->get('/product/(:num)', 'Home::productDetails/$1');
$routes->get('/how-it-works', 'Home::howItWorks');
$routes->get('/upload-products', 'Home::uploadProducts');
$routes->get('/payment-successful', 'Home::paymentSuccessful');
$routes->post('/payment-successful', 'Home::paymentSuccessful');
$routes->get('/payment-failed', 'Home::paymentFailed');
$routes->post('/payment-failed', 'Home::paymentFailed');
$routes->get('/my-products', 'Product::myProducts');
$routes->get('/product/bids/(:num)', 'Product::productBids/$1');
$routes->get('/my-bids', 'Product::myBids');
$routes->get('/transactions', 'Home::transactions');
$routes->get('/buy-credits', 'Home::buyCredits');

$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/register', 'Auth::register');
$routes->post('/auth/register', 'Auth::register');
$routes->get('/auth/logout', 'Auth::logout');
$routes->post('/auth/logout', 'Auth::logout');
$routes->get('/auth/verify-otp', 'Auth::verifyOtp');
$routes->post('/auth/verify-otp', 'Auth::verifyOtp');
$routes->get('/edit-profile', 'Auth::editProfile');
$routes->post('/edit-profile', 'Auth::updateProfile');
$routes->post('/product/upload', 'Product::upload');
$routes->get('/product/edit/(:num)', 'Product::edit/$1');
$routes->post('/product/update/(:num)', 'Product::update/$1');
$routes->post('/product/place-bid', 'Product::placeBid');
$routes->post('/credits/purchase', 'Home::purchaseCredits');
$routes->post('/product/delete/(:num)', 'Product::delete/$1');
$routes->set404Override(static function () {
    $response = service('response');
    $response->setStatusCode(404);
    echo view('errors/html/error_404');
    return '';
});
$routes->get('/404', 'Home::pageNotFound');
