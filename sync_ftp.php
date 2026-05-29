<?php
// sync_ftp.php
$ftp_server = "ftpupload.net";
$ftp_user = "if0_41940951";
$ftp_pass = "MBVj9MgEBB5ZA";

echo "Syncing Moonlight Grocery files to live InfinityFree website...\n";

$conn_id = ftp_connect($ftp_server);
if (!$conn_id) {
    die("Error: Could not connect to FTP server: $ftp_server\n");
}

if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
    echo "Connected successfully to $ftp_server as $ftp_user.\n";
    ftp_pasv($conn_id, true); // Enable passive mode
    
    $remote_dir = "/htdocs";
    
    // Files to sync
    $files = [
        'index.php',
        'cart.php',
        'cart_action.php',
        'checkout.php',
        'invoice.php',
        'run_migration.php',
        'send_otp.php',
        'verify_otp.php',
        'database.sql',
        'includes/header.php',
        'includes/footer.php',
        'includes/lang.php',
        'chatbot/api.php',
        'chatbot/chatbot_widget.php',
        'assets/css/style.css',
        'admin/index.php',
        'login.php',
        'register.php',
        'reset_password.php',
        'update_password.php'
    ];
    
    foreach ($files as $file) {
        $local_file = __DIR__ . '/' . $file;
        $remote_file = $remote_dir . '/' . $file;
        
        // Ensure remote directory exists if it's in a subdirectory
        $dir = dirname($remote_file);
        if ($dir !== $remote_dir) {
            @ftp_mkdir($conn_id, $dir);
        }
        
        if (ftp_put($conn_id, $remote_file, $local_file, FTP_BINARY)) {
            echo "Uploaded: $file -> Successful\n";
        } else {
            echo "Failed:   $file -> FAILED!\n";
        }
    }
} else {
    echo "Error: FTP Login failed.\n";
}

ftp_close($conn_id);
echo "Synchronization finished!\n";
?>
