<?php
declare(strict_types=1);

/** İletişim sayfası: formu doğrular, mesajı kaydeder ve e-posta iletmeyi dener. */
function handleContactPage(): void
{
    $site = siteContent();
    $errors = [];
    $sent = false;
    $old = ['name' => '', 'phone' => '', 'email' => '', 'message' => ''];

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        foreach ($old as $key => $_) {
            $old[$key] = trim((string) ($_POST[$key] ?? ''));
        }

        // Honeypot: botlar doldurur, insanlar görmez
        if (trim((string) ($_POST['company_website'] ?? '')) !== '') {
            $sent = true; // bota başarı gösterip mesajı yok say
        } else {
            if ($old['name'] === '') {
                $errors[] = 'Lütfen adınızı yazın.';
            }
            if ($old['phone'] === '' && $old['email'] === '') {
                $errors[] = 'Telefon veya e-posta alanlarından en az birini doldurun.';
            }
            if ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'E-posta adresi geçersiz görünüyor.';
            }
            if ($old['message'] === '') {
                $errors[] = 'Lütfen mesajınızı yazın.';
            }

            if ($errors === []) {
                $sent = storeContactMessage($old) && true;
                if (!$sent) {
                    $errors[] = 'Mesaj kaydedilemedi, lütfen telefonla ulaşın.';
                } else {
                    tryMailContactMessage($site['settings'], $old);
                    $old = ['name' => '', 'phone' => '', 'email' => '', 'message' => ''];
                }
            }
        }
    }

    renderPage('contact', [
        'pageTitle' => 'İletişim',
        'errors' => $errors,
        'sent' => $sent,
        'old' => $old,
    ]);
}

/** Mesajı data/messages.json'a ekler (posta çalışmasa da kayıt kalsın). */
function storeContactMessage(array $fields): bool
{
    $file = DATA_DIR . '/messages.json';
    $messages = [];
    if (is_file($file)) {
        $decoded = json_decode((string) file_get_contents($file), true);
        if (is_array($decoded)) {
            $messages = $decoded;
        }
    }
    $messages[] = $fields + ['date' => date('c'), 'ip' => $_SERVER['REMOTE_ADDR'] ?? ''];
    $json = json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return $json !== false && file_put_contents($file, $json, LOCK_EX) !== false;
}

/** Hosting mail() destekliyorsa bildirimi info adresine yollar; başarısızlık sessizce geçilmez, log düşer. */
function tryMailContactMessage(array $settings, array $fields): void
{
    $to = $settings['email'] ?? '';
    if ($to === '' || !function_exists('mail')) {
        return;
    }
    $body = "Yeni teklif/iletişim mesajı:\n\n"
        . "Ad: {$fields['name']}\nTelefon: {$fields['phone']}\nE-posta: {$fields['email']}\n\n"
        . "Mesaj:\n{$fields['message']}\n";
    $headers = 'From: site@crcvinc.com' . "\r\n" . 'Content-Type: text/plain; charset=UTF-8';
    if (!@mail($to, 'CRC Vinç — Yeni İletişim Mesajı', $body, $headers)) {
        error_log('crcvinc: iletisim e-postasi gonderilemedi (mesaj messages.json icinde sakli).');
    }
}
