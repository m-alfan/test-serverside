Core API
===============

Version v1.0
- initial project

### Requirements
----------------
- Minimal Web server kamu suppport PHP 5.4.0.
- Database sqlite dan PDO SQLite extension haurs di aktifkan,

### Instalation
---------------
Ketikan perintah berikut di dalam directory htdocs / webroot kamu
```
git clone [url-git] server-side
cd server-side
composer global require "fxp/composer-asset-plugin:^1.2.0"
composer install -vvv
php yii migrate
```
Sekarang kamu sudah bisa mengakses aplikasi menggunakan url :
```
http://localhost/server-side/api/public_html/v1
```
header :
- token : [token]
- auth_key: [auth_key]

api yang tersedia :
- GET `http://localhost/server-side/api/public_html/v1/`
- POST or PUT `http://localhost/server-side/api/public_html/v1/login`
- POST or PUT `http://localhost/server-side/api/public_html/v1/signup`
- GET `http://localhost/server-side/api/public_html/v1/user`
- POST or PUT `http://localhost/server-side/api/public_html/v1/change-password`
- POST or PUT `http://localhost/server-side/api/public_html/v1/change-account`
- DELETE `http://localhost/server-side/api/public_html/v1/delete`

## Contoh request dan respon
Url : `http://localhost/server-side/api/public_html/v1/login`
Request :
```
Method = POST or PUT
Header =
    auth_key : Z2V0VG9rZW5Gcm9tQWNjZXNzTG9naW4=
    token : Z2V0VG9rZW5Gcm9tQWNjZXNzTG9naW4=
    Content-Type : application/json
Data body param
    username = username
    password = password
```
Respon :
```
{
  "status": "success",
  "access": {
    "token": null,
    "expired": null
  },
  "data": {
    "id": 1,
    "username": "alfan",
    "email": "alfan@alfan.com",
    "auth_key": "QEwWBGOrsdnpdckFAwVD-bChGdWBfE8G",
    "token": "anG4uUW9NgXuWR-oQsZL788hwe9qNgnI",
    "expire": 1483914509
  }
}
```
Catatan :
- `v1/` tidak perlu menggunakan header token dan auth_key
- untuk api `v1/login`, `v1/signup` gunakan token dan auth_key default `Z2V0VG9rZW5Gcm9tQWNjZXNzTG9naW4=` pada header ketika request, jika token ingin di ubah ada pada file `api\config\params.php`
- token hanya berlaku satu kali request dan token baru akan di kirim bersamaan dengan respon dari request yang berhasil

### Config Database
-------------------
Default database yang di gunakan aplikasi adalah sqlite, jika kamu ingin mengubahnya, silakan ganti akses db di file `server-side\api\config\db.php`
```
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=server-side',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];
```
lakukan migrasi data ke database baru
```
php yii migrate
```