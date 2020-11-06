<?php
include 'vendor/autoload.php';

/**
 * To create Child Accounts you need a PrintNode Integrator Account. 
 * You then need to authenticate with your API Key.
 **/

$credentials = new \PrintNode\Credentials\ApiKey('YOUR_API_KEY');

$request = new \PrintNode\Request($credentials);

// Initialise a Child Account
/** @var \PrintNode\Entity\Account $account */
$account = \PrintNode\Entity::makeFromResponse($request, \PrintNode\Entity\Account::class, (object)[
    "firstname" => "A",
    "lastname" => "ALastName",
    "password" => "superStrongPassword",
    "email" => "email@example.com"
]);

// Post the Child Account to the API
$aNewAccount = $request->createChildAccount($account, $apiKeys = [], $tags = []);

// You can get the Child Account ID from the response object
$id = $aNewAccount->Account->id;
