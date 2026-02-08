# E-Commerce CMS (Laravel)

Laravel-based e-commerce backend with product CRUD (multiple images), cart, orders, and REST APIs. Includes Stripe payment gateway integration for checkout.

## Requirements

- **PHP** >= 8.2
- **Composer**
- **MySQL** >= 8
- **Node.js** & npm (for frontend assets)

## Setup

### 1. Clone and install dependencies

```bash
git clone <repository-url> ecommerce
cd ecommerce
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Database

Create a MySQL database (e.g. `ecommerce`) and set in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

Run migrations:

```bash
php artisan migrate
```

Optional: seed admin user and sample data (if seeders exist):

```bash
php artisan db:seed
```

### 3. Storage link (for product images)

```bash
php artisan storage:link
```

### 4. Optional: Stripe (for checkout payment)

In `.env` add:

```env
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
```

In `config/services.php`, Stripe is already configured to use these keys. Without them, checkout still creates orders but won’t return a payment intent.

### 5. Run the application

```bash
php artisan serve
```

- **Web:** http://localhost:8000  
- **API base:** http://localhost:8000/api  

For production, point your web server to the `public` directory.

## Database backup (SQL)

To export a DB backup for setup/submission:

```bash
# MySQL (replace DB name and user)
mysqldump -u your_user -p ecommerce > database/backup.sql
```

Include `database/backup.sql` (or similar) in the repo or submission so others can import it:

```bash
mysql -u your_user -p ecommerce < database/backup.sql
```

## API documentation

- **API reference:** See [API.md](API.md) in the project root.
- **Postman:** Import [postman_collection.json](postman_collection.json) (see below) for all endpoints.

## Project structure (summary)

- **Phase 1:** Relational DB (products, product_images, users, carts), Laravel MVC, product CRUD with multiple images, GET API for products with images.
- **Phase 2:** POST API add to cart (user_id = 1), GET API cart list; CMS shows cart items.
- **Extra:** Update/delete cart APIs; cart total and item count; checkout API with Stripe; orders table and admin order list/view; exception handling in APIs.

## Admin

- Login at `/login`. Admin users have `is_admin = 1` in the database.
- Admin can: manage products (CRUD), view cart, view orders at `/orders` and order detail at `/orders/{id}`.

## License

MIT.
