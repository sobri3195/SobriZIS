<template>
    <AppLayout>
        <div class="relative bg-gradient-to-r from-green-600 to-green-700 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">
                        Berbagi Kebaikan dengan Mudah dan Transparan
                    </h1>
                    <p class="text-xl mb-8 text-green-100">
                        Salurkan Zakat, Infaq, dan Sedekah Anda melalui platform digital yang aman dan terpercaya
                    </p>
                    <div class="flex justify-center space-x-4">
                        <Link :href="route('programs.index')" class="bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-green-50 transition-colors">
                            Lihat Program
                        </Link>
                        <a href="#calculator" class="bg-green-800 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-900 transition-colors">
                            Kalkulator Zakat
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 -mt-16">
                <div class="card text-center">
                    <div class="text-green-600 text-4xl font-bold mb-2">
                        {{ formatCurrency(stats.total_donations) }}
                    </div>
                    <div class="text-gray-600">Total Donasi</div>
                </div>
                <div class="card text-center">
                    <div class="text-green-600 text-4xl font-bold mb-2">
                        {{ stats.total_donors }}+
                    </div>
                    <div class="text-gray-600">Donatur</div>
                </div>
                <div class="card text-center">
                    <div class="text-green-600 text-4xl font-bold mb-2">
                        {{ stats.active_programs }}
                    </div>
                    <div class="text-gray-600">Program Aktif</div>
                </div>
            </div>

            <div class="mt-16">
                <h2 class="text-3xl font-bold text-center mb-8">Program Unggulan</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="program in featuredPrograms" :key="program.id" class="card hover:shadow-lg transition-shadow">
                        <img
                            v-if="program.featured_image"
                            :src="program.featured_image"
                            :alt="program.title"
                            class="w-full h-48 object-cover rounded-lg mb-4"
                        />
                        <div class="mb-2">
                            <span class="badge badge-info">{{ program.category }}</span>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">{{ program.title }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ program.description.substring(0, 100) }}...</p>
                        
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Terkumpul</span>
                                <span class="font-semibold">{{ program.progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div
                                    class="bg-green-600 h-2 rounded-full"
                                    :style="{ width: program.progress_percentage + '%' }"
                                ></div>
                            </div>
                            <div class="flex justify-between text-sm mt-1">
                                <span class="text-green-600 font-semibold">{{ formatCurrency(program.collected_amount) }}</span>
                                <span class="text-gray-500">dari {{ formatCurrency(program.target_amount) }}</span>
                            </div>
                        </div>

                        <Link
                            :href="route('programs.show', program.slug)"
                            class="btn-primary w-full text-center"
                        >
                            Donasi Sekarang
                        </Link>
                    </div>
                </div>
            </div>

            <div id="calculator" class="mt-16 bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-8">
                <h2 class="text-3xl font-bold text-center mb-4">Kalkulator Zakat</h2>
                <p class="text-center text-gray-600 mb-8">
                    Hitung kewajiban zakat Anda dengan mudah dan akurat
                </p>
                <div class="max-w-2xl mx-auto">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <button class="card text-center hover:shadow-md transition-shadow cursor-pointer">
                            <div class="text-3xl mb-2">💰</div>
                            <div class="font-semibold">Zakat Maal</div>
                        </button>
                        <button class="card text-center hover:shadow-md transition-shadow cursor-pointer">
                            <div class="text-3xl mb-2">💼</div>
                            <div class="font-semibold">Zakat Profesi</div>
                        </button>
                        <button class="card text-center hover:shadow-md transition-shadow cursor-pointer">
                            <div class="text-3xl mb-2">🌾</div>
                            <div class="font-semibold">Zakat Pertanian</div>
                        </button>
                        <button class="card text-center hover:shadow-md transition-shadow cursor-pointer">
                            <div class="text-3xl mb-2">✨</div>
                            <div class="font-semibold">Zakat Emas</div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-16">
                <h2 class="text-3xl font-bold text-center mb-8">Mengapa SobriZIS?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Aman & Terpercaya</h3>
                        <p class="text-gray-600">Platform dengan enkripsi tingkat bank dan sertifikasi keamanan internasional</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">100% Transparan</h3>
                        <p class="text-gray-600">Lacak donasi Anda secara real-time dan lihat bukti penyaluran</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Mudah & Cepat</h3>
                        <p class="text-gray-600">Berbagai metode pembayaran: QRIS, Transfer Bank, E-Wallet</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_donations: 0,
            total_donors: 0,
            active_programs: 0,
        }),
    },
    featuredPrograms: {
        type: Array,
        default: () => [],
    },
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};
</script>
