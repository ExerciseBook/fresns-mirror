@extends('panel::common.layout')

@section('body')
    @include('panel::common.header')
    <main>
      @yield('content')
    </main>

    @include('panel::common.footer')
@endsection
