@php
    $fontRegularPath = public_path('fonts/Bahij Nassim-Regular.ttf');
    $fontBoldPath = public_path('fonts/Bahij Nassim-Bold.ttf');
    $fontRegularB64 = file_exists($fontRegularPath) ? base64_encode(file_get_contents($fontRegularPath)) : null;
    $fontBoldB64 = file_exists($fontBoldPath) ? base64_encode(file_get_contents($fontBoldPath)) : null;
@endphp
@if($fontRegularB64 && $fontBoldB64)
<style>
    @font-face {
        font-family: 'Bahij Nassim';
        src: url(data:font/ttf;base64,{{ $fontRegularB64 }}) format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }
    @font-face {
        font-family: 'Bahij Nassim';
        src: url(data:font/ttf;base64,{{ $fontBoldB64 }}) format('truetype');
        font-weight: 700;
        font-style: normal;
        font-display: swap;
    }
</style>
@endif
