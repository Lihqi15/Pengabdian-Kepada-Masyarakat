<?php
/********************************************
 * CONFIG DATABASE
 ********************************************/
$host = "localhost";
$user = "root";        // sesuaikan
$pass = "";            // sesuaikan
$db   = "smp_porisindah"; // sesuaikan

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

/********************************************
 * JIKA FORM DIKIRIM
 ********************************************/
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /********************************************
     * ANTI-SPAM (HONEYPOT)
     ********************************************/
    if (!empty($_POST['website'])) { // field tersembunyi untuk bot
        die("Spam terdeteksi.");
    }

    // Mengamankan data dari form
    $name    = htmlspecialchars(mysqli_real_escape_string($conn, $_POST["name"]));
    $email   = htmlspecialchars(mysqli_real_escape_string($conn, $_POST["email"]));
    $message = htmlspecialchars(mysqli_real_escape_string($conn, $_POST["message"]));

    // Validasi
    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>alert('Semua field wajib diisi!'); history.back();</script>";
        exit;
    }

    // Ambil IP dan waktu
    $ip    = $_SERVER['REMOTE_ADDR'];
    $waktu = date("Y-m-d H:i:s");

    /********************************************
     * SIMPAN KE DATABASE
     ********************************************/
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message, ip, waktu) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $message, $ip, $waktu);
    $saveToDB = $stmt->execute();
    $stmt->close();

    /********************************************
     * KIRIM EMAIL VIA PHPMailer
     ********************************************/
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require "PHPMailer/src/Exception.php";
    require "PHPMailer/src/PHPMailer.php";
    require "PHPMailer/src/SMTP.php";

    $mail = new PHPMailer(true);

    try {
        // SETTINGS SMTP
        $mail->isSMTP();
        $mail->Host       = "smtp.gmail.com";       // ganti sesuai server
        $mail->SMTPAuth   = true;
        $mail->Username   = "emailkamu@domain.com"; // ganti email pengirim
        $mail->Password   = "APP_PASSWORD_GMAIL";   // gunakan App Password Gmail
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;

        // PENGIRIM & TUJUAN
        $mail->setFrom("emailkamu@domain.com", "Website SMP Poris Indah");
        $mail->addAddress("porisindah.id@gmail.com"); // email admin
        $mail->addReplyTo($email, $name);

        // PESAN
        $mail->isHTML(false);
        $mail->Subject = "Pesan Baru dari Website SMP Poris Indah";
        $mail->Body    = "
Ada pesan baru dari website SMP Poris Indah:

Nama   : $name
Email  : $email
IP     : $ip
Waktu  : $waktu

Pesan:
$message
        ";

        $sendEmail = $mail->send();

    } catch (Exception $e) {
        $sendEmail = false;
    }

    /********************************************
     * HASIL
     ********************************************/
    if ($saveToDB && $sendEmail) {
        echo "<script>alert('Pesan berhasil dikirim & disimpan!'); window.location.href='contact.html';</script>";
    } 
    else if ($saveToDB && !$sendEmail) {
        echo "<script>alert('Pesan disimpan, tetapi email gagal dikirim.'); window.location.href='contact.html';</script>";
    }
    else {
        echo "<script>alert('Gagal mengirim pesan.'); history.back();</script>";
    }
}

$conn->close();
?>
