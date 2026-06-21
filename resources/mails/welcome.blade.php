<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Taddabur</title>
</head>

<body style="margin:0; padding:0; background-color:#F1EAD9; font-family: Georgia, 'Times New Roman', serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#F1EAD9; padding: 32px 16px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:560px; background-color:#ffffff; border-radius:8px; overflow:hidden; border:1px solid #E5DDD0;">

                    {{-- Header band --}}
                    <tr>
                        <td style="background-color:#0D3D22; padding: 36px 32px; text-align:center;">
                            <div style="font-family: 'Amiri', 'Times New Roman', serif; font-size: 22px; color:#E8BE6D; opacity:0.9; margin-bottom: 6px;"
                                dir="rtl" lang="ar">
                                بِسْمِ اللَّهِ
                            </div>
                            <div style="font-size: 24px; font-weight:bold; color:#E8BE6D; letter-spacing: -0.3px;">
                                🌙 Taddabur
                            </div>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px 32px 24px;">
                            <h1 style="margin:0 0 16px; font-size: 22px; color:#1A1A2E;">
                                Welcome, {{ $name }}
                            </h1>
                            <p style="margin:0 0 16px; font-size: 15px; line-height: 1.7; color:#4a4a4a;">
                                Your account is ready. Taddabur is a simple space to read the Qur'an, understand
                                its Tafsir, and learn the stories of the Prophets — built so every Muslim can
                                access deep Quranic knowledge, free to start.
                            </p>

                            {{-- Ayah block --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background-color:#FAF6EE; border-left: 3px solid #C9963A; border-radius: 4px; margin: 24px 0;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <div style="font-family: 'Amiri', 'Times New Roman', serif; font-size: 20px; color:#0D3D22; text-align:right; line-height: 1.9; margin-bottom: 8px;"
                                            dir="rtl" lang="ar">
                                            اقْرَأْ بِاسْمِ رَبِّكَ الَّذِي خَلَقَ
                                        </div>
                                        <div
                                            style="font-size: 13px; color:#9A6F2B; font-style: italic; text-align:right;">
                                            "Read in the name of your Lord who created." — Al-'Alaq 96:1
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 28px; font-size: 15px; line-height: 1.7; color:#4a4a4a;">
                                That was the very first command revealed — to read. We'd be honored to be part
                                of yours.
                            </p>

                            {{-- CTA button --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                                <tr>
                                    <td style="border-radius: 8px; background-color:#C9963A;">
                                        <a href="{{ url('/quran') }}" target="_blank"
                                            style="display:inline-block; padding: 13px 32px; font-size: 15px; font-weight:bold; color:#1A1A2E; text-decoration:none; font-family: Arial, sans-serif;">
                                            Start Reading →
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 24px 32px 32px; border-top: 1px solid #E5DDD0;">
                            <p
                                style="margin:0; font-size: 12px; color:#9a9a9a; line-height:1.6; font-family: Arial, sans-serif;">
                                You're receiving this because you created a Taddabur account.<br>
                                Taddabur — Read the Quran. Understand the Tafsir. Know the Prophets.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
