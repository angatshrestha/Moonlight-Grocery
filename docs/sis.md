# Software Requirements Specification (SRS)

## 1. Introduction

### 1.1 Purpose
This Software Requirements Specification defines the core features and system requirements for the **Moonlight Grocery eCommerce Platform**. It captures the requirements for building a modern, user-friendly grocery shopping system, including user personas, functional requirements, non-functional requirements, user stories, and acceptance criteria.

---

### 1.2 Product Overview
Moonlight Grocery is an online grocery shopping platform designed to provide a fast, simple, and visually appealing shopping experience. It allows users to browse products, add items to a cart, and place orders efficiently using a clean and modern interface.

---

### 1.3 Scope
The system will support:
- browsing grocery products by category  
- searching and filtering products  
- adding and managing items in a cart  
- placing orders with delivery details  
- basic user account functionality  

The system will not initially include:
- advanced payment gateway integration (basic flow only)  
- real-time delivery tracking  
- full supplier/vendor management system  

---

## 2. Primary Users and Goals

### 2.1 Primary Users
- students and young adults  
- working professionals with busy schedules  
- users who prefer online grocery shopping  

---

### 2.2 Secondary Users
- admin managing products and orders  
- small grocery suppliers (future scope)  

---

### 2.3 User Goals
- quickly find grocery products  
- purchase items easily without confusion  
- manage cart and orders efficiently  
- save time compared to physical shopping  

---

### 2.4 User Actions
Users will perform actions such as:
- browse products by category  
- search for specific items  
- apply filters (price, category)  
- add/remove items from cart  
- update item quantity  
- proceed to checkout and place order  

---

## 3. User Personas

### Persona 1: Busy Student
- Name: Alex  
- Age: 21  
- Background: University student managing studies and part-time job  
- Goals: Quickly order groceries online  
- Pain Points: Limited time, prefers simple and fast interface  

---

### Persona 2: Working Professional
- Name: Daniel  
- Age: 29  
- Background: Full-time employee with a busy schedule  
- Goals: Convenient grocery shopping without visiting stores  
- Pain Points: Time-consuming physical shopping, complex apps  

---

### Persona 3: Admin User
- Name: Priya  
- Age: 32  
- Background: Store manager managing online inventory  
- Goals: Update products and manage orders efficiently  
- Pain Points: Needs a simple system to manage listings  

---

## 4. Functional Requirements

### 4.1 Product Browsing
- FR1: The system shall display a list of grocery products.  
- FR2: The system shall organize products by categories.  
- FR3: The system shall display product details including name, price, and image.  

---

### 4.2 Search and Filtering
- FR4: The system shall allow users to search for products by name.  
- FR5: The system shall allow filtering by category and price range.  
- FR6: The system shall update results dynamically based on filters.  

---

### 4.3 Shopping Cart
- FR7: The system shall allow users to add products to the cart.  
- FR8: The system shall allow users to remove products from the cart.  
- FR9: The system shall allow users to update product quantity.  
- FR10: The system shall calculate the total price of items in the cart.  

---

### 4.4 Checkout Process
- FR11: The system shall allow users to proceed to checkout.  
- FR12: The system shall collect delivery details (address, contact).  
- FR13: The system shall confirm and place the order.  

---

### 4.5 User Account
- FR14: The system shall allow users to register and log in.  
- FR15: The system shall store basic user information securely.  

---

### 4.6 Admin Panel
- FR16: The system shall allow admin to add products.  
- FR17: The system shall allow admin to update product details.  
- FR18: The system shall allow admin to delete products.  
- FR19: The system shall allow admin to view and manage orders.  

---

## 5. Non-Functional Requirements

### 5.1 Usability
- NFR1: The interface shall be simple and easy to use.  
- NFR2: The system shall support responsive design for mobile and desktop.  

---

### 5.2 Performance
- NFR3: Product pages should load within 2–3 seconds.  
- NFR4: Search results should appear quickly without delay.  

---

### 5.3 Reliability
- NFR5: The system should operate without crashes during normal usage.  
- NFR6: The system should handle invalid input gracefully.  

---

### 5.4 Security
- NFR7: User data shall be stored securely.  
- NFR8: Login system shall protect against unauthorized access.  

---

### 5.5 Scalability
- NFR9: The system should support increasing number of users and products in future.  

---

## 6. User Stories

### Shopping
- As a user, I want to browse products so that I can see available groceries.  
- As a user, I want to search for items so that I can quickly find what I need.  
- As a user, I want to filter products so that I can narrow down choices.  

---

### Cart and Checkout
- As a user, I want to add items to cart so that I can purchase multiple products.  
- As a user, I want to update quantities so that I can control my order.  
- As a user, I want to place an order so that I can receive groceries.  

---

### Admin
- As an admin, I want to manage products so that I can keep listings updated.  
- As an admin, I want to view orders so that I can process them efficiently.  

---

## 7. Acceptance Criteria

### For Product Browsing
- Users can view products with name, price, and image.  
- Products are organized by categories.  

---

### For Search and Filtering
- Users can search for products by name.  
- Filters update results correctly.  

---

### For Cart
- Users can add/remove items from cart.  
- Total price updates automatically.  

---

### For Checkout
- Users can enter delivery details.  
- Order can be successfully placed.  

---

### For Admin
- Admin can add, update, and delete products.  
- Admin can view customer orders.  

---

### For Performance and Usability
- System works on mobile and desktop.  
- Pages load within defined performance limits.  

---

## 8. Summary
The primary users are students and working professionals who need a fast and simple way to shop for groceries online. Their goals are to browse, select, and purchase products efficiently. This SRS defines the core requirements for building a scalable, user-friendly eCommerce platform and serves as a foundation for future development and enhancements.
