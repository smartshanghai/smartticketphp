<?php

namespace smartshanghai\smartticketphp;

chdir(__DIR__);
require_once('../vendor/autoload.php');

// Fill out these fields, then run the file to watch your provided e-ticket get verified.
$apikey = '';

$ticketToken = '';
$ticketSecret = '';

$verificationClientUsername = '';
$verificationClientPassword = '';

// Instantiate smtk wrapper object.
$smtk = new SmartTicket($apikey);

// Show some ticket metadata
$ticketResponse = $smtk->getElectronicTicket($ticketToken, $ticketSecret);
var_dump($ticketResponse);

// Check whether the ticket is already verified or not.
$ticketIsAlreadyVerified = $smtk->electronicTicketIsVerified($ticketToken, $ticketSecret);
echo 'Ticket is verified?: '.($ticketIsAlreadyVerified ? 'yes' : 'no')."\n\n";

//
$response = $smtk->validateElectronicTicket($ticketToken, $ticketSecret, $verificationClientUsername, $verificationClientPassword);
var_dump($response);

// What about now? is the ticket verified?
$ticketIsAlreadyVerified = $smtk->electronicTicketIsVerified($ticketToken, $ticketSecret);
echo 'Ticket is verified?: '.($ticketIsAlreadyVerified ? 'yes' : 'no')."\n\n";