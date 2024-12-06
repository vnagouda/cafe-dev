document.addEventListener('DOMContentLoaded', () => {
    const cartCountElement = document.getElementById('cart-count-header');
    const toastContainer = document.getElementById('toast-container');
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    const cartSummary = document.getElementById('cart-summary');

    // Function to show toast notifications
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `px-4 py-2 rounded-lg shadow-md text-white mb-2 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.textContent = message;
        toastContainer?.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Update cart count in the header
    async function updateCartCount() {
        try {
            const response = await fetch('/cafe2/user/cart.php?action=count');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            if (data.status === 'success') {
                cartCountElement.textContent = data.count || 0;
            } else {
                console.error('Error fetching cart count:', data.message);
            }
        } catch (error) {
            console.error('Fetch error:', error);
        }
    }

    // Fetch and display cart items
    async function fetchCartItems() {
        if (!cartItemsContainer || !cartTotalElement || !cartSummary) return;

        try {
            const response = await fetch('/cafe2/user/cart.php?action=view');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            if (data.status === 'success') {
                cartItemsContainer.innerHTML = '';
                let total = 0;

                data.cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;

                    const cartItem = document.createElement('div');
                    cartItem.className = 'bg-white shadow-lg rounded-lg overflow-hidden';
                    cartItem.innerHTML = `
                        <img src="${item.image}" alt="${item.name}" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h2 class="text-xl font-bold">${item.name}</h2>
                            <p class="text-gray-500">Quantity: ${item.quantity}</p>
                            <p class="text-gray-500">Price: $${item.price.toFixed(2)}</p>
                            <p class="text-yellow-600 font-bold mt-2">Total: $${itemTotal.toFixed(2)}</p>
                        </div>
                    `;
                    cartItemsContainer.appendChild(cartItem);
                });

                if (total > 0) {
                    cartSummary.classList.remove('hidden');
                    cartTotalElement.textContent = total.toFixed(2);
                } else {
                    cartSummary.classList.add('hidden');
                }
            } else {
                console.error('Failed to fetch cart items:', data.message);
            }
        } catch (error) {
            console.error('Fetch error:', error);
        }
    }

    // Add item to cart
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', async () => {
            const itemId = button.dataset.id;

            try {
                const response = await fetch('/cafe2/user/cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ item_id: itemId, action: 'add' }),
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.status === 'success') {
                    updateCartCount();
                    showToast('Item added to cart successfully!');
                } else {
                    showToast('Failed to add item to cart.', 'error');
                    console.error('Add to cart error:', data.message);
                }
            } catch (error) {
                showToast('An error occurred.', 'error');
                console.error('Fetch error:', error);
            }
        });
    });

    // Initialize cart count and items
    if (cartCountElement) {
        updateCartCount();
    }

    if (cartItemsContainer) {
        fetchCartItems();
    }
});
