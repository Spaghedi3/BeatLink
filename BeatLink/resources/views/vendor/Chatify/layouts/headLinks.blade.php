<title>{{ config('chatify.name') }}</title>

{{-- Vite assets --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Meta tags --}}
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="id" content="{{ $id }}">
<meta name="messenger-color" content="{{ $messengerColor }}">
<meta name="messenger-theme" content="{{ $dark_mode }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="url" content="{{ url('').'/'.config('chatify.routes.prefix') }}" data-user="{{ Auth::user()->id }}">

{{-- External scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/chatify/font.awesome.min.js') }}"></script>
<script src="{{ asset('js/chatify/autosize.js') }}"></script>
<script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>

{{-- External styles --}}
<link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css" />
<link href="{{ asset('css/chatify/style.css') }}" rel="stylesheet" />
<link href="{{ asset('css/chatify/'.$dark_mode.'.mode.css') }}" rel="stylesheet" />

{{-- Messenger primary color --}}
<style>
  :root {
    --primary-color: {
        {
        $messengerColor
      }
    }

    ;
  }
</style>