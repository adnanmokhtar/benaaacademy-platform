<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="robots" content="NOINDEX, NOFOLLOW"/>
    <meta name="viewport" content="width=device-width initial-scale=1.0"/>
    <title>{{ option("site_name") }} - {{ trans("admin::common.cms") }}</title>
    <link rel="icon" type="image/png" href="{{ assets("admin::favicon.png") }}"/>
    <link href="{{ assets("admin::css/app.css") }}" rel="stylesheet"/>

    @if (defined("DIRECTION") && DIRECTION == "rtl")
        <link href="{{ assets("admin::css/app.rtl.css") }}" rel="stylesheet"/>
    @endif

    <script>
        base_url = "{{ url(ADMIN) }}/";
        baseURL = "{{ url(ADMIN) }}/";
    </script>

    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    @yield("head")
    @stack("head")

    @foreach(Action::fire("admin.head") as $output)
        {!! $output !!}
    @endforeach
</head>

<body
    class="@if(Auth::user()->color == "blue") dark-theme @endif @if (defined("DIRECTION") && DIRECTION == "rtl") rtls @endif @if (isset($_COOKIE["mini_nav"]) and $_COOKIE["mini_nav"] == "1") mini-navbar @endif">

<div class="loadingWrapper">
    <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
</div>

<div class="bodyWrapper">

    @include("media::manager")

    <div
        class="main-container @if (isset($_COOKIE["active_container"]) and $_COOKIE["active_container"]) active @endif">

        <div id="wrapper">

            <nav class="main-nav navbar-default navbar-static-side" role="navigation">
                <div class="sidebar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="nav-header">
                            <div class="dropdown profile-element">

                                <span>
                                    <img alt="image" class="img-circle" src="{{ Auth::user()->photo_url }}"/>
                                </span>

                                <a data-toggle="dropdown" class="dropdown-toggle" href="#" style="">

                                    <span class="clear">

                                        <span class="block sm-t-xs">
                                            <strong
                                                class="font-bold">{{ ucfirst(Auth::user()->first_name) . " " . ucfirst(Auth::user()->last_name) }}</strong>
                                        </span>

                                        @if (Auth::user()->role)
                                            <span class="text-muted text-xs block">
                                                {{ Auth::user()->role->name }}
                                                <b class="caret"></b>
                                            </span>
                                        @endif
                                    </span>

                                </a>

                                <ul class="dropdown-menu m-t-xs">

                                    <li>
                                        <a href="{{ route("admin.users.edit", array("id" => Auth::user()->id)) }}">{{ trans("auth::auth.edit_profile") }}</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li><a class="ask" message="{{ trans("admin::common.sure_logout") }}"
                                           href="{{ route("admin.auth.logout") }}">{{ trans("auth::auth.logout") }}</a>
                                    </li>
                                </ul>

                            </div>

                            <div class="logo-element">
                                <i class="fa fa-circle" aria-hidden="true"></i>
                            </div>

                        </li>

                        {!!  Navigation::get("sidebar")->render() !!}

                    </ul>
                </div>
            </nav>

            <div id="page-wrapper" class="gray-bg" style="overflow: hidden !important">
                <div class="row border-bottom">
                    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                        <div class="navbar-header">
                            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="javascript:void(0)"><i
                                    class="fa fa-bars"></i> </a>
                        </div>
                        <ul class="nav navbar-top-links navbar-right">

                            <li>
                                <span
                                    class="m-r-sm text-muted welcome-message">{{ strtoupper(Config::get("site_title", Config::get("site_name"))) }}</span>
                            </li>

                            {!! Navigation::get("topnav")->render() !!}

                            <li>
                                <a href="{{ route("admin.auth.logout") }}" class="ask"
                                   message="{{ trans("admin::common.sure_logout") }}">
                                    <i class="fa fa-sign-out"></i>
                                </a>
                            </li>
                        </ul>

                    </nav>
                </div>

                @yield("content")

                @foreach(Action::fire("admin.content.end") as $output)
                    {!! $output !!}
                @endforeach

            </div>

        </div>

    </div>

</div>

<script type="text/javascript" src="{{ assets('admin::js/app.js') }}"></script>

<script>

    /*
     * check cms is loaded in iframe.
     */
    $(document).ready(function () {
        if (window.self !== window.top) {
            $("#page-wrapper").css("margin", 0);
            $("#page-wrapper nav").remove();
            $("#wrapper .main-nav").remove();
            $(".btn-main").remove();
        }
    });

    var confirm_box = function (message, callback) {

        if (message === undefined) {
            callback();
        }

        bootbox.dialog({
            message: message,
            buttons: {
                success: {
                    label: "{{ trans("admin::common.yes") }}",
                    className: "btn-primary",
                    callback: function () {
                        if (typeof(callback) == "function") {
                            callback();
                        }
                    }
                },
                danger: {
                    label: "{{ trans("admin::common.cancel") }}",
                    className: "btn-default",
                    callback: function () {
                    }
                },
            },
            className: "bootbox-sm"
        });
    }

    var alert_box = function (message, callback) {
        bootbox.dialog({
            message: message,
            buttons: {
                success: {
                    label: "{{ trans("admin::common.yes") }}",
                    className: "btn-primary",
                    callback: function () {
                        if (typeof(callback) == "function") {
                            callback();
                        }

                    }
                }
            },
            className: "bootbox-sm"
        });
    }

</script>

<script>

    $(document).ready(function () {

        $('.chosen-select').chosen();

        $('[data-toggle="tooltip"]').tooltip();

        if (!$(this).parent().hasClass("action-box")) {
            $(this).attr('style', 'width: 100%');
        }

        resizeChosen();
        jQuery(window).on('resize', resizeChosen);
    });

    function resizeChosen() {
        $(".chosen-container").each(function () {
            if (!$(this).parent().hasClass("action-box")) {
                $(this).attr('style', 'width: 100%');
            }
        });
    }

    $(document).ready(function () {

        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

        var url = window.location.href;
        $("#side-menu li a[href='" + url + "']").parents("li").addClass("active");
        $("#side-menu li a[href='" + url + "']").parents("li").parents(".nav-second-level").addClass("collapse in");

        $("#side-menu li.active").each(function () {
            $(this).children("ul").addClass("in");
        });

        $(".minimalize-styl-2").click(function () {
            if ($("body").hasClass("mini-navbar")) {
                $.cookie('mini_nav', "1", {path: '/'});
            } else {
                $.cookie('mini_nav', "0", {path: '/'});
            }
        });

        $('a.ask').on('click', function (e) {
            e.preventDefault();
            $this = $(this);
            bootbox.dialog({
                message: $this.attr('message'),
                buttons: {
                    success: {
                        label: "{{ trans("admin::common.yes") }}",
                        className: "btn-primary",
                        callback: function () {
                            location.href = $this.attr('href');
                        }
                    },
                    danger: {
                        label: "{{ trans("admin::common.cancel") }}",
                        className: "btn-default",
                        callback: function () {
                        }
                    },
                },
                className: "bootbox-sm"
            });
        });

    });

</script>

@yield("footer")
@stack("footer")

@foreach(Action::fire("admin.footer") as $output)
    {!! $output !!}
@endforeach
</body>
</html>


