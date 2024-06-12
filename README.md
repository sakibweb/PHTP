# PHTP
PHTP is a PHP Library for handling Time-based One-Time Passwords (TOTP) and other OTP uses
```
// Example usages:

// 1. Generate a base32 encoded secret key
$secretData = PHTP::key(24);
if (!is_array($secretData)) {
    $secret = $secretData;
    echo "Generated Secret: $secret\n";

    // 2. Generate OTP with a default 30-second validity period
    $otpData = PHTP::code($secret);
    if (!is_array($otpData)) {
        echo "Generated OTP (30 sec): " . $otpData . "\n";
    }

    // 3. Generate OTP with a custom validity period (30 minutes)
    $otpData30min = PHTP::code($secret, 'TOTP', 6, 1800);
    if (!is_array($otpData30min)) {
        echo "Generated OTP (30 min): " . $otpData30min . "\n";
    }

    // 4. Generate a URI for TOTP setup
    $uriData = PHTP::url('account@example.com', $secret, 6, 30, 'ExampleIssuer', 'SHA1');
    if (!is_array($uriData)) {
        echo "Generated URI: " . $uriData . "\n";
    }
}
```
