<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Shop</title>
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
            <a href="{{ route('shop.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('feedtanstorelogo.png') }}" alt="Feedtan Store" class="h-12">
            </a>

            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('shop.index') }}#shop" class="text-gray-700 hover:text-green-600 font-medium">Shop</a>
                <a href="{{ route('shop.index') }}#products" class="text-gray-700 hover:text-green-600 font-medium">Products</a>
                <a href="{{ route('shop.index') }}#contact" class="text-gray-700 hover:text-green-600 font-medium">Contact</a>
            </nav>

            <div class="flex items-center gap-4">
                <a href="{{ route('shop.index') }}" class="flex items-center gap-2 text-gray-700 hover:text-green-600">
                    <i class="fas fa-arrow-left text-lg"></i>
                    <span class="hidden sm:inline">Back</span>
                </a>
                <button id="cartBtn" class="relative p-2 text-gray-700 hover:text-green-600">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span id="cartCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Product Detail -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Images -->
            <div class="space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden aspect-square flex items-center justify-center p-6">
                    @php
                        $primaryImage = $product->images->firstWhere('is_primary', true);
                        $imageToShow = $primaryImage ? $primaryImage->image_path : $product->image;
                    @endphp
                    @if($imageToShow)
                        <img id="mainImage" src="{{ $imageToShow }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain">
                    @else
                        <i class="fas fa-box text-8xl text-gray-300"></i>
                    @endif
                </div>

                @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($product->images as $image)
                            <button onclick="changeImage('{{ $image->image_path }}')" class="bg-white rounded-xl shadow-sm border border-gray-100 aspect-square overflow-hidden hover:border-green-500 transition flex items-center justify-center p-2">
                                <img src="{{ $image->image_path }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        @if($product->category)
                            <span><i class="fas fa-folder mr-2"></i>{{ $product->category->name }}</span>
                        @endif
                        @if($product->brand)
                            <span><i class="fas fa-tag mr-2"></i>{{ $product->brand->name }}</span>
                        @endif
                        <span class="{{ $product->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                            <i class="fas fa-check-circle mr-2"></i>{{ $product->quantity > 0 ? $product->quantity . ' in stock' : 'Out of stock' }}
                        </span>
                    </div>
                </div>

                <div class="text-4xl font-bold text-green-700">
                    TZS {{ number_format($product->selling_price, 2) }}
                </div>

                @if($product->description)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-lg mb-3 text-gray-900">Description</h3>
                        <p class="text-gray-700">{{ $product->description }}</p>
                    </div>
                @endif

                <div class="flex gap-4">
                    <div class="flex items-center gap-2">
                        <label class="font-semibold text-gray-900">Qty:</label>
                        <input type="number" id="quantity" value="1" min="1" max="{{ $product->quantity }}" class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-center focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }})" class="flex-1 bg-green-600 text-white py-3 px-8 rounded-lg hover:bg-green-700 transition font-semibold flex items-center justify-center gap-2" {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                        <i class="fas fa-cart-plus"></i>
                        Add to Cart
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('shop.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 py-3 px-8 rounded-lg transition font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Continue Shopping
                    </a>
                    <a href="{{ route('shop.checkout') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-8 rounded-lg transition font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-credit-card"></i>
                        Checkout
                    </a>
                </div>
            </div>
        </div>
    </main>

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
                        <li><a href="{{ route('shop.index') }}#shop" class="hover:text-white transition">Shop</a></li>
                        <li><a href="{{ route('shop.index') }}#products" class="hover:text-white transition">Products</a></li>
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
        let cart = [];

        // Initialize cart from localStorage
        function initCart() {
            const saved = localStorage.getItem('shopCart');
            if (saved) {
                cart = JSON.parse(saved);
                updateCartDisplay();
            }
        }

        function changeImage(imageSrc) {
            document.getElementById('mainImage').src = imageSrc;
        }

        function addToCart(id, name, price) {
            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.quantity += quantity;
            } else {
                cart.push({ id, name, price, quantity });
            }
            saveCart();
            updateCartDisplay();
            openCart();
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

            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            if (totalItems > 0) {
                cartCount.textContent = totalItems;
                cartCount.classList.remove('hidden');
                emptyCart.classList.add('hidden');
                cartFooter.classList.remove('hidden');
                
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

        initCart();
    </script>
</body>
</html>
