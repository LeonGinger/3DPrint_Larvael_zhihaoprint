<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ Admin::title() }} @if($header) | {{ $header }}@endif</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    {!! Admin::css() !!}

    <script src="{{ Admin::jQuery() }}"></script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    @include('vendor.ueditor.assets')
    <script src="{{ asset('vendor/laydate/laydate.js') }}"></script>
    <script src="{{ asset('vendor/echarts/echarts.min.js') }}"></script>
</head>

<body class="hold-transition {{config('admin.skin')}} {{join(' ', config('admin.layout'))}}">
<div class="wrapper">

    @include('admin::partials.header')

    @include('admin::partials.sidebar')

    <div class="content-wrapper" id="pjax-container">
        @yield('content')
        {!! Admin::script() !!}
    </div>

    @include('admin::partials.footer')

</div>

<script>
    function LA() {}
    LA.token = "{{ csrf_token() }}";
</script>

<!-- REQUIRED JS SCRIPTS -->
{!! Admin::js() !!}
<script>
  $(function(){
    $('.sidebar-menu li:not(.treeview) > a').on('click', function(){
      var $parent = $(this).parent().addClass('active');
      $parent.siblings('.treeview.active').find('> a').trigger('click');
      $parent.siblings().removeClass('active').find('li').removeClass('active');
    });

    $(window).on('load', function(){
      $('.sidebar-menu a').each(function(){
        if(this.href === window.location.href){
          $(this).parent().addClass('active')
            .closest('.treeview-menu').addClass('.menu-open')
            .closest('.treeview').addClass('active');
        }
      });
    });
  });
</script>
</body>
</html>
