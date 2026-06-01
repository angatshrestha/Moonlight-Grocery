# Moonlight Grocery — Project History, Deployment & Deliverables

This document provides a comprehensive history of how the **Moonlight Grocery** web application was designed, built, tested, and deployed to production hosting on InfinityFree.

---

## 1. Project Background & Tech Stack

The Moonlight Grocery platform is a secure, interactive, and responsive e-commerce web application built for local organic produce sales. The platform features separate portal access for Customers, Delivery Drivers, and System Administrators.

### Key Architectural Layers
*   **Backend Logic (PHP)**: Pure PHP handles session management, multi-role user authentications (Customer, Driver, Admin), checkout calculations, invoice generation, database operations, and AI chatbot routing.
*   **Database (MySQL)**: Dynamic data storage using relational schemas. All critical operations use **PDO prepared statements** to completely safeguard against SQL injection.
*   **Frontend Interfaces (HTML5, Bootstrap 4, JS)**: Responsive web interfaces styled using **Bootstrap 4** and custom vanilla variables inside `style.css`. Lightweight client-side processes are offloaded to **Vanilla JavaScript** to perform dynamic UI adjustments (like live calculations, rating stars, and prompt responses) without page reloads.

---

## 2. Advanced Application Features

We successfully integrated high-value features to deliver a premium user experience:

*   **Secure Authentication & OTP Verification**: Custom signup, login, and secure password hashing using native PHP `bcrypt` (`password_hash()`). Features a mock OTP verification flow on checkout.
*   **Loyalty Points Engine**: Programmed to reward users with **1 point per $1 spent**, which accumulates and is redeemable at checkout for direct discounts ($0.01 per point).
*   **Integrated Payment Gateways**: Implemented credit card handling along with real-time **PayPal JavaScript SDK** API integration and e-Sewa support.
*   **Leaflet.js Map & GPS Routing**: An interactive customer tracking portal utilizing **Leaflet.js** and the open-source **OSRM Routing API**. It uses the browser **Geolocation API** on the Driver Portal to capture coordinates on delivery updates and calculate live driving ETAs for customers.
*   **AI Chatbot Assistant**: A floating client widget connected via custom cURL request to the **Gemini AI API** to answer store-related inquiries instantly.
*   **Showcase Feedback Portal & QR Integration**: A live feedback wall allowing real-time reviews. Includes a clean, scan-to-draft email QR code that uses the **goQR.me API** to directly draft inquiries straight to `angatshrestha2@gmail.com`.

---

## 3. Deployment Workflow & FTP Synchronization

To make pushing local updates easy and robust, we designed a custom deployment sync system instead of manual uploads:

1.  **Remote Connection Configuration**: Set up connection coordinates targeting **InfinityFree** via FTP:
    *   **FTP Host**: `ftpupload.net`
    *   **FTP Username**: `if0_41940951`
2.  **Auto-Synchronization Script (`sync_ftp.php`)**:
    *   Programmed a server script utilizing PHP's native `ftp_*` functions to establish a secure data socket in passive mode.
    *   Maintains an array of active files (e.g. `index.php`, `cart.php`, `review.php`, stylesheet assets, subdirectories).
    *   Recursively builds remote directories and uploads updated source files in binary mode straight to the remote server's `/htdocs` folder.
    *   Scans and cleanses deprecated server files automatically to maintain a clean directory structure.

---

## 4. Quality Assurance, Testing & Resolution

Quality was ensured using structured test phases as outlined in `testing.md`:

### Testing Levels Executed
*   **Unit Testing**: Isolated validation of cart sums, rating logic, and database operations.
*   **Integration Testing**: Verified database integrity during checkouts, chatbot routing, and coordinates passing from driver database columns onto customer maps.
*   **Manual Testing**: Thoroughly tested all workflows (registering accounts, product browsing, adding/editing items, driver order processing, and administrative status updating).

### QA Results Dashboard

| Test Case | Expected Outcome | Actual Outcome | Status |
| :--- | :--- | :--- | :--- |
| **User Sign-in** | Authenticate user & start safe PHP Session | Authenticated and saved session data | **Passed** |
| **Product Listings** | Read catalog entries dynamically | Rendered product tiles from MySQL table | **Passed** |
| **Cart Quantities** | Adjust items & recalculate subtotal | Re-evaluated cart value in real time | **Passed** |
| **Simulated OTP** | Generate verification code and validate | Successfully validated mock code | **Passed** |
| **Checkout Flow** | Complete payment and log transaction | Added order record & deducted points | **Passed** |
| **Driver Location** | Catch driver GPS coordinates | Saved latitude/longitude to database | **Passed** |

*   **Bugs Discovered & Resolved**: Resolved connection exceptions during host transitions, corrected relative link paths, eliminated visual styling inconsistencies using CSS overrides, and replaced the deprecated Google Charts QR generator with the modern `api.qrserver.com` service.

---

## 5. Final Project Deliverables

At the completion of the project cycle, we compiled and handed over the following core deliverables:

1.  **Showcase Application Codebase**: Clean, well-commented HTML, CSS, JavaScript, and PHP source files.
2.  **Production Live Link**: Fully functioning showcase portal hosted at [moonlight999.infinityfree.me](http://moonlight999.infinityfree.me/review.php).
3.  **Relational Database Mapping**: Exported `moonlight_grocery.sql` containing schema structures and sample records.
4.  **Operational Manifests**: The comprehensive `README.md` layout and structural `testing.md` checklist detailing all test iterations.
