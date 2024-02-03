@if($version)

    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        {!! trans("admin::options.version_available", ["version" => $version]) !!}
    </div>

    <br/>

    <div class="ibox-content" style="direction: ltr">

        <h2 class="text-left">{{ trans("admin::options.how_update") }}</h2>

        <small>First update your composer package</small>

        <ul class="todo-list m-t small-list">
            <li>
                <span class="m-l-xs"><strong>$</strong> composer update dot/platform</span>
            </li>
        </ul>

        <br/>

        <small>Installing update using this artisan command</small>

        <ul class="todo-list m-t small-list">
            <li>
                <span class="m-l-xs"> <strong>$</strong> php artisan dot:install</span>
            </li>
        </ul>
    </div>

@else

    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

        {!! trans("admin::options.up_to_date") !!}
    </div>

@endif
