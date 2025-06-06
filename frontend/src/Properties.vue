<template>
    <div class="container">

        <form @submit.prevent="">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"></path>
                        </svg>
                    </span>
                    <input v-model="searchQuery" class="form-control" type="text" placeholder="Search" />
                </div>
            </div>
        </form>

        <h2>Properties</h2>
        <div v-if="properties.length" class="row row-cols-1 row-cols-lg-2 g-2">
            <div v-for="property in filteredProperties" :key="property.id" class="col">
                <div class="card h-100">
                    <img :src="property.imageurl" alt="property image" width="100%" height="250px" style="object-fit:cover;"/>
                    <div class="card-body">
                        <h5 class="card-title">{{ property.name }}</h5>
                        <h6 class="card-title">{{ property.area }}, {{ property.city }}</h6>
                        <p class="card-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-leaf-fill" viewBox="0 0 16 16">
                                <path d="M1.4 1.7c.217.289.65.84 1.725 1.274 1.093.44 2.885.774 5.834.528 2.02-.168 3.431.51 4.326 1.556C14.161 6.082 14.5 7.41 14.5 8.5q0 .344-.027.734C13.387 8.252 11.877 7.76 10.39 7.5c-2.016-.288-4.188-.445-5.59-2.045-.142-.162-.402-.102-.379.112.108.985 1.104 1.82 1.844 2.308 2.37 1.566 5.772-.118 7.6 3.071.505.8 1.374 2.7 1.75 4.292.07.298-.066.611-.354.715a.7.7 0 0 1-.161.042 1 1 0 0 1-1.08-.794c-.13-.97-.396-1.913-.868-2.77C12.173 13.386 10.565 14 8 14c-1.854 0-3.32-.544-4.45-1.435-1.124-.887-1.889-2.095-2.39-3.383-1-2.562-1-5.536-.65-7.28L.73.806z"/>
                            </svg>
                            {{ property.emission }}</p>
                        <p v-if="property.rate" class="card-text">
                            FROM £{{ property.rate }}.00 PER NIGHT</p>
                        <p v-else class="card=text">RATES ON REQUEST</p>
                    </div>
                </div>
            </div>
            <div v-if="!filteredProperties.length">
                <p>No results</p>
            </div>
        </div>
        <div v-else class="d-flex justify-content-center">
            <div class="spinner-border" style="width: 5rem; height: 5rem;" role="status">
                <span class="visually-hidden">Loading properties...</span>
            </div>
        </div> 
    </div>
    


</template>
<script setup>
    import {ref, computed, onMounted } from 'vue'
    const properties = ref([]);
    const searchQuery = ref('');

    const filteredProperties = computed(() => {
        if(!searchQuery.value){
            return properties.value;
        }
        return properties.value.filter(property =>
            property.area.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
    });


    onMounted(() => {
        fetchProperties();
    });

    async function fetchProperties(){
        try {
            const response = await fetch('/api/properties');
            properties.value = await response.json();
        } catch (error) {
            console.error(error)
        }
    }

</script>

<style scoped>
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>