<?php
// =======================================================
// 1. ZMIEŃ NA ADRES E-MAIL, NA KTÓRY MAJĄ PRZYCHODZIĆ LEADY:
$recipient = "biuro@finansebezstresu.pl"; // <--- ZMIEŃ TO!
// =======================================================

// 2. ZMIEŃ NA ADRES E-MAIL Z TWOJEJ DOMENY (np. formularz@finansebezstresu.pl)
// Poprawia dostarczalność i zapobiega, by e-maile były oznaczane jako spam
$sender_email = "biuro@finansebezstresu.pl"; // <--- ZMIEŃ TO!

// Nazwa pliku HTML (do przekierowania)
$return_page = "index (4).html"; // Używam nazwy Twojego pliku, upewnij się, że tak się nazywa na serwerze

// Użyj kodowania UTF-8
header('Content-Type: text/html; charset=utf-8');

// 1. Sprawdzenie, czy formularz został wysłany metodą POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Pobranie i wstępne czyszczenie danych
    // strip_tags() usuwa znaczniki HTML
    // trim() usuwa białe znaki
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone = strip_tags(trim($_POST["phone"])); 
    $message = trim($_POST["message"]);
    
    // 3. Weryfikacja danych
    if ( empty($name) OR empty($message) OR empty($phone) OR !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
        // Jeśli brakuje danych lub e-mail jest niepoprawny
        header("Location: $return_page?status=error_data");
        exit;
    }

    // 4. Budowanie treści wiadomości
    $subject = "NOWY LEAD ZE STRONY - " . $name;
    
    // Treść e-maila w formacie tekstowym
    $email_content = "Otrzymano nowe zapytanie z formularza kontaktowego:\n\n";
    $email_content .= "Imię i Nazwisko: $name\n";
    $email_content .= "E-mail: $email\n";
    $email_content .= "Telefon: $phone\n\n";
    $email_content .= "Wiadomość:\n--------------------\n$message\n--------------------";

    // 5. Budowanie nagłówków wiadomości
    // Ustawienie Reply-To na adres klienta (kluczowe, aby móc mu odpisać)
    $email_headers = "From: Formularz Kontaktowy <$sender_email>\r\n";
    $email_headers .= "Reply-To: $email\r\n";
    $email_headers .= "MIME-Version: 1.0\r\n";
    $email_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // 6. Wysyłanie e-maila
    if (mail($recipient, "=?UTF-8?B?".base64_encode($subject)."?=", $email_content, $email_headers)) {
        // Sukces
        header("Location: $return_page?status=success");
        exit;
    } else {
        // Błąd wysyłki
        header("Location: $return_page?status=error_send");
        exit;
    }

} else {
    // Jeśli ktoś wszedł na ten plik bezpośrednio
    header("Location: $return_page");
    exit;
}
?>
