<template>
    <div class="container">
        <nav class="nav nav-tabs mb-3">
            <a class="nav-link" :class="{ active: activeTab === 'albums' }" @click="activeTab = 'albums'" href="#">Albums</a>
            <a class="nav-link" :class="{ active: activeTab === 'apartments' }" @click="activeTab = 'apartments'" href="#">Apartments</a>
        </nav>

        <div v-if="activeTab === 'albums'">
            <Albums />
        </div>

        <div v-if="activeTab === 'apartments'">
            <Apartments />
        </div>
    </div>
</template>
  
<script setup>
    import { ref } from 'vue'
    import Albums from './Albums.vue'
    import Apartments from './Apartments.vue'
    
    const activeTab = ref('albums');
</script>
  