@extends('mails.layout')
@section('title', 'Welcome to Taddabur')
@section('preheader', 'Your account is ready. Begin your journey with the Quran.')
@section('heading', 'Welcome to Taddabur')

@section('content')
  <p style="margin:0 0 14px;">Assalamu alaikum {{ \Illuminate\Support\Str::before($name, ' ') }},</p>
  <p style="margin:0 0 18px;">Your email is verified and your account is ready. Taddabur is a free space to read,
    understand and reflect on the Quran. Here is where you can begin:</p>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 6px;">
    @foreach ([
        ['url' => route('quran.index'), 'title' => 'Read and listen to the Quran', 'text' => 'Five translations, scholar tafsir and 12 reciters.'],
        ['url' => route('hadith.index'), 'title' => 'Explore the Hadith', 'text' => 'Six major collections, searchable, with clear grade labels.'],
        ['url' => route('stories.index'), 'title' => 'Stories of the Prophets', 'text' => '25 stories built from explicit Quranic statements.'],
        ['url' => route('dashboard'), 'title' => 'Your journey', 'text' => 'Your reading progress, bookmarks and notes are saved in your account.'],
      ] as $item)
      <tr>
        <td valign="top" width="28" style="padding:8px 0;font-size:16px;line-height:1.5;color:#c9963a;">&#10022;</td>
        <td style="padding:8px 0;font-size:15px;line-height:1.5;color:#3a3a35;">
          <a href="{{ $item['url'] }}"
            style="color:#14301f;font-weight:bold;text-decoration:none;border-bottom:1px solid #c9963a;">{{ $item['title'] }}</a>
          <div style="font-size:14px;line-height:1.5;color:#6b6556;padding-top:2px;">{{ $item['text'] }}</div>
        </td>
      </tr>
    @endforeach
  </table>

  @include('mails.partials.button', ['url' => route('dashboard'), 'label' => 'Continue to Taddabur'])
@endsection

@section('verse')
  <div style="font-size:15px;line-height:1.7;font-style:italic;color:#14301f;">"Read! In the name of your Lord who
    created."</div>
  <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#9a6f1c;margin-top:6px;">Al-&lsquo;Alaq 96:1
  </div>
@endsection

@section('footnote')
  You are receiving this email because you created an account on Taddabur.
@endsection