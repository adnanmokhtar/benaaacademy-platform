<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ option("site_name") }} - CMS</title>
    <link href="{{ assets("admin::css/bootstrap.min.css") }}" rel="stylesheet"/>
    <link href="{{ assets("admin::font-awesome/css/font-awesome.css") }}" rel="stylesheet"/>
    <link href="{{ assets("admin::css/animate.css") }}" rel="stylesheet"/>
    <link href="{{ assets("admin::css/style.css") }}" rel="stylesheet"/>
    <link href="{{ assets("admin::css/auth.css") }}" rel="stylesheet"/>

    <style>

        h1 {
            font-family: Tahoma;
        }

    </style>

</head>

<body class="gray-bg">

@yield("content")

<!-- Mainly scripts -->
<script src="{{ assets("admin::js/jquery-2.1.1.js") }}"></script>
<script src="{{ assets("admin::js/bootstrap.min.js") }}"></script>

</body>

</html>
