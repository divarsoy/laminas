<template>
    <div class="container">
      <h2>Album List (from Vue)</h2>
      <table class="table">
        <thead>
            <tr>
                <th>Album Title</th>
                <th>Artist</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="album in albumCollection.albums" :key="album.id">
                <td>{{ album.title }}</td>
                <td>{{ album.artist }}</td>
                <td>
                    <button :data-id= "album.id" @click="deleteItem" class="btn button btn-danger">x</button>
                </td>
            </tr>
        </tbody>
    </table>
    <form @:submit.prevent = "addItem">
        <div class="row">
            <div class="col">
                <input type="text" class="form-control" required v-model="title" placeholder="title"/>
            </div>      
            <div class="col">
                <input type="text" class="form-control" required v-model="artist" placeholder="artist"/>
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
    const artist = ref('');
    const title = ref('');

    const albumCollection = reactive({
        albums: []
    });

    onMounted(() => {
        fetchAlbums();
    })

    async function fetchAlbums() {
        try {
            const response = await fetch('/album/api');
            const data = await response.json();
            albumCollection.albums = data;
        } catch (error) {
            console.error('Error listing albums:', error);
        }
   }

    async function addItem() {
        const requestOptions = {
            method:'POST',
            headers: {'content-Type': 'application/json'},
            body: JSON.stringify({
                artist: artist.value,
                title: title.value
            })
        }
        try {
            const response = await fetch('/album/apiAdd', requestOptions);
            const result = await response.json();
            if(response.status === 400) {
                console.error('Validation error:', result);
            }
            title.value = '';
            artist.value = '';
            await fetchAlbums();
        } catch (error) {
            console.error('Error adding album:', error);
        }
    }

    async function deleteItem(event) {
        const id = event.target.getAttribute('data-id');
        const requestOptions = {
            method:'DELETE',
            headers: {'content-Type': 'application/json'},
            body: JSON.stringify({
                id: id
            })
        }
        try{
            const response = await fetch('/album/apiDelete/'+id, requestOptions);
            const data = await response.json();
            await fetchAlbums();
        } catch (error) {
            console.error('Error deleting album:', error);
        }
    }

</script>
  