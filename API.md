# E-Commerce API Reference

**Base URL:** `http://localhost:8000/api` (or your domain)

All cart APIs use **user_id = 1** (hardcoded). Responses use JSON. Errors return `success: false` with appropriate HTTP status and `message` or `errors`.

---

## 1. GET – All products with multiple images

**Endpoint:** `GET /api/products`

**Response:** All products with image URLs.

**Example:**
```bash
curl http://localhost:8000/api/products
```

**Sample response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Product Name",
      "price": 99.99,
      "description": "...",
      "quantity": 10,
      "images": [
        { "id": 1, "url": "http://localhost:8000/storage/products/xxx.jpg", "path": "products/xxx.jpg" }
      ]
    }
  ]
}
```

---

## 2. POST – Add product to cart

**Endpoint:** `POST /api/cart`

**Body (JSON):**
- `product_id` (required): existing product ID
- `quantity` (required): integer, min 1

**Example:**
```bash
curl -X POST http://localhost:8000/api/cart \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1, "quantity": 2}'
```

**Sample response (201):**
```json
{
  "success": true,
  "message": "Product added to cart.",
  "data": {
    "id": 1,
    "user_id": 1,
    "product_id": 1,
    "product_name": "Product Name",
    "quantity": 2,
    "price": 99.99,
    "total": 199.98
  }
}
```

**Errors:** 422 (validation), 500 (server error)

---

## 3. GET – Cart list (with cart total)

**Endpoint:** `GET /api/cart`  
**Query:** `?user_id=1` (optional, default 1)

**Response:** Cart items plus `cart_total` and `items_count`.

**Example:**
```bash
curl http://localhost:8000/api/cart
curl http://localhost:8000/api/cart?user_id=1
```

**Sample response (200):**
```json
{
  "success": true,
  "user_id": 1,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "product_id": 1,
      "product_name": "Product Name",
      "product_price": 99.99,
      "quantity": 2,
      "total": 199.98,
      "product_images": [
        { "id": 1, "url": "http://localhost:8000/storage/products/xxx.jpg" }
      ]
    }
  ],
  "cart_total": 199.98,
  "items_count": 2
}
```

---

## 4. GET – Cart by user ID (URL)

**Endpoint:** `GET /api/cart/user/{user_id}`

**Example:** `GET /api/cart/user/1`

**Response:** Same structure as GET /api/cart (with `cart_total`, `items_count`).

---

## 5. PUT – Update cart item quantity

**Endpoint:** `PUT /api/cart/{id}`

**Body (JSON):**
- `quantity` (required): integer, min 1

**Example:**
```bash
curl -X PUT http://localhost:8000/api/cart/1 \
  -H "Content-Type: application/json" \
  -d '{"quantity": 3}'
```

**Sample response (200):**
```json
{
  "success": true,
  "message": "Cart item updated.",
  "data": {
    "id": 1,
    "quantity": 3,
    "total": 299.97
  }
}
```

**Errors:** 404 (cart item not found), 422 (validation), 500

---

## 6. DELETE – Remove cart item

**Endpoint:** `DELETE /api/cart/{id}`

**Example:**
```bash
curl -X DELETE http://localhost:8000/api/cart/1
```

**Sample response (200):**
```json
{
  "success": true,
  "message": "Cart item removed."
}
```

**Errors:** 404 (cart item not found), 500

---

## 7. POST – Checkout (create order + Stripe payment intent)

**Endpoint:** `POST /api/checkout`

**Body (JSON, optional):**
- `payment_method`: e.g. `"stripe"` (default)
- `currency`: e.g. `"inr"` (default)

Creates an order from the current cart (user_id = 1), clears the cart, and if `STRIPE_SECRET` is set, returns a Stripe PaymentIntent `client_secret` for the frontend to complete payment.

**Example:**
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Sample response (201):**
```json
{
  "success": true,
  "message": "Order created. Complete payment to confirm.",
  "data": {
    "order_id": 1,
    "order_number": "ORD-XXXXXXXX",
    "subtotal": 199.98,
    "tax": 0,
    "total": 199.98,
    "status": "pending",
    "payment_intent": {
      "client_secret": "pi_xxx_secret_xxx",
      "id": "pi_xxx"
    },
    "items": [
      {
        "product_id": 1,
        "product_name": "Product Name",
        "quantity": 2,
        "price": 99.99,
        "total": 199.98
      }
    ]
  }
}
```

If Stripe is not configured, `payment_intent` may be `null` or contain an `error` message. Order is still created.  
**Errors:** 422 (empty cart), 500

---

## CMS (Web)

- **Products (CRUD):** `/products` – list, create, edit, show, delete; multiple images per product.
- **Cart:** `/cart` – cart items (default user_id = 1).
- **Orders (Admin):** `/orders` – list; `/orders/{id}` – view order details.
- **Login:** `/login` – admin/customer login.
