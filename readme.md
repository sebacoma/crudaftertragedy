git clone https://github.com/sebacoma/crudaftertragedy.git

CD back

composer install

cp .env.example .env

php artisan key:generate

Configura la base de datos en el archivo .env

php artisan migrate



---------------------
si le da problemas el archivo revise XAMPP/php/php.ini
y busque donde dice  ;extension=zip y elimine los ;