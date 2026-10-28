<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') — Taddabur</title>
    @yield('head')
    <style>
        @font-face {
            font-family: 'Amiri Quran';
            src: url('/fonts/amiri-quran-arabic.woff2') format('woff2');
            font-display: swap;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(160deg, #1B5E3B 0%, #0a2a18 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            text-align: center;
            color: #fff;
            padding: 2rem 1.25rem;
        }

        .box {
            width: 100%;
            max-width: 460px;
        }

        .brand {
            display: inline-block;
            margin-bottom: 1.75rem;
        }

        .brand img {
            display: block;
            width: 220px;
            max-width: 100%;
            height: auto;
        }

        .istirja {
            font-family: 'Amiri Quran', 'Amiri', 'Traditional Arabic', 'Noto Naskh Arabic', serif;
            font-size: 1.7rem;
            color: #E3C27A;
            margin: 0 0 0.5rem;
            line-height: 2;
        }

        .translation {
            color: rgba(255, 255, 255, 0.8);
            font-style: italic;
            font-size: 0.95rem;
            margin: 0 0 1.5rem;
        }

        .code {
            font-size: 4rem;
            font-weight: 700;
            color: #C9963A;
            margin: 0;
            line-height: 1.1;
        }

        h1 {
            font-size: 1.3rem;
            margin: 0.5rem 0 0.75rem;
        }

        .msg {
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.7;
            font-size: 0.95rem;
            margin: 0 0 0.5rem;
        }

        .actions {
            margin-top: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            padding: 0.7rem 1.8rem;
            background: #C9963A;
            color: #0a2a18;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
        }

        .btn:hover {
            background: #E3C27A;
        }

        .btn-ghost {
            background: transparent;
            color: #E3C27A;
            border: 1px solid rgba(227, 194, 122, 0.5);
        }

        .btn-ghost:hover {
            background: rgba(227, 194, 122, 0.12);
        }

        .btn:focus-visible {
            outline: 3px solid #fff;
            outline-offset: 3px;
        }

        .note {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.7);
        }

        [hidden] {
            display: none !important;
        }
    </style>
</head>

<body>
    <main class="box">
        <a href="/" class="brand" aria-label="Taddabur home">
            <img src="/images/mail-logo.png" alt="Taddabur" width="220" height="46">
        </a>

        @hasSection('istirja')
            <p class="istirja" lang="ar" dir="rtl">@yield('istirja')</p>
            <p class="translation">@yield('translation')</p>
        @endif

        @hasSection('code')
            <p class="code">@yield('code')</p>
        @endif

        <h1>@yield('heading')</h1>
        <p class="msg">@yield('message')</p>

        @yield('extra')

        <div class="actions">
            @hasSection('actions')
                @yield('actions')
            @else
                <a class="btn" href="/">Return Home</a>
            @endif
        </div>

        @hasSection('note')
            <p class="note">@yield('note')</p>
        @endif
    </main>
</body>

</html>