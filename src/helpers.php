<?php

declare(strict_types=1);

namespace Rasim\PhpMvcCore;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Endroid\QrCode\Writer\QrCodeWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

if (!function_exists('Rasim\PhpMvcCore\flash')) {
    function flash(string $name = '', string $message = '', string $class = 'alert alert-success'): void
    {
        if (!empty($name)) {
            if (!empty($message) && empty($_SESSION[$name])) {
                if (!empty($_SESSION[$name])) {
                    unset($_SESSION[$name]);
                }
                if (!empty($_SESSION[$name . '_class'])) {
                    unset($_SESSION[$name . '_class']);
                }
                $_SESSION[$name] = $message;
                $_SESSION[$name . '_class'] = $class;
            } elseif (!empty($_SESSION[$name]) && empty($message)) {
                $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : 'success';
                echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
                unset($_SESSION[$name]);
                unset($_SESSION[$name . '_class']);
            }
        }
    }
}

if (!function_exists('Rasim\PhpMvcCore\redirect')) {
    function redirect(string $page): void
    {
        header('location: ' . (defined('URLROOT') ? URLROOT . '/' . $page : $page));
    }
}

if (!function_exists('Rasim\PhpMvcCore\getIPAddress')) {
    function getIPAddress(): string
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }
}

if (!function_exists('Rasim\PhpMvcCore\microTimeSet')) {
    function microTimeSet(): string
    {
        $parts = explode(' ', microtime());
        $usec = substr(str_replace('0.', '.', $parts[0]), 0, -2);
        return date('Ymd H:i:s', (int)$parts[1]) . $usec;
    }
}

if (!function_exists('Rasim\PhpMvcCore\writeLog')) {
    function writeLog(int $code, mixed $data): void
    {
        $log = microTimeSet() .
            '  {"User":{"Code":' . $code .
            ',"IP":"' . getIPAddress() . '"' .
            ',"Agent":"' . ($_SERVER['HTTP_USER_AGENT'] ?? '') . '"' .
            ',"Data":' . json_encode($data) .
            '}}' .
            PHP_EOL;
        $logsDir = './logs/';
        if (!is_dir($logsDir)) {
            mkdir($logsDir, 0755, true);
        }
        file_put_contents($logsDir . date('Ymd') . '.lg', $log, FILE_APPEND);
    }
}

if (!function_exists('Rasim\PhpMvcCore\getUrl')) {
    function getUrl(): string
    {
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return $scheme . '://' . $host . $uri;
    }
}

if (!function_exists('Rasim\PhpMvcCore\HTTPStatus')) {
    function HTTPStatus(int $code = 0, string $sts = '', string $forwardURL = '/', string $msg = '', mixed $data = null): void
    {
        http_response_code($code);
        if ($data === null) {
            $json_data = [
                'status' => $sts,
                'url' => getUrl(),
                'forwardURL' => $forwardURL,
                'message' => $msg,
            ];
        } else {
            $json_data = [
                'status' => $sts,
                'url' => getUrl(),
                'forwardURL' => $forwardURL,
                'message' => $msg,
                'data' => $data,
            ];
        }
        writeLog($code, $json_data);
        echo json_encode($json_data);
    }
}

if (!function_exists('Rasim\PhpMvcCore\jwtEncode')) {
    function jwtEncode(int $jwt_start_time, int $jwt_end_time, string $aud, array $user_arr_data): string
    {
        $payload = [
            'iss' => getUrl(),
            'iat' => time(),
            'nbf' => time() + $jwt_start_time,
            'exp' => time() + $jwt_end_time,
            'aud' => $aud,
            'data' => $user_arr_data,
        ];
        return JWT::encode($payload, defined('JWT_SECRET_KEY') ? JWT_SECRET_KEY : 'changeme', 'HS512');
    }
}

if (!function_exists('Rasim\PhpMvcCore\jwtDecode')) {
    function jwtDecode(string $jwt): array
    {
        $key = defined('JWT_SECRET_KEY') ? JWT_SECRET_KEY : 'changeme';
        return (array) JWT::decode($jwt, new Key($key, 'HS512'));
    }
}

if (!function_exists('Rasim\PhpMvcCore\generateRandomString')) {
    function generateRandomString(int $length = 20): string
    {
        $keys = array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z'));
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $key;
    }
}

if (!function_exists('Rasim\PhpMvcCore\console_log')) {
    function console_log(mixed $output, bool $with_script_tags = true): void
    {
        $js_code = "<script>console.log(" . json_encode($output, JSON_HEX_TAG) . ");</script>";
        if (!$with_script_tags) {
            $js_code = json_encode($output, JSON_HEX_TAG);
        }
        echo $js_code;
    }
}

if (!function_exists('Rasim\PhpMvcCore\generateQrCode')) {
    function generateQrCode(string $data, string $outputFile = 'qrcode.png'): void
    {
        $writer = new QrCodeWriter();
        $qrCode = $writer->write(
            $data,
            ErrorCorrectionLevel::High,
            new \Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin()
        );
        file_put_contents($outputFile, (string) $qrCode);
    }
}
