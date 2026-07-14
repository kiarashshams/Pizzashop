<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "vendor/autoload.php";
include "config.php";

use PragmaRX\Google2FA\Google2FA;

$google2fa = new Google2FA();

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// Check if user already has a secret and if 2FA is enabled
$stmt = $conn->prepare("
SELECT google2fa_secret, two_factor_enabled
FROM users
WHERE id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row["two_factor_enabled"] == 1) {

    include "includes/header.php";

    echo '
    <div class="container mt-5">
        <div class="alert alert-success text-center">
            <h3>✅ Google Authenticator is already enabled.</h3>
            <p>Your account is already protected with Two-Factor Authentication.</p>

            <a href="profile.php" class="btn btn-primary mt-3">
                Back to Profile
            </a>
        </div>
    </div>';

    include "includes/footer.php";
    exit();
}

if (empty($row["google2fa_secret"])) {

    $secret = $google2fa->generateSecretKey();

    $save = $conn->prepare("
        UPDATE users
        SET google2fa_secret = ?
        WHERE id = ?
    ");

    $save->bind_param("si", $secret, $user_id);
    $save->execute();
    $save->close();

} else {

    $secret = $row["google2fa_secret"];

}


// Create Google Authenticator URL
$qrCodeUrl = $google2fa->getQRCodeUrl(
    "Pizza Shop",
    $username,
    $secret
);

$builder = new Builder(
    writer: new PngWriter(),
    writerOptions: [],
    validateResult: false,
    data: $qrCodeUrl,
    encoding: new Encoding('UTF-8'),
    errorCorrectionLevel: ErrorCorrectionLevel::High,
    size: 300,
    margin: 10,
    roundBlockSizeMode: RoundBlockSizeMode::Margin
);

$result = $builder->build();

$qr = $result->getDataUri();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $code = trim($_POST["code"]);

    if ($google2fa->verifyKey($secret, $code)) {

        $update = $conn->prepare("
            UPDATE users
            SET two_factor_enabled = 1
            WHERE id = ?
        ");

        $update->bind_param("i", $user_id);
        $update->execute();
        $update->close();

        $_SESSION["success"] =
        "Google Authenticator has been enabled successfully.";

        header("Location: profile.php");
        exit();

    } else {

        $message = "
        <div class='alert alert-danger'>
            Invalid authentication code.
        </div>";

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Enable 2FA</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5 text-center">

<h2>Enable Google Authenticator</h2>

<p>Scan this QR Code using Google Authenticator.</p>

<img src="<?php echo $qr; ?>" alt="QR Code" class="img-fluid">

<h4 class="mt-4">
Secret Key
</h4>

<p>
<?php echo $secret; ?>
</p>
<?php echo $message; ?>

<form method="POST" class="mt-4">

    <input
        type="text"
        name="code"
        class="form-control mb-3"
        maxlength="6"
        placeholder="Enter 6-digit code"
        required>

    <button
        class="btn btn-success w-100">

        Enable 2FA

    </button>

</form>

</div>

</body>

</html>