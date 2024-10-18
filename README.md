# PHTP
## PHTP - PHP Time-based One-Time Password Library

PHTP is a lightweight PHP library for generating and validating Time-based One-Time Passwords (TOTP) and other OTPs. It is designed to work seamlessly with authenticator applications such as Google Authenticator, Microsoft Authenticator, and others.

## Features
- Base32 encoding and decoding
- Support for generating One-Time Passwords (OTPs) and Time-based OTPs (TOTP)
- Configurable digits, time period, hash algorithms (SHA1, SHA256, SHA512)
- Generate secret keys for TOTP setup
- Generate URIs for TOTP setup compatible with major authenticator apps

## Installation
Simply include the `PHTP` class in your project:
```php
require_once 'path/to/PHTP.php';
```

## Usage

### 1. Generate a One-Time Password (OTP)
You can easily generate a TOTP or OTP using the `code` function. This function supports multiple hash algorithms and customizable time periods.

```php
$secret = 'JBSWY3DPEHPK3PXP'; // Base32 encoded secret key
$otp = PHTP::code($secret);
echo "Generated OTP: " . $otp;
```

**Parameters:**
- `$secret` (string) - Base32 encoded secret.
- `$mode` (string) - Either 'TOTP' or 'OTP' (default: 'TOTP').
- `$digits` (int) - Number of digits for the OTP (default: 6).
- `$time` (int) - Time period in seconds (default: 30).
- `$offset` (int) - Time offset in seconds (default: 0).
- `$algo` (string) - Hashing algorithm: 'sha1', 'sha256', or 'sha512' (default: 'sha1').

**Example:**
```php
$otp = PHTP::code('JBSWY3DPEHPK3PXP', 'TOTP', 6, 30, 0, 'sha1');
echo "Your OTP: $otp";
```

### 2. Generate a Secret Key
You can generate a base32 encoded secret key that will be used by the TOTP algorithm. This secret can be shared with the user’s authenticator app.

```php
$secret = PHTP::key();
echo "Generated Secret Key: " . $secret;
```

**Parameters:**
- `$length` (int) - Length of the secret key (default: 24).

**Example:**
```php
$secret = PHTP::key(24);
echo "Generated Secret: $secret";
```

### 3. Generate a TOTP Setup URI
Generate a URI that can be scanned by Google Authenticator, Microsoft Authenticator, or other TOTP-compatible apps to easily set up two-factor authentication.

```php
$account = 'user@example.com';
$secret = 'JBSWY3DPEHPK3PXP';
$uri = PHTP::url($account, $secret);
echo "TOTP Setup URI: " . $uri;
```

**Parameters:**
- `$account` (string) - Account name (e.g., email or username).
- `$secret` (string) - Base32 encoded secret key.
- `$digits` (int) - Number of digits for the OTP (optional).
- `$time` (int) - Time period in seconds (optional).
- `$issuer` (string) - Issuer name (optional).
- `$algo` (string) - Hashing algorithm (optional).

**Example:**
```php
$uri = PHTP::url('user@example.com', 'JBSWY3DPEHPK3PXP', 6, 30, 'YourApp', 'sha1');
echo "Scan this QR code: " . $uri;
```

### 4. Base32 Encoding and Decoding
If you need to encode or decode data in base32 format, PHTP provides simple methods to handle this.

```php
$encoded = PHTP::base32Encode('Some data');
$decoded = PHTP::base32Decode($encoded);
```

### Error Handling
PHTP provides basic error checking. If invalid inputs are provided, functions will return an error message in the form of an array:

```php
$otp = PHTP::code('InvalidSecret');
if (isset($otp['fail'])) {
    echo "Error: " . $otp['fail'];
}
```

---

## License
This library is open-sourced under the [MIT License](LICENSE).

## Contributions
Contributions are welcome! Please submit pull requests via GitHub at [PHTP GitHub](https://github.com/sakibweb).
