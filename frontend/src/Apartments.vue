<template>
    <div>
        <h2>Apartment List</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>City</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="apartment in apartmentCollection.apartments" :key="apartment.id">
                    <td>{{ apartment.name }}</td>
                    <td>{{ apartment.city }}</td>
                    <td>
                        <button :data-id="apartment.id" @click="deleteApartment" class="btn button btn-danger">x</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <form @submit.prevent="addApartment">
            <div class="row">
                <div class="col">
                    <input type="text" class="form-control" required v-model="name" placeholder="Apartment Name"/>
                </div>      
                <div class="col">
                    <input type="text" class="form-control" required v-model="city" placeholder="City"/>
                </div>
                <div class="col"> 
                    <button class="btn button btn-success">Add new</button>
                </div> 
            </div>  
        </form>
    </div>
</template>
  
<script setup>
    import { reactive, ref, onMounted } from 'vue'
    const name = ref('');
    const city = ref('');

    const apartmentCollection = reactive({
        apartments: []
    });

    onMounted(() => {
        fetchApartments();
    })

    async function fetchApartments() {
        try {
            const response = await fetch('/api/apartments');
            const data = await response.json();
            apartmentCollection.apartments = data;
        } catch (error) {
            console.error('Error listing apartments:', error);
        }
    }

    async function addApartment() {
        const requestOptions = {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                name: name.value,
                city: city.value
            })
        }
        try {
            const response = await fetch('/api/apartments', requestOptions);
            const result = await response.json();
            if(response.status === 422) {
                console.error('Validation error:', result);
            }
            name.value = '';
            city.value = '';
            await fetchApartments();
        } catch (error) {
            console.error('Error adding apartment:', error);
        }
    }

    async function deleteApartment(event) {
        const id = event.target.getAttribute('data-id');
        const requestOptions = {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'}
        }
        try {
            const response = await fetch(`/api/apartments/${id}`, requestOptions);
            await fetchApartments();
        } catch (error) {
            console.error('Error deleting apartment:', error);
        }
    }
</script> 