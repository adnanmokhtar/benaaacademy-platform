<div class="row">
    <div class="col-md-6">

        <div class="form-group">
            <label
                for="site_name">{{ trans("admin::options.attributes.site_name") }}</label>
            <input name="option[site_name]" type="text" required="required"
                   value="{{ Request::old("option.site_name", option("site_name")) }}"
                   class="form-control" id="site_name"
                   placeholder="{{ trans("admin::options.attributes.site_name") }}">
        </div>

        <div class="form-group">
            <label
                for="site_slogan">{{ trans("admin::options.attributes.site_slogan") }}</label>
            <input name="option[site_slogan]" type="text"
                   value="{{ @Request::old("option.site_slogan", option("site_slogan")) }}"
                   class="form-control" id="site_slogan"
                   placeholder="{{ trans("admin::options.attributes.site_slogan") }}">
        </div>

        <div class="form-group">
            <label
                for="site_email">{{ trans("admin::options.attributes.site_email") }}</label>
            <input name="option[site_email]" type="email" required="required"
                   value="{{ @Request::old("option.site_email", option("site_email")) }}"
                   class="form-control" id="site_email"
                   placeholder="{{ trans("admin::options.attributes.site_email") }}">
        </div>

        <div class="form-group">
            <label
                for="site_copyrights">{{ trans("admin::options.attributes.site_copyrights") }}</label>
            <input name="option[site_copyrights]" type="text"
                   value="{{ @Request::old("option.site_copyrights", option("site_copyrights")) }}"
                   class="form-control" id="site_copyrights"
                   placeholder="{{ trans("admin::options.attributes.site_copyrights") }}">
        </div>

        <div class="form-group">
            <label
                for="timezone">{{ trans("admin::options.attributes.timezone") }}</label>
            <select id="app_timezone" class="form-control chosen-select chosen-rtl"
                    name="option[site_timezone]">

                @for ($i = -12; $i <= 12; $i++)
                    <?php
                    if ($i == 0) {
                        $zone = "";
                    } elseif ($i > 0) {
                        $zone = "+$i";
                    } else {
                        $zone = $i;
                    }
                    ?>
                    <option
                        value="Etc/GMT{{ $zone }}"
                        @if (option("site_timezone") == "Etc/GMT" . $zone) selected="selected" @endif>
                        GMT{{ $zone }}</option>
                @endfor
            </select>
        </div>

        <div class="form-group">
            <label
                for="date_format">{{ trans("admin::options.attributes.date_format") }}</label>
            <select id="date_format" class="form-control chosen-select chosen-rtl"
                    name="option[site_date_format]">
                @foreach (array("Y-m-d H:i A", "Y-m-d", "d/m/Y", "H:i A") as $format)
                    <option
                        value="{{ $format }}"
                        @if (option("site_date_format") == $format) selected="selected" @endif>{{ date($format, time() - 2 * 60 * 60) }}</option>
                @endforeach
                <option
                    value="relative"
                    @if (option("site_date_format") == "relative") selected="selected" @endif>{{ time_ago(time() - 2 * 60 * 60) }}</option>
            </select>
        </div>

        @if(config("i18n.locales"))
            <div class="form-group">
                <label
                    for="app_locale">{{ trans("admin::options.attributes.locale") }}</label>
                <select id="app_locale" class="form-control chosen-select chosen-rtl"
                        name="option[site_locale]">
                    @foreach (config("i18n.locales") as $code => $lang)
                        <option value="{{ $code }}"
                                @if (option("site_locale") == $code) {
                                selected="selected" @endif>{{ $lang["title"] }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <fieldset>
            <legend>{{ trans("admin::options.attributes.site_status") }}</legend>

            <div class="form-group switch-row">
                <label class="col-sm-10 control-label"
                       for="site_status">{{ trans("admin::options.attributes.site_status") }}</label>
                <div class="col-sm-2">
                    <input
                        @if (option("site_status", 1)) checked="checked" @endif
                    type="checkbox" name="site_status"
                        value="1"
                        class="switcher switcher-sm site_status">
                    <input type="hidden" name="option[site_status]" value="{{ option("site_status", 1) }}"/>
                </div>
            </div>

            <div class="form-group">
                <label
                    for="offline_message">{{ trans("admin::options.attributes.offline_message") }}</label>
                <br/>
                <textarea class="form-control" id="offline_message"
                          name="option[site_offline_message]"
                          placeholder="{{ trans("admin::options.attributes.offline_message") }}">{{ Request::old("option.site_offline_message", option("site_offline_message")) }}</textarea>
            </div>

        </fieldset>

    </div>
    <div class="col-md-6">

        <div class="widget style1 navy-bg">
            <div class="row">

                <div class="col-xs-8 text-left">
                    <span> {{ trans("admin::options.dot_version") }}: </span>
                    <h2 class="font-bold" style="font-family: sans-serif,Verdana, Arial">
                        {{ Plugin::get("admin")->getVersion() }}
                    </h2>
                </div>

                <div class="col-xs-4 text-center">
                    <i class="fa fa-cloud fa-5x"></i>
                </div>

            </div>
        </div>

        <div class="row text-center">
            <a href="javascript:void(0)"
               data-loading-text="{{ trans("admin::options.checking") }}"
               class="btn btn-primary btn-labeled btn-main check-update"> <span
                    class="btn-label icon fa fa-life-ring"></span> &nbsp;
                {{ trans("admin::options.check_for_update") }}
            </a>
        </div>

        <br/> <br/>

        <div class="update-status"></div>

    </div>
</div>


@section("footer")

    <script>

        $(document).ready(function () {

            var site_status = document.querySelector(".site_status");

            site_status.onchange = function () {
                $("[name=option\\[site_status\\]]").val(site_status.checked ? 1 : 0);
            };

            new Switchery(site_status);

            $(".check-update").click(function () {

                var base = $(this);

                base.button("loading");

                $.post("{{ route("admin.options.check_update") }}", function (result) {

                    $(".update-status").html(result);
                    base.button("reset");

                }).fail(function () {
                    base.button("reset");
                });

            });

        });

    </script>


@stop
