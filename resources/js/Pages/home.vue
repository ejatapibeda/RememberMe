<template>
    <div>
        <div class="min-h-screen">
            <div class="container mx-auto px-4 py-12">
                <div class="flex flex-col lg:flex-row items-start justify-center gap-12 lg:gap-20">

                    <div class="w-full lg:w-1/2 flex flex-col items-center lg:sticky lg:top-24">
                        <h1 class="text-4xl md:text-5xl font-bold text-blue-700 mb-8 font-poppins drop-shadow-sm">
                            Never Forget Again!
                        </h1>
                        <div class="relative">
                            <img src="../assets/logo.png" alt="Cat Reminder"
                                class="w-72 md:w-96 lg:w-[450px] object-contain drop-shadow-2xl" />

                            <div
                                class="absolute -top-4 right-12 w-10 h-10 bg-blue-100 rounded-full opacity-40 animate-bounce">
                            </div>
                            <div
                                class="absolute -top-10 right-6 w-5 h-5 bg-blue-300 rounded-full opacity-40 animate-bounce">
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-1/2 max-w-xl">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-3xl font-kaushan text-gray-800">Tugas Aktif</h2>
                            <div v-if="isLoading"
                                class="animate-spin h-5 w-5 border-2 border-blue-500 border-t-transparent rounded-full">
                            </div>
                        </div>

                        <div class="space-y-8">
                            <TransitionGroup v-if="filteredTasks.length > 0" name="list" tag="div"
                                class="flex flex-col gap-8">
                                <div v-for="(task, index) in filteredTasks" :key="task.id" :class="[
                                    getColor(index).bg,
                                    'relative w-full h-52 rounded-[45px] shadow-xl overflow-hidden transform transition-all hover:scale-[1.02] duration-300'
                                ]">

                                    <div class="absolute top-0 left-0 w-full flex items-start z-10">
                                        <div class="backdrop-blur-sm px-6 py-3 rounded-br-[35px] flex items-center gap-3 shadow-sm min-w-[140px] transition-colors duration-300"
                                            :style="{ backgroundColor: task.prioritas === 'ya' ? '#FFD83A' : 'rgba(255, 255, 255, 0.9)' }">
                                            <CalendarDaysIcon class="w-5 h-5"
                                                :class="task.prioritas === 'ya' ? 'text-black' : 'text-gray-600'" />
                                            <span class="font-bold font-sans text-xs"
                                                :class="task.prioritas === 'ya' ? 'text-black' : 'text-gray-700'">
                                                {{ formatDateTime(task.tanggal) }}
                                            </span>
                                        </div>
                                        <div class="px-6 py-3 flex-grow text-left">
                                            <span class="font-dancing text-2xl text-gray-700 opacity-90">
                                                {{ task.kategori?.nama_kategori || 'Kategori' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="absolute top-5 right-8 z-20">
                                        <div class="cursor-pointer transition-transform active:scale-90"
                                            @click="toggleCheck(task)">
                                            <div v-if="!task.is_done"
                                                class="w-9 h-9 bg-white border-2 border-gray-800 rounded-2xl shadow-sm flex items-center justify-center">
                                                <XMarkIcon v-if="task.its_over"
                                                    class="w-7 h-7 text-red-600 font-bold" />
                                            </div>
                                            <CheckIcon v-else class="w-12 h-12 text-blue-600 drop-shadow-md" />
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-center justify-center h-full w-full p-8 pt-12">
                                        <h2
                                            :class="[getColor(index).text, 'font-kaushan text-5xl text-center leading-tight drop-shadow-md filter brightness-90']">
                                            {{ task.tugas }}
                                        </h2>
                                    </div>
                                </div>
                            </TransitionGroup>

                            <div v-else-if="!isLoading"
                                class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-[40px] py-16 px-8 text-center flex flex-col items-center gap-6 transition-colors duration-300">
                                <div class="relative">
                                    <CalendarDaysIcon
                                        class="w-20 h-20 text-blue-200 group-hover:text-blue-300 transition-colors shadow-sm" />
                                    <div
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-400 rounded-full animate-ping">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <p class="font-kaushan text-4xl text-gray-400">Belum ada tugas nih...</p>
                                    <p class="font-poppins text-gray-500 text-sm italic">"Jangan biarkan hari ini
                                        berlalu tanpa rencana!"</p>
                                </div>
                                <router-link to="/Create_Tugas"
                                    class="mt-4 px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full shadow-lg transform transition-all hover:scale-110 active:scale-95 flex items-center gap-2">
                                    <span class="text-xl">✨</span> Ayo buat tugas sekarang!
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import {
        ref,
        computed,
        onMounted
    } from 'vue';
    import {
        storeToRefs
    } from 'pinia';
    import {
        XMarkIcon,
        CalendarDaysIcon,
        CheckIcon
    } from '@heroicons/vue/24/solid';
    import { useTugasStore } from "@/Stores/Tugas";

    const tugasStore = useTugasStore();
    const { tugasList, isLoading } = storeToRefs(tugasStore);

    onMounted(async () => {
        await tugasStore.fetchTugas();
    });

  const colorPalette = [
    { 
        bg: 'bg-[#B2F5EA]', // Teal/Mint medium
        text: 'text-[#2C7A7B]', 
        accent: 'text-[#285E61]' 
    },
    { 
        bg: 'bg-[#FED7E2]', // Pink medium
        text: 'text-[#97266D]', 
        accent: 'text-[#702459]' 
    },
    { 
        bg: 'bg-[#BEE3F8]', // Biru medium (lebih gelap dari sky blue)
        text: 'text-[#2C5282]', 
        accent: 'text-[#2A4365]' 
    },
    { 
        bg: 'bg-[#FEEBC8]', // Orange/Cream hangat
        text: 'text-[#9C4221]', 
        accent: 'text-[#7B341E]' 
    }
];
    const getColor = (index) => colorPalette[index % colorPalette.length];

    // Format Tanggal & Jam untuk tampilan
    const formatDateTime = (dateTimeString) => {
        if (!dateTimeString) return "";
        const date = new Date(dateTimeString);
        return date.toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).replace(/\//g, '-').replace(',', '');
    };

    const toggleCheck = async (task) => {
        task.is_done = !task.is_done;
        const success = await tugasStore.updateStatus(task.id, task.is_done);
        if (!success) {
            task.is_done = !task.is_done;
            alert("Gagal memperbarui status tugas.");
        }
    };

    const isDone = (t) => t.is_done === true || t.is_done === 1 || t.is_done === "1";

    const filteredTasks = computed(() => {
        let list = Array.isArray(tugasList.value) ? [...tugasList.value] : [];

        // Hanya sembunyikan tugas selesai; tugas lewat waktu (its_over) tetap tampil di Tugas Aktif
        list = list.filter((t) => !isDone(t));

        return list.sort((a, b) => {
            if (a.prioritas === "ya" && b.prioritas !== "ya") return -1;
            if (a.prioritas !== "ya" && b.prioritas === "ya") return 1;
            return new Date(b.tanggal) - new Date(a.tanggal);
        });
    });
</script>

<style scoped>
    /* Menambahkan font yang lebih tebal dan jelas */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Patrick+Hand&family=Kaushan+Script&display=swap');

    .font-poppins {
        font-family: 'Poppins', sans-serif;
    }

    /* Font Kaushan untuk Judul (Tetap dipertahankan tapi kita beri penebalan di CSS) */
   

    /* Mengganti font kategori ke Patrick Hand agar lebih tebal dari Dancing Script */
    .font-dancing {
        font-family: 'Patrick Hand', cursive;
        font-weight: 400;
    }

    /* Animasi List Vertikal */
    .list-enter-active,
    .list-leave-active {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .list-enter-from,
    .list-leave-to {
        opacity: 0;
        transform: translateX(30px) scale(0.95);
    }

    .list-move {
        transition: transform 0.5s ease;
    }
</style>