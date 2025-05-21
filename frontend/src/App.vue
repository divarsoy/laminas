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
                <div class="row justify-content-start">
                    <button class="col-md-5 btn button btn-success">Edit</button>
                    <button :data-id= "album.id" @click="deleteItem" class="col-md-5 offset-md-1 btn button btn-danger">Delete</button>
                </div>
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
    import { reactive, ref } from 'vue'
    const artist = ref('')
    const title = ref('')
    const placeHolder = ref('')


    const albumCollection = reactive({
        albums: []
    });

    fetchAlbums();
    function fetchAlbums() {
        fetch('/album/api')
        .then(res => res.json())
        .then(data => {
            albumCollection.albums = data;
        });
    }

    function addItem() {

        const requestOptions = {
            method:'POST',
            headers: {'content-Type': 'application/json'},
            body: JSON.stringify({
                id:0,
                artist: artist.value,
                title: title.value
            })
        }
        fetch('/album/apiAdd', requestOptions)
        .then(response => response.json())
        .then(data => placeHolder.value = data);

        title.value = '';
        artist.value = '';
        fetchAlbums();
    }
    function deleteItem(event) {
        const id = event.target.getAttribute('data-id');
        const requestOptions = {
            method:'DELETE',
            headers: {'content-Type': 'application/json'},
            body: JSON.stringify({
                id: id
            })
        }
        fetch('/album/apiDelete/'+id, requestOptions)
        .then(response => response.json())
        .then(data => {
            placeHolder.value = data;
            fetchAlbums();
        });
    }

</script>
  