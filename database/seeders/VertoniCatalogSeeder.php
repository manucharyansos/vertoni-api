<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class VertoniCatalogSeeder extends Seeder
{
    private string $assetBase = 'https://verthoni.com/images/home/';

    public function run(): void
    {
        $categories = $this->seedCategories();
        $this->seedProducts($categories);
        $this->seedHomeContent($categories);
        $this->seedSettings();
        $this->replacePlaceholderContent();
    }

    private function url(string $file): string
    {
        return $this->assetBase . $file;
    }

    private function seedAsset(string $relativePath): ?string
    {
        $source = database_path('seeders/assets/' . ltrim($relativePath, '/'));

        if (! File::exists($source)) {
            return null;
        }

        $target = storage_path('app/public/' . ltrim($relativePath, '/'));
        File::ensureDirectoryExists(dirname($target));
        File::copy($source, $target);

        return ltrim($relativePath, '/');
    }


    private function containsPlaceholder(?string $value): bool
    {
        if (! filled($value)) {
            return false;
        }

        return str_contains(mb_strtolower(strip_tags((string) $value)), 'lorem')
            || str_contains(mb_strtolower(strip_tags((string) $value)), 'ipsum')
            || str_contains(mb_strtolower(strip_tags((string) $value)), 'լոռեմ');
    }

    private function replacePlaceholderContent(): void
    {
        Category::query()->get()->each(function (Category $category): void {
            $needsFix = collect([
                $category->description_hy,
                $category->description_ru,
                $category->description_en,
                $category->menu_description,
                $category->home_description_hy,
                $category->home_description_ru,
                $category->home_description_en,
            ])->contains(fn ($value) => $this->containsPlaceholder($value));

            if (! $needsFix) {
                return;
            }

            $category->forceFill([
                'description_hy' => 'Կաշվե արտադրանքի բաժին՝ ընտրված մոդելներով, նկարներով և անհատական պատվերի հնարավորությամբ։',
                'description_ru' => 'Раздел кожаных изделий с выбранными моделями, фотографиями и возможностью индивидуального заказа.',
                'description_en' => 'A leather goods category with selected models, photos and custom-order options.',
                'menu_description' => 'Ընտրված կաշվե արտադրանք՝ մաքուր դիզայնով և պատվերի հնարավորությամբ։',
                'home_description_hy' => 'Ընտրված կաշվե արտադրանք՝ մաքուր դիզայնով և պատվերի հնարավորությամբ։',
                'home_description_ru' => 'Отобранные кожаные изделия с чистым дизайном и возможностью заказа.',
                'home_description_en' => 'Selected leather goods with clean design and order options.',
            ])->save();
        });

        Product::query()->get()->each(function (Product $product): void {
            $needsFix = collect([
                $product->short_description_hy,
                $product->short_description_ru,
                $product->short_description_en,
                $product->description_hy,
                $product->description_ru,
                $product->description_en,
                $product->meta_description_hy,
                $product->meta_description_ru,
                $product->meta_description_en,
            ])->contains(fn ($value) => $this->containsPlaceholder($value));

            if (! $needsFix) {
                return;
            }

            $nameHy = $product->name_hy ?: 'Կաշվե արտադրանք';
            $nameRu = $product->name_ru ?: 'Кожаное изделие';
            $nameEn = $product->name_en ?: 'Leather item';

            $product->forceFill([
                'short_description_hy' => $nameHy . '՝ բնական կաշվից, կոկիկ մշակմամբ և առօրյա օգտագործման համար հարմար կառուցվածքով։',
                'short_description_ru' => $nameRu . ' из натуральной кожи с аккуратной отделкой и практичной конструкцией для ежедневного использования.',
                'short_description_en' => $nameEn . ' made from natural leather with clean finishing and a practical everyday structure.',
                'description_hy' => '<p>Ապրանքը ստեղծված է ընտրված կաշվից՝ ուշադրություն դարձնելով ձևին, կարին և օգտագործման հարմարությանը։ Գույնը, չափը և դետալները կարելի է ճշտել պատվերի ժամանակ։</p>',
                'description_ru' => '<p>Изделие выполнено из выбранной кожи с вниманием к форме, строчке и удобству использования. Цвет, размер и детали можно уточнить при заказе.</p>',
                'description_en' => '<p>The item is made from selected leather with attention to shape, stitching and practical use. Colour, size and details can be confirmed during ordering.</p>',
                'meta_description_hy' => $nameHy . '՝ բնական կաշվից և անհատական պատվերի հնարավորությամբ։',
                'meta_description_ru' => $nameRu . ' из натуральной кожи с возможностью индивидуального заказа.',
                'meta_description_en' => $nameEn . ' made from natural leather with custom-order options.',
            ])->save();
        });

        HomeSection::query()->get()->each(function (HomeSection $section): void {
            $needsFix = collect([
                $section->description_hy,
                $section->description_ru,
                $section->description_en,
            ])->contains(fn ($value) => $this->containsPlaceholder($value));

            if (! $needsFix) {
                return;
            }

            $section->forceFill([
                'description_hy' => 'Բաժինը ներկայացնում է VERTONI-ի ձեռքի աշխատանքը, կաշվի ընտրությունը և պատրաստի արտադրանքի պրեմիում տեսքը։',
                'description_ru' => 'Блок показывает ручную работу VERTONI, выбор кожи и премиальный вид готового изделия.',
                'description_en' => 'This section presents VERTONI craft, leather selection and the premium look of the finished item.',
            ])->save();
        });

        Banner::query()->get()->each(function (Banner $banner): void {
            $needsFix = collect([
                $banner->subtitle_hy,
                $banner->subtitle_ru,
                $banner->subtitle_en,
            ])->contains(fn ($value) => $this->containsPlaceholder($value));

            if (! $needsFix) {
                return;
            }

            $banner->forceFill([
                'subtitle_hy' => 'Կաշվե արտադրանք՝ ընտրված նյութերով, մաքուր դիզայնով և պատվերի հնարավորությամբ։',
                'subtitle_ru' => 'Кожаные изделия из выбранных материалов, с чистым дизайном и возможностью заказа.',
                'subtitle_en' => 'Leather goods made with selected materials, clean design and order options.',
            ])->save();
        });
    }

    private function seedCategories(): array
    {
        $definitions = [
            'shoes' => [
                'hy' => 'Կոշիկներ',
                'ru' => 'Обувь',
                'en' => 'Shoes',
                'desc_hy' => 'Բնական կաշվից կոշիկներ՝ ձեռքի մաքուր աշխատանքով և պրեմիում արտաքինով։',
                'desc_ru' => 'Обувь из натуральной кожи с аккуратной ручной отделкой и премиальным видом.',
                'desc_en' => 'Natural leather shoes with clean handcrafted finishing and a premium look.',
                'image' => '431470126_18249246283245376_2811127355696771942_n.jpg',
            ],
            'bags' => [
                'hy' => 'Պայուսակներ',
                'ru' => 'Сумки',
                'en' => 'Bags',
                'desc_hy' => 'Կաշվե պայուսակներ՝ ամենօրյա օգտագործման, գործնական տեսքի և անհատական պատվերի համար։',
                'desc_ru' => 'Кожаные сумки для ежедневного использования, делового образа и индивидуального заказа.',
                'desc_en' => 'Leather bags for daily use, business looks and custom orders.',
                'image' => 'hero-main.jpg',
            ],
            'wallets' => [
                'hy' => 'Դրամապանակներ',
                'ru' => 'Кошельки',
                'en' => 'Wallets',
                'desc_hy' => 'Դրամապանակներ, քարտապանակներ և փոքր կաշվե իրեր՝ մաքուր կարերով ու ամուր կառուցվածքով։',
                'desc_ru' => 'Кошельки, картхолдеры и небольшие кожаные изделия с аккуратными швами и прочной конструкцией.',
                'desc_en' => 'Wallets, cardholders and small leather goods with clean stitching and durable structure.',
                'image' => 'cat-wallets.jpg',
            ],
            'belts' => [
                'hy' => 'Գոտիներ',
                'ru' => 'Ремни',
                'en' => 'Belts',
                'desc_hy' => 'Կաշվե գոտիներ՝ դասական ձևով, ամուր ֆուռնիտուրայով և կարգավորվող չափերով։',
                'desc_ru' => 'Кожаные ремни классической формы, с прочной фурнитурой и регулируемыми размерами.',
                'desc_en' => 'Leather belts with classic shape, strong hardware and adjustable sizing.',
                'image' => 'pr-sling.jpg',
            ],
            'phone-cases' => [
                'hy' => 'Հեռախոսի պատյաններ',
                'ru' => 'Чехлы для телефона',
                'en' => 'Phone cases',
                'desc_hy' => 'Կաշվե պատյաններ հեռախոսների համար՝ պաշտպանիչ կառուցվածքով և ընդգծված դիզայնով։',
                'desc_ru' => 'Кожаные чехлы для телефонов с защитной конструкцией и выразительным дизайном.',
                'desc_en' => 'Leather phone cases with protective construction and distinctive design.',
                'image' => '128253415_223373355841829_4192908763760116336_n.jpg',
            ],
            'hats' => [
                'hy' => 'Գլխարկներ',
                'ru' => 'Кепки',
                'en' => 'Caps',
                'desc_hy' => 'Կաշվե դետալներով գլխարկներ՝ սպորտային և ամենօրյա կերպարի համար։',
                'desc_ru' => 'Кепки с кожаными деталями для спортивного и ежедневного образа.',
                'desc_en' => 'Caps with leather details for sporty and everyday styling.',
                'image' => '130712700_174677084356261_7035878406196892773_n.jpg',
            ],
            'accessories' => [
                'hy' => 'Աքսեսուարներ և նվերներ',
                'ru' => 'Аксессуары и подарки',
                'en' => 'Accessories & gifts',
                'desc_hy' => 'Փոքր կաշվե աքսեսուարներ, նվերային լուծումներ և անհատական դիզայնի փորձարկումներ։',
                'desc_ru' => 'Небольшие кожаные аксессуары, подарочные решения и индивидуальные дизайнерские варианты.',
                'desc_en' => 'Small leather accessories, gift solutions and custom design experiments.',
                'image' => '135847206_1035939966875270_2669387468219529570_n.jpg',
            ],
        ];

        $created = [];

        foreach ($definitions as $slug => $data) {
            $category = Category::updateOrCreate(
                ['slug_hy' => $slug],
                [
                    'type' => 'catalog',
                    'parent_id' => null,
                    'name_hy' => $data['hy'],
                    'name_ru' => $data['ru'],
                    'name_en' => $data['en'],
                    'slug_ru' => $slug,
                    'slug_en' => $slug,
                    'description_hy' => $data['desc_hy'],
                    'description_ru' => $data['desc_ru'],
                    'description_en' => $data['desc_en'],
                    'image' => $this->url($data['image']),
                    'menu_title' => $data['hy'],
                    'menu_description' => $data['desc_hy'],
                    'menu_image' => $this->url($data['image']),
                    'home_title_hy' => $data['hy'],
                    'home_title_ru' => $data['ru'],
                    'home_title_en' => $data['en'],
                    'home_description_hy' => $data['desc_hy'],
                    'home_description_ru' => $data['desc_ru'],
                    'home_description_en' => $data['desc_en'],
                    'home_image' => $this->url($data['image']),
                    'attribute_schema' => [
                        ['key' => 'material', 'label' => 'Նյութ', 'type' => 'text', 'filterable' => false],
                        ['key' => 'color', 'label' => 'Գույն', 'type' => 'text', 'filterable' => true],
                        ['key' => 'size', 'label' => 'Չափ', 'type' => 'text', 'filterable' => true],
                    ],
                    'is_active' => true,
                    'sort_order' => array_search($slug, array_keys($definitions), true) + 1,
                    'menu_order' => array_search($slug, array_keys($definitions), true) + 1,
                    'show_on_home' => in_array($slug, ['bags', 'shoes', 'wallets'], true),
                    'home_sort_order' => array_search($slug, array_keys($definitions), true) + 1,
                    'meta_title_hy' => $data['hy'] . ' | VERTONI',
                    'meta_title_ru' => $data['ru'] . ' | VERTONI',
                    'meta_title_en' => $data['en'] . ' | VERTONI',
                    'meta_description_hy' => $data['desc_hy'],
                    'meta_description_ru' => $data['desc_ru'],
                    'meta_description_en' => $data['desc_en'],
                ]
            );

            $created[$slug] = $category;
        }

        return $created;
    }

    private function seedProducts(array $categories): void
    {
        $groups = [
            'shoes' => [
                'category' => 'shoes',
                'base_hy' => 'Կաշվե կոշիկ',
                'base_ru' => 'Кожаная обувь',
                'base_en' => 'Leather shoes',
                'price' => 52000,
                'files' => [
                    '126811805_289908895657114_2212817038246664544_n.jpg',
                    '131474338_414530406266175_4128928689306987911_n.jpg',
                    '131894976_216541239972435_5132522011184223560_n.jpg',
                    '131897017_701771870732073_2473379255822023798_n.jpg',
                    '134123719_215274723570789_5646777592786011812_n.jpg',
                    '135247596_1103276860109829_5951802709857082286_n.jpg',
                    '135765286_392891491775858_1312193501548652305_n.jpg',
                    '143082526_158795882491258_5357291616928622174_n.jpg',
                    '144479340_333029491271543_7658549687841363733_n.jpg',
                    '146097662_474733167244096_1839040723139240376_n.jpg',
                    '148340627_413812859911856_6226020470243170729_n.jpg',
                    '150429893_2780470305615134_9144825285920925143_n.jpg',
                    '166181672_795221988057054_801621230698016484_n.jpg',
                    '173959386_123802386456862_6484887285799102847_n.jpg',
                    '174164120_288955862671034_1930070039187692558_n.jpg',
                    '174492102_1158065044653663_2523368017951732350_n.jpg',
                    '175937634_298296055171293_2900914336318728751_n.jpg',
                    '176472041_459916535295076_3527039909352375685_n.jpg',
                    '192661749_2742544662711096_1661749835912037418_n.jpg',
                    '218365969_198831895414836_4306282208533776781_n.jpg',
                    '219596791_144569354422660_9165681351144941726_n.jpg',
                    '219746072_350400776553505_2720710684003189561_n.jpg',
                    '236410730_4347067198691167_6380771675065106760_n.jpg',
                    '238511758_162264952686973_3538611581872373595_n.jpg',
                    '240677573_535675067708270_4880749909946647076_n.jpg',
                    '241371031_996574201124606_2984723566811984427_n.jpg',
                    '241434153_860531264592599_121415392265983633_n.jpg',
                    '242007481_230667525683224_152962916493486854_n.jpg',
                    '242211713_882769075953238_6915439781298599979_n.jpg',
                    '271323346_983268705881550_3852011811647026892_n.jpg',
                    '375219107_18224799106245376_3803605478103694906_n.jpg',
                    '376503551_18224750995245376_349704540299571908_n.jpg',
                    '376824610_18224950069245376_8232100505616658080_n.jpg',
                    '408786128_18237387565245376_4911938638559354787_n.jpg',
                    '424918830_18246961276245376_2242483425737814554_n.jpg',
                    '424952750_18246926419245376_9190692076758397970_n.jpg',
                    '431470126_18249246283245376_2811127355696771942_n.jpg',
                    '433883685_18251125441245376_4697570884514327745_n.jpg',
                    '433946581_18250895974245376_2696433990755195240_n.jpg',
                    '433953543_18250948744245376_6523124134555415918_n.jpg',
                    '434425225_18251013292245376_5586258401995846168_n.jpg',
                    '438162055_18255493036245376_8412139094142447783_n.jpg',
                    '438164749_18255863257245376_2676299573961236774_n.jpg',
                    '441135078_18255863134245376_3704205923958949841_n.jpg',
                    '452588240_18265034941245376_3660997902628787123_n.jpg',
                    '463109727_18275585713245376_8802695155574697877_n.jpg',
                    '474082621_18287496091245376_5899842619196203871_n.jpg',
                    '502698907_1616473232267985_2628205675144281445_n.jpg',
                    '504153492_701145622807872_7020632421881388330_n.jpg',
                    '504223274_1712245269683657_2989877622500962112_n.jpg',
                    '555300954_1113063410958593_1222754279368090218_n.jpg',
                    'cat-gifts.jpg',
                    'hero-side.jpg',
                    'pr-briefcase.jpg',
                    'pr-case.jpg',
                ],
            ],
            'bags' => [
                'category' => 'bags',
                'base_hy' => 'Կաշվե պայուսակ',
                'base_ru' => 'Кожаная сумка',
                'base_en' => 'Leather bag',
                'price' => 69000,
                'files' => [
                    '131850760_383635342918897_5012399966683357478_n.jpg',
                    '131891585_811992796017282_5618569434695719086_n.jpg',
                    '131930730_845772366272664_5576893995963934079_n.jpg',
                    '131944789_1257071584663548_942681721949917955_n.jpg',
                    '132299864_1028838374293048_6482560683728428692_n.jpg',
                    '136436782_874033790030385_7224702478484938661_n.jpg',
                    '234885936_2898136063732746_2253726848854944963_n.jpg',
                    '242025409_642383576728449_7527724503384134175_n.jpg',
                    '242860863_564279588163459_5128770719864606512_n.jpg',
                    '270229609_929640857944511_17437329603159272_n.jpg',
                    '402416180_18234801763245376_6882863337260916009_n.jpg',
                    '419348568_18242850538245376_9000571777248228034_n.jpg',
                    '424931897_18246478144245376_1746614103416021596_n.jpg',
                    '424955116_18247196689245376_8018683982916120003_n.jpg',
                    '424976932_18246428017245376_5617339919161921935_n.jpg',
                    '470901641_18284802847245376_5085953142446543552_n.jpg',
                    '474117208_18287233486245376_3807686899577802837_n.jpg',
                    '491087146_617421587967671_4282020771414988154_n.jpg',
                    '500778264_1194753431954078_6248894081654758351_n.jpg',
                    '504362852_1059977442221261_8949158919235845314_n.jpg',
                    '579598041_18541766089045147_6035188916783845510_n.jpg',
                    'cat-cases.jpg',
                    'hero-main.jpg',
                ],
            ],
            'wallets' => [
                'category' => 'wallets',
                'base_hy' => 'Կաշվե դրամապանակ',
                'base_ru' => 'Кожаный кошелек',
                'base_en' => 'Leather wallet',
                'price' => 24000,
                'files' => [
                    '133989573_116491090296119_7323587296697315976_n.jpg',
                    '135356490_891486704947597_8526221498275390298_n.jpg',
                    '135423260_426301808517751_963067457054136149_n.jpg',
                    '144058790_3839725982754521_1203222932118466818_n.jpg',
                    '151023747_1811389105683361_1123698790036868781_n.jpg',
                    '151122914_274867610648616_702565536861233655_n.jpg',
                    '218634132_492377055193657_7762148541405230361_n.jpg',
                    '307331596_1192481807980559_2751621682862946008_n.jpg',
                    '406683580_18236850061245376_5210518984651440608_n.jpg',
                    '424965899_18246907666245376_835950339871381252_n.jpg',
                    '553273203_18389242444122395_2872867952063057611_n.jpg',
                    '562409396_18377587792151707_3004463760474617265_n.jpg',
                    '562418207_18070249036990197_1550768953416322801_n.jpg',
                    'cat-belts.jpg',
                    'cat-wallets.jpg',
                    'pr-cardholder.jpg',
                ],
            ],
            'belts' => [
                'category' => 'belts',
                'base_hy' => 'Կաշվե գոտի',
                'base_ru' => 'Кожаный ремень',
                'base_en' => 'Leather belt',
                'price' => 18000,
                'files' => [
                    '424715140_18246650812245376_3817495668795387651_n.jpg',
                    '424931968_18246601822245376_2680702324851944126_n.jpg',
                    'pr-sling.jpg',
                ],
            ],
            'phone-cases' => [
                'category' => 'phone-cases',
                'base_hy' => 'Կաշվե պատյան',
                'base_ru' => 'Кожаный чехол',
                'base_en' => 'Leather phone case',
                'price' => 17000,
                'files' => [
                    '127746365_382798599711872_9114278724632185855_n.jpg',
                    '128253415_223373355841829_4192908763760116336_n.jpg',
                    '144620700_4969547513115327_9006447487803931346_n.jpg',
                ],
            ],
            'hats' => [
                'category' => 'hats',
                'base_hy' => 'Կաշվե դետալով գլխարկ',
                'base_ru' => 'Кепка с кожаной деталью',
                'base_en' => 'Cap with leather detail',
                'price' => 14000,
                'files' => [
                    '130712700_174677084356261_7035878406196892773_n.jpg',
                    '144162635_422724132400932_625271799522633942_n.jpg',
                    '219981954_2009439802542816_3601801695246584559_n.jpg',
                    '416023584_18241554316245376_1424247683141886014_n.jpg',
                    '419195608_18242844724245376_9175422511188941662_n.jpg',
                    '438083112_18254956435245376_7161150887327101361_n.jpg',
                    '559017667_18287410360274469_1018427187381189628_n.jpg',
                    'cat-bags.jpg',
                    'pr-wallet.jpg',
                ],
            ],
            'accessories' => [
                'category' => 'accessories',
                'base_hy' => 'Կաշվե աքսեսուար',
                'base_ru' => 'Кожаный аксессуар',
                'base_en' => 'Leather accessory',
                'price' => 12000,
                'files' => [
                    '135847206_1035939966875270_2669387468219529570_n.jpg',
                    '136103480_876562809759433_2730693526750461319_n.jpg',
                    '176992215_500122287833157_7774785902008284246_n.jpg',
                    '271675214_1069238416973366_8986162665157507187_n(1).jpg',
                    '271675214_1069238416973366_8986162665157507187_n.jpg',
                    'e1676026-a7f8-4dca-8062-86e646631340.png',
                ],
            ],
        ];

        foreach ($groups as $groupSlug => $group) {
            $category = $categories[$group['category']] ?? null;
            if (! $category) {
                continue;
            }

            foreach ($group['files'] as $index => $file) {
                $number = $index + 1;
                $slug = 'vertoni-' . $groupSlug . '-' . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
                $price = $group['price'] + (($index % 5) * 3000);
                $stock = 3 + ($index % 7);
                $copy = $this->productCopy($groupSlug, $group, $number);

                $product = Product::updateOrCreate(
                    ['slug_hy' => $slug],
                    [
                        'category_id' => $category->id,
                        'sku' => strtoupper('VRT-' . substr($groupSlug, 0, 3) . '-' . str_pad((string) $number, 3, '0', STR_PAD_LEFT)),
                        'name_hy' => $group['base_hy'] . ' #' . str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                        'name_ru' => $group['base_ru'] . ' #' . str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                        'name_en' => $group['base_en'] . ' #' . str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                        'slug_ru' => $slug,
                        'slug_en' => $slug,
                        'short_description_hy' => $copy['short_hy'],
                        'short_description_ru' => $copy['short_ru'],
                        'short_description_en' => $copy['short_en'],
                        'description_hy' => $copy['description_hy'],
                        'description_ru' => $copy['description_ru'],
                        'description_en' => $copy['description_en'],
                        'specifications' => $copy['specifications'],
                        'highlights' => $copy['highlights'],
                        'meta_title_hy' => $copy['meta_title_hy'],
                        'meta_title_ru' => $copy['meta_title_ru'],
                        'meta_title_en' => $copy['meta_title_en'],
                        'meta_description_hy' => $copy['meta_description_hy'],
                        'meta_description_ru' => $copy['meta_description_ru'],
                        'meta_description_en' => $copy['meta_description_en'],
                        'price' => $price,
                        'old_price' => $index % 6 === 0 ? $price + 9000 : null,
                        'stock' => $stock,
                        'has_variants' => false,
                        'main_image' => $this->url($file),
                        'is_active' => true,
                        'is_featured' => $index < 3,
                        'show_on_home' => $index < 2,
                        'home_sort_order' => $index + 1,
                    ]
                );

                ProductImage::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'image' => $this->url($file),
                    ],
                    ['sort_order' => 0]
                );
            }
        }
    }

    private function productCopy(string $groupSlug, array $group, int $number): array
    {
        $nameHy = $group['base_hy'] . ' #' . str_pad((string) $number, 2, '0', STR_PAD_LEFT);
        $nameRu = $group['base_ru'] . ' #' . str_pad((string) $number, 2, '0', STR_PAD_LEFT);
        $nameEn = $group['base_en'] . ' #' . str_pad((string) $number, 2, '0', STR_PAD_LEFT);

        $copyByGroup = [
            'shoes' => [
                'short_hy' => 'Բնական կաշվից կոշիկ՝ մաքուր ձևվածքով, ամուր կարով և ամենօրյա կրելու համար հարմար կառուցվածքով։',
                'short_ru' => 'Обувь из натуральной кожи с чистым кроем, прочной строчкой и удобной посадкой на каждый день.',
                'short_en' => 'Natural leather shoes with a clean pattern, strong stitching and a comfortable everyday fit.',
                'description_hy' => '<p>Այս մոդելը նախատեսված է ամենօրյա և դասական կերպարների համար։ Կաշվի ընտրությունը, ներբանի հարմարությունը և ձեռքի ավարտը տալիս են կոկիկ տեսք ու երկար օգտագործման զգացում։</p><p>Չափը, գույնը և դետալները կարելի է հստակեցնել պատվերի ժամանակ։</p>',
                'description_ru' => '<p>Эта модель подходит для повседневных и классических образов. Натуральная кожа, удобная посадка и ручная отделка дают аккуратный вид и долгий срок использования.</p><p>Размер, цвет и детали можно уточнить при заказе.</p>',
                'description_en' => '<p>This model is made for everyday and classic looks. Natural leather, comfortable construction and hand finishing create a clean look made to last.</p><p>Size, colour and details can be confirmed during ordering.</p>',
                'specs' => [['key' => 'type', 'label' => 'Տեսակ', 'value' => 'Կոշիկ'], ['key' => 'material', 'label' => 'Նյութ', 'value' => 'Բնական կաշի'], ['key' => 'finish', 'label' => 'Ավարտ', 'value' => 'Ձեռքի աշխատանք']],
                'highlights' => ['Բնական կաշվե վերնամաս', 'Հարմար ամենօրյա կրում', 'Չափի ու գույնի ճշգրտում'],
            ],
            'bags' => [
                'short_hy' => 'Կաշվե պայուսակ՝ առօրյա օգտագործման, գործնական դասավորության և պրեմիում արտաքինի համար։',
                'short_ru' => 'Кожаная сумка для ежедневного использования, практичной организации и премиального образа.',
                'short_en' => 'A leather bag made for daily use, practical organisation and a premium appearance.',
                'description_hy' => '<p>Պայուսակը ստեղծված է այնպես, որ միաժամանակ լինի ներկայանալի և գործնական։ Կաշվե մակերեսը պահում է ձևը, իսկ կառուցվածքը հարմար է ամենօրյա իրերի համար։</p><p>Կարելի է պատվիրել նախընտրած գույնով, չափով կամ ֆուռնիտուրայով։</p>',
                'description_ru' => '<p>Сумка сделана так, чтобы быть одновременно презентабельной и практичной. Кожаная поверхность держит форму, а конструкция подходит для ежедневных вещей.</p><p>Можно заказать нужный цвет, размер или фурнитуру.</p>',
                'description_en' => '<p>The bag is designed to be both presentable and practical. The leather surface holds its shape, while the structure fits everyday essentials.</p><p>Colour, size and hardware can be customised during ordering.</p>',
                'specs' => [['key' => 'type', 'label' => 'Տեսակ', 'value' => 'Պայուսակ'], ['key' => 'material', 'label' => 'Նյութ', 'value' => 'Բնական կաշի'], ['key' => 'use', 'label' => 'Օգտագործում', 'value' => 'Առօրյա / գործնական']],
                'highlights' => ['Պրեմիում տեսք', 'Ամուր կառուցվածք', 'Անհատական գույն ու չափ'],
            ],
            'wallets' => [
                'short_hy' => 'Կաշվե դրամապանակ կամ քարտապանակ՝ կոմպակտ ձևով, ամուր կարերով և մաքուր դետալներով։',
                'short_ru' => 'Кожаный кошелек или картхолдер компактной формы, с прочной строчкой и аккуратными деталями.',
                'short_en' => 'A compact leather wallet or cardholder with strong stitching and clean details.',
                'description_hy' => '<p>Փոքր կաշվե իր՝ նախատեսված քարտերի, կանխիկի և ամենօրյա մանր իրերի համար։ Կոկիկ կառուցվածքը թույլ է տալիս այն հեշտությամբ օգտագործել գրպանում կամ պայուսակում։</p>',
                'description_ru' => '<p>Небольшое кожаное изделие для карт, наличных и ежедневных мелочей. Аккуратная конструкция удобна для кармана или сумки.</p>',
                'description_en' => '<p>A small leather piece for cards, cash and everyday essentials. Its clean structure makes it easy to carry in a pocket or bag.</p>',
                'specs' => [['key' => 'type', 'label' => 'Տեսակ', 'value' => 'Դրամապանակ / քարտապանակ'], ['key' => 'material', 'label' => 'Նյութ', 'value' => 'Բնական կաշի'], ['key' => 'format', 'label' => 'Ֆորմատ', 'value' => 'Կոմպակտ']],
                'highlights' => ['Կոմպակտ չափ', 'Մաքուր կարեր', 'Հարմար նվերի համար'],
            ],
            'belts' => [
                'short_hy' => 'Դասական կաշվե գոտի՝ ամուր հիմքով, մետաղական ֆուռնիտուրայով և կարգավորվող չափով։',
                'short_ru' => 'Классический кожаный ремень с прочной основой, металлической фурнитурой и регулируемым размером.',
                'short_en' => 'A classic leather belt with a solid base, metal hardware and adjustable sizing.',
                'description_hy' => '<p>Գոտին նախատեսված է դասական և առօրյա հագուստի համար։ Ամուր կաշին և պարզ ձևը թույլ են տալիս այն օգտագործել երկար ժամանակ՝ առանց ավելորդ դեկորի։</p>',
                'description_ru' => '<p>Ремень подходит для классической и повседневной одежды. Плотная кожа и простой дизайн рассчитаны на долгое использование без лишнего декора.</p>',
                'description_en' => '<p>The belt fits classic and everyday outfits. Solid leather and a simple shape make it practical for long-term use without unnecessary decoration.</p>',
                'specs' => [['key' => 'type', 'label' => 'Տեսակ', 'value' => 'Գոտի'], ['key' => 'material', 'label' => 'Նյութ', 'value' => 'Բնական կաշի'], ['key' => 'hardware', 'label' => 'Ֆուռնիտուրա', 'value' => 'Մետաղական']],
                'highlights' => ['Ամուր կաշի', 'Կարգավորվող չափ', 'Դասական տեսք'],
            ],
            'phone-cases' => [
                'short_hy' => 'Կաշվե պատյան հեռախոսի համար՝ պաշտպանիչ ձևով և ձեռքի կոկիկ ավարտով։',
                'short_ru' => 'Кожаный чехол для телефона с защитной формой и аккуратной ручной отделкой.',
                'short_en' => 'A leather phone case with a protective shape and clean hand finishing.',
                'description_hy' => '<p>Պատյանը պաշտպանում է հեռախոսը և միաժամանակ տալիս է զուսպ կաշվե տեսք։ Կարելի է հարմարեցնել մոդելի, գույնի և դետալների պահանջներին։</p>',
                'description_ru' => '<p>Чехол защищает телефон и одновременно дает сдержанный кожаный вид. Можно адаптировать под модель, цвет и детали.</p>',
                'description_en' => '<p>The case protects the phone while giving it a restrained leather look. It can be adapted to the phone model, colour and details.</p>',
                'specs' => [['key' => 'type', 'label' => 'Տեսակ', 'value' => 'Հեռախոսի պատյան'], ['key' => 'material', 'label' => 'Նյութ', 'value' => 'Բնական կաշի'], ['key' => 'fit', 'label' => 'Հարմարեցում', 'value' => 'Ըստ մոդելի']],
                'highlights' => ['Պաշտպանիչ կառուցվածք', 'Ըստ հեռախոսի մոդելի', 'Կաշվե մաքուր տեսք'],
            ],
            'hats' => [
                'short_hy' => 'Գլխարկ կաշվե դետալով՝ առօրյա կերպարի համար, մինիմալ ձևով և VERTONI շեշտադրումով։',
                'short_ru' => 'Кепка с кожаной деталью для повседневного образа, минимальной формой и акцентом VERTONI.',
                'short_en' => 'A cap with a leather detail for everyday styling, minimal shape and a VERTONI accent.',
                'description_hy' => '<p>Գլխարկը նախատեսված է պարզ, առօրյա կերպարների համար։ Կաշվե դետալը ավելացնում է բրենդային շեշտադրում՝ առանց ծանրացնելու ընդհանուր տեսքը։</p>',
                'description_ru' => '<p>Кепка создана для простых повседневных образов. Кожаная деталь добавляет фирменный акцент, не перегружая общий вид.</p>',
                'description_en' => '<p>The cap is made for simple everyday looks. The leather detail adds a branded accent without making the design heavy.</p>',
                'specs' => [['key' => 'type', 'label' => 'Տեսակ', 'value' => 'Գլխարկ'], ['key' => 'detail', 'label' => 'Դետալ', 'value' => 'Կաշվե շեշտադրում'], ['key' => 'style', 'label' => 'Ոճ', 'value' => 'Առօրյա']],
                'highlights' => ['Կաշվե դետալ', 'Թեթև առօրյա տեսք', 'Բրենդային շեշտադրում'],
            ],
            'accessories' => [
                'short_hy' => 'Փոքր կաշվե աքսեսուար՝ նվերի, ամենօրյա օգտագործման կամ անհատական պատվերի համար։',
                'short_ru' => 'Небольшой кожаный аксессуар для подарка, ежедневного использования или индивидуального заказа.',
                'short_en' => 'A small leather accessory for gifting, everyday use or a custom order.',
                'description_hy' => '<p>Աքսեսուարը փոքր դետալ է, որը լրացնում է կերպարը կամ դառնում է գործնական նվեր։ Կարելի է հարմարեցնել գույնով, չափով, գրությամբ կամ այլ դետալներով։</p>',
                'description_ru' => '<p>Аксессуар — небольшая деталь, которая дополняет образ или становится практичным подарком. Можно адаптировать цвет, размер, надпись или другие детали.</p>',
                'description_en' => '<p>The accessory is a small detail that completes a look or works as a practical gift. Colour, size, engraving or other details can be customised.</p>',
                'specs' => [['key' => 'type', 'label' => 'Տեսակ', 'value' => 'Աքսեսուար'], ['key' => 'material', 'label' => 'Նյութ', 'value' => 'Բնական կաշի'], ['key' => 'custom', 'label' => 'Անհատականացում', 'value' => 'Հնարավոր է']],
                'highlights' => ['Հարմար նվեր', 'Փոքր և գործնական', 'Անհատական դետալների հնարավորություն'],
            ],
        ];

        $copy = $copyByGroup[$groupSlug] ?? $copyByGroup['accessories'];

        return [
            'short_hy' => $copy['short_hy'],
            'short_ru' => $copy['short_ru'],
            'short_en' => $copy['short_en'],
            'description_hy' => $copy['description_hy'],
            'description_ru' => $copy['description_ru'],
            'description_en' => $copy['description_en'],
            'specifications' => $copy['specs'],
            'highlights' => $copy['highlights'],
            'meta_title_hy' => $nameHy . ' | VERTONI',
            'meta_title_ru' => $nameRu . ' | VERTONI',
            'meta_title_en' => $nameEn . ' | VERTONI',
            'meta_description_hy' => $copy['short_hy'],
            'meta_description_ru' => $copy['short_ru'],
            'meta_description_en' => $copy['short_en'],
        ];
    }

    private function seedHomeContent(array $categories): void
    {
        $bannerVideos = [
            'banners/01KMXTTQ8TKNC7KNHT08DCVM4T.mp4',
            'banners/01KMXV4GY4RBPH9SNF85W8GXFG.mp4',
            'banners/01KMXVMW1PSBXVB7231ZWZ4RDH.mp4',
        ];

        $bannerCopies = [
            [
                'title_hy' => 'Ձեռքի աշխատանք՝ կաշվե արտադրանքի մաքուր ձևով',
                'title_ru' => 'Ручная работа в чистой форме кожаных изделий',
                'title_en' => 'Handcrafted leather in a clean premium form',
                'subtitle_hy' => 'Ընտրված կաշին, ճշգրիտ ձևվածքը և ձեռքով ավարտված դետալները միանում են արտադրանքի մեջ, որը ստեղծվում է երկար օգտագործման համար։',
                'subtitle_ru' => 'Отборная кожа, точный крой и ручная отделка соединяются в изделиях для долгого использования.',
                'subtitle_en' => 'Selected leather, precise patterns and handmade details come together in products made for long-term use.',
                'button_link' => '/shop',
                'fallback_image' => 'hero-main.jpg',
            ],
            [
                'title_hy' => 'Պայուսակներ և աքսեսուարներ անհատական պատվերով',
                'title_ru' => 'Сумки и аксессуары под индивидуальный заказ',
                'title_en' => 'Bags and accessories made to order',
                'subtitle_hy' => 'Ընտրեք ձևը, գույնը, չափը և դետալները․ արտադրանքը հարմարեցվում է պատվերի նպատակին։',
                'subtitle_ru' => 'Выберите форму, цвет, размер и детали — изделие адаптируется под задачу заказа.',
                'subtitle_en' => 'Choose the shape, colour, size and details — the item is adapted to the purpose of the order.',
                'button_link' => '/custom-order',
                'fallback_image' => 'hero-side.jpg',
            ],
            [
                'title_hy' => 'Փոքր դետալներ, որոնք դարձնում են կերպարը ամբողջական',
                'title_ru' => 'Малые детали, которые делают образ цельным',
                'title_en' => 'Small details that complete the look',
                'subtitle_hy' => 'Դրամապանակներ, պատյաններ, գոտիներ և նվերային իրեր՝ նույն կոկիկ ձեռագրով։',
                'subtitle_ru' => 'Кошельки, чехлы, ремни и подарочные изделия — с тем же аккуратным почерком.',
                'subtitle_en' => 'Wallets, cases, belts and gift items with the same clean craft signature.',
                'button_link' => '/shop',
                'fallback_image' => 'cat-wallets.jpg',
            ],
        ];

        foreach ($bannerCopies as $index => $copy) {
            $videoPath = $this->seedAsset($bannerVideos[$index] ?? '') ?: null;

            Banner::updateOrCreate(
                ['sort_order' => $index + 1],
                [
                    'title_hy' => $copy['title_hy'],
                    'title_ru' => $copy['title_ru'],
                    'title_en' => $copy['title_en'],
                    'subtitle_hy' => $copy['subtitle_hy'],
                    'subtitle_ru' => $copy['subtitle_ru'],
                    'subtitle_en' => $copy['subtitle_en'],
                    'button_text_hy' => $index === 1 ? 'Սկսել պատվերը' : 'Դիտել հավաքածուն',
                    'button_text_ru' => $index === 1 ? 'Начать заказ' : 'Смотреть коллекцию',
                    'button_text_en' => $index === 1 ? 'Start order' : 'View collection',
                    'button_link' => $copy['button_link'],
                    'image' => $videoPath ?: $this->url($copy['fallback_image']),
                    'is_active' => true,
                ]
            );
        }

        // The custom-order CTA is already available in the hero/banner and footer.
        // Keeping a separate home section with the same purpose made the homepage repeat itself.
        HomeSection::query()->where('key', 'custom-order')->delete();

        HomeSection::updateOrCreate(
            ['key' => 'craft-video'],
            [
                'type' => 'editorial_video',
                'category_id' => $categories['shoes']?->id ?? null,
                'eyebrow_hy' => 'VERTONI Atelier',
                'eyebrow_ru' => 'VERTONI Atelier',
                'eyebrow_en' => 'VERTONI Atelier',
                'title_hy' => 'Կաշվի ընտրությունից մինչև վերջնական դետալ',
                'title_ru' => 'От выбора кожи до финальной детали',
                'title_en' => 'From leather selection to the final detail',
                'description_hy' => 'Սա այն ներքևի վիդեո բաժինն է, որը թողել ենք seed-ում․ այն ցույց է տալիս բրենդի ձեռքի աշխատանքը և պրեմիում շեշտը։',
                'description_ru' => 'Это нижний видеоблок, который оставлен в seed: он показывает ручную работу бренда и премиальный акцент.',
                'description_en' => 'This is the lower video section kept in the seed: it shows the brand craft and premium detail.',
                'button_text_hy' => 'Դիտել ապրանքները',
                'button_text_ru' => 'Смотреть товары',
                'button_text_en' => 'View products',
                'button_link' => '/shop',
                'image' => $this->url('hero-main.jpg'),
                'video' => $this->seedAsset('banners/01KPKYN7PZZVRP4Y6VSWPV4GGT.mp4'),
                'layout' => 'full_bleed',
                'text_position' => 'bottom_center',
                'theme' => 'dark',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'site_name', 'label' => 'Site name', 'value' => 'VERTONI', 'group' => 'site', 'type' => 'text', 'sort_order' => 1],
            ['key' => 'site_email', 'label' => 'Email', 'value' => 'info@verthoni.com', 'group' => 'contact', 'type' => 'text', 'sort_order' => 2],
            ['key' => 'site_location', 'label' => 'Location', 'value' => 'Yerevan, Armenia', 'group' => 'contact', 'type' => 'text', 'sort_order' => 3],
            ['key' => 'instagram_url', 'label' => 'Instagram', 'value' => 'https://www.instagram.com/vertoni.leather/', 'group' => 'social', 'type' => 'url', 'sort_order' => 4],
            ['key' => 'facebook_url', 'label' => 'Facebook', 'value' => 'https://www.facebook.com/Vertoni.Official', 'group' => 'social', 'type' => 'url', 'sort_order' => 5],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, ['is_public' => true])
            );
        }
    }
}
