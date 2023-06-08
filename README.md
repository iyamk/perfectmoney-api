# perfectmoney-api

## Complete library for Perfect money

This library is suitable for any project and aims to be simple and easy to use.

## Installation

```
composer require iyamk/perfectmoney-api
```

## Usage

First you need a configuration file. Create a config_pm.php file in a convenient location:
```
<?php

# If not loaded before
require_once __DIR__ . '/vendor/autoload.php';

# Instance creation
$pm = new \PM\Api('your wallet number', 'alternate passphrase', 'https://example.com/status_url', 'https://example.com/done_url', 'https://example.com/fail_url', 'My corp.', 'your account id', 'account password');

```

Now, in the place where the payment form is displayed, add:
```
require_once __DIR__.'/config_pm.php';

echo $pm->getForm('your order id', '1.23', 'description payment');
```

This will display a form, click Pay and make your payment

Then add the following code to the point of the payment status handler:
```
require_once __DIR__.'/config_pm.php';

$r = $pm->checkPay();
if ($r['status'])
{
	// The request has been validated and can be trusted, now populate the value into the database and make the necessary checks
	print_r($r);
}
else
{
	// An error has occurred, you can write it to the log
	echo $r['error'];
}
```

Enjoy it!

Request balances:

```
$r = $pm->getBalance();
print_r($r);
```

# Documentation

Here: https://iyamk.github.io/perfectmoney-api/

# Contributing

Contribute with us via pull request