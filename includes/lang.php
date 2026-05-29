<?php
// Translation helper for English and Nepali language support
// Moonlight Grocery Multilingual System

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if a language change is requested via URL
if (isset($_GET['lang'])) {
    $selectedLang = trim($_GET['lang']);
    if (in_array($selectedLang, ['en', 'ne'])) {
        $_SESSION['lang'] = $selectedLang;
    }
    // Redirect back to clean URL
    $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    // Strip lang parameter from referer to prevent redirect loops
    $referer = preg_replace('/(\?|&)lang=[^&]+/', '', $referer);
    header("Location: $referer");
    exit;
}

// Default to English if no preference is set in session
$currentLang = $_SESSION['lang'] ?? 'en';

// Master translation dictionary
$translations = [
    'en' => [
        'Shop' => 'Shop',
        'Cart' => 'Cart',
        'My Orders' => 'My Orders',
        'Admin Dashboard' => 'Admin Dashboard',
        'Logout' => 'Logout',
        'Login' => 'Login',
        'Sign Up' => 'Sign Up',
        'Welcome Back' => 'Welcome Back',
        'Welcome to Moonlight' => 'Welcome to Moonlight',
        'Discover fresh produce and daily essentials.' => 'Discover fresh produce and daily essentials.',
        'Shop now' => 'Shop now',
        'Daily Offers' => 'Daily Offers',
        'Special Offer' => 'Special Offer',
        'Add to Cart' => 'Add to Cart',
        'Featured Products' => 'Featured Products',
        'View All' => 'View All',
        'Add' => 'Add',
        'Categories' => 'Categories',
        'All Products' => 'All Products',
        'Specials & Offers' => 'Specials & Offers',
        'Search products...' => 'Search products...',
        'No products found in this category. We are adding more products soon!' => 'No products found in this category. We are adding more products soon!',
        'Catalogue' => 'Catalogue',
        'All Specials & Offers' => 'All Specials & Offers',
        'Ways to Shop' => 'Ways to Shop',
        'Healthylife +Pharmacy' => 'Healthylife +Pharmacy',
        'Everyday Extra' => 'Everyday Extra',
        'Fresh Market Update' => 'Fresh Market Update',
        'Quick Links' => 'Quick Links',
        'Shop All' => 'Shop All',
        'Your Account' => 'Your Account',
        'Shopping Cart' => 'Shopping Cart',
        'Contact Us' => 'Contact Us',
        'Delivering the freshest organic produce and daily essentials right to your doorstep since 2026. Quality you can trust.' => 'Delivering the freshest organic produce and daily essentials right to your doorstep since 2026. Quality you can trust.',
        'Daily Featured Products' => 'Daily Featured Products',
        'Fresh Daily' => 'Fresh Daily'
    ],
    'ne' => [
        'Shop' => 'पसल',
        'Cart' => 'झोला',
        'My Orders' => 'मेरो अर्डरहरू',
        'Admin Dashboard' => 'प्रशासक प्यानल',
        'Logout' => 'बाहिर निस्कनुहोस्',
        'Login' => 'लगइन',
        'Sign Up' => 'दर्ता गर्नुहोस्',
        'Welcome Back' => 'स्वागत छ',
        'Welcome to Moonlight' => 'मूनलाइटमा स्वागत छ',
        'Discover fresh produce and daily essentials.' => 'ताजा जैविक उत्पादनहरू र दैनिक आवश्यक वस्तुहरू पाउनुहोस्।',
        'Shop now' => 'अहिले खरिद गर्नुहोस्',
        'Daily Offers' => 'दैनिक अफरहरू',
        'Special Offer' => 'विशेष अफर',
        'Add to Cart' => 'झोलामा थप्नुहोस्',
        'Featured Products' => 'विशेष उत्पादनहरू',
        'View All' => 'सबै हेर्नुहोस्',
        'Add' => 'थप्नुहोस्',
        'Categories' => 'वर्गहरू',
        'All Products' => 'सबै उत्पादनहरू',
        'Specials & Offers' => 'विशेष र अफरहरू',
        'Search products...' => 'उत्पादन खोज्नुहोस्...',
        'No products found in this category. We are adding more products soon!' => 'यस वर्गमा कुनै उत्पादन फेला परेन। हामी चाँडै थप उत्पादनहरू थप्दैछौं!',
        'Catalogue' => 'क्याटलग हेर्नुहोस्',
        'All Specials & Offers' => 'सबै विशेष अफरहरू',
        'Ways to Shop' => 'किनमेल गर्ने तरिकाहरू',
        'Healthylife +Pharmacy' => 'स्वस्थ जीवन +फार्मेसी',
        'Everyday Extra' => 'दैनिक थप सुविधा',
        'Fresh Market Update' => 'ताजा बजार विवरण',
        'Quick Links' => 'छिटो लिङ्कहरू',
        'Shop All' => 'सबै पसल',
        'Your Account' => 'तपाईंको खाता',
        'Shopping Cart' => 'किनमेल झोला',
        'Contact Us' => 'हामीलाई सम्पर्क गर्नुहोस',
        'Delivering the freshest organic produce and daily essentials right to your doorstep since 2026. Quality you can trust.' => 'सन् २०२६ देखि तपाईंको ढोकामै सबैभन्दा ताजा जैविक उत्पादन र दैनिक आवश्यक वस्तुहरू पुर्‍याउँदै। विश्वासिलो गुणस्तर।',
        'Daily Featured Products' => 'दैनिक विशेष उत्पादनहरू',
        'Fresh Daily' => 'ताजा दैनिक'
    ]
];

// Global translation helper function
function __($key) {
    global $translations, $currentLang;
    if (isset($translations[$currentLang][$key])) {
        return $translations[$currentLang][$key];
    }
    // Fallback to key itself
    return $key;
}
?>
