<?php
/**
 * @license MIT
 * (Parts) taken from MIT (old) version from Laravel Spark, combined  and modified
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

    <!-- Spark Globals -->
    @include('spark::scripts.globals')

    <!-- Injected Scripts -->
    @yield('scripts', '')

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body id="spark-layout">
<!-- Vue App For Spark Screens -->
<div id="spark-app" v-cloak>
    <div id="subscribo-spark-checkout-plugin">
        <subscribo-spark-checkout-plugin inline-template>
            <div id="subscribo-spark-checkout-plugin-panel" class="panel panel-default">
                <div class="panel-heading">@yield('heading')</div>

                <div class="panel-body">
                    @yield('content')
                </div>
            </div>
        </subscribo-spark-checkout-plugin>
    </div>

    <!-- JavaScript Application -->
    <script src="{{ elixir('js/app.js') }}"></script>
</div>
</body>
</html>
