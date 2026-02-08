# E-Commerce API Reference

**Base URL:** `http://localhost:8000/api` (or your domain)

All cart APIs use **user_id = 1** (hardcoded). Responses use JSON. Errors return `success: false` with appropriate HTTP status and `message` or `errors`.

---

## 1 POST - Register 
 
 Endpoint: POST /api/register

 ## Request Body (customer)

```json
{
  "name": "Pratiksha",
  "email": "pratiksha22@gmail.com",
  "password": "123456",
  "password_confirmation": "123456"
}

curl -X POST http://localhost:8000/api/register \
-H "Content-Type: application/json" \
-d '{
"name": "Pratiksha",
"email": "pratiksha22@gmail.com",
"password": "123456",
"password_confirmation": "123456"
}'


{
  "success": true,
  "message": "Registered successfully.",
  "data": {
    "id": 4,
    "name": "Pratiksha",
    "email": "pratiksha22@gmail.com",
    "phone": null,
    "is_admin": false
  }
}




 ## Request Body (admin)

 {
  "name": "Admin User",
  "email": "adminn@example.com",
  "password": "123456",
  "password_confirmation": "123456",
  "is_admin": true
}


curl -X POST http://localhost:8000/api/register \
-H "Content-Type: application/json" \
-d '{

  "name": "Admin User",
  "email": "adminn@example.com",
  "password": "123456",
  "password_confirmation": "123456",
  "is_admin": true
}




{
    "success": true,
    "message": "Registered successfully.",
    "data": {
        "id": 7,
        "name": "Admin User",
        "email": "adminn@example.com",
        "phone": null,
        "is_admin": true
    }
}



## 1. POST – Login

**Endpoint:** `POST /api/login`

**Body (JSON):**
- `email` (optional if email_or_phone set): user email
- `email_or_phone` (optional if email set): email or phone number
- `password` (required): password

**Example:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
  "email": "adminn@example.com",
  "password": "123456"

}'
```


**Sample response (200):**
```json
{
    "success": true,
    "message": "Logged in successfully.",
    "data": {
        "id": 7,
        "name": "Admin User",
        "email": "adminn@example.com",
        "phone": null,
        "is_admin": true
    }
}
```

**Errors:** 401 (invalid credentials), 422 (validation), 500

---



## 3. GET – All products with multiple images

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

## 3.1 POST – Create product with image (Postman)

**Endpoint:** `POST /api/products`

**Create with image (form-data):**

1. Method: **POST**
2. URL: `http://localhost:8000/api/products`
3. Body tab → select **form-data** (not raw JSON).
4. Add rows:

| KEY        | TYPE | VALUE        |
|------------|------|--------------|
| name       | Text | Book         |
| price      | Text | 100          |
| quantity   | Text | 10           |
| description| Text | aaaaaa       |





**Sample response (201):**
```json
{
    "success": true,
    "message": "Product created successfully.",
    "data": {
        "id": 5,
        "name": "Book",
        "price": 100,
        "description": "aaaaaaaaaa",
        "quantity": 10,
        "images": []
    }
}'''



---

## 3.2 PUT – Edit product (update after adding, via Postman)

**Endpoint:** `PUT /api/products/{id}` or `PATCH /api/products/{id}` (use the actual product ID number in the URL, e.g. `.../products/1` or `.../products/5` — do not send the literal `{5}`).

**Important – form-data (with or without file):** PHP does not parse **PUT** request bodies. When sending **form-data**, use **POST** to the same URL (e.g. `POST http://localhost:8000/api/products/5`) 
1. Method: **POST** (for form-data) or **PUT** (for JSON only)
2. URL: `http://localhost:8000/api/products/5` 
3. Body → **form-data** (use POST):

| KEY        | TYPE | VALUE             |
|------------|------|-------------------|
| name       | Text | Book              |
| price      | Text | 200               |
| quantity   | Text | 5                 |
| description| Text | Updated description |
| image      | File | (optional; use key `image` or `images[]`) |



**Sample response (200):**
```json
{
    "success": true,
    "message": "Product updated successfully.",
    "data": {
        "id": 5,
        "name": "Book",
        "price": 200,
        "description": "aaaaa",
        "quantity": 5,
        "images": [
            {
                "id": 9,
                "url": "http://localhost:8000/storage/products/LKhKozYHzBKUEYAQgdOqb9VMl9GDErn1RFERt4By.jpg",
                "path": "products/LKhKozYHzBKUEYAQgdOqb9VMl9GDErn1RFERt4By.jpg"
            }
        ]
    }
}
```



---

## 4. POST – Add product to cart

**Endpoint:** `POST /api/cart`

**Body (JSON):**
{"product_id": 1, "quantity": 2}

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
        "id": 16,
        "user_id": 1,
        "product_id": 1,
        "product_name": "wallper",
        "quantity": 2,
        "price": 2000,
        "total": 4000
    }
}
```



---

## 5. GET – Cart list (with cart total)

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
            "id": 16,
            "user_id": 1,
            "product_id": 1,
            "product_name": "wallper",
            "product_price": 2000,
            "quantity": 2,
            "total": 4000,
            "product_images": [
                {
                    "id": 1,
                    "url": "http://localhost:8000/storage/products/e96kvRifPLwCtsx9ro9TRhxikj85TouXzwHqZtjV.webp"
                },
                {
                    "id": 2,
                    "url": "http://localhost:8000/storage/products/OZ028eaWr5v1kUm9wO8xsSnphaCp4jmDzCAPsXjB.webp"
                }
            ]
        }
    ],
    "cart_total": 4000,
    "items_count": 2
}
```



## 7. PUT – Update cart item quantity

**Endpoint:** `PUT /api/cart/{id}`

**Body (JSON):**
- `quantity` (required): integer, min 1

**Example:**
```bash
curl -X PUT http://localhost:8000/api/cart/16 \
  -H "Content-Type: application/json" \
  -d '{"quantity": 3}'
```

**Sample response (200):**
```json
{
    "success": true,
    "message": "Cart item updated.",
    "data": {
        "id": 16,
        "quantity": 3,
        "total": 6000
    }
}
```







