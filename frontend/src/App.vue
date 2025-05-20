<template>
    <div>
      <h2>Album List (from Vue)</h2>
      <ul>
        <li v-for="album in albums" :key="album.id">
          {{ album.title }} - {{ album.artist }}
        </li>
      </ul>
    </div>
</template>
  
<script>
export default {
    data() {
        return {
        albums: []
        }
    },
    mounted() {
        fetch('/album/api')
        .then(res => res.json())
        .then(data => this.albums = data);
    }
}
</script>
  