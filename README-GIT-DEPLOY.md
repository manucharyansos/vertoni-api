# Vertoni API — Git deploy կարգ

Կոշտ կանոն՝ իրական `.env` ֆայլը Git չի մտնում։ Սերվերում պահում ես առանձին։ Git-ում պահվում են միայն `.env.example`, `.env.local.example`, `.env.production.example` ֆայլերը։

## Առաջին տեղադրում սերվերում

```bash
cd /home/USER/api.verthoni.com
# կամ դատարկ folder-ում clone արա քո repository-ն
git clone <YOUR_BACKEND_REPO_URL> .

cp .env.production.example .env
nano .env

composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --class=VertoniCatalogSeeder --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`.env`-ում պարտադիր լրացրու իրական DB/MAIL/APP_URL արժեքները։ Example ֆայլերի placeholder-ները production չեն։

## Հետագա update սերվերում

```bash
cd /home/USER/api.verthoni.com
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --class=VertoniCatalogSeeder --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Եթե `.env`-ը արդեն մտել է Git history

Սա ուղղակի սխալ չէ, սա security risk է։ Հանիր tracking-ից և փոխիր գաղտնաբառերը։

```bash
git rm --cached .env .env.local .env.production 2>/dev/null || true
git add .gitignore .env.example .env.local.example .env.production.example
git commit -m "Remove real environment files from repository"
```

Դրանից հետո փոխիր DB-ի, mail-ի և ցանկացած API գաղտնաբառ, որովհետև դրանք արդեն կարող էին բացված լինել։

## Seeder

Seeder-ը ստեղծում է հիմնական կատեգորիաները, ապրանքները, նկարները, home section-ը, banner-ը և հիմնական public settings-ը։

```bash
php artisan db:seed --class=VertoniCatalogSeeder --force
```

Seeder-ը idempotent է․ նույնը երկրորդ անգամ աշխատացնելիս պետք է թարմացնի գոյություն ունեցող տվյալները, ոչ թե կրկնօրինակի։
