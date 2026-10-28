<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="color-scheme" content="light">
  <meta name="supported-color-schemes" content="light">
  <title>@yield('title', 'Taddabur')</title>
  <style>
    @media only screen and (max-width: 480px) {
      .px {
        padding-left: 22px !important;
        padding-right: 22px !important;
      }

      .h1 {
        font-size: 24px !important;
      }
    }
  </style>
</head>

<body style="margin:0;padding:0;background-color:#f3ede0;">

  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;font-size:1px;line-height:1px;">
    @yield('preheader')&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
  </div>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3ede0;">
    <tr>
      <td align="center" style="padding:28px 12px;">

        <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
          style="width:100%;max-width:600px;background-color:#fffdf8;border:1px solid #e3d6b8;border-radius:12px;">

          {{-- Header --}}
          <tr>
            <td align="center" bgcolor="#0f3d24"
              style="background-color:#0f3d24;padding:32px 24px 24px;border-radius:12px 12px 0 0;">
              @include('mails.partials.arabic-image', ['file' => 'bismillah', 'alt' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'width' => 230, 'align' => 'center', 'color' => '#e3c27a', 'size' => 24])
              <div style="height:16px;line-height:16px;font-size:0;">&nbsp;</div>
              @if (file_exists(public_path('images/mail-logo.png')))
                <img
                  src="{{ isset($message) ? $message->embed(public_path('images/mail-logo.png')) : asset('images/mail-logo.png') }}"
                  width="277" alt="Taddabur"
                  style="display:block;margin:0 auto;border:0;width:277px;max-width:100%;height:auto;">
              @else
                <div
                  style="font-family:Georgia,'Times New Roman',serif;font-size:28px;line-height:1.2;letter-spacing:7px;color:#ffffff;text-transform:uppercase;">
                  Taddabur</div>
                <div dir="rtl"
                  style="font-family:'Traditional Arabic','Noto Naskh Arabic','Amiri',Georgia,serif;font-size:20px;line-height:1.6;color:#c9963a;">
                  تَدَبُّر</div>
              @endif
              <div
                style="font-family:Georgia,'Times New Roman',serif;font-style:italic;font-size:13px;line-height:1.6;letter-spacing:1px;color:#cfd8cf;padding-top:6px;">
                Understand. Reflect. Grow.</div>
            </td>
          </tr>
          <tr>
            <td height="4" bgcolor="#c9963a" style="height:4px;line-height:4px;font-size:0;background-color:#c9963a;">
              &nbsp;</td>
          </tr>

          {{-- Body --}}
          <tr>
            <td class="px"
              style="padding:36px 40px 12px;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.7;color:#3a3a35;">
              <h1 class="h1"
                style="margin:0;font-family:Georgia,'Times New Roman',serif;font-size:28px;line-height:1.25;font-weight:normal;color:#14301f;">
                @yield('heading')</h1>
              <div
                style="width:48px;height:3px;line-height:3px;font-size:0;background-color:#c9963a;margin:14px 0 22px;">
                &nbsp;</div>
              @yield('content')
            </td>
          </tr>

          {{-- Verse card (optional) --}}
          @hasSection('verse')
            <tr>
              <td class="px" style="padding:8px 40px 32px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                  style="background-color:#f6efdc;border-left:3px solid #c9963a;border-radius:0 8px 8px 0;">
                  <tr>
                    <td style="padding:18px 22px;font-family:Georgia,'Times New Roman',serif;color:#3a3a35;">
                      @yield('verse')
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          @endif

          {{-- Footer --}}
          <tr>
            <td class="px" align="center"
              style="padding:24px 40px 30px;border-top:1px solid #eadfc6;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.7;color:#7a7463;">
              @hasSection('footnote')
                <div style="margin-bottom:12px;">@yield('footnote')</div>
              @endif
              <div style="font-family:Georgia,'Times New Roman',serif;font-style:italic;font-size:13px;color:#9a6f1c;">
                Free to understand. Free to reflect. Free forever.</div>
              <div style="margin-top:8px;">
                Need help? <a href="mailto:{{ config('mail.from.address') }}"
                  style="color:#9a6f1c;text-decoration:none;">{{ config('mail.from.address') }}</a>
              </div>
              <div style="margin-top:6px;">
                <a href="{{ route('terms') }}" style="color:#7a7463;">Terms</a> &nbsp;&middot;&nbsp; <a
                  href="{{ route('privacy') }}" style="color:#7a7463;">Privacy Policy</a>
              </div>
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>
</body>

</html>