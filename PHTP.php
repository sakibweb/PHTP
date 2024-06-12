<?php
/**
 * PHTP is a PHP Library for handling Time-based One-Time Passwords (TOTP) and other OTP uses.
 * Supports multiple applications like Google Authenticator, Microsoft Authenticator, etc.
 *
 * @category Library
 * @package  PHTP
 * @url https://github.com/sakibweb
 */
class PHTP {
    private static $base32Map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Base32 decode a given string.
     *
     * @param string $in The base32 encoded string.
     * @return string The decoded string.
     */
    private static function base32Decode($in) {
        $length = strlen($in);
        $n = $bitShift = 0;
        $out = '';

        for ($i = 0; $i < $length; $i++) {
            $n = ($n << 5) + stripos(self::$base32Map, $in[$i]);
            $bitShift = ($bitShift + 5) % 8;
            if ($bitShift < 5) {
                $out .= chr(($n & (255 << $bitShift)) >> $bitShift);
            }
        }

        return $out;
    }

    /**
     * Generate a One-Time Password (OTP) or Time-based One-Time Password (TOTP).
     *
     * @param string $secret The base32 encoded secret key.
     * @param string $mode The mode, either 'TOTP' or 'OTP' (default is 'TOTP').
     * @param int $digits The number of digits in the OTP (default is 6).
     * @param int $time The time period in seconds (default is 30).
     * @param int $offset The time offset (default is 0).
     * @param string $algo The hash algorithm (default is 'sha1').
     * @return array|string The generated OTP or an error message.
     */
    public static function code($secret, $mode = 'TOTP', $digits = 6, $time = 30, $offset = 0, $algo = 'sha1') {
        if (strlen($secret) < 16 || strlen($secret) % 8 != 0) {
            return ['fail' => 'Length of secret must be a multiple of 8, and at least 16 characters'];
        } elseif (preg_match('/[^A-Z2-7]/i', $secret) === 1) {
            return ['fail' => 'Secret contains non-base32 characters'];
        }

        $digits = intval($digits);
        if ($digits < 6 || $digits > 8) {
            return ['fail' => 'Digits must be 6, 7, or 8'];
        } elseif (!in_array(strtolower($algo), ['sha1', 'sha256', 'sha512'])) {
            return ['fail' => 'Algorithm must be SHA1, SHA256, or SHA512'];
        }

        $seed = self::base32Decode($secret);
        if (strtoupper($mode) === 'TOTP') {
            $time = str_pad(pack('N', intval(($offset + time()) / $time)), 8, "\x00", STR_PAD_LEFT);
        } elseif (strtoupper($mode) === 'OTP') {
            $time = str_pad(pack('N', time() + $time), 8, "\x00", STR_PAD_LEFT);
        }

        $hash = hash_hmac(strtolower($algo), $time, $seed, false);
        $otp = (hexdec(substr($hash, hexdec($hash[-1]) * 2, 8)) & 0x7fffffff) % pow(10, $digits);

        return sprintf("%0{$digits}d", $otp);
    }

    /**
     * Generate a base32 encoded secret key.
     *
     * @param int $length The length of the secret (default is 24).
     * @return array|string The generated secret key or an error message.
     */
    public static function key($length = 24) {
        if ($length < 16 || $length % 8 !== 0) {
            return ['fail' => 'Length must be a multiple of 8, and at least 16'];
        }

        $secret = '';
        while ($length--) {
            $random = @gettimeofday()['usec'] % 53;
            while ($random--) {
                mt_rand();
            }
            $secret .= self::$base32Map[mt_rand(0, 31)];
        }

        return $secret;
    }

    /**
     * Generate a URI for the TOTP setup.
     *
     * @param string $account The account name.
     * @param string $secret The base32 encoded secret key.
     * @param int|null $digits The number of digits in the OTP.
     * @param int|null $time The time period in seconds.
     * @param string|null $issuer The issuer name.
     * @param string|null $algo The hash algorithm.
     * @return array|string The generated URI or an error message.
     */
    public static function url($account, $secret, $digits = null, $time = null, $issuer = null, $algo = null) {
        if (empty($account) || empty($secret)) {
            return ['fail' => 'You must provide at least an account and a secret'];
        } elseif (strpos($account . $issuer, ':') !== false) {
            return ['fail' => 'Neither account nor issuer can contain a colon (:) character'];
        }

        $account = rawurlencode($account);
        $issuer = rawurlencode($issuer);
        $label = empty($issuer) ? $account : "$issuer:$account";

        return 'otpauth://totp/' . $label . "?secret=$secret" . (is_null($algo) ? '' : "&algorithm=$algo") .
                (is_null($digits) ? '' : "&digits=$digits") . (is_null($time) ? '' : "&period=$time") . (empty($issuer) ? '' : "&issuer=$issuer");
    }
}
?>
