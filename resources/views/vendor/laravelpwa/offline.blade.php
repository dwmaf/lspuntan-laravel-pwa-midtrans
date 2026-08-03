<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LSP UNTAN') }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            background-color: #f3f4f6;
            color: #1f2937;
            height: 100vh;
            overflow: hidden;
        }

        .dark body {
            background-color: #111827;
            color: #d1d5db;
        }

        .container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .card {
            width: 100%;
            max-width: 28rem;
            padding: 2rem;
            border-radius: 0.5rem;
        }

        .dark .card {
            background-color: #1f2937;
        }

        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .icon {
            width: 4rem;
            height: 4rem;
            margin-bottom: 1rem;
            color: #9ca3af;
        }

        h1 {
            font-size: 1.5rem;
            line-height: 2rem;
            font-weight: 700;
        }

        p {
            font-size: 1.125rem;
            line-height: 1.75rem;
        }

        .small-text {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .flex {
            display: flex;
            align-items: center;
            gap: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="content">
                <!-- ver 1 -->
                <!-- <svg xmlns="http://www.w3.org/2000/svg" width="4rem" height="4rem" viewBox="0 0 24 24">
                    <path d="M0 0h24v24H0z" fill="none" />
                    <path fill="currentColor" d="m19.05 21.9l-8.7-8.75q-.775.175-1.487.475t-1.338.725q-.525.35-1.162.363T5.3 14.275q-.45-.45-.412-1.088t.537-1.012Q6 11.75 6.638 11.4t1.312-.65L5.7 8.5q-.65.35-1.262.738t-1.188.837q-.5.4-1.137.4t-1.063-.45Q.6 9.575.625 8.95t.525-1.025q.55-.45 1.125-.862T3.5 6.3L2.1 4.9q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l16.975 16.975q.3.3.3.713t-.3.712q-.3.275-.712.288t-.713-.288m-8.825-1.637Q9.5 19.525 9.5 18.5q0-1.05.725-1.775T12 16t1.775.725t.725 1.775q0 1.025-.725 1.763T12 21t-1.775-.737m8.6-6.138q-.4.4-.937.388t-.938-.413l-.25-.25l-.25-.25l-2.4-2.4q-.325-.325-.125-.675t.7-.225q1.125.275 2.137.775t1.888 1.175q.45.35.513.913t-.338.962m4.125-4.1q-.425.45-1.05.462t-1.125-.387q-1.8-1.475-4.037-2.287T12 7q-.525 0-1.012.038T10 7.15q-.625.1-1.125-.262t-.6-.988t.275-1.125t1-.6q.6-.1 1.213-.137T12 4q3.125 0 5.888 1.038T22.85 7.9q.5.425.525 1.05t-.425 1.075" />
                </svg>
                <h1>Anda offline</h1>
                <p class="small-text">Silakan periksa koneksi Anda dan coba lagi.</p> -->

                <!-- ver 2 -->
                <img src="/images/icons/icon-152x152.png" alt="logo lsp untan" >
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28px" height="28px" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path fill="currentColor" d="M6.5 20q-2.3 0-3.9-1.6T1 14.5q0-1.925 1.188-3.425T5.25 9.15q.075-.2.15-.387t.15-.413L2.1 4.9q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l17 17q.275.275.288.688t-.288.712q-.275.275-.687.288t-.713-.263L17.15 20zm0-2h8.65L7.1 9.95q-.05.275-.075.525T7 11h-.5q-1.45 0-2.475 1.025T3 14.5t1.025 2.475T6.5 18m15.1.75l-1.45-1.4q.425-.35.638-.812T21 15.5q0-1.05-.725-1.775T18.5 13H17v-2q0-2.075-1.463-3.537T12 6q-.675 0-1.3.163t-1.2.512l-1.45-1.45q.875-.6 1.863-.912T12 4q2.925 0 4.963 2.038T19 11q1.725.2 2.863 1.488T23 15.5q0 .975-.375 1.813T21.6 18.75m-6.775-6.725" />
                    </svg>
                    <h1>Anda offline</h1>
                </div>
            </div>
        </div>
    </div>

</body>

</html>