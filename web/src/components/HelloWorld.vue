<script lang="ts">

export default {
  name: "Home",
  data() {
    return {
      posts: [],
    };
  },

  methods: {
    async getData() {
      try {
        let response = await fetch("http://localhost:8080/admin/realms/api/users");
        this.posts = await response.json();
        console.log(this.posts);
      } catch (error) {
        console.log(error);
      }
    },
  },

  created() {
    this.getData();
  },
};
</script>


