<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?= APP_NAME ?></title>
        <meta name="description" content=""/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <!--<link rel="apple-touch-icon" href="apple-touch-icon.png">-->

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,700"/>
        <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/bootstrap.min.css"/>
        <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/bootstrap-select.css"/> 
        <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/bootstrap-theme.min.css"/>
        <link rel="stylesheet" href="<?= ASSETS_PATH ?>admin/css/toastr.min.css"/>
        <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/loader.css"/>
        <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/main.css"/>

        <script type="text/javascript" src="<?= ASSETS_PATH ?>js/jquery.min.js"></script>
        <script type="text/javascript" src="<?= ASSETS_PATH ?>js/vendor/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?= ASSETS_PATH ?>js/vendor/bootstrap-select.js"></script>
        <script type="text/javascript" src="<?= ASSETS_PATH ?>admin/js/jquery.validate.js"></script>
        <script type="text/javascript" src="<?= ASSETS_PATH ?>admin/js/additional-methods.min.js"></script>
        <script type="text/javascript" src="<?= ASSETS_PATH; ?>js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
        <script type="text/javascript" src="<?= ASSETS_PATH ?>admin/plugins/toastr/toastr.min.js"></script>
        <script type="text/javascript" src="<?= ASSETS_PATH ?>js/local-time.js"></script>

        <script type="text/javascript" src="<?= ASSETS_PATH ?>/js/sha1.min.js"></script>
        <script type="text/javascript">
            jQuery.sha1 = sha1;</script>
        <script type="text/javascript">
            var base_url = '<?= BASE_URL; ?>';
            var home_url = '<?= DASHBOARD_PATH; ?>';
            var date_format_js = '<?= DATE_FORMAT_JS; ?>';
			
            //name validation
            jQuery.validator.addMethod("lettersonly", function (value, element) {
                return this.optional(element) || value == value.match(/^[a-zA-Z\s]+$/);
            });

            //Email validation
            jQuery.validator.addMethod("email", function (value, element) {
                var reg_email = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return reg_email.test(value);
            });

            jQuery.validator.addMethod("pwcheck", function (value) {
                return /^[A-Za-z0-9\d=!@#$%^&*()_\-]*$/.test(value) // consists of only these
                        && /[a-z]/.test(value) // has a lowercase letter
                        && /[A-Z]/.test(value) // has a upper case letter
                        && /[=!@#$%^&*()_\-]/.test(value) // has a special char
                        && /\d/.test(value) // has a digit
            }, 'Please enter valid password.');

            jQuery.validator.addMethod("no_space_allow", function (value, element) {
                var value1 = value.replace(/&nbsp;/g, '');
                if (value1.trim() === "") {
                    return false;
                } else {
                    return true;
                }
            }, 'This field is required.');
        </script>
    </head>
    <body class="default-theme">
        <input type="hidden" value="<?= $this->security->get_csrf_hash(); ?>" id="csrf_token_data" name="<?= $this->security->get_csrf_token_name() ?>">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <script type="text/javascript">
            toastr.options = {
                "preventDuplicates": true,
                "preventOpenDuplicates": true,
                "closeButton": true,
                "progressBar": true
            };
        </script>

        <?php if ($this->session->flashdata('success')) { ?>
            <script>toastr.success('<?= $this->session->flashdata('success') ?>')</script>
        <?php } if ($this->session->flashdata('failure')) { ?>
            <script>toastr.error('<?= $this->session->flashdata('failure') ?>')</script>
        <?php } ?>

        <nav class="navbar navbar-inverse custom_navbar navbar-fixed-top">
            <div class="container no_pad">
                <div class="row">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#my_navbar">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>                        
                        </button>
                        <a class="navbar-brand visible-xs" href="<?= DOMAIN_URL ?>">
                            <img src="<?php echo ASSETS_PATH; ?>/images/logo.png" class="img-responsive mobile_logo m_top_12" />
                        </a>
                    </div>
                    <div class="collapse navbar-collapse" id="my_navbar">
                        <ul class="nav navbar-nav hidden-xs">
                            <li><a href="<?= DOMAIN_URL ?>uat"><img src="<?php echo ASSETS_PATH; ?>/images/logo.png" class="img-responsive site_logo m_top_10" /></a></li>
                        </ul>
                        <ul class="main_navigation">
                            <li class="navigation_li">
                                <a href="<?= DOMAIN_URL ?>uat" class="<?= $this->router->method == "index" ? "active" : "" ?> font_color_3">
                                    <?= lang("header_home") ?>
                                </a>
                            </li>
                            <li class="navigation_li">
                                <a href="<?= DASHBOARD_PATH ?>about-us" class="<?= $this->router->method == "about_us" ? "active" : "" ?> font_color_3">
                                    <?= lang("header_about_us") ?>
                                </a>
                            </li>
                            <li class="navigation_li">
                                <a href="<?= DASHBOARD_PATH ?>plan" class="<?= $this->router->method == "plan" ? "active" : "" ?> font_color_3">
                                    <?= lang("header_plan") ?>
                                </a>
                            </li>
                            <li class="navigation_li">
                                <a href="<?= DASHBOARD_PATH ?>contact-us" class="<?= $this->router->method == "contact_us" ? "active" : "" ?> font_color_3">
                                    <?= lang("header_contact_us") ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
