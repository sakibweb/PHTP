# PHTP
# PHTP - PHP Time-based One-Time Password (TOTP) Library

PHTP is a lightweight PHP library for generating and verifying Time-based One-Time Passwords (TOTP) and standard One-Time Passwords (OTP).  It's designed to be easy to use and integrate into your PHP applications for enhanced security.  PHTP is compatible with popular authenticator apps like Google Authenticator and Microsoft Authenticator.

## Features

* **TOTP and OTP Generation:** Create secure time-based and standard OTPs.
* **OTP Verification:**  Verify user-submitted OTPs against the secret key.
* **Customizable OTP Length and Algorithm:** Control the number of digits and hashing algorithm (SHA1, SHA256, SHA512).
* **Time Drift Tolerance (TOTP):**  Handle slight time discrepancies between server and client.
* **OTP Expiration (OTP):** Implement time-limited OTPs.
* **URI Generation:** Create setup URIs for easy integration with authenticator apps.
* **Error Handling:**  Provides clear error messages for invalid inputs or other issues.


## Installation

Simply include the `PHTP.php` file in your project:

```php
require_once 'PHTP.php';
```


## Usage


### 1. Generating a Secret Key

The first step is to generate a secret key. This key should be stored securely for each user and used for both generating and verifying OTPs.

```php
$keyResult = PHTP::key(24); // Generates a 24-character secret key for TOTP

if ($keyResult['status']) {
  $secret = $keyResult['data'];
  echo "Secret Key: " . $secret;  // Store this securely!
} else {
  echo "Error: " . $keyResult['message'];
}


// For OTP mode, specify the mode:
$otpKeyResult = PHTP::key(24, 'OTP');

if ($otpKeyResult['status']) {
    $otpSecret = $otpKeyResult['data'];
    echo "OTP Secret: ".$otpSecret; // Store this securely for OTP usage
} else {
    echo "Error: ".$otpKeyResult['message'];
}


```

The `key()` function takes an optional `$length` parameter (default 24) which must be a multiple of 8 and at least 16. It also takes an optional `$mode` parameter with 'TOTP' as default, or 'OTP'.


### 2. Generating an OTP

Once you have a secret key, you can generate an OTP.

**TOTP:**

```php
$codeResult = PHTP::code($secret); // $secret is the base32 encoded secret key

if ($codeResult['status']) {
  $otp = $codeResult['data'];
  echo "TOTP: " . $otp;
} else {
  echo "Error: " . $codeResult['message'];
}
```

**OTP:**
Requires `PHTM` library. Assuming you have the `PHTM` class, and it handles date/time operations with functions like `setTime` and `calculate`.

```php
$otpCodeResult = PHTP::code($otpSecret, 'OTP'); // $otpSecret from Step 1

if ($otpCodeResult['status']) {
    $otp = $otpCodeResult['data'];
    echo "OTP: " . $otp;
} else {
    echo "Error: " . $otpCodeResult['message'] . ". New OTP: ".$otpCodeResult['data']; // Handle expiry and get new OTP
}
```


**Optional parameters for `code()`:**

* `$mode`:  'TOTP' (default) or 'OTP'.
* `$digits`: The number of digits in the OTP (default 6).
* `$time`: The time interval in seconds for TOTP (default 30).  For OTP, it adds this time to the creation time embedded in the `$otpSecret`.
* `$offset`: Time offset in seconds to account for drift (default 0). Use only with `TOTP` mode.
* `$algo`: Hashing algorithm: 'sha1' (default), 'sha256', or 'sha512'.



### 3. Verifying an OTP

To verify a user-submitted OTP:

```php
$userOtp = $_POST['otp']; // Get the OTP from the user


$verifyResult = PHTP::verify($userOtp, $secret); // Or use $otpSecret for OTP mode


if ($verifyResult['status']) {
  echo "OTP is valid!";
} else {
  echo "Invalid OTP: " . $verifyResult['message'];
//  print_r($verifyResult); // Debugging (show returned error if needed).
}
```


The `verify()` function takes the same optional parameters as `code()`:  `$mode`, `$digits`, `$time`, `$offset`, and `$algo`. Be sure to use the same parameters for verification that you used for generation.


### 4. Generating a Setup URI

PHTP can generate a URI that can be encoded as a QR code for easy setup with authenticator apps.

```php
$urlResult = PHTP::url('user@example.com', $secret, 6, 30, 'My App'); // TOTP


if ($urlResult['status']) {
    $uri = $urlResult['data'];
    echo "Setup URI: " . $uri;  // Display as QR code or link
} else {
    echo "Error: ".$urlResult['message'];
}

```

**Parameters for `url()`:**

* `$account`: The user's account name (e.g., email).
* `$secret`: The base32 encoded secret key.
* `$digits`:  OTP length (optional).
* `$time`: Time interval (optional).
* `$issuer`: The name of your app/service (optional).
* `$algo`: The HMAC hash algorithm (optional).


## Error Handling

All PHTP functions return an associative array with the following structure:

```
[
  'status' => (bool) true/false indicating success or failure,
  'message' => (string)  A message describing the result or error,
  'data' => (mixed) The generated OTP, secret key, or URI on success, or additional error data on failure.
]
```

Always check the `status` element to determine if the operation was successful.


## Contributing

Contributions are welcome! Please submit pull requests via GitHub at [PHTP GitHub](https://github.com/sakibweb).


## License

This library is open-sourced under the [MIT License](LICENSE).
