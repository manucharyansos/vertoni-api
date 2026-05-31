# API-ում արված փոփոխություններ

- Ավելացվել է `VertoniCatalogSeeder`, որը frontend-ի `public/images/home` նկարների հիման վրա ստեղծում է կատեգորիաներ, ապրանքներ և product images։
- `DatabaseSeeder`-ը մաքրվել է․ test user չի ստեղծում, կանչում է roles/permissions և catalog seeder։
- `.env.example`, `.env.local.example`, `.env.production.example` ֆայլերը մաքրվել են իրական գաղտնիքներից։
- `.gitignore`-ը խստացվել է, որպեսզի իրական `.env`-երը Git չմտնեն, բայց example ֆայլերը մնան։
- Ավելացվել է `README-GIT-DEPLOY.md`՝ սերվերից Git pull/deploy-ի քայլերով։
- PHP syntax check-ը անցել է բոլոր `app`, `database`, `routes`, `config` PHP ֆայլերի վրա։

Չստուգված սանդբոքսում՝ իրական DB migrate/seed execution, որովհետև միջավայրում SQLite PDO driver չկար։ Սերվերում աշխատացրու `php artisan migrate --force` և `php artisan db:seed --class=VertoniCatalogSeeder --force`։
