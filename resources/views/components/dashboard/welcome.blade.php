{{-- resources/views/components/dashboard/welcome.blade.php --}}

<div class="d-card">

    <div class="d-greeting-salam">السلام عليكم ورحمة الله وبركاته</div>
    <div class="d-greeting-name">{{ $user->name }} 🌙</div>
    <div class="d-greeting-date">
        {{ \App\Helpers\ArabicHelper::hijriDate() }} · {{ now()->format('l, j F Y') }}
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
