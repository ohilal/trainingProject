
 @extends('layouts.guest')

 @section('content')
    <div class="container mx-auto">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                <img src="/img/admin/menu/login.jpg " alt="Login Image" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">تسجيل الدخول</h1>
                                    </div>
                                    <!-- <form class="user" method="post" action="{{ route('login') }}">
                                        @csrf
                                        <div class="form-group">
                                            <input name="email" type="email" 
                                            class="form-control form-control-user @error('email') is-invalid @enderror"
                                                id="email" aria-describedby="email"
                                                placeholder="Enter Email Address...">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror    
                                        </div>
                                        <div class="form-group">
                                            <input name="password" type="password"
                                            class="form-control form-control-user @error('email') is-invalid @enderror"
                                                id="password" placeholder="Password" value="password">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    {{ $message }}
                                                </span>
                                            @enderror  
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <input value="Login" type="submit" class="btn btn-primary btn-user btn-block" />
                                        
                                        <hr>
                                        
                                    </form> -->
<!-- New login form using LDAP -->
 <form class="user form-horizontal" method="post" action="{{ route('login') }}">
    @csrf
    
   <!-- Find the email input section and replace it -->
<div>
    <label for="username" class="block font-medium text-sm text-gray-700 form-label">
        {{ __('اسم المستخدم') }}
    </label>
    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 form-control">
       @error('username')
           <div class="text-danger">{{ $message }}</div>
       @enderror
</div>

<!-- Ensure the password field remains -->
<div class="mt-4">
    <label for="password" class="block font-medium text-sm text-gray-700 form-label">
        {{ __('كلمة المرور') }}
    </label>
    <input id="password" type="password" name="password" required autocomplete="current-password" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 form-control">
    @error('password')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
    
    <button type="submit" class="btn btn-dark mt-3" >تسجيل الدخول</button>
</form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
    
@endsection