@extends('panel::common.layout')

@section('body')
    @yield('content')

    <div class="fresns-tips">
      @include('panel::common.tips')
    </div>
@endsection
