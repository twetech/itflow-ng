<?php
/*
 * Client Portal
 * Landing / Home page for the client portal
 */

$last_page = $_GET['last_page'] ?? null;

$company_id = 1;
require_once '/var/www/nestogy/includes/config/config.php';

require_once '/var/www/nestogy/includes/get_settings.php';

require_once '/var/www/nestogy/includes/functions/functions.php';


require '../bootstrap.php';

use Twetech\Nestogy\Auth\Auth;

$auth = new Auth($pdo);

// Check if the user has a valid "Remember Me" cookie
$auth->checkRememberMe();

if (Auth::check()) {
    // User is already logged in, redirect them to the dashboard
    header('Location: /public/');
    exit;
} 


if (!isset($_SESSION)) {
    // HTTP Only cookies
    ini_set("session.cookie_httponly", true);
    if ($config_https_only) {
        // Tell client to only send cookie(s) over HTTPS
        ini_set("session.cookie_secure", true);
    }
    session_start();
}

// Check to see if client portal is enabled
if ($config_client_portal_enable == 0) {
    echo "Client Portal is Disabled";
    exit();
}

$ip = sanitizeInput(getIP());
$user_agent = sanitizeInput($_SERVER['HTTP_USER_AGENT']);

$sql_settings = mysqli_query($mysqli, "SELECT config_azure_client_id, config_login_message FROM settings WHERE company_id = 1");
$settings = mysqli_fetch_array($sql_settings);
$azure_client_id = $settings['config_azure_client_id'];
$config_login_message = nullable_htmlentities($settings['config_login_message']);

$company_sql = mysqli_query($mysqli, "SELECT company_name, company_logo FROM companies WHERE company_id = 1");
$company_results = mysqli_fetch_array($company_sql);
$company_name = $company_results['company_name'];
$company_logo = $company_results['company_logo'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {

    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_message'] = 'Invalid e-mail';
    } else {
        $sql = mysqli_query($mysqli, "SELECT * FROM contacts WHERE contact_email = '$email' AND contact_archived_at IS NULL LIMIT 1");
        $row = mysqli_fetch_array($sql);
        if ($row['contact_auth_method'] == 'local') {
            if (password_verify($password, $row['contact_password_hash'])) {

                $_SESSION['client_logged_in'] = true;
                $_SESSION['client_id'] = intval($row['contact_client_id']);
                error_log("Client ID: " . $_SESSION['client_id']);
                $_SESSION['contact_id'] = intval($row['contact_id']);
                $_SESSION['login_method'] = "local";

                // Redirect to last page
                if (isset($last_page)) {
                    header("Location: $last_page");
                } else {
                    header("Location: index.php");
                }

                mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Client Login', log_action = 'Success', log_description = 'Client contact $row[contact_email] successfully logged in locally', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $row[contact_client_id]");

            } else {
                mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Client Login', log_action = 'Failed', log_description = 'Failed client portal login attempt using $email', log_ip = '$ip', log_user_agent = '$user_agent'");
                $_SESSION['login_message'] = 'Incorrect username or password.'; #incorrect password
            }

        } else {
            mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Client Login', log_action = 'Failed', log_description = 'Failed client portal login attempt using $email', log_ip = '$ip', log_user_agent = '$user_agent'");
            $_SESSION['login_message'] = 'Incorrect username or password.'; #incorrect email
        }
    }
}

?>

<!doctype html>

<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="/includes/assets/" data-template="horizontal-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>ITFlow-NG</title>

    <meta name="description" content="" />

    <link rel="manifest" href="/manifest.json">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/includes/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="/includes/assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="/includes/assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="/includes/assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="/includes/assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/includes/assets/vendor/css/rtl/theme-default.css"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/includes/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/includes/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="/includes/assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="/includes/assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.1/css/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="/includes/assets/vendor/libs/spinkit/spinkit.css" />
    <link rel="stylesheet" href="/includes/assets/vendor/libs/toastr/toastr.css" />
    <link rel="stylesheet" href="/includes/assets/vendor/libs/apex-charts/apex-charts.css" />


    <!-- Page CSS -->
    <link rel="stylesheet" href="/includes/assets/vendor/css/old_pages/page-auth.css" />


    <!-- Helpers -->
    <script src="/includes/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="/includes/assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="/includes/assets/js/config.js"></script>

    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/78o64w2w2bmaf98z8p7idos4tjloc808tr1j9iv8efl63nce/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
</head>


<body class="hold-transition login-page">
    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0">
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
                <div class="w-100 d-flex justify-content-center">
                    <img src="/includes/assets/img/illustrations/boy-with-rocket-light.png" class="img-fluid"
                        alt="Login image" width="700" data-app-dark-img="illustrations/boy-with-rocket-dark.png"
                        data-app-light-img="illustrations/boy-with-rocket-light.png" />
                </div>
            </div>
            <!-- /Left Text -->

            <!-- Login -->
            <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    <!-- Logo -->
                    <div class="login-logo mb-4">
                        <?php if (!empty($company_logo)) { ?>
                            <img alt="<?= $company_name ?> logo" height="110" width="380" class="img-fluid mb-4"
                                src="<?= "/uploads/settings/$company_logo"; ?>">
                        <?php } else { ?>
                            <b><?= $company_name ?></b> <br>Client Portal Login</h2>
                        <?php } ?>
                    </div>
                    <!-- /Logo -->
                    <div class="card-body login-card-body">
                        <?php if (!empty($config_login_message)) { ?>
                            <p class="login-box-msg px-0"><?= nl2br($config_login_message); ?></p>
                        <?php } ?>
                        <?php
                        if (!empty($_SESSION['login_message'])) { ?>
                            <p class="login-box-msg text-danger">
                                <?php
                                echo $_SESSION['login_message'];
                                unset($_SESSION['login_message']);
                                ?>
                            </p>
                            <?php
                        }
                        ?>
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Registered Client Email"
                                    name="email" required autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <input type="password" class="form-control" placeholder="Client Password"
                                    name="password" required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col text-center mt-2">
                                <button type="submit" class="btn btn-success mb-3" name="login">Sign in</button>
                            </div>

                            <div class="divider mt-2">
                                <div class="divider-text">or</div>
                            </div>

                            <?php
                            if (!empty($config_smtp_host)) { ?>
                                <h6 class="text-center"><a href="login_reset.php">Forgot password?</a></h6>
                            <?php } ?>

                            <div class="divider">
                                <div class="divider-text">Internal Logins</div>
                            </div>

                        </form>
                        <div class="col text-center">
                        <a href="/public/login.php">
                                <button type="button" class="btn btn-label-secondary">Employee Login</button>
                            </a>
                            <a href="/public/login.php">
                                <button type="button" class="btn btn-label-secondary">Vendor Login</button>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            <!-- /Login -->
        </div>
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->




    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="/includes/assets/vendor/libs/popper/popper.js"></script>
    <script src="/includes/assets/vendor/js/bootstrap.js"></script>
    <script src="/includes/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="/includes/assets/vendor/libs/hammer/hammer.js"></script>

    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.1/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.1/js/responsive.bootstrap5.js"></script>

    <script src="/includes/js/reformat_datetime.js"></script>

    <script src="/includes/assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <script src="/includes/assets/vendor/libs/block-ui/block-ui.js"></script>
    <script src="/includes/assets/vendor/libs/sortablejs/sortable.js"></script>
    <script src="/includes/assets/vendor/libs/toastr/toastr.js"></script>
    <script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="/includes/assets/js/main.js"></script>

    <script src="/includes/js/dynamic_modal_loading.js"></script>

    <!-- Page JS -->

    <script src="/includes/assets/js/cards-actions.js"></script>
</body>

</html>