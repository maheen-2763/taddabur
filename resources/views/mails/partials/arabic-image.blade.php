{{--
Arabic ko image ke taur par dikhata hai, taaki har mail app mein Amiri jaisa hi dikhe.
$file : public/images/mail/<file>.png
    $alt : Arabic text (image na khule to yahi dikhega)
    $width : px
    $align : 'center' | 'right'
    $color / $size : sirf fallback text ke liye
    --}}
    @php
        $path = public_path('images/mail/' . $file . '.png');
        $align = $align ?? 'right';
        $color = $color ?? '#14301f';
        $size = $size ?? 26;
    @endphp
    @if (file_exists($path))
        <img src="{{ isset($message) ? $message->embed($path) : asset('images/mail/' . $file . '.png') }}"
            width="{{ $width }}" alt="{{ $alt }}"
            style="display:block;border:0;width:{{ $width }}px;max-width:100%;height:auto;{{ $align === 'center' ? 'margin:0 auto;' : 'margin-left:auto;' }}">
    @else
        <div dir="rtl"
            style="font-family:'Traditional Arabic','Noto Naskh Arabic','Amiri',Georgia,serif;font-size:{{ $size }}px;line-height:1.9;color:{{ $color }};text-align:{{ $align }};">
            {{ $alt }}</div>
    @endif