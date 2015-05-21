# haravan.php

Lightweight multi-paradigm PHP (JSON) client for the [Haravan API](http://api.haravan.com/).


## Requirements

* PHP 4 with [cURL support](http://php.net/manual/en/book.curl.php).


## Getting Started

Basic needs for authorization and redirecting

```php
<?php

	require 'haravan.php';
	if (isset($_GET['code'])) { // if the code param has been sent to this page... we are in Step 2
		// Step 2: do a form POST to get the access token
		$haravanClient = new HaravanClient($_GET['shop'], "", HARAVAN_API_KEY, HARAVAN_SECRET);
		session_unset();
		
		// Now, request the token and store it in your session.
		$_SESSION['token'] = $haravanClient->getAccessToken($_GET['code'], REDIRECT_URI);
		if ($_SESSION['token'] != '')
			$_SESSION['shop'] = $_GET['shop'];
	
		header("Location: index.php");
		exit;		
	}
	// if they posted the form with the shop name
	 else if (isset($_POST['shop']) || isset($_GET['shop'])) {
	
		// Step 1: get the shopname from the user and redirect the user to the
		// haravan authorization page where they can choose to authorize this app
		$shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
		$haravanClient = new HaravanClient($shop, "", HARAVAN_API_KEY, HARAVAN_SECRET);
	
		
		// redirect to authorize url
		header("Location: " . $haravanClient->getAuthorizeUrl(HARAVAN_SCOPE, REDIRECT_URI));
		exit;
	}
	
	// first time to the page, show the form below
?>
	<p>Install this app in a shop to get access to its private admin data.</p> 
 
	<p style="padding-bottom: 1em;">
		<span class="hint">Don&rsquo;t have a shop to install your app in handy? <a href="https://app.haravan.com/services/partners/">Create a test shop.</a></span>
	</p> 
	 
	<form action="" method="post">
	  <label for='shop'><strong>The URL of the Shop</strong> 
	    <span class="hint">(enter it exactly like this: myshop.myharavan.com)</span> 
	  </label> 
	  <p> 
	    <input id="shop" name="shop" size="45" type="text" value="" /> 
	    <input name="commit" type="submit" value="Install" /> 
	  </p> 
	</form>

```

Once you have authorized and stored the token in the session, you can make API calls

Making API calls:

```php
<?php

	require 'haravan.php';

	$sc = new HaravanClient($_SESSION['shop'], $_SESSION['token'], $api_key, $secret);

	try
	{
		// Get all products
		$products = $sc->call('GET', '/admin/products.json', array('published_status'=>'published'));

	}
	catch (HaravanApiException $e)
	{
		/* 
		 $e->getMethod() -> http method (GET, POST, PUT, DELETE)
		 $e->getPath() -> path of failing request
		 $e->getResponseHeaders() -> actually response headers from failing request
		 $e->getResponse() -> curl response object
		 $e->getParams() -> optional data that may have been passed that caused the failure

		*/
	}
	catch (HaravanCurlException $e)
	{
		// $e->getMessage() returns value of curl_errno() and $e->getCode() returns value of curl_ error()
	}
?>
```

When receiving requests from the Haravan API, validate the signature value:

```php
<?php

    $sc = new HaravanClient($_GET['shop'], '', HARAVAN_API_KEY, HARAVAN_SECRET);

    if(!$sc->validateSignature($_GET))
        die('Error: invalid signature.');

?>
```
