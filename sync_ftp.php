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
    
    // Files to sync (Clean restored files)
    $files = [
        'index.php',
        'cart.php',
        'cart_action.php',
        'checkout.php',
        'invoice.php',
        'database.sql',
        'products.php',
        'includes/header.php',
        'includes/footer.php',
        'assets/css/style.css',
        'admin/index.php',
        'admin/products.php',
        'admin/orders.php',
        'chatbot/api.php',
        'chatbot/chatbot_widget.php',
        'login.php',
        'register.php',
        'reset_password.php',
        'update_password.php',
        'import_remote_db.php',
        'moonlight_grocery.sql'
    ];
    
    foreach ($files as $file) {
        $local_file = __DIR__ . '/' . $file;
        $remote_file = $remote_dir . '/' . $file;
        
        if (!file_exists($local_file)) {
            echo "Skipping (not found locally): $file\n";
            continue;
        }
        
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
    
    // Clean up / Delete old deprecated enterprise files from the remote server
    $files_to_delete = [
        'run_migration.php',
        'send_otp.php',
        'verify_otp.php',
        'includes/lang.php'
    ];
    
    echo "\nCleaning up deprecated files from remote server...\n";
    foreach ($files_to_delete as $del_file) {
        $remote_del = $remote_dir . '/' . $del_file;
        if (@ftp_delete($conn_id, $remote_del)) {
            echo "Deleted remote file: $del_file -> Successful\n";
        } else {
            echo "Skipped/Not found remote file: $del_file\n";
        }
    }
} else {
    echo "Error: FTP Login failed.\n";
}

ftp_close($conn_id);
echo "Synchronization finished!\n";
?>
