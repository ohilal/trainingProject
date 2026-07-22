<!DOCTYPE html>
<html lang="ar">
   {{-- <html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"> --}}
@include('layouts.header')


<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">
    @include("layouts.menus.sidebar")

     <!-- Content Wrapper -->
     <div id="content-wrapper" class="d-flex flex-column">
         
        <!-- Main Content -->
        <div id="content">

            @include("layouts.menus.top")


            <!-- Begin Page Content -->
            <div class="container-fluid">

                @include('layouts.msg')
                @yield('content')
            </div>
        </div>

    </div>
</div>


@include('layouts.footer')

</body>
</html>