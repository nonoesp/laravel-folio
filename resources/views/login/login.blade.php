@extends('folio::login.layout')

@php
	$shouldHideMenu = true;
	$site_title = 'Log In · '.config('folio.title');
	$headline = session('login.headline') ? session('login.headline') : 'Welcome back, friend.';
@endphp

@section('content')

    @if(session('error'))
        <p style="color: #e36129">{{ trans('folio::base.'.session('error')) }}</p>
    @endif

    <form action="{{ route('login') }}" method="post" accept-charset="UTF-8">

        @csrf
        <input name="email" type="email" value="{{ session('email') }}" placeholder="Email" {{ session('email') ?: 'autofocus' }}/>
        <input name="password" type="password" placeholder="Password" {{ session('email') ? 'autofocus' : null }}/>
        <label><input type="checkbox" name="remember" id="remember" checked> Remember Me</label>
        <button type="submit">Sign in</button>

    </form>

@endsection