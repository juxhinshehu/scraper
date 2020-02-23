## Installation commands

git clone https://github.com/juxhinshehu/scraper.git

php artisan migrate

you may need to execute: composer dump-autoload

configure variables in .env . especially DB_DATABASE
DB_USERNAME, DB_PASSWORD

php artisan config:cache


## Usage Manual

To scrap a profile use: http://127.0.0.1:8000/tiktok-profile-scraper/realmadrid/
(substitute realmadrid with any give profile id. No need to put '@' before the profile id)

To scrap a video use: http://127.0.0.1:8000/tiktok-video-scraper/realmadrid/6721977173101579526
(again substitute profile id and video id with your given ones)

To run tests execute: vendor/bin/phpunit


