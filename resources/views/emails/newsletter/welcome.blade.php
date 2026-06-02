@php
    $copy = match ($locale) {
        'ru' => [
            'title' => 'Спасибо за подписку на VERTONI',
            'lead' => 'Теперь вы будете получать новости о новых изделиях, специальных предложениях и важных обновлениях бренда.',
            'button' => 'Перейти на сайт',
            'footer' => 'Вы получили это письмо, потому что подписались на новости VERTONI.',
        ],
        'en' => [
            'title' => 'Thank you for subscribing to VERTONI',
            'lead' => 'You will now receive updates about new pieces, special offers and important brand news.',
            'button' => 'Visit website',
            'footer' => 'You received this email because you subscribed to VERTONI updates.',
        ],
        default => [
            'title' => 'Շնորհակալություն VERTONI-ին բաժանորդագրվելու համար',
            'lead' => 'Այսուհետ կստանաք նոր ապրանքների, հատուկ առաջարկների և կարևոր թարմացումների մասին նամակներ։',
            'button' => 'Անցնել կայք',
            'footer' => 'Այս նամակը ստացել եք, որովհետև բաժանորդագրվել եք VERTONI-ի նորություններին։',
        ],
    };
@endphp
<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $copy['title'] }}</title>
</head>
<body style="margin:0;background:#f7f2ea;font-family:Arial,Helvetica,sans-serif;color:#2b2118;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f7f2ea;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#fffaf3;border-radius:22px;overflow:hidden;border:1px solid #e3d4bf;">
                    <tr>
                        <td style="padding:34px 30px 18px;text-align:center;background:#24170f;color:#fff7e8;">
                            <div style="font-size:14px;letter-spacing:4px;text-transform:uppercase;opacity:.8;">VERTONI</div>
                            <h1 style="margin:18px 0 0;font-size:28px;line-height:1.25;">{{ $copy['title'] }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px;text-align:center;">
                            <p style="margin:0 0 26px;font-size:16px;line-height:1.7;">{{ $copy['lead'] }}</p>
                            <a href="{{ $frontendUrl }}" style="display:inline-block;background:#8b5a2b;color:#fff;text-decoration:none;padding:13px 24px;border-radius:999px;font-weight:700;">{{ $copy['button'] }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 30px 26px;text-align:center;color:#7a6a5b;font-size:12px;line-height:1.6;">
                            {{ $copy['footer'] }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
