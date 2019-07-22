<template>
  <div class="box" style="padding:10px">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <select v-model="selected">
            <option disabled value>Select Campaign</option>
            <option
              v-for="camp in campaigns "
              v-bind:key="camp.id"
              v-bind:value="camp.id"
            >{{ camp.name }}</option>
          </select>
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          <input class="form-control" v-on:change="search" v-model="userId" placeholder="User ID" />
        </div>
      </div>
      <div class="col-md-12" v-if="answers">
        <div class="container-fluid">
          <ul class="list-group">
            <li class="list-group-item active">{{findSeletedCampaign().name}}</li>
            <li class="list-group-item list-group-item-info" :key="poll.id" v-for="poll in findSeletedCampaign().polls">
              <a href="#" class="list-group-item list-group-item-info">{{poll.name}}</a>
              <ul class="list-group">
                <li class="list-group-item list-group-item-success" v-for="question in poll.questions" :key="question.id">
                  <a href="#" class="list-group-item list-group-item-success">{{question.name}}</a>
                  <ul class="list-group">
                    <li class="list-group-item  list-group-item-warning" v-for="answer in question.answers" :key="answer.id">{{answer.name}}</li>
                  </ul>
                </li>
              </ul>
            </li>
          </ul>
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
      answers: false,
      selected: "",
      seletedCampaign: false,
      campaigns: []
    };
  },
  methods: {
    search: function() {
      console.log("search");
      axios
        .get("/admin/get_user_answers/" + this.selected + "/" + this.userId)
        .then(response => {
          if (response.data.success && response.data.success.length) {
            this.answers = response.data.success;
            this.findSeletedCampaign();
          } else {
            this.answers = false;
          }
          console.log(this.answers);
        })
        .catch(error => {
          console.log(error);
          this.errored = true;
        })
        .finally(() => (this.loading = false));
    },
    findSeletedCampaign: function() {
      return this.campaigns.reduce(function(a, b) {
        return b.id == this.selected ? b : {};
      });
    },
    hasPredictionInPoll(pollId){

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
