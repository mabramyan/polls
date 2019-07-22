<template>
  <div class="box" style="padding:10px">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <select v-model="selected">
            <option disabled value>Select Campaign</option>
            <option v-for="camp in campaigns " v-bind:key="camp.id" v-bind:value="camp.id">{{ camp.name }}</option>
          </select>
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          <input class="form-control" v-on:change="search" v-model="userId" placeholder="User ID" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      userId: null,
      loading: true,
      errored: false,
      selected: "",
      campaigns: []
    };
  },
  methods: {
    search: function() {
      console.log("search");
    }
  },
  mounted() {
    axios
      .post("/admin/get_campaigns")
      .then(response => {
        this.campaigns = response.data.data;
      })
      .catch(error => {
        console.log(error);
        this.errored = true;
      })
      .finally(() => (this.loading = false));
  }
};
</script>
