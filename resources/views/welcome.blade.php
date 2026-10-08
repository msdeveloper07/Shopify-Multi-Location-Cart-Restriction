<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout Application</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#2563EB',
            secondary: '#1E40AF',
          },
        },
      },
    }
  </script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-white text-gray-800">

  <!-- Hero Section -->
  <section class="bg-gradient-to-br from-blue-50 to-blue-100 py-24">
    <div class="max-w-7xl mx-auto px-6 text-center">
      <h1 class="text-5xl font-extrabold text-gray-900 mb-4">Checkout Control</h1>
      <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
        Prevent mixed-location carts and simplify fulfillment. Your customers can only checkout products from a single location — exactly how you want it.
      </p>
      <a href="#" class="inline-block px-8 py-3 bg-primary text-white rounded-xl font-medium shadow hover:bg-secondary transition">
        Get Started
      </a>
    </div>
  </section>

  <!-- How It Works Section -->
  <section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-6 text-center">
      <h2 class="text-3xl font-bold mb-12">How It Works</h2>
      <div class="grid md:grid-cols-3 gap-10 text-left">
        <div class="p-6 bg-gray-50 rounded-xl shadow-md hover:shadow-lg transition">
          <div class="mb-4 text-blue-600"><i data-lucide="shopping-cart" class="w-6 h-6"></i></div>
          <h3 class="text-xl font-semibold mb-2">Detect Cart Locations</h3>
          <p class="text-gray-600">We automatically monitor the locations of all products added to the cart in real-time.</p>
        </div>
        <div class="p-6 bg-gray-50 rounded-xl shadow-md hover:shadow-lg transition">
          <div class="mb-4 text-blue-600"><i data-lucide="lock" class="w-6 h-6"></i></div>
          <h3 class="text-xl font-semibold mb-2">Block Mixed Checkouts</h3>
          <p class="text-gray-600">If items come from more than one location, checkout is disabled with a customizable warning message.</p>
        </div>
        <div class="p-6 bg-gray-50 rounded-xl shadow-md hover:shadow-lg transition">
          <div class="mb-4 text-blue-600"><i data-lucide="settings" class="w-6 h-6"></i></div>
          <h3 class="text-xl font-semibold mb-2">Merchant Controls</h3>
          <p class="text-gray-600">Configure behavior from the dashboard – override by tag, location, or product type.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Benefits Section -->
  <section class="bg-blue-50 py-20">
    <div class="max-w-6xl mx-auto px-6 text-center">
      <h2 class="text-3xl font-bold mb-12">Why Merchants Love It</h2>
      <div class="grid md:grid-cols-2 gap-10 text-left">
        <ul class="space-y-4 text-lg text-gray-700 list-disc list-inside">
          <li>Prevent order splits across warehouses</li>
          <li>Ensure smooth and accurate shipping</li>
          <li>Reduces confusion at checkout</li>
          <li>Customizable error messages and UI integration</li>
        </ul>
        <ul class="space-y-4 text-lg text-gray-700 list-disc list-inside">
          <li>No coding required – works out of the box</li>
          <li>Compatible with all major Shopify themes</li>
          <li>Ongoing support and app updates</li>
          <li>Built with performance in mind</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-6 text-center">
      <h2 class="text-3xl font-bold mb-12">What Merchants Are Saying</h2>
      <div class="grid md:grid-cols-2 gap-10 text-left">
        <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition">
          <p class="italic text-gray-700 mb-4">"Before this app, we struggled with orders being split across multiple warehouses. Now everything flows smoothly."</p>
          <p class="font-bold text-gray-900">— Alex M., Fulfillment Manager</p>
        </div>
        <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-lg transition">
          <p class="italic text-gray-700 mb-4">"We love how it just works — super easy to install and no issues since day one."</p>
          <p class="font-bold text-gray-900">— Priya S., DTC Store Owner</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-900 text-white py-12">
    <div class="max-w-6xl mx-auto px-6 text-center">
      <h3 class="text-2xl font-semibold mb-4">Single Location Checkout App</h3>
      <p class="text-gray-400 mb-6">Your go-to solution for enforcing location-specific order rules on Shopify.</p>
      <div class="space-x-6">
        <a href="#" class="text-gray-400 hover:text-white text-sm">Support</a>
        <a href="#" class="text-gray-400 hover:text-white text-sm">Terms</a>
        <a href="#" class="text-gray-400 hover:text-white text-sm">Privacy</a>
      </div>
      <p class="text-gray-500 text-xs mt-6">&copy; 2025 Browns. All rights reserved.</p>
    </div>
  </footer>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>
