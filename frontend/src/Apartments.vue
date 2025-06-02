<template>
    <div class="container">
        <h2>Apartment List</h2>
        
        <div class="card mb-2">
            <div class="card-body">
                <h3 class="card-title h5 mb-2">Add New Apartment</h3>
                <form @submit.prevent="addApartment">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" required v-model="name" placeholder="Apartment Name"/>
                        </div>      
                        <div class="col-md-4">
                            <input type="text" class="form-control" required v-model="city" placeholder="City"/>
                        </div>
                        <div class="col-md-4"> 
                            <button type="submit" class="btn btn-primary">Add Apartment</button>
                        </div> 
                    </div>
                </form>
            </div>
        </div>

        <div v-if="editingApartment && editingApartment.id" class="card mb-2">
            <div class="card-body">
                <h3 class="card-title h5 mb-2">Edit Apartment</h3>
                <form @submit.prevent="updateApartment">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" required v-model="editingApartment.name" placeholder="Apartment Name"/>
                        </div>      
                        <div class="col-md-4">
                            <input type="text" class="form-control" required v-model="editingApartment.city" placeholder="City"/>
                        </div>
                        <div class="col-md-4"> 
                            <button type="submit" class="btn btn-success me-2">Save Changes</button>
                            <button type="button" @click="cancelEdit" class="btn btn-secondary">Cancel</button>
                        </div> 
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>City</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="apartment in apartmentCollection.apartments" :key="apartment.id">
                                <td>{{ apartment.name }}</td>
                                <td>{{ apartment.city }}</td>
                                <td class="text-end">
                                    <button @click="startEdit(apartment)" class="btn btn-sm btn-outline-primary me-2">
                                        Edit
                                    </button>
                                    <button :data-id="apartment.id" @click="deleteApartment" class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
    
<script setup>
    import { reactive, ref, onMounted } from 'vue'

    const name = ref('');
    const city = ref('');
    const editingApartment = ref(null);

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
        }
        try {
            const response = await fetch(`/api/apartments/${id}`, requestOptions);
            await fetchApartments();
        } catch (error) {
            console.error('Error deleting apartment:', error);
        }
    }

    function startEdit(apartment) {
        editingApartment.value = { ...apartment };
    }

    function cancelEdit() {
        editingApartment.value = null;
    }

    async function updateApartment() {
        if (!editingApartment.value || !editingApartment.value.id) {
            console.error('Editing apartment failed due to invalid apartment information', editingApartment.value);
            return;
        }

        const requestOptions = {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                name: editingApartment.value.name,
                city: editingApartment.value.city
            })
        }
        try {
            const response = await fetch(`/api/apartments/${editingApartment.value.id}`, requestOptions);
            if (response.ok) {
                await fetchApartments();
                editingApartment.value = null;
            }
        } catch (error) {
            console.error('Error updating apartment:', error);
        }
    }
</script> 