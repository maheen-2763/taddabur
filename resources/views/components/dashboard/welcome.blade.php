{{-- resources/views/components/dashboard/welcome.blade.php --}}

<div class="d-card d-welcome-card">

    {{-- Header row: Salam left, Dates right --}}
    <div class="d-welcome-header">
        <div class="d-greeting-salam">السلام عليكم ورحمة الله وبركاته</div>
        <div class="d-welcome-dates">
            <span class="d-date-hijri">{{ \App\Helpers\ArabicHelper::hijriDate() }}</span>
            <span class="d-date-divider">·</span>
            <span class="d-date-gregorian">{{ now()->format('l, j F Y') }}</span>
        </div>
    </div>

    <div class="d-greeting-name">{{ $user->name }}</div>

    <div class="d-greeting-dynamic">
        <i class="bi {{ \App\Helpers\GreetingHelper::getTimeIcon() }} d-greeting-icon"></i>
        {{ \App\Helpers\GreetingHelper::getTimeBasedGreeting() }}
        <span class="d-greeting-arabic">بَارَكَ اللّٰهُ فِيكَ</span>
    </div>

    <div class="d-greeting-ayah-wrap">
        <p class="arabic">رَّبِّ زِدْنِي عِلْمًا</p>
        <p class="d-greeting-translation">"My Lord, increase me in knowledge."</p>
        <small class="d-greeting-source">— Surah Taha (20:114)</small>
    </div>

    <p class="text-muted mt-3 mb-0" style="font-size:0.85rem">
        May Allah increase you in beneficial knowledge, deepen your
        understanding of the Quran, and make your journey of Taddabur
        a source of light.
    </p>

    @if (!$user->isPremium())
        <div class="mt-3 text-end">
            <a href="{{ route('subscription.upgrade') }}" class="btn-gold btn btn-sm">
                Upgrade Plan
            </a>
        </div>
    @endif

</div>
