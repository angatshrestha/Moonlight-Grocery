# Week 4 — System Architecture

## Architecture Document

### Project: Moonlight Grocery

---

## 1. Introduction

This document explains the technical architecture for **Moonlight Grocery**, a small online grocery store based in  Sydney, serving mainly Nepali, Indian, and Bangladeshi customers.

The system will allow customers to:
- browse products
- place orders
- pay online
- track deliveries

It will also allow the store admin to:
- manage products
- manage stock
- manage customers
- manage orders

This architecture is designed to be:
- practical for a small business  
- low cost  
- easy to manage  
- ready for future growth  

---

## 2. Architecture Goal

The main goal of the architecture is to build a system that is:

- easy to use  
- secure  
- scalable  
- responsive on mobile and desktop  
- simple to maintain  
- suitable for future mobile app integration  

---

## 3. Recommended Architecture Style

### 3.1 Monolith or Microservices?

For Moonlight Grocery, the recommended architecture is a **modular monolith**.

### What is Modular Monolith?

A modular monolith is a single application divided into modules such as:

- User Management  
- Product Catalog  
- Cart  
- Orders  
- Payments  
- Delivery Tracking  
- Admin Dashboard  
- Notifications  

### Why Modular Monolith?

- business is small  
- cheaper to build and host  
- easier for small/student team  
- simpler deployment  
- easier debugging and testing  
- less infrastructure complexity  

### Why Not Microservices?

- complex communication between services  
- harder deployment  
- more DevOps work  
- higher cost  
- difficult monitoring  

### Future Plan

Later, system can be split into microservices:
- Payment Service  
- Delivery Service  
- Notification Service  

---

## 4. High-Level System Architecture

The system follows a **3-tier architecture**:

### 1. Presentation Layer
- Customer website  
- Admin dashboard  
- Future mobile app  

### 2. Application Layer
- authentication logic  
- order processing  
- stock updates  
- payment processing  
- delivery updates  

### 3. Data Layer
- customer data  
- product data  
- order data  
- payment records  
- supplier details  
- delivery details  

---

## 5. Proposed Tech Stack

### 5.1 Frontend

- React.js  
- HTML5  
- CSS3  
- JavaScript  
- Bootstrap / Tailwind CSS  

**Reason:**
- fast UI  
- reusable components  
- responsive design  
- easy API integration  

---

### 5.2 Backend

- Node.js  
- Express.js  

**Reason:**
- fast development  
- same language (JS) frontend + backend  
- good for REST APIs  

---

### 5.3 Database

- PostgreSQL (preferred)  

**Reason:**
- strong relational structure  
- reliable transactions  
- secure and scalable  

---

### 5.4 Authentication & Security

- JWT (JSON Web Token)  
- bcrypt (password hashing)  
- HTTPS  
- Role-based access  

**Roles:**
- Customer  
- Admin  
- Supplier  

---

### 5.5 Payment Gateway

- Stripe (recommended)  
- PayPal  

**Reason:**
- secure  
- easy integration  
- supports multiple payment methods  

---

### 5.6 Cloud & Hosting

- Frontend: Vercel / Netlify  
- Backend: Render / Railway / AWS  
- Database: PostgreSQL cloud  

---

### 5.7 Mobile App (Future)

- React Native  

---

## 6. Main System Components

### 6.1 Customer Interface

- register/login  
- browse products  
- search  
- add to cart  
- place order  
- payment  
- track delivery  
- order history  

---

### 6.2 Admin Dashboard

- manage products  
- update prices  
- manage stock  
- manage users  
- manage orders  
- reports  
- promotions  

---

### 6.3 Product Management

- add/edit/delete products  
- categorize products  
- upload images  
- update stock  

**Categories:**
- rice & flour  
- spices  
- lentils  
- frozen food  
- snacks  
- drinks  
- Nepali / Indian / Bangladeshi products  

---

### 6.4 Cart Module

- add/remove items  
- update quantity  
- calculate total  
- apply discounts  

---

### 6.5 Order Management

- create order  
- confirm order  
- update status  
- generate receipt  

**Statuses:**
- Pending  
- Confirmed  
- Packed  
- Out for Delivery  
- Delivered  
- Cancelled  

---

### 6.6 Payment Module

- connect payment gateway  
- verify payment  
- store transactions  

**Methods:**
- card  
- digital wallet  
- cash on delivery  

---

### 6.7 Delivery Tracking

- assign delivery  
- update status  
- estimated delivery  

**Future:**
- Google Maps API  
- GPS tracking  

---

### 6.8 Notification Module

- order confirmation  
- payment updates  
- delivery updates  

**Channels:**
- email  
- SMS  
- in-app  

---

### 6.9 Supplier Management

- supplier profiles  
- product supply  
- inventory restock  

---

## 7. Authentication Design

### Process

1. User registers  
2. Password hashed (bcrypt)  
3. User logs in  
4. Backend verifies  
5. JWT token generated  
6. Token sent to frontend  
7. Stored securely  
8. Sent with requests  
9. Backend validates  

---

### Authorization

**Customer:**
- browse, order, track  

**Admin:**
- full control  

**Supplier:**
- manage supply  

---

### Security Measures

- hashed passwords  
- HTTPS  
- token expiry  
- input validation  
- SQL injection protection  
- XSS & CSRF protection  

---

## 8. Data Flow

###  Order Processing Data Flow Diagram
```text
+----------------------+
|      Customer        |
| Browse & Add to Cart |
+----------------------+
           |
           v
+----------------------+
|   Frontend (React)   |
|  Displays Products   |
+----------------------+
           |
           v
+----------------------+
| Backend API Layer    |
| Node.js + Express    |
+----------------------+
           |
           v
+------------------------------+
| Product Module               |
| Fetch Products from DB       |
+------------------------------+
           |
           v
+----------------------+
|   PostgreSQL DB      |
|   Product Data       |
+----------------------+
           |
           v
+----------------------+
|   Frontend (Cart)    |
|  User Proceeds       |
|  to Checkout         |
+----------------------+
           |
           v
+------------------------------+
| Order Module                 |
| Create Order Record          |
+------------------------------+
           |
           v
+------------------------------+
| Payment Gateway (Stripe)     |
| Process Payment              |
+------------------------------+
      |                |
      | Success        | Failed
      v                v
+------------------+   +----------------------+
| Payment Stored   |   |  Show Error to User  |
| in Database      |   +----------------------+
+------------------+
           |
           v
+------------------------------+
| Update Order Status          |
| (Confirmed / Processing)     |
+------------------------------+
           |
           v
+------------------------------+
| Notification Module          |
| Email / SMS Confirmation     |
+------------------------------+
           |
           v
+------------------------------+
| Admin Dashboard              |
| View & Process Order         |
+------------------------------+
           |
           v
+------------------------------+
| Delivery Module              |
| Update Delivery Status       |
+------------------------------+
           |
           v
+----------------------+
|   Customer           |
| Receives Updates     |
+----------------------+
```
---
This diagram shows the step-by-step process from product browsing to order delivery. It highlights how the frontend, backend, database, and external services interact to complete an order successfully.

### Example: Order Process

1. User browses  
2. API fetches products  
3. User adds to cart  
4. Checkout  
5. Order created  
6. Payment processed  
7. Order confirmed  
8. Stock updated  
9. Notification sent  
10. Admin processes order  

---

## 9. Database Design

### Tables

- Users  
- Roles  
- Customers  
- Admins  
- Suppliers  
- Products  
- Categories  
- Inventory  
- Cart  
- Cart_Items  
- Orders  
- Order_Items  
- Payments  
- Deliveries  
- Notifications  

### Relationships

- customer → many orders  
- order → many products  
- product → one category  
- payment → one order  
- delivery → one order  

---

## 10. API Design

### Auth APIs
- POST /api/auth/register  
- POST /api/auth/login  

### Product APIs
- GET /api/products  
- POST /api/products  

### Cart APIs
- GET /api/cart  
- POST /api/cart/add  

### Order APIs
- POST /api/orders  
- GET /api/orders  

### Payment APIs
- POST /api/payments/create  

---

## 11. Architecture Diagram 
```
+---------------------------+
|     Frontend (React)      |
|  Customer & Admin UI      |
+---------------------------+
            |
            v
+---------------------------+
|   Backend API Layer       |
|   Node.js + Express       |
+---------------------------+
            |
            v
+------------------------------------------------------+
|                   Core Modules                       |
| Auth | Product | Cart | Order | Payment | Notification|
+------------------------------------------------------+
            |
            v
+---------------------------+
|     PostgreSQL Database   |
| Users | Orders | Products |
+---------------------------+
            |
            v
+---------------------------------------------+
|        External Services / APIs             |
| Stripe | Email | SMS | Google Maps API     |
+---------------------------------------------+
```
## 12. Deployment

- Frontend → Vercel  
- Backend → Render  
- Database → PostgreSQL Cloud  

---

## 13. Scalability Plan

Future upgrades:

- microservices split  
- Redis caching  
- CDN  
- message queue  
- analytics  

---

## 14. Advantages

- low cost  
- simple  
- scalable  
- secure  
- easy maintenance  

---

## 15. Limitations

- harder to scale at very large size  
- single backend system  

---

## 16. Final Recommendation

- React.js  
- Node.js + Express  
- PostgreSQL  
- JWT  
- Stripe  
- Google Maps API  

---

## 17. Conclusion

This architecture provides a strong and scalable foundation for Moonlight Grocery. It supports all core features while remaining simple and cost-effective, with room for future expansion.
