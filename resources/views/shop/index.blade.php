<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedtan Store - Shop Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Manrope', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-4 text-sm">
                <span class="flex items-center gap-2">
                    <i class="fas fa-phone"></i>
                    +255 700 000 000
                </span>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-whatsapp text-lg"></i>
                </a>
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-facebook text-lg"></i>
                </a>
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-instagram text-lg"></i>
                </a>
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-twitter text-lg"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('feedtanstorelogo.png') }}" alt="Feedtan Store" class="h-12">
            </div>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="#shop" class="text-gray-700 hover:text-green-600 font-medium">Shop</a>
                <a href="#products" class="text-gray-700 hover:text-green-600 font-medium">Products</a>
                <a href="#contact" class="text-gray-700 hover:text-green-600 font-medium">Contact</a>
            </nav>
            
            <div class="flex items-center gap-4">
                <button id="cartBtn" class="relative p-2 text-gray-700 hover:text-green-600">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span id="cartCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                </button>
                
                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="md:hidden p-2 text-gray-700 hover:text-green-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div id="mobileMenu" class="md:hidden hidden bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col gap-4">
                <a href="#shop" class="text-gray-700 hover:text-green-600 font-medium py-2">Shop</a>
                <a href="#products" class="text-gray-700 hover:text-green-600 font-medium py-2">Products</a>
                <a href="#contact" class="text-gray-700 hover:text-green-600 font-medium py-2">Contact</a>
            </div>
        </div>
    </header>

    <!-- Hero Carousel -->
    <section id="shop" class="relative overflow-hidden">
        <div id="carousel" class="flex transition-transform duration-500 ease-in-out">
            <!-- Slide 1 -->
            <div class="min-w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">Shop Online with Feedtan Store</h1>
                    <p class="text-xl opacity-90 mb-8 max-w-2xl mx-auto">Discover quality products at unbeatable prices, delivered right to your door!</p>
                    <a href="#products" class="inline-block bg-white text-green-700 font-semibold px-10 py-4 rounded-xl hover:bg-gray-100 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-shopping-bag mr-2"></i> Browse Products
                    </a>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="min-w-full bg-gradient-to-r from-blue-600 to-purple-700 text-white py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">Fast & Reliable Delivery</h1>
                    <p class="text-xl opacity-90 mb-8 max-w-2xl mx-auto">Get your orders delivered within 24 hours in major cities!</p>
                    <a href="#contact" class="inline-block bg-white text-blue-700 font-semibold px-10 py-4 rounded-xl hover:bg-gray-100 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-truck mr-2"></i> Learn More
                    </a>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="min-w-full bg-gradient-to-r from-orange-500 to-pink-600 text-white py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="text-4xl md:text-6xl font-bold mb-6">Special Offers & Discounts</h1>
                    <p class="text-xl opacity-90 mb-8 max-w-2xl mx-auto">Enjoy exclusive deals and save big on your favorite products!</p>
                    <a href="#products" class="inline-block bg-white text-orange-600 font-semibold px-10 py-4 rounded-xl hover:bg-gray-100 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-percent mr-2"></i> Shop Deals
                    </a>
                </div>
            </div>
        </div>

        <!-- Carousel Controls -->
        <button id="prevBtn" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white p-3 rounded-full transition">
            <i class="fas fa-chevron-left text-2xl"></i>
        </button>
        <button id="nextBtn" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white p-3 rounded-full transition">
            <i class="fas fa-chevron-right text-2xl"></i>
        </button>

        <!-- Carousel Indicators -->
        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex gap-3">
            <button class="carousel-indicator w-3 h-3 rounded-full bg-white/50 hover:bg-white transition" data-index="0"></button>
            <button class="carousel-indicator w-3 h-3 rounded-full bg-white/50 hover:bg-white transition" data-index="1"></button>
            <button class="carousel-indicator w-3 h-3 rounded-full bg-white/50 hover:bg-white transition" data-index="2"></button>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Our Products</h2>
                <p class="text-gray-600">Choose from our wide range of quality products</p>
            </div>
            <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col" id="product-{{ $product->id }}">
                    <a href="{{ route('shop.product', $product) }}" class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center overflow-hidden">
                        @php
                            $primaryImage = $product->images->firstWhere('is_primary', true);
                            $imageToShow = $primaryImage ? $primaryImage->image_path : $product->image;
                        @endphp
                        @if($imageToShow)
                            <img src="{{ $imageToShow }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain">
                        @else
                            <i class="fas fa-box text-6xl text-gray-400"></i>
                        @endif
                    </a>
                    <div class="p-5 flex-1 flex flex-col">
                        <a href="{{ route('shop.product', $product) }}">
                            <h3 class="font-semibold text-lg text-gray-900 mb-1 hover:text-green-700 transition">{{ $product->name }}</h3>
                        </a>
                        <p class="text-xs text-gray-500 mb-3">{{ $product->category->name ?? 'Uncategorized' }} • {{ $product->brand->name ?? 'Generic' }}</p>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-2xl font-bold text-green-700">TZS {{ number_format($product->selling_price, 2) }}</span>
                            <span class="text-xs text-gray-500">{{ $product->quantity }} in stock</span>
                        </div>
                        <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }})" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium">
                            <i class="fas fa-cart-plus mr-2"></i> Add to Cart
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        </div>
    </section>

    <!-- Cart Sidebar -->
    <div id="cartSidebar" class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-50">
        <div class="h-full flex flex-col">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Your Cart</h3>
                <button onclick="closeCart()" class="p-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="cartItems" class="flex-1 overflow-y-auto p-6">
                <div id="cartItemsList"></div>
                <div id="emptyCart" class="text-center py-12">
                    <i class="fas fa-shopping-cart text-6xl text-gray-200 mb-4"></i>
                    <p class="text-gray-500">Your cart is empty</p>
                </div>
            </div>
            <div id="cartFooter" class="p-6 border-t border-gray-200 hidden">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-semibold text-gray-900">Total:</span>
                    <span id="cartTotal" class="text-2xl font-bold text-green-700">TZS 0.00</span>
                </div>
                <a href="{{ route('shop.checkout') }}" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition block text-center">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    </div>

    <!-- Cart Sidebar Overlay -->
    <div id="cartOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40" onclick="closeCart()"></div>

    <!-- Floating Action Buttons -->
    <div class="fixed bottom-6 right-6 flex flex-col gap-4 z-40">
        <a href="{{ route('shop.checkout') }}" id="floatingCheckoutBtn" class="bg-green-600 text-white p-4 rounded-full shadow-lg hover:bg-green-700 transition flex items-center gap-2 hidden">
            <i class="fas fa-credit-card text-xl"></i>
            <span class="text-sm font-semibold hidden md:inline">Checkout</span>
        </a>
        <a href="https://wa.me/255700000000" target="_blank" class="bg-green-500 text-white p-4 rounded-full shadow-lg hover:bg-green-600 transition flex items-center gap-2">
            <i class="fab fa-whatsapp text-xl"></i>
            <span class="text-sm font-semibold hidden md:inline">Book on WhatsApp</span>
        </a>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <img src="{{ asset('feedtanstorelogo.png') }}" alt="Feedtan Store" class="h-10 mb-4 opacity-90">
                    <p class="text-gray-400">Quality products delivered to your doorstep.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-lg mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#shop" class="hover:text-white transition">Shop</a></li>
                        <li><a href="#products" class="hover:text-white transition">Products</a></li>
                    </ul>
                </div>
                <div id="contact">
                    <h4 class="font-semibold text-lg mb-4">Contact</h4>
                    <p class="text-gray-400 mb-2"><i class="fas fa-phone mr-2"></i> +255 700 000 000</p>
                    <p class="text-gray-400 mb-2"><i class="fas fa-envelope mr-2"></i> info@feedtanstore.com</p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-500">
                <p>&copy; 2024 Feedtan Store. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Carousel functionality
        let currentSlide = 0;
        const totalSlides = 3;
        const carousel = document.getElementById('carousel');
        const indicators = document.querySelectorAll('.carousel-indicator');
        
        function updateCarousel() {
            carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
            indicators.forEach((indicator, index) => {
                if (index === currentSlide) {
                    indicator.classList.add('bg-white');
                    indicator.classList.remove('bg-white/50');
                } else {
                    indicator.classList.remove('bg-white');
                    indicator.classList.add('bg-white/50');
                }
            });
        }
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }
        
        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }
        
        document.getElementById('nextBtn').addEventListener('click', nextSlide);
        document.getElementById('prevBtn').addEventListener('click', prevSlide);
        
        indicators.forEach(indicator => {
            indicator.addEventListener('click', function() {
                currentSlide = parseInt(this.dataset.index);
                updateCarousel();
            });
        });
        
        // Auto-advance carousel every 5 seconds
        setInterval(nextSlide, 5000);
        
        // Initialize carousel
        updateCarousel();

        // Cart functionality
        let cart = [];

        // Initialize cart from localStorage
        function initCart() {
            const saved = localStorage.getItem('shopCart');
            if (saved) {
                cart = JSON.parse(saved);
                updateCartDisplay();
            }
        }

        function addToCart(id, name, price) {
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({ id, name, price, quantity: 1 });
            }
            saveCart();
            updateCartDisplay();
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            saveCart();
            updateCartDisplay();
        }

        function updateQuantity(id, change) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.quantity += change;
                if (item.quantity < 1) {
                    removeFromCart(id);
                } else {
                    saveCart();
                    updateCartDisplay();
                }
            }
        }

        function saveCart() {
            localStorage.setItem('shopCart', JSON.stringify(cart));
        }

        function calculateTotal() {
            return cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        }

        function updateCartDisplay() {
            const cartCount = document.getElementById('cartCount');
            const cartItemsList = document.getElementById('cartItemsList');
            const cartFooter = document.getElementById('cartFooter');
            const emptyCart = document.getElementById('emptyCart');
            const cartTotal = document.getElementById('cartTotal');
            const floatingCheckoutBtn = document.getElementById('floatingCheckoutBtn');

            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            if (totalItems > 0) {
                cartCount.textContent = totalItems;
                cartCount.classList.remove('hidden');
                emptyCart.classList.add('hidden');
                cartFooter.classList.remove('hidden');
                floatingCheckoutBtn.classList.remove('hidden');
                
                let itemsHtml = '';
                cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    itemsHtml += `
                        <div class="flex items-center gap-4 py-4 border-b border-gray-100">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-box text-gray-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 truncate">${item.name}</h4>
                                <p class="text-sm text-gray-500">TZS ${item.price.toFixed(2)} each</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="updateQuantity(${item.id}, -1)" class="w-8 h-8 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition flex items-center justify-center">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="w-8 text-center font-medium">${item.quantity}</span>
                                <button onclick="updateQuantity(${item.id}, 1)" class="w-8 h-8 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 transition flex items-center justify-center">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                });
                cartItemsList.innerHTML = itemsHtml;
                cartTotal.textContent = 'TZS ' + calculateTotal().toLocaleString('en-US', { minimumFractionDigits: 2 });
            } else {
                cartCount.classList.add('hidden');
                emptyCart.classList.remove('hidden');
                cartFooter.classList.add('hidden');
                floatingCheckoutBtn.classList.add('hidden');
                cartItemsList.innerHTML = '';
            }
        }

        function openCart() {
            document.getElementById('cartSidebar').classList.remove('translate-x-full');
            document.getElementById('cartOverlay').classList.remove('hidden');
        }

        function closeCart() {
            document.getElementById('cartSidebar').classList.add('translate-x-full');
            document.getElementById('cartOverlay').classList.add('hidden');
        }

        document.getElementById('cartBtn').addEventListener('click', openCart);
        
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        });
        
        // Close mobile menu when clicking on links
        const mobileLinks = document.querySelectorAll('#mobileMenu a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('mobileMenu').classList.add('hidden');
            });
        });

        initCart();
    </script>
</body>
</html>
