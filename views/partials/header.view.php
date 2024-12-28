<?php
use core\App;
?>
<!doctype html>
<html lang="ua">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="/public/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/public/css/tailwind.css">
    <script src="/public/js/alertify.min.js"></script>
    <script src="/public/js/api/alertify.config.js"></script>
    <link rel="stylesheet" href="/public/css/alertify-themes/bootstrap.min.css" />
    <link rel="stylesheet" href="/public/css/alertify.min.css" />
    <link rel="stylesheet" href="/public/css/style.css">
    <script src="public/js/jquery-3.7.1.js"></script>
    <title><?= $title ?? App::route()->title() ?></title>
</head>
<body>
