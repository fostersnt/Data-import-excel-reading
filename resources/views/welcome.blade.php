<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    </head>
    <body class="antialiased">
        <div>
            <form action="{{route('upload')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="mycsv" id="mycsv">
                <input type="submit" value="Upload">
            </form>
        </div>
    </body>
</html>
