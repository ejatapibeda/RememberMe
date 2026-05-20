<template>
    <div class="container mx-auto px-4 py-8 md:py-12">
        <div class="flex flex-col items-center justify-center mb-10">
            <h1 class="font-kaushan text-4xl text-gray-800 mb-8">Kategori</h1>

            <router-link to="/Create_kategori"
                class="flex items-center gap-3 px-6 py-3 bg-pink-100 text-pink-700 rounded-full shadow-md hover:bg-pink-200 transition-colors duration-200 text-xl font-kaushan mb-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Buat Kategori</span>
            </router-link>
        </div>

        <div v-if="isLoading" class="text-center font-poppins text-xl text-gray-500">
            Memuat kategori...
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <router-link 
                v-for="(category, index) in kategoriList" 
                :key="category.id" 
                :to="`/Show_Kategori/${category.id}`" 
                class="block group"
            >
                <div :class="[
                    getColor(index).bg,
                    'relative w-full h-48 rounded-[40px] shadow-lg flex items-center justify-center p-6',
                    'transform transition-all duration-300 group-hover:scale-105 group-hover:shadow-2xl cursor-pointer'
                ]">
                    <h2 :class="[
                        getColor(index).text,
                        'font-poppins text-3xl font-semibold select-none text-center break-words'
                    ]">
                        {{ category.nama_kategori }}
                    </h2>
                </div>
            </router-link>

            <div v-if="kategoriList.length === 0" class="col-span-full text-center text-gray-400 font-poppins mt-10">
                <p class="text-2xl">Belum ada kategori.</p>
                <p>Klik tombol "Buat Kategori" untuk memulai.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {
        onMounted
    } from 'vue';
    import {
        useKategoriStore
    } from '@/Stores/Kategori';
    import {
        storeToRefs
    } from 'pinia';

    const kategoriStore = useKategoriStore();
    const {
        kategoriList,
        isLoading
    } = storeToRefs(kategoriStore);

    // Daftar palet warna pastel yang lebih banyak
    const colorPalette = [{
            bg: 'bg-green-100',
            text: 'text-green-800'
        },
        {
            bg: 'bg-pink-100',
            text: 'text-pink-800'
        },
        {
            bg: 'bg-blue-200',
            text: 'text-blue-800'
        },
        {
            bg: 'bg-yellow-100',
            text: 'text-yellow-800'
        },
        {
            bg: 'bg-purple-100',
            text: 'text-purple-800'
        },
        {
            bg: 'bg-orange-100',
            text: 'text-orange-800'
        },
        {
            bg: 'bg-teal-100',
            text: 'text-teal-800'
        },
        {
            bg: 'bg-red-100',
            text: 'text-red-800'
        },
    ];

    // Fungsi untuk mengambil warna berdasarkan index
    const getColor = (index) => {
        return colorPalette[index % colorPalette.length];
    };

    onMounted(() => {
        kategoriStore.fetchKategori();
    });
</script>