new features addded
  - invoice for customer and owner
  - Real time GPS tracking
  - order tracking
  - changes in admin portal
  - chanegs in item categories list
  - assigned the driver portal
  - add card payment system
  - many more
  
NOW SYSTEM ACCEPTS PAYPAL payment on real time

moonlightgrocery.epizy.com (hosting on infinity free) Demo


# Moonlight Grocery Website: Requirements Report

This document outlines the functional and non-functional features and requirements built into the Moonlight Grocery web application.

## 1. Functional Requirements (Features & Capabilities)
*These are the specific behaviors, features, and functions the system is built to perform.*

### 1.1. Customer-Facing Features
*   **User Authentication:** Customers can register, securely log in, and log out of their accounts. Password reset functionality is available.
*   **Product Browsing:** Customers can view a dynamic catalog of organic produce and groceries fetched directly from the database.
*   **Shopping Cart Management:** Customers can add items to their cart, adjust quantities, and remove items dynamically.
*   **Checkout & Logistics:** 
    *   Customers input granular delivery addresses (Street, City, State, Postcode).
    *   **OTP Verification:** Phone numbers are verified via a simulated One-Time Password (OTP) system before an order can be placed.
    *   **Loyalty Points:** Customers earn 1 point per $1 spent and can redeem accumulated points (100 points = $1) at checkout.
*   **Payment Gateway Integration:** The checkout system dynamically handles Credit Card (simulated), official PayPal (JS SDK integration), and e-Sewa payment methods.
*   **Live Order Tracking:** Customers can view the real-time status of their order. Once picked up, the system uses the driver's live GPS coordinates and the OSRM Routing API to calculate and display accurate driving ETAs on an OpenStreetMap interface.
*   **AI Chatbot Assistant:** An integrated Gemini-powered AI chatbot widget can answer customer questions dynamically from the bottom corner of the screen.
*   **Invoice Generation:** Customers can generate, view, and print dynamic HTML invoices for their past orders.

### 1.2. Admin & Staff Features
*   **Admin Dashboard:** Authorized administrators can access a protected control panel.
*   **Product Management:** Admins can Create, Read, Update, and Delete (CRUD) grocery products, manage stock levels, and upload product images.
*   **Order Management:** Admins can view all customer orders, assign specific drivers to deliveries, and update order statuses (Pending, Processing, Delivered).
*   **Driver Portal:** A dedicated, mobile-friendly interface for delivery drivers. Drivers can view their assigned orders and update statuses ("Picked Up", "Delivered").
*   **Driver GPS Capture:** When a driver updates an order status, the browser's Geolocation API automatically captures their precise latitude and longitude and saves it to the database to power the customer's tracking map.

---

## 2. Non-Functional Requirements (System Quality Attributes)
*These define how the system operates regarding performance, security, and usability.*

### 2.1. Security
*   **SQL Injection Prevention:** All database interactions utilize PHP Data Objects (PDO) with strictly parameterized prepared statements to prevent malicious SQL injections.
*   **Password Cryptography:** User passwords are encrypted using PHP's native `password_hash()` (bcrypt) before being stored in the database.
*   **Access Control:** strict session-based authentication routes ensure unauthenticated users cannot access the admin panel, driver portal, or checkout pages.
*   **Payment Security:** Offloading payment processing to official third-party SDKs (like PayPal) ensures sensitive financial data is not stored on the local server.

### 2.2. Usability & User Interface
*   **Responsive Design:** The entire application is built using the Bootstrap 4 framework, ensuring the UI adapts fluidly to desktop monitors, tablets, and mobile phones.
*   **Dynamic UX:** Interactions like cart updates, OTP generation, and payment method toggling utilize JavaScript to provide instant feedback without requiring a full page reload.

### 2.3. Performance & Reliability
*   **Optimized Routing:** Integration with the free Nominatim and OSRM APIs ensures lightweight, rapid calculation of geographic coordinates and driving times without burdening the local server.
*   **Relational Integrity:** The MySQL database schema utilizes foreign keys (e.g., linking `orders` to `users`, and `order_items` to `products`) to maintain strict data integrity.
*   **Timezone Synchronization:** The server configuration explicitly sets the timezone (`Australia/Sydney`) to ensure all timestamps, ETAs, and invoices are perfectly accurate for the target demographic.

### 2.4. Maintainability
*   **Modular Architecture:** The codebase utilizes reusable PHP components (e.g., `header.php`, `footer.php`, `config.php`) to ensure that global UI or database configuration changes only need to be made in a single file.
*   **Version Control:** The entire codebase is tracked using Git and synchronized to a remote GitHub repository to manage updates and deployments cleanly.
