<template>
    <div class="container">
        <nav class="nav nav-tabs mb-3">
            <a class="nav-link" :class="{ active: activeTab === 'property-search' }" @click="activeTab = 'property-search'" href="#">Property Search</a>
            <a class="nav-link" :class="{ active: activeTab === 'properties' }" @click="activeTab = 'properties'" href="#">Properties</a>
            <a class="nav-link" :class="{ active: activeTab === 'albums' }" @click="activeTab = 'albums'" href="#">Albums</a>
            <a class="nav-link" :class="{ active: activeTab === 'apartments' }" @click="activeTab = 'apartments'" href="#">Apartments</a>
        </nav>

        <div v-if="activeTab === 'property-search'">
            <PropertySearch />
        </div>

        <div v-if="activeTab === 'properties'">
            <Properties />
        </div>

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
    import Properties from './Properties.vue'
    import PropertySearch from './components/PropertySearch.vue'
    
    const activeTab = ref('property-search');
</script>
  
<style>
@import 'bootstrap-icons/font/bootstrap-icons.css';
</style>
  