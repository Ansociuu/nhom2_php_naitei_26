# nhom2_php_naitei_26

Mock project NAITEI 26 - PHP. Nhóm 2.

## Thông tin chung

- Đề tài: SUN Booking tours
- Repository: https://github.com/awesome-academy/nhom2_php_naitei_26
- Redmine: https://edu-redmine.sun-asterisk.vn/projects/nhom2_php_naitei_26
- Thời gian thực hiện: 10 ngày full

## Yêu cầu kỹ thuật

- User site: REST API kèm document.
- Admin site: server side rendering.

## Cài đặt

Yêu cầu: PHP 8.3+, Composer, Node 18+, **MySQL 8+**.

```bash
composer install
cp .env.example .env
php artisan key:generate

# tạo database (tên mặc định theo .env.example)
mysql -u root -e "CREATE DATABASE nhom2_php_naitei_26 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE DATABASE nhom2_php_naitei_26_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

php artisan migrate --seed
npm install && npm run build
```

- `nhom2_php_naitei_26_test` là database dành riêng cho `php artisan test`, khai báo trong `phpunit.xml`.
  Test luôn chạy trên MySQL local kể cả khi `.env` đang trỏ ra DB từ xa.
- Tài khoản admin sau khi seed: `admin@sunbooking.test` / `password`.
- Cấu trúc CSDL: xem [docs/database.md](docs/database.md).

### Dùng DB chung của nhóm

Nhóm có một MySQL 8.4 dùng chung trên Aiven (database `defaultdb`), đã migrate và seed sẵn.
Thông tin kết nối trao đổi trong nhóm, **không commit vào repo**. Sau khi điền vào `.env`:

```bash
php artisan migrate   # chỉ chạy migration còn thiếu, KHÔNG dùng migrate:fresh trên DB chung
```

Khi trỏ ra DB từ xa nên để `CACHE_STORE=file` và `SESSION_DRIVER=file` trong `.env` local,
vì mỗi request đi qua internet sẽ rất chậm nếu cache/session cũng nằm trên đó.

## Thành viên

- Phan Hữu Đại
- Nguyễn Đình Chiến
- Nguyễn Văn An B

## Quy trình làm việc

Quy ước tạo ticket, estimate time và liên kết Pull Request với Redmine được mô tả trong file REDMINE.md.

## Các bước thực hiện

1. Design database
2. Add tasks on Redmine + estimate time
3. Init project
4. Init models, add relationship
5. Design static pages
6. Other pulls
# nhom2_php_naitei_26
