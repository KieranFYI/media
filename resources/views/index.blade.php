@extends('adminlte::page')

@section('content')
    <div class="row pt-3">
        <div class="col-12" id="vuejs-media">
            <media-index />
        </div>
    </div>
@endsection

@pushOnce('js')
@routes('media')
@routes('media-api')
<script src="{{ mix('vue.js', 'vendor/kieranfyi/media') }}"></script>
@endpushOnce