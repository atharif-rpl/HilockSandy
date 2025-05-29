{{-- resources/views/layouts/partials/navbar.blade.php --}}
<nav class="bg-white border-b shadow-sm mb-4 sticky-top" id="mainNavbar">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
            {{-- Logo & Brand --}}
            <div class="flex items-center space-x-4">
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}"
                   class="flex items-center space-x-2 text-xl font-bold text-gray-800 hover:text-blue-600 transition-colors">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice text-white text-sm"></i>
                    </div>
                    <span>{{ config('app.name', 'HilockSandy') }}</span>
                </a>

                {{-- Breadcrumb (Desktop Only) --}}
                <div class="hidden lg:flex items-center text-sm text-gray-500" id="breadcrumb">
                    <!-- Breadcrumb will be populated by JavaScript -->
                </div>
            </div>

            {{-- Search Bar (Desktop) --}}
            <div class="hidden lg:flex flex-1 max-w-md mx-8">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="globalSearch"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                           placeholder="Search invoices, customers...">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <kbd class="inline-flex items-center border border-gray-200 rounded px-2 py-0.5 text-xs font-sans font-medium text-gray-400">
                            Ctrl K
                        </kbd>
                    </div>
                </div>

                {{-- Search Results Dropdown --}}
                <div id="searchResults" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden">
                    <div class="p-2">
                        <div class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">Recent Searches</div>
                        <div id="searchResultsList">
                            <!-- Search results will be populated here -->
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side Actions --}}
            <div class="flex items-center space-x-3">
                {{-- Theme Toggle --}}
                <button id="themeToggle"
                        class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Toggle Dark Mode">
                    <i class="fas fa-moon text-sm"></i>
                </button>

                {{-- Notifications (Authenticated Users Only) --}}
                @auth
                <div class="relative">
                    <button id="notificationsBtn"
                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors relative"
                            title="Notifications">
                        <i class="fas fa-bell text-sm"></i>
                        <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center" id="notificationBadge">
                            3
                        </span>
                    </button>

                    {{-- Notifications Dropdown --}}
                    <div id="notificationsDropdown"
                         class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                <button class="text-xs text-blue-600 hover:text-blue-800" id="markAllReadBtn">
                                    Mark all as read
                                </button>
                            </div>
                        </div>
                        <div class="max-h-64 overflow-y-auto" id="notificationsList">
                            <!-- Notifications will be populated here -->
                        </div>
                        <div class="p-3 border-t border-gray-200 text-center">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View all notifications</a>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="relative">
                    <button id="quickActionsBtn"
                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                            title="Quick Actions">
                        <i class="fas fa-plus text-sm"></i>
                    </button>

                    {{-- Quick Actions Dropdown --}}
                    <div id="quickActionsDropdown"
                         class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden">
                        <div class="py-1">
                            @if(Auth::user()->role === 'admin')
                            <a href="{{ route('invoices.create') }}"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-file-plus w-4 mr-3 text-blue-500"></i>
                                New Invoice
                            </a>
                            @endif
                            <a href="#" id="quickSearchBtn"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-search w-4 mr-3 text-green-500"></i>
                                Quick Search
                            </a>
                            <a href="#" id="exportDataBtn"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-download w-4 mr-3 text-purple-500"></i>
                                Export Data
                            </a>
                        </div>
                    </div>
                </div>
                @endauth

                {{-- Mobile Menu Toggle --}}
                <button class="lg:hidden p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors"
                        id="mobileMenuToggle">
                    <i class="fas fa-bars text-sm"></i>
                </button>

                {{-- Desktop Menu --}}
                <div class="hidden lg:flex items-center space-x-6">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'text-gray-700 hover:text-blue-500' }}">
                            <i class="fas fa-tachometer-alt mr-1"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('invoices.index') }}"
                            class="text-sm font-medium transition-colors {{ request()->routeIs('invoices.*') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'text-gray-700 hover:text-blue-500' }}">
                            <i class="fas fa-file-invoice mr-1"></i>
                            Invoices
                        </a>

                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('invoices.create') }}"
                                class="text-sm font-medium transition-colors {{ request()->routeIs('invoices.create') ? 'text-blue-600 border-b-2 border-blue-600 pb-1' : 'text-gray-700 hover:text-blue-500' }}">
                                <i class="fas fa-plus mr-1"></i>
                                Create
                            </a>
                        @endif
                    @endauth
                </div>

                {{-- User Menu --}}
                <div class="hidden lg:flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-gray-700 hover:text-blue-500 transition-colors">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                Register
                            </a>
                        @endif
                    @else
                        <div class="relative" id="userMenuContainer">
                            <button id="userMenuBtn"
                                    class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none bg-gray-50 hover:bg-gray-100 px-3 py-2 rounded-lg transition-colors">
                                <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <div id="userMenuDropdown"
                                 class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden">
                                <div class="p-3 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                            <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                            <div class="text-xs text-blue-600 font-medium">{{ ucfirst(Auth::user()->role) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-1">
                                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user w-4 mr-3 text-gray-400"></i>
                                        My Profile
                                    </a>
                                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog w-4 mr-3 text-gray-400"></i>
                                        Settings
                                    </a>
                                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-question-circle w-4 mr-3 text-gray-400"></i>
                                        Help & Support
                                    </a>
                                </div>

                                <div class="border-t border-gray-200">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); this.closest('form').submit();"
                                           class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class="fas fa-sign-out-alt w-4 mr-3"></i>
                                            Logout
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobileMenu" class="lg:hidden border-t border-gray-200 bg-gray-50 hidden">
        <div class="px-4 py-3 space-y-3">
            {{-- Mobile Search --}}
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="mobileGlobalSearch"
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                       placeholder="Search invoices, customers...">
            </div>

            @auth
                {{-- Mobile Navigation Links --}}
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('invoices.index') }}"
                       class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('invoices.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-file-invoice w-5 mr-3"></i>
                        View Invoices
                    </a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('invoices.create') }}"
                           class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('invoices.create') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-plus w-5 mr-3"></i>
                            Create Invoice
                        </a>
                    @endif
                </div>

                {{-- Mobile User Info --}}
                <div class="border-t border-gray-200 pt-3 mt-3">
                    <div class="flex items-center space-x-3 px-3 py-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 text-sm">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-blue-600 font-medium">{{ ucfirst(Auth::user()->role) }}</div>
                        </div>
                    </div>

                    <div class="space-y-1 mt-2">
                        <a href="#" class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user w-5 mr-3 text-gray-400"></i>
                            My Profile
                        </a>
                        <a href="#" class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog w-5 mr-3 text-gray-400"></i>
                            Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="flex items-center px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 w-full">
                                <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                                Logout
                            </a>
                        </form>
                    </div>
                </div>
            @else
                {{-- Mobile Guest Links --}}
                <div class="space-y-1">
                    <a href="{{ route('login') }}"
                       class="flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-sign-in-alt w-5 mr-3"></i>
                        Login
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700">
                            <i class="fas fa-user-plus w-5 mr-3"></i>
                            Register
                        </a>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>

{{-- Global Search Modal --}}
<div id="searchModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" id="searchModalBackdrop"></div>
    <div class="fixed top-20 left-1/2 transform -translate-x-1/2 w-full max-w-2xl mx-4">
        <div class="bg-white rounded-lg shadow-xl">
            <div class="p-4 border-b border-gray-200">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="modalSearchInput"
                           class="block w-full pl-10 pr-3 py-3 border-0 text-lg placeholder-gray-500 focus:outline-none focus:ring-0"
                           placeholder="Search invoices, customers, or anything..."
                           autofocus>
                </div>
            </div>
            <div class="max-h-96 overflow-y-auto" id="modalSearchResults">
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-search text-2xl mb-2"></i>
                    <p>Start typing to search...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sample data for search and notifications
    const sampleInvoices = [
        { number: 'INV-2023-001', customer: 'PT Maju Bersama', amount: 5750000 },
        { number: 'INV-2023-002', customer: 'CV Teknologi Nusantara', amount: 8250000 },
        { number: 'INV-2023-003', customer: 'PT Sejahtera Abadi', amount: 12500000 }
    ];

    const sampleNotifications = [
        {
            id: 1,
            title: 'New Invoice Created',
            message: 'Invoice INV-2023-089 has been created',
            time: '10 minutes ago',
            type: 'info',
            unread: true
        },
        {
            id: 2,
            title: 'Payment Received',
            message: 'Payment for INV-2023-076 has been received',
            time: '2 hours ago',
            type: 'success',
            unread: true
        },
        {
            id: 3,
            title: 'Invoice Overdue',
            message: 'INV-2023-065 is overdue by 5 days',
            time: '1 day ago',
            type: 'warning',
            unread: false
        }
    ];

    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');

    mobileMenuToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('hidden');
        const icon = mobileMenuToggle.querySelector('i');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
    });

    // Theme toggle
    const themeToggle = document.getElementById('themeToggle');
    themeToggle.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        const icon = themeToggle.querySelector('i');
        if (document.body.classList.contains('dark-mode')) {
            icon.className = 'fas fa-sun text-sm';
            localStorage.setItem('darkMode', 'true');
        } else {
            icon.className = 'fas fa-moon text-sm';
            localStorage.setItem('darkMode', 'false');
        }
    });

    // Check for saved theme preference
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
        themeToggle.querySelector('i').className = 'fas fa-sun text-sm';
    }

    // Notifications
    const notificationsBtn = document.getElementById('notificationsBtn');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    const notificationsList = document.getElementById('notificationsList');
    const notificationBadge = document.getElementById('notificationBadge');

    function renderNotifications() {
        const unreadCount = sampleNotifications.filter(n => n.unread).length;
        notificationBadge.textContent = unreadCount;
        notificationBadge.style.display = unreadCount > 0 ? 'flex' : 'none';

        notificationsList.innerHTML = sampleNotifications.map(notification => {
            const typeIcons = {
                info: 'fa-info-circle text-blue-500',
                success: 'fa-check-circle text-green-500',
                warning: 'fa-exclamation-triangle text-yellow-500'
            };

            return `
                <div class="p-3 hover:bg-gray-50 border-b border-gray-100 ${notification.unread ? 'bg-blue-50' : ''}">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <i class="fas ${typeIcons[notification.type]}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                            <p class="text-sm text-gray-500">${notification.message}</p>
                            <p class="text-xs text-gray-400 mt-1">${notification.time}</p>
                        </div>
                        ${notification.unread ? '<div class="w-2 h-2 bg-blue-500 rounded-full"></div>' : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    if (notificationsBtn) {
        notificationsBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationsDropdown.classList.toggle('hidden');
            renderNotifications();
        });

        // Mark all as read
        document.getElementById('markAllReadBtn').addEventListener('click', function() {
            sampleNotifications.forEach(n => n.unread = false);
            renderNotifications();
        });
    }

    // Quick Actions
    const quickActionsBtn = document.getElementById('quickActionsBtn');
    const quickActionsDropdown = document.getElementById('quickActionsDropdown');

    if (quickActionsBtn) {
        quickActionsBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            quickActionsDropdown.classList.toggle('hidden');
        });
    }

    // User Menu
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenuDropdown = document.getElementById('userMenuDropdown');

    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenuDropdown.classList.toggle('hidden');
        });
    }

    // Global Search
    const globalSearch = document.getElementById('globalSearch');
    const searchResults = document.getElementById('searchResults');
    const searchModal = document.getElementById('searchModal');
    const modalSearchInput = document.getElementById('modalSearchInput');
    const modalSearchResults = document.getElementById('modalSearchResults');

    function performSearch(query) {
        if (query.length < 2) return [];

        return sampleInvoices.filter(invoice =>
            invoice.number.toLowerCase().includes(query.toLowerCase()) ||
            invoice.customer.toLowerCase().includes(query.toLowerCase())
        );
    }

    function renderSearchResults(results, container) {
        if (results.length === 0) {
            container.innerHTML = '<div class="p-4 text-center text-gray-500">No results found</div>';
            return;
        }

        container.innerHTML = results.map(result => `
            <a href="#" class="block p-3 hover:bg-gray-50 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-gray-900">${result.number}</div>
                        <div class="text-sm text-gray-500">${result.customer}</div>
                    </div>
                    <div class="text-sm font-medium text-gray-900">
                        ${result.amount.toLocaleString('id-ID')}
                    </div>
                </div>
            </a>
        `).join('');
    }

    if (globalSearch) {
        globalSearch.addEventListener('input', function() {
            const query = this.value;
            if (query.length > 0) {
                const results = performSearch(query);
                renderSearchResults(results, document.getElementById('searchResultsList'));
                searchResults.classList.remove('hidden');
            } else {
                searchResults.classList.add('hidden');
            }
        });

        globalSearch.addEventListener('blur', function() {
            setTimeout(() => searchResults.classList.add('hidden'), 200);
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+K to open search modal
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchModal.classList.remove('hidden');
            modalSearchInput.focus();
        }

        // Escape to close search modal
        if (e.key === 'Escape') {
            searchModal.classList.add('hidden');
        }
    });

    // Search modal
    if (searchModal) {
        document.getElementById('searchModalBackdrop').addEventListener('click', function() {
            searchModal.classList.add('hidden');
        });

        modalSearchInput.addEventListener('input', function() {
            const query = this.value;
            const results = performSearch(query);

            if (query.length === 0) {
                modalSearchResults.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        <i class="fas fa-search text-2xl mb-2"></i>
                        <p>Start typing to search...</p>
                    </div>
                `;
            } else {
                renderSearchResults(results, modalSearchResults);
            }
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        if (notificationsDropdown) notificationsDropdown.classList.add('hidden');
        if (quickActionsDropdown) quickActionsDropdown.classList.add('hidden');
        if (userMenuDropdown) userMenuDropdown.classList.add('hidden');
        if (searchResults) searchResults.classList.add('hidden');
    });

    // Breadcrumb generation
    function generateBreadcrumb() {
        const path = window.location.pathname;
        const breadcrumb = document.getElementById('breadcrumb');

        if (!breadcrumb) return;

        let breadcrumbHTML = '<i class="fas fa-chevron-right mx-2"></i>';

        if (path.includes('/invoices/create')) {
            breadcrumbHTML += '<span class="text-gray-400">Invoices</span> <i class="fas fa-chevron-right mx-2"></i> <span class="text-gray-600">Create</span>';
        } else if (path.includes('/invoices/') && path.includes('/edit')) {
            breadcrumbHTML += '<span class="text-gray-400">Invoices</span> <i class="fas fa-chevron-right mx-2"></i> <span class="text-gray-600">Edit</span>';
        } else if (path.includes('/invoices/')) {
            breadcrumbHTML += '<span class="text-gray-400">Invoices</span> <i class="fas fa-chevron-right mx-2"></i> <span class="text-gray-600">Detail</span>';
        } else if (path.includes('/invoices')) {
            breadcrumbHTML += '<span class="text-gray-600">Invoices</span>';
        } else if (path.includes('/dashboard')) {
            breadcrumbHTML += '<span class="text-gray-600">Dashboard</span>';
        }

        breadcrumb.innerHTML = breadcrumbHTML;
    }

    generateBreadcrumb();

    // Initialize notifications
    if (notificationsList) {
        renderNotifications();
    }
});
</script>

<style>
/* Dark mode styles */
.dark-mode {
    background-color: #1a1a1a;
    color: #e0e0e0;
}

.dark-mode nav {
    background-color: #2d2d2d;
    border-color: #404040;
}

.dark-mode .bg-white {
    background-color: #2d2d2d;
}

.dark-mode .text-gray-800 {
    color: #e0e0e0;
}

.dark-mode .text-gray-700 {
    color: #b0b0b0;
}

.dark-mode .text-gray-500 {
    color: #888;
}

.dark-mode .border-gray-200 {
    border-color: #404040;
}

.dark-mode .bg-gray-50 {
    background-color: #333;
}

.dark-mode .hover\:bg-gray-100:hover {
    background-color: #404040;
}

/* Sticky navbar */
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 1020;
}

/* Search modal animation */
#searchModal {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Notification badge pulse */
#notificationBadge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Smooth transitions */
.transition-colors {
    transition: color 0.2s ease, background-color 0.2s ease;
}

/* Mobile responsive improvements */
@media (max-width: 1024px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>