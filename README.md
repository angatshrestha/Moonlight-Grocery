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





---
# Moonlight Grocery: Syntax & Concepts Guide

Throughout the development of the Moonlight Grocery platform, we used a variety of programming languages and specific syntax patterns. This document serves as a cheat sheet for all the major syntax and tools used in your codebase.

---

## 1. PHP (Backend Logic)
PHP handles all the "server-side" processing, like talking to the database, authenticating users, and doing math.

### Superglobals
*   `$_SESSION`: Used to remember who is logged in across different pages. (e.g., `$_SESSION['user_id'] = 5;`)
*   `$_POST`: Captures hidden data sent from forms when users click submit (e.g., `$_POST['email']`).
*   `$_GET`: Captures data sent inside the website URL (e.g., `?offer=1` becomes `$_GET['offer']`).

### Database Queries (PDO)
We used **PDO (PHP Data Objects)** to talk to MySQL securely.
*   `$pdo->query("SELECT * FROM products")`: Executes a simple SQL query immediately.
*   `$pdo->prepare(...)` and `$stmt->execute([...])`: A two-step process called a "prepared statement." We used this anywhere a user typed something (like a login or checkout form) to prevent hackers from injecting malicious SQL code.
*   `$stmt->fetch()`: Retrieves a single row from the database (like getting one specific user's profile).
*   `$stmt->fetchAll()`: Retrieves a list/array of multiple rows (like getting all the items in an order).

### Security & Redirection
*   `password_hash($password, PASSWORD_DEFAULT)`: Takes a plain text password and scrambles it securely before saving it to the database.
*   `password_verify($input, $hashed_password)`: Checks if a typed password matches the scrambled one in the database.
*   `header("Location: index.php")`: Instantly redirects the user's browser to a different page.

---

## 2. JavaScript (Frontend Interaction)
JavaScript runs in the user's browser to make the page dynamic without needing to refresh.

### DOM Manipulation
*   `document.getElementById('my-element')`: Finds a specific HTML tag on the page so JS can modify it.
*   `document.querySelector('input[name="street"]')`: Finds an HTML element based on advanced CSS selectors.
*   `element.style.display = 'none'`: Hides an element from the screen (used heavily in the checkout payment toggles).

### External APIs
*   `fetch('api.php', { method: 'POST' })`: Sends invisible requests to our server in the background (used by our AI Chatbot to get Gemini answers without reloading the page).
*   `navigator.geolocation.getCurrentPosition()`: The browser API we used in the Driver Portal to ask the driver's phone for its exact GPS latitude and longitude.

### Third-Party Libraries
*   `paypal.Buttons({...}).render(...)`: The syntax required by the official PayPal SDK to draw their secure payment buttons onto our checkout page.
*   `L.map('map')` & `L.Routing.control(...)`: The syntax from Leaflet.js and OSRM used to draw the live maps and calculate driving durations.

---

## 3. SQL (Database Language)
SQL is used to read and write data to the MySQL database tables.

*   `SELECT * FROM table`: Retrieves data.
*   `INSERT INTO orders (user_id, total) VALUES (?, ?)`: Creates a brand new record.
*   `UPDATE users SET points = points + 10 WHERE id = 1`: Modifies an existing record (e.g., giving loyalty points).
*   `DELETE FROM products WHERE id = 5`: Removes a record.
*   `JOIN`: Used in our complex queries to combine data from multiple tables (e.g., getting the Order details AND the User's name in one query).

---

## 4. HTML & CSS (Structure & Styling)
We utilized the **Bootstrap 4** framework to make styling fast and responsive.

### HTML Structure
*   `require_once 'includes/header.php'`: Instead of writing the `<head>` and navbar on every single page, we wrote it once and "included" it everywhere.
*   `<form method="POST">`: The wrapper around all our inputs (login, checkout, product adding) that tells the browser to send the data securely to PHP.

### Bootstrap CSS Classes
Instead of writing custom CSS for everything, we used Bootstrap's built-in utility classes:
*   `container`: Centers the content with nice margins on the sides.
*   `row` and `col-md-4`: Used to create responsive grids (like the 3-column product layout that stacks into a single column on mobile phones).
*   `btn btn-primary`: Instantly styles a boring link or button into a nice blue, clickable button.
*   `alert alert-success`: Draws the green success boxes you see after placing an order or logging in.

### Custom CSS
*   `var(--primary-color)`: We used CSS Custom Properties (variables) in `style.css` so we could easily change the entire theme color of the website by only editing one single line.
