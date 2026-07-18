<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Taddabur</title>
</head>

<body style="margin:0; padding:0; background-color:#F1EAD9; font-family: Georgia, 'Times New Roman', serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#F1EAD9; padding: 32px 16px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:560px; background-color:#ffffff; border-radius:8px; overflow:hidden; border:1px solid #E5DDD0;">

                    {{-- Header band (same as welcome email) --}}
                    <tr>
                        <td style="background-color:#0D3D22; padding: 32px 32px 28px; text-align:center;">

                            <div style="margin-bottom: 10px;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="56"
                                    height="56" style="display:inline-block;">
                                    <path
                                        d="M50 8 C37 8 26 17 24 29 C22 42 30 54 43 57 C36 54 31 47 31 39 C31 27 39 17 50 15 C51 15 52 14.5 53 14 C52 11 51 8 50 8 Z"
                                        fill="#E8BE6D" />
                                    <path
                                        d="M53 14 C60 16 66 24 66 33 C66 44 59 53 49 56 C52 55 55 53 57 50 C62 46 65 40 65 33 C65 24 60 17 53 14 Z"
                                        fill="#C9963A" opacity="0.5" />
                                    <path
                                        d="M68 17 L69.4 21.8 L74 22 L70.6 25 L71.8 30 L68 27.5 L64.2 30 L65.4 25 L62 22 L66.6 21.8 Z"
                                        fill="#E8BE6D" opacity="0.9" />
                                    <rect x="48.5" y="57" width="3" height="4" rx="1.5" fill="#C9963A"
                                        opacity="0.7" />
                                    <rect x="48.5" y="62" width="3" height="4" rx="1.5" fill="#C9963A"
                                        opacity="0.6" />
                                    <path d="M34 68 C36 63 42 66 50 65 C58 66 64 63 66 68 Z" fill="#E8BE6D"
                                        opacity="0.9" />
                                    <circle cx="50" cy="65" r="2" fill="#E8BE6D" opacity="0.85" />
                                    <path d="M36 68 L33 90 Q33 93 36 93 L64 93 Q67 93 67 90 L64 68 Z" fill="#C9963A"
                                        opacity="0.82" />
                                    <line x1="43" y1="68" x2="40" y2="93" stroke="#0D3D22"
                                        stroke-width="0.8" opacity="0.4" />
                                    <line x1="57" y1="68" x2="60" y2="93" stroke="#0D3D22"
                                        stroke-width="0.8" opacity="0.4" />
                                    <rect x="33" y="78" width="34" height="3" fill="#9A6F2B" opacity="0.45" />
                                    <path d="M43 69 L43 77 L57 77 L57 69 Z" fill="#FAF6EE" opacity="0.25" />
                                    <path d="M43 81 L43 90 L57 90 L57 81 Z" fill="#FAF6EE" opacity="0.22" />
                                    <ellipse cx="50" cy="80" rx="5" ry="6" fill="#FAF6EE"
                                        opacity="0.12" />
                                    <path d="M33 93 L36 97 L64 97 L67 93 Z" fill="#9A6F2B" opacity="0.7" />
                                    <circle cx="41" cy="70" r="1.2" fill="#FAF6EE" opacity="0.35" />
                                    <circle cx="59" cy="70" r="1.2" fill="#FAF6EE" opacity="0.35" />
                                </svg>
                            </div>

                            <div
                                style="font-family: Georgia, serif; font-size: 22px; font-weight: bold;
                                        color: #E8BE6D; letter-spacing: 5px; line-height: 1;">
                                TADDABUR
                            </div>

                            <div style="font-family: 'Amiri', 'Times New Roman', serif; font-size: 13px;
                                        color: rgba(232,190,109,0.62); direction: rtl; margin-top: 5px;"
                                lang="ar" dir="rtl">
                                تدبر
                            </div>

                            <div
                                style="margin: 14px auto 0; width: 60px; height: 1px;
                                        background: rgba(201,150,58,0.4);">
                            </div>

                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px 32px 24px;">
                            <h1 style="margin:0 0 16px; font-size: 22px; color:#1A1A2E;">
                                Verify Your Email, {{ $name }}
                            </h1>
                            <p style="margin:0 0 24px; font-size: 15px; line-height: 1.7; color:#4a4a4a;">
                                Almost there — please confirm your email address to activate your Taddabur
                                account and start exploring the Qur'an, Tafsir, and stories of the Prophets.
                            </p>

                            {{-- CTA button --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto 28px;">
                                <tr>
                                    <td style="border-radius: 8px; background-color:#C9963A;">
                                        <a href="{{ $verificationUrl }}" target="_blank"
                                            style="display:inline-block; padding: 13px 32px; font-size: 15px; font-weight:bold; color:#1A1A2E; text-decoration:none; font-family: Arial, sans-serif;">
                                            Verify Email Address →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 8px; font-size: 13px; line-height: 1.6; color:#9a9a9a;">
                                If the button doesn't work, copy and paste this link into your browser:
                            </p>
                            <p
                                style="margin:0 0 24px; font-size: 12px; line-height: 1.6; color:#C9963A; word-break: break-all;">
                                {{ $verificationUrl }}
                            </p>

                            <p style="margin:0; font-size: 13px; line-height: 1.6; color:#9a9a9a;">
                                If you did not create a Taddabur account, no further action is required.
                            </p>
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
