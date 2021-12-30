@extends('panel::common.layout')

@section('body')
  @include('panel::common.header')

  <div class="container-fluid">
    <div class="row">
      @yield('sidebar')
      <!--设置区域 开始-->
      <div class="col-lg-10 fresns-setting mt-3 mt-lg-0 p-lg-3">
        <div class="bg-white mb-2 p-3 p-lg-5">
          @yield('content')
        </div>

        @include('panel::common.footer')
      </div>
      <!--设置区域 结束-->
    </div>
  </div>

@endsection

