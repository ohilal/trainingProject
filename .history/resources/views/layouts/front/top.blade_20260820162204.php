<nav class="navbar navbar-expand-lg navbar-light px-4  py-3 py-lg-0">
    <a href="{{ route('home') }}" class="navbar-brand p-0">
        <h1 class="m-0">بنك المعرفة</h1>
        <!-- <img src="img/logo.png" alt="Logo"> -->
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="fa fa-bars"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav mx-auto py-0 text-nowrap">
            <a href="{{ route('home') }}" class="nav-item nav-link">الرئيسية </a>
            <li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">البرامج التدريبية</a>
  <ul class="dropdown-menu">
     @forelse ($departments as $department)
                        <li class="mx-2" data-filter=".department-{{ $department->id }}">{{ $department->title }}
                            <a class="dropdown-item" href=".department-{{ $department->id }}"> {{ $department->title }} </a>
                        </li>
                    @empty
                    @endforelse
    <li><a class="dropdown-item" href="{{ route('front.courses') }}"> برامج فنية</a></li>
    <li><a class="dropdown-item"  href="{{ route('front.courses') }}">برامج إدارية وسلوكية  </a></li>
   <li><a class="dropdown-item" href="{{ route('front.courses') }}">برامج  التحول الرقمى  </a></li>
    <li><a class="dropdown-item" href="{{ route('front.courses') }}">  برامج اللغات   </a></li>
 <li><a class="dropdown-item" href="{{ route('front.courses') }}"> برامج الكمبيوتر </a></li>
</ul>
</li>
   <li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"> مكتبة الوسائط</a>
  <ul class="dropdown-menu">
         <li><a class="dropdown-item" href="#"> المكتبة الالكترونية </a></li>
    <li><a class="dropdown-item" href="#">  معرض الصور</a></li>
    <li><a class="dropdown-item" href="#"> معرض الفيديو   </a></li>

</ul>
</li>
 <li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"> الأنشطة والفعاليات </a>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#"> Calendar</a></li>
    <li><a class="dropdown-item" href="{{ route('front.events') }}"> حفلات تكريم</a></li>
 <li><a class="dropdown-item" href="#"> مبادرات</a></li>
    <li><a class="dropdown-item" href="#"> تكريم حجاج </a></li>
 <li><a class="dropdown-item" href="#"> الموظف المتميز </a></li>
</ul>
</li>

            <a href="{{ route('front.organizationStructure') }}" class="nav-item nav-link">دليل مهام الإدارات</a>

  
            <a href="" class="nav-item nav-link">ركن الابتكار</a>
        </div>
        @if (Route::has('login'))
        @auth
        <a href="{{ route('dashboard') }}" class="btn btn-sm rounded-pill py-2 px-4 ms-3 d-none d-sm-block text-nowrap">لوحة التحكم</a>
        @else
        <a href="{{ route('login') }}" class="btn btn-sm rounded-pill py-2 px-4 ms-3 d-none d-sm-block text-nowrap">تسجيل الدخول</a>    
        @endauth
        
        @endif
    </div>
</nav>