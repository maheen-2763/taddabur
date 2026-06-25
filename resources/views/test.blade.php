<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Al-Quran Interactive Gateway</title>
    <link rel="stylesheet" href="{{ asset('css/test.css') }}?v={{ time() }}">
</head>

<body>

    <div class="portal-container">
        <div class="sacred-gate">

            <!-- Left Gate (Folds Left) -->
            <div class="gate left-gate">
                <div class="gate-pattern left-pattern">
                    <div class="gate-title-half">القرآن</div>
                </div>
            </div>

            <!-- Right Gate (Folds Right) -->
            <div class="gate right-gate">
                <div class="gate-pattern right-pattern">
                    <div class="gate-title-half">الكريم</div>
                </div>
            </div>

            <!-- The Inner Sanctuary Page (Revealed Behind Gates) -->
            <div class="sanctuary-core">
                <div class="divine-noor"></div>
                <div class="geometric-overlay"></div>

                <h3 class="sanctuary-title">الْفِهْرِسْ <br><span>The Sacred Index</span></h3>

                <div class="mihrab-books-wrapper">
                    <!-- Mihrab Book 1: 30 Juz -->
                    <a href="/juz" class="mihrab-book">
                        <div class="mihrab-cover">
                            <div class="arch-design"></div>
                            <span class="arabic">الأجزاء</span>
                            <span class="english">30 JUZ</span>
                        </div>
                        <div class="mihrab-inside">
                            <span>Enter<br>Index</span>
                        </div>
                    </a>

                    <!-- Mihrab Book 2: Surah List -->
                    <a href="/surah" class="mihrab-book">
                        <div class="mihrab-cover">
                            <div class="arch-design"></div>
                            <span class="arabic">السور</span>
                            <span class="english">SURAH</span>
                        </div>
                        <div class="mihrab-inside">
                            <span>Enter<br>Index</span>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
