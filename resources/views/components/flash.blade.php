@if (session('success'))
    <div class="container mt-3 flash-autohide" data-autohide="5000">
        <div class="alert alert-islamic-success d-flex align-items-center gap-2" role="status">
            <i class="bi bi-check-circle-fill"></i>
            <span class="flex-grow-1">{{ session('success') }}</span>
            <button type="button" class="btn-close" aria-label="Close" data-autohide-close></button>
        </div>
    </div>

    @once
        <style>
            .flash-autohide {
                transition: opacity .5s ease, transform .5s ease;
            }

            .flash-autohide.is-hiding {
                opacity: 0;
                transform: translateY(-6px);
            }

            @media (prefers-reduced-motion: reduce) {
                .flash-autohide {
                    transition: none;
                }
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-autohide]').forEach((el) => {
                    const delay = parseInt(el.dataset.autohide, 10) || 5000;
                    let timer;
                    let gone = false;

                    const hide = () => {
                        if (gone) return;
                        gone = true;
                        clearTimeout(timer);
                        el.classList.add('is-hiding');
                        setTimeout(() => el.remove(), 500);
                    };
                    const start = () => { if (!gone) timer = setTimeout(hide, delay); };
                    const stop = () => clearTimeout(timer);

                    // Hover/focus par ruk jata hai, hatne par phir 5 second ka timer
                    el.addEventListener('mouseenter', stop);
                    el.addEventListener('mouseleave', start);
                    el.addEventListener('focusin', stop);
                    el.addEventListener('focusout', start);
                    el.querySelector('[data-autohide-close]')?.addEventListener('click', hide);
                    start();
                });
            });
        </script>
    @endonce
@endif