<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:url" content="<?= current_url(); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= !empty($page_title) ? $page_title : ''; ?>" />
    <meta property="og:description" content="<?= !empty($meta_description) ? $meta_description : ''; ?>" />
    <?php if (!empty($btnShareImg)) : ?>
        <meta property="og:image" content="<?= $btnShareImg ?>" />
    <?php endif; ?>
    <title>MedSign</title>
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/patient-web-min.css?<?= WEB_VERSION; ?>" media="all" />
    <?php /* ?>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/webstyle.css?<?= WEB_VERSION; ?>" media="all" />
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/bootstrap.min.css?<?= WEB_VERSION; ?>" media="all" />
    <!-- Slick nav CSS -->
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/slicknav.min.css?<?= WEB_VERSION; ?>" media="all" />
    <!-- Iconfont CSS -->
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/icofont.css?<?= WEB_VERSION; ?>" media="all" />
    <!-- Slick CSS -->
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/slick.css?<?= WEB_VERSION; ?>">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>web/css/font-awesome.min.css?<?= WEB_VERSION; ?>">
    <!-- Owl carousel CSS -->
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/style.css?<?= WEB_VERSION; ?>" media="all" />
    <!-- Responsive CSS -->
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/responsive.css?<?= WEB_VERSION; ?>" media="all" />
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/select2.min.css?<?= WEB_VERSION; ?>" media="all" />
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/bootstrap-datetimepicker.css?<?= WEB_VERSION; ?>" media="all" />
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/patient_web.css?<?= WEB_VERSION; ?>" media="all" />
    <link rel="stylesheet" type="text/css" href="<?= ASSETS_PATH ?>web/css/tab_to_accordion.css?<?= WEB_VERSION; ?>" media="all" />
    <?php */ ?>
    <?php 
        $contro_name = $this->uri->segment(2);
    ?>
    <!-- Favicon Icon -->
    <link rel="icon" type="image/png" href="<?= ASSETS_PATH ?>web/img/ic_launcher.png?<?= WEB_VERSION; ?>" />
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    <!-- analytics JS -->
    <?php
        $_GET['g_no'] = 3; 
        include(DOCROOT_PATH.'google_analytics.php');
    ?>
    <script type="text/javascript">
        var site_url = "<?= DOMAIN_URL ?>";
        var contro_name = "<?= $this->uri->segment(2) ?>";
    </script>
</head>
