<?php 
  session_start();
  if(isset($_SESSION["Password"]) || isset($_SESSION["Unique_ID"])){
    header("Location: /dashboard");
  }
  include ('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Login | <?=$app_title?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link class="main-stylesheet" href="pages/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/toastr.min.css" rel="stylesheet" type="text/css" />
    <style>
    .form-group-default {
      padding-bottom: 20px;
    }

    .form-group-default.focused {
      border: none !important;
      border-bottom: solid 1px grey !important;

    }
  </style>
    <script type="text/javascript">
    window.onload = function()
    {
      // fix for windows 8
      if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
        document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="pages/css/windows.chrome.fix.css" />'
    }
    </script>
  </head>
  <body class="fixed-header ">
  <div class="login-wrapper row p-0 m-0">
    <div class="col-xl-4 col-lg-4 p-0 m-0 login_col1" style="height: 100vh;">
      <div class="login-container bg-white h-100  d-flex  align-items-center justify-content-center">
        <div class="p-l-50 p-r-50 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
          <div class="new_custom_h d-flex  align-items-start">
            <div class="university_new_logo" style="text-align:center;">
              <img src="assets/img/university/sanskaram.png" class="img-fluid" alt="">
              <p class="universityname_t text-center">Sanskaram University</p>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 p-0 m-0 login_col2" style="height: 100vh;">
      <div class="bg-pic login_form_new d-flex h-100 justify-content-center align-items-center " style="background-image: url('<?= $login_cover ?>'); background-repeat: no-repeat; background-size: cover;">
        <div class="">
          <div class="card card-body custom_login_form_card m-0">
            <?php if (!empty($dark_logo)) { ?>
              <img src="<?= $dark_logo ?>" alt="logo" data-src="<?= $dark_logo ?>" data-src-retina="<?= $dark_logo ?>" height="50">
            <?php } ?>
            <h2 class="mt-3 fw-bold title_text_login">Welcome</h2>
            <p class="mb-4 title_text_login">Sign in to your account</p>          
              <form id="form-login" class="p-t-15" role="form" autocomplete="off" action="app/login/login">

                <div class="form-group form-group-default custom_new_fields">
                  <label class="mb-2">User Name</label>
                  <div class="controls">
                    <input type="text" name="username" style="text-transform: uppercase" placeholder="Username" class="form-control" required>
                  </div>
                </div>
                <div class="form-group form-group-default custom_new_fields">
                  <label class="mb-2">Password</label>
                  <div class="controls">
                    <input type="password" class="form-control" name="password" placeholder="Credentials" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12 no-padding sm-p-l-10 d-flex justify-content-between">
                    <div class="form-check">
                      <input type="checkbox" checked value="1" id="checkbox1">
                      <label for="checkbox1">Remember me</label>
                    </div>
                    <a href="#" class="normal newlogin">Lost your password?</a>

                  </div>
                  <div class="col-md-12 d-flex align-items-center justify-content-center">
                    <button aria-label="" class="btn newlogin1 btn-lg m-t-10 w-50 rounded-pill custom_login_btn" type="submit">Sign in</button>
                  </div>
                </div>

              </form>           
          </div>
        </div>
      </div>
    </div>

  </div>
    <!-- BEGIN VENDOR JS -->
    <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
    <!--  A polyfill for browsers that don't support ligatures: remove liga.js if not needed-->
    <script src="assets/plugins/liga.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="assets/plugins/popper/umd/popper.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-actual/jquery.actual.min.js"></script>
    <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script type="text/javascript" src="assets/plugins/select2/js/select2.full.min.js"></script>
    <script type="text/javascript" src="assets/plugins/classie/classie.js"></script>
    <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <!-- END VENDOR JS -->
    <script src="pages/js/pages.min.js"></script>
    <script src="assets/js/toastr.min.js"></script>
        
    <script>
    toastr.options = {
      "closeButton": false,
      "debug": false,
      "newestOnTop": false,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "3000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
    </script>

    <script>
    $(function()
    {
      $('#form-login').validate();
      $("#form-login").on("submit", function(e){
        if($('#form-login').valid()){
          $(':input[type="submit"]').prop('disabled', true);
          var formData = new FormData(this);
          $.ajax({
              url: this.action,
              type: 'post',
              data: formData,
              cache:false,
              contentType: false,
              processData: false,
              dataType: "json",
              success: function(data) {
                  if(data.status==200){
                    toastr.success(data.message);
                    window.setTimeout(function() {
                      window.location.href = data.url;
                    }, 1000);
                  }else{
                    $(':input[type="submit"]').prop('disabled', false);
                    toastr.error(data.message);
                  }
              }
          });
          e.preventDefault();
        }
      });
    })
    </script>
  </body>
</html>
