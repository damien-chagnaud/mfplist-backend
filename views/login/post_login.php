<?php
require_once '../lib/credentials.php';
require_once '../lib/logger.php';

$contenType = filter_input(INPUT_SERVER , 'CONTENT_TYPE',FILTER_SANITIZE_STRING);
$verify = false;
$response_code = 200;
header("Cache-Control: no-cache");

function getRateLimitFile($key)
{
    return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mfplist_login_rate_' . md5($key) . '.json';
}

function getRateLimitAttempts($key, $windowSeconds)
{
    $file = getRateLimitFile($key);
    if (!file_exists($file)) {
        return array();
    }

    $raw = file_get_contents($file);
    $attempts = json_decode($raw, true);
    if (!is_array($attempts)) {
        return array();
    }

    $cutoff = time() - $windowSeconds;
    return array_values(array_filter($attempts, function ($ts) use ($cutoff) {
        return is_int($ts) && $ts >= $cutoff;
    }));
}

function isRateLimited($key, $maxAttempts, $windowSeconds)
{
    $attempts = getRateLimitAttempts($key, $windowSeconds);
    return count($attempts) >= $maxAttempts;
}

function addRateLimitAttempt($key, $windowSeconds)
{
    $attempts = getRateLimitAttempts($key, $windowSeconds);
    $attempts[] = time();
    file_put_contents(getRateLimitFile($key), json_encode($attempts), LOCK_EX);
}

function clearRateLimit($key)
{
    $file = getRateLimitFile($key);
    if (file_exists($file)) {
        @unlink($file);
    }
}

try{
    if (str_contains($contenType,'application/json')) {
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        $email = (isset($data->email))? $data->email: '';
        $password = (isset($data->password))? $data->password: '';
        if (is_string($email) && is_string($password) && $email !== '' && $password !== '') {
            $verify = true;
        }
    }
} catch (Exception $e) {
    Logger::safeError('post_login payload parsing failed.', array('exception' => $e->getMessage()));
    $response_code = 400;
}

if($verify) {
    $emailKey = strtolower(trim($email));
    $rateKey = ($emailKey !== '' ? $emailKey : 'noemail') . '|' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $maxAttempts = 8;
    $windowSeconds = 900;

    if (isRateLimited($rateKey, $maxAttempts, $windowSeconds)) {
        echo json_encode(['message' => 'Too many attempts. Try again later.']);
        $response_code = 429;
        header("Content-Type: application/json; charset=UTF-8", true, $response_code);
        exit;
    }

    $cred = new Credentials();
    $uid = $cred->checkCredentials($email, $password);
    if($uid!==false){
        $token = $cred->generateToken($uid);
        clearRateLimit($rateKey);
        echo json_encode(['message' => 'successful', 'token' => $token, 'user_id' => $uid]);
        $response_code = 200;
    } else {
        addRateLimitAttempt($rateKey, $windowSeconds);
        echo json_encode(['message' => 'failed']);
        $response_code = 401;
    }

} else {
    $response_code = 420;
}

header("Content-Type: application/json; charset=UTF-8", true, $response_code);