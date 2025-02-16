<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required configuration files
include 'config/config.php';
include 'pwdLoading.html';
// include "index.php"; 

// Get user input from POST request
$login_type = $_POST['forget-login_type']; // User type (e.g., 'student')
$ic = $_POST['forget-ID']; // Get user IC number
// $result = mysqli_query($con, "SELECT * FROM $login_type WHERE ic='$ic'") or die(mysqli_error($con));
$check_database_query = mysqli_query($con, "SELECT * FROM $login_type WHERE ic='$ic'");
$check_login_query = mysqli_num_rows($check_database_query);//see how many result row

// Check if user exists in database
if($check_login_query == 1) {
    // Fetch user data
    $row = mysqli_fetch_array($check_database_query);// get result as an array
    $email = $row['email'];
    $name = $row['name'];

    // Generate and update temporary password
    $oriPass = generate_password(8); // Generate 8-character password
    $temp= md5($oriPass); // Hash password for storage
    $sql = "UPDATE $login_type SET password='$temp' WHERE ic='$ic'";
    mysqli_query($con,$sql); ?>
    <script type="text/javascript">
        var recipientMail = '<?php echo $email ?>';
        var recipientName = '<?php echo $name ?>';
        var tempPass = '<?php echo $temp ?>';
        var oriPass = '<?php echo $oriPass ?>';
        var data = {
            service_id: 'ghg_monitoring',
            template_id: 'verificationEmail',
            user_id: 'OsTteFuPtsr2E-XpB',
            template_params: {
                'from_name':"ghg_monitoring_admin",
                'to_name': recipientName,
                'message': 'Your new temporary password is been reset to :'+oriPass,
                'email': recipientMail
            }
        };
        $.ajax('https://api.emailjs.com/api/v1.0/email/send', {
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json'
        }).done(function() {
            message => console.log(message)
        }).fail(function(error) {
            console.log(JSON.stringify(error))
        }); </script>
        <?php echo "
        <div id='alert-modal' tabindex='-1' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content'>
            <div class='modal-body d-flex flex-column align-items-center'>
                <div class='alert' role='alert'>Please login again with the password sent. Redirecting you back to login page</div>
            </div>
            </div>
        </div>
    </div>
        " ?>
        <script> setTimeout(function(){ window.location="login.php";}, 3000);
        
      </script>
<?php
        }else { echo "<div id='alert-modal' tabindex='-1' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-centered'>
                <div class='modal-content'>
                <div class='modal-body d-flex flex-column align-items-center'>
                    <div class='alert' role='alert'>Invalid ID with credidential. Redirecting you back to login page</div>
                </div>
                </div>
            </div>
        </div>"?>
<script>setTimeout(function(){ window.location="login.php";}, 3000);</script>

    <?php
}

function generate_password($length) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars),0,$length);
}
?>


