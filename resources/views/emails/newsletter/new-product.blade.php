@php
    $copy = match ($locale) {
        'ru' => [
            'eyebrow' => 'Новинка',
            'title' => 'Новый товар уже на сайте',
            'button' => 'Посмотреть товар',
            'price' => 'Цена',
            'footer' => 'Вы получили это письмо, потому что подписались на новости VERTONI.',
        ],
        'en' => [
            'eyebrow' => 'New arrival',
            'title' => 'A new product is now available',
            'button' => 'View product',
            'price' => 'Price',
            'footer' => 'You received this email because you subscribed to VERTONI updates.',
        ],
        default => [
            'eyebrow' => 'Նորույթ',
            'title' => 'Նոր ապրանքը արդեն կայքում է',
            'button' => 'Դիտել ապրանքը',
            'price' => 'Գին',
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
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#fffaf3;border-radius:22px;overflow:hidden;border:1px solid #e3d4bf;">
                    <tr>
                        <td style="padding:28px 30px;text-align:center;background:#24170f;color:#fff7e8;">
                            <div style="font-size:13px;letter-spacing:4px;text-transform:uppercase;color:#d9b06f;">{{ $copy['eyebrow'] }}</div>
                            <h1 style="margin:14px 0 0;font-size:30px;line-height:1.25;">{{ $copy['title'] }}</h1>
                        </td>
                    </tr>
                    @if($imageUrl)
                        <tr>
                            <td style="padding:0;background:#2b2118;">
                                <img src="{{ $imageUrl }}" alt="{{ $productName }}" style="display:block;width:100%;max-height:360px;object-fit:cover;border:0;">
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="padding:30px;">
                            <h2 style="margin:0 0 12px;font-size:25px;line-height:1.3;color:#2b2118;">{{ $productName }}</h2>
                            @if($shortDescription)
                                <p style="margin:0 0 18px;font-size:15px;line-height:1.7;color:#5b4b3d;">{{ $shortDescription }}</p>
                            @endif
                            @if($product->display_price !== null)
                                <p style="margin:0 0 24px;font-size:16px;color:#2b2118;"><strong>{{ $copy['price'] }}:</strong> {{ number_format((float) $product->display_price, 0, '.', ' ') }} ֏</p>
                            @endif
                            <a href="{{ $productUrl }}" style="display:inline-block;background:#8b5a2b;color:#fff;text-decoration:none;padding:13px 24px;border-radius:999px;font-weight:700;">{{ $copy['button'] }}</a>
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
