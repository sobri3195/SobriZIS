<template>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <Link :href="route('home')" class="text-2xl font-bold text-green-600">
                                SobriZIS
                            </Link>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <Link
                                :href="route('home')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                                :class="isActive('home') ? 'border-green-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                            >
                                Home
                            </Link>
                            <Link
                                :href="route('programs.index')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                                :class="isActive('programs.*') ? 'border-green-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                            >
                                Programs
                            </Link>
                            <Link
                                :href="route('transparency')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                                :class="isActive('transparency') ? 'border-green-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                            >
                                Transparency
                            </Link>
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <template v-if="$page.props.auth.user">
                            <div class="ml-3 relative">
                                <div class="flex items-center space-x-4">
                                    <Link :href="route('dashboard')" class="text-sm text-gray-700 hover:text-gray-900">
                                        Dashboard
                                    </Link>
                                    <button @click="logout" class="text-sm text-gray-700 hover:text-gray-900">
                                        Logout
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex items-center space-x-4">
                                <Link :href="route('login')" class="text-sm text-gray-700 hover:text-gray-900">
                                    Login
                                </Link>
                                <Link :href="route('register')" class="btn-primary">
                                    Register
                                </Link>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <slot />
        </main>

        <footer class="bg-gray-800 text-white mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-lg font-bold mb-4">SobriZIS</h3>
                        <p class="text-gray-400 text-sm">
                            Platform digital untuk pengelolaan Zakat, Infaq, dan Sedekah yang transparan dan akuntabel.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><Link :href="route('programs.index')" class="hover:text-white">Programs</Link></li>
                            <li><Link :href="route('transparency')" class="hover:text-white">Transparency</Link></li>
                            <li><a href="#" class="hover:text-white">About Us</a></li>
                            <li><a href="#" class="hover:text-white">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold mb-4">Legal</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                            <li><a href="#" class="hover:text-white">Terms of Service</a></li>
                            <li><a href="#" class="hover:text-white">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold mb-4">Contact</h4>
                        <ul class="space-y-2 text-sm text-gray-400">
                            <li>Email: info@sobrizis.com</li>
                            <li>Phone: +62 21 1234567</li>
                            <li>Jakarta, Indonesia</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
                    <p>&copy; {{ currentYear }} SobriZIS. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const currentYear = new Date().getFullYear();

const isActive = (routeName) => {
    return route().current(routeName);
};

const logout = () => {
    router.post(route('logout'));
};
</script>
