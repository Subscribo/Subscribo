<?php
/**
 * Localized version of Laravel Welcome template
 * Taken from Laravel Framework (www.laravel.com) and modified
 *
 * @license MIT
 *
 */
/** @var \Subscribo\Localizer $localizer */
$localizer = \Subscribo\Localization::localizer('app', 'main');
?>
<!DOCTYPE html>
<html lang="{{ $localizer->getBCPLocale() }}">
    <head>
        <title>
            @section('pageTitle')
            {{ $localizer->trans('page.title') }}
            @show
        </title>
        <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
                margin-bottom: 40px;
            }

            .quote {
                font-size: 24px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">{{ $localizer->trans('welcome.title') }}</div>
                <div class="quote">{{ Inspiring::quote() }}</div>
            </div>
        </div>
    </body>
</html>
