<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="/includes/assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>ITFlow-NG</title>

    <meta name="description" content="" />

    <link rel="manifest" href="/manifest.json">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/includes/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="/includes/assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="/includes/assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="/includes/assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="/includes/assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/includes/assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/includes/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/includes/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
    <link rel="stylesheet" href="/includes/assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.1/css/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="/includes/assets/vendor/libs/spinkit/spinkit.css" />
    <link href="/includes/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/includes/assets/vendor/libs/plyr/plyr.css" />
    <link rel="stylesheet" href="/includes/assets/vendor/libs/toastr/toastr.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Page CSS -->

    
    <?= $page_css ?? ''?>

    <script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>

    <!-- Helpers -->
    <script src="/includes/assets/vendor/js/helpers.js"></script>
    <script src="/includes/js/confirm_modal.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="/includes/assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="/includes/assets/js/config.js"></script>

    <script>
      if (typeof navigator.serviceWorker !== 'undefined') {
        navigator.serviceWorker.register('/service-worker.js')
      }
    </script>
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/aq84ecg358zq9b4i9ea6hjaxqpx4mirbbtm7h5khkwevpqac/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <style>


        /* Initial state for cards */
        body:not(.content-loaded) .card {
            opacity: 0;
            transform: translateY(30px);
        }

        /* Animation styles */
        .card {
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
            will-change: opacity, transform;
        }

        .card.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
  </head>


<body>
