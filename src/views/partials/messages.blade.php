@if (Session::has("message"))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {!! Session::get("message") !!}
    </div>
@endif

@if ($errors->count() > 0)
    <div class="alert alert-danger alert-dark">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {!! implode(' <br /> ', $errors->all()) !!}
    </div>
@endif
