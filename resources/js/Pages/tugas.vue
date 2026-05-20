<template>
    <div class="container mx-auto px-4 py-8 md:py-12">
        <div class="flex flex-col items-center justify-center mb-10">
            <h1 class="font-kaushan text-4xl text-gray-800 mb-8">Tugas Saya</h1>

            <div class="flex items-center space-x-4">
                <router-link to="/Create_Tugas"
                    class="flex items-center gap-3 px-6 py-3 bg-pink-100 text-pink-700 rounded-full shadow-md hover:bg-pink-200 transition-all text-xl font-kaushan">
                    <PlusCircleIcon class="w-7 h-7 text-pink-700" />
                    <span>Buat Tugas</span>
                </router-link>

                <div class="relative">
                    <button @click="searchDropdownOpen = !searchDropdownOpen"
                        class="p-3 bg-gray-200 rounded-full shadow-md hover:bg-gray-300 transition-all">
                        <MagnifyingGlassIcon class="w-6 h-6 text-gray-700" />
                    </button>

                    <div v-if="searchDropdownOpen"
                        class="absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl p-4 border border-gray-100 z-50">
                        <div class="relative">
                            <input type="text" v-model="searchQuery" placeholder="Cari judul tugas..."
                                class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-400 focus:outline-none text-sm" />
                            <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" />
                        </div>
                        <button v-if="searchQuery" @click="searchQuery = ''"
                            class="text-[10px] text-red-500 mt-2 hover:underline">Hapus Pencarian</button>
                    </div>
                </div>

                <div class="relative">
                    <button @click="toggleFilterDropdown"
                        class="p-3 bg-gray-200 rounded-full shadow-md hover:bg-gray-300 transition-all">
                        <FunnelIcon class="w-6 h-6 text-gray-700" />
                    </button>
                    <div v-if="filterDropdownOpen"
                        class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl p-5 border border-gray-100 z-50">

                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Rentang Tanggal</label>
                            <div class="flex items-center gap-2">
                                <input type="date" v-model="filterDate.start"
                                    class="w-full p-2 text-xs border border-gray-200 rounded-lg" />
                                <span class="text-gray-400">&</span>
                                <input type="date" v-model="filterDate.end"
                                    class="w-full p-2 text-xs border border-gray-200 rounded-lg" />
                            </div>
                            <button v-if="filterDate.start || filterDate.end" @click="resetDateFilter"
                                class="text-[10px] text-red-500 mt-2 hover:underline">Hapus Filter Tanggal</button>
                        </div>

                        <hr class="mb-4 border-gray-100" />

                        <label class="block text-sm font-bold text-gray-700 mb-3">Filter Kategori</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            <label v-for="cat in kategoriList" :key="cat.id"
                                class="flex items-center cursor-pointer group">
                                <input type="checkbox" :value="cat.id" v-model="selectedCategories"
                                    class="form-checkbox h-4 w-4 text-purple-600 rounded border-gray-300" />
                                <span
                                    class="ml-3 text-sm text-gray-600 group-hover:text-purple-700">{{ cat.nama_kategori }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isLoading" class="text-center font-poppins text-gray-500">Memuat tugas...</div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div v-for="(task, index) in filteredTasks" :key="task.id" class="relative">

                <router-link :to="`/Show_Tugas/${task.id}`" class="block">
                    <div
                        :class="[getColor(index).bg,'relative w-full h-64 rounded-[40px] shadow-lg overflow-hidden transform transition-all hover:scale-105 duration-300']">
                        <div class="absolute top-0 left-0 z-10">
                            <div class="backdrop-blur-sm px-5 py-3 rounded-br-[35px] flex items-center gap-3 shadow-sm transition-colors duration-300"
                                :style="{ backgroundColor: task.prioritas === 'ya' ? '#FFD83A' : 'rgba(255, 255, 255, 0.9)' }">

                                <CalendarDaysIcon class="w-5 h-5"
                                    :class="task.prioritas === 'ya' ? 'text-black' : 'text-gray-600'" />

                                <div class="flex flex-col">
                                    <span class="font-bold font-sans text-xs"
                                        :class="task.prioritas === 'ya' ? 'text-black' : 'text-gray-700'">
                                        {{ formatDateTime(task.tanggal) }}
                                    </span>
                                    <span v-if="task.prioritas === 'ya'"
                                        class="text-[10px] font-bold text-orange-700 flex items-center gap-1">
                                        ⭐ <span class="uppercase tracking-tighter">Prioritas</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-center justify-center h-full w-full p-8 transition-all duration-500"
                            :class="{ 'opacity-30 grayscale blur-[1px]': task.is_done }">
                            <h2
                                :class="[getColor(index).text, 'font-kaushan text-4xl text-center leading-tight mb-2', { 'line-through': task.is_done }]">
                                {{ task.tugas }}
                            </h2>
                            <span :class="[getColor(index).accent, 'font-dancing text-2xl opacity-80']">
                                {{ task.kategori?.nama_kategori || 'Tanpa Kategori' }}
                            </span>
                        </div>

                        <div v-if="task.is_done"
                            class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-4/5 h-1 bg-gray-800 rotate-[-5deg] rounded-full shadow-sm opacity-50"></div>
                        </div>
                    </div>
                </router-link>

                <div class="absolute top-5 right-8 z-20">
                    <div class="cursor-pointer transition-transform active:scale-90" @click.stop="toggleCheck(task)">
                        <CheckIcon v-if="task.is_done" class="w-12 h-12 text-blue-600 drop-shadow-md" />
                        <XMarkIcon v-else-if="task.its_over" class="w-12 h-12 text-red-600 drop-shadow-md" />
                        <div v-else
                            class="w-9 h-9 bg-white border-2 border-gray-800 rounded-2xl shadow-sm flex items-center justify-center">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div v-if="!isLoading && filteredTasks.length === 0" class="text-center py-20 opacity-40 font-kaushan text-3xl">
            Belum ada tugas...
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { 
    FunnelIcon, 
    XMarkIcon, 
    PlusCircleIcon, 
    CalendarDaysIcon, 
    CheckIcon,
    MagnifyingGlassIcon // Pastikan import icon search
} from '@heroicons/vue/24/solid';
import { useTugasStore } from '@/Stores/Tugas';
import { useKategoriStore } from '@/Stores/Kategori';
import { storeToRefs } from 'pinia';

// 1. Inisialisasi Store
const tugasStore = useTugasStore();
const kategoriStore = useKategoriStore();
const { tugasList, isLoading } = storeToRefs(tugasStore);
const { kategoriList } = storeToRefs(kategoriStore);

// 2. State untuk Filter & UI
const searchDropdownOpen = ref(false);
const filterDropdownOpen = ref(false);
const searchQuery = ref("");
const selectedCategories = ref([]);
const filterDate = ref({ start: '', end: '' });

// 3. Lifecycle (Digabung jadi satu agar lebih rapi)
onMounted(async () => {
    await Promise.all([
        tugasStore.fetchTugas(),
        kategoriStore.fetchKategori()
    ]);
});

// 4. Helper Formatting
const formatDateTime = (dateTimeString) => {
    if (!dateTimeString) return "";
    const date = new Date(dateTimeString);
    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).replace(/\//g, '-');
};

// 5. Logika Filter Utama (Digabung jadi satu Computed)
// 5. Logika Filter Utama (Disesuaikan dengan nama kolom 'tugas')
const filteredTasks = computed(() => {
    let list = Array.isArray(tugasList.value) ? [...tugasList.value] : [];

    const isDone = (t) => t.is_done === true || t.is_done === 1 || t.is_done === "1";

    // A. Sembunyikan hanya tugas selesai; tugas lewat waktu (its_over) tetap tampil
    list = list.filter((t) => !isDone(t));

    // B. Filter Pencarian Teks (Mencari berdasarkan kolom 'tugas')
    if (searchQuery.value) {
        list = list.filter(t => 
            // PENTING: Gunakan t.tugas karena di template kamu pakai task.tugas
            t.tugas && t.tugas.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
    }

    // C. Filter berdasarkan Kategori (Menggunakan id_kategori)
    if (selectedCategories.value.length > 0) {
        list = list.filter(t => selectedCategories.value.includes(t.id_kategori));
    }

    // D. Filter berdasarkan Rentang Tanggal
    if (filterDate.value.start && filterDate.value.end) {
        const start = new Date(filterDate.value.start);
        const end = new Date(filterDate.value.end);
        list = list.filter(t => {
            const taskDate = new Date(t.tanggal);
            return taskDate >= start && taskDate <= end;
        });
    }

    // E. Pengurutan (Prioritas "Ya" di atas, lalu urutkan tanggal terdekat)
    return list.sort((a, b) => {
        if (a.prioritas === 'ya' && b.prioritas !== 'ya') return -1;
        if (a.prioritas !== 'ya' && b.prioritas === 'ya') return 1;
        return new Date(a.tanggal) - new Date(b.tanggal);
    });
});

// 6. Actions
const toggleCheck = async (task) => {
    const originalStatus = task.is_done;
    task.is_done = !task.is_done; // UI update cepat

    const success = await tugasStore.updateStatus(task.id, task.is_done);
    if (!success) {
        task.is_done = originalStatus; // Rollback jika gagal
        alert("Gagal memperbarui status tugas.");
    }
};

const resetDateFilter = () => {
    filterDate.value = { start: '', end: '' };
};

const toggleFilterDropdown = () => {
    filterDropdownOpen.value = !filterDropdownOpen.value;
    if (filterDropdownOpen.value) searchDropdownOpen.value = false; // Tutup pencarian jika filter dibuka
};

const toggleSearchDropdown = () => {
    searchDropdownOpen.value = !searchDropdownOpen.value;
    if (searchDropdownOpen.value) filterDropdownOpen.value = false; // Tutup filter jika pencarian dibuka
};

// Color Palette Tetap Sama
const colorPalette = [
    { bg: 'bg-[#B2F5EA]', text: 'text-[#2C7A7B]', accent: 'text-[#285E61]' },
    { bg: 'bg-[#FED7E2]', text: 'text-[#97266D]', accent: 'text-[#702459]' },
    { bg: 'bg-[#BEE3F8]', text: 'text-[#2C5282]', accent: 'text-[#2A4365]' },
    { bg: 'bg-[#FEEBC8]', text: 'text-[#9C4221]', accent: 'text-[#7B341E]' }
];
const getColor = (index) => colorPalette[index % colorPalette.length];
</script>

<style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Dancing+Script:wght@600;700&family=Kaushan+Script&display=swap');

    .font-kaushan {
        font-family: 'Segoe Print', cursive;
        /* font-family: 'Kaushan Script', cursive; */
    }

    .font-dancing {
        font-family: 'Sanchez', cursive;
        /* font-family: 'Dancing Script', cursive; */
    }

    .font-poppins {
        font-family: 'Poppins', sans-serif;
    }
</style>