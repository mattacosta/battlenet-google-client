Battle.net for Google API Clients
=================================

Extends the Google APIs Client Library to enable use with the Battle.net API
service.

This allows the use of the [OAuth 2.0 protocol] [1] for Google APIs and
Battle.net APIs without the need of another library built from scratch.
Additionally, a service class is also provided to access Battle.net profile
information using the client library.

## Requirements
 - [Google APIs Client Library for PHP] [2]

## Example
```php
$config = new BattleNet_Config('us');
$client = new Google_Client($config);
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUrl($redirect_uri);
$client->addScope(array('wow.profile', 'sc2.profile'));

// Authorization example:

// 1. Create a link to start the authorization flow.
$client->setState($csrf_token);
$client->createAuthUrl();
// 2. Then check the state and authenticate the user.
$client->authenticate($code);

// Service example:

// 1. Create a service and get profile information.
$service = new BattleNet_Service_Account($client);
$data = $service->profile->getStarcraftProfile();
```

[1]: http://tools.ietf.org/html/draft-ietf-oauth-v2-22
[2]: https://github.com/google/google-api-php-client
