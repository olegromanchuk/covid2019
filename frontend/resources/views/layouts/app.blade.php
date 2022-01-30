<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MaWaSys') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">


    {{--    jQuery--}}
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>

    {{--    Datatables--}}
    <script type="text/javascript" src="{{ asset('js/DataTables/datatables.min.js') }}" defer></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('js/DataTables/datatables.min.css') }}"/>


</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/home') }}">
                {{ config('app.name', '') }}
            </a>
            @auth
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false">Call Records<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li class="nav-item"><a class="nav-link" href="{{ route('callrecords-ini') }}">Call Records</a>
                            <li class="nav-item"><a class="nav-link" href="{{ route('campaigns') }}">Campaigns</a></li>
                            </li>
                            {{--                                <li class="nav-item"><a class="nav-link" href="/load/1">Load numbers</a></li>--}}
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false">Contacts<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li class="nav-item"><a class="nav-link" href="/contacts">Contacts</a></li>
                            <li class="nav-item"><a class="nav-link" href="/load-contacts">Load contacts</a></li>
                        </ul>
                    </li>

{{--                    <li class="nav-item">--}}
{{--                        --}}{{--                            <a class="nav-link" href="/start/1"> Start campaign </a>--}}
{{--                        <a class="nav-link" data-toggle="modal" data-target="#modalStartCampaign" href="#"> Start  campaign </a>--}}
{{--                    </li>--}}

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false">Help<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                                <li class="nav-item">
                                        <a class="nav-link" data-toggle="modal" data-target="#modalHelp" href="#"> Help </a>
                                </li>
                                <li class="nav-item">
                                        <a class="nav-link" data-toggle="modal" data-target="#modalAbout" href="#"> About </a>
                                </li>
                        </ul>
                    </li>
                </ul>
            @endauth
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            {{--                                <li class="nav-item">--}}
                            {{--                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>--}}
                            {{--                                </li>--}}
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} ({{ Auth::user()->email }})<span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('flash-message')


    <main class="py-4">
        @yield('content')
    </main>
</div>

@include('about')
@include('help')



</body>
</html>
