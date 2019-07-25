<template>
  <div class="box" style="padding:10px">
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <select class="form-control" v-model="selected" @change="filterPolls($event)">
                <option disabled value>Select Campaign</option>
                <option
                  v-for="camp in campaigns "
                  v-bind:key="camp.id"
                  v-bind:value="camp.id"
                >{{ camp.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <select v-if="selected" class="form-control" v-model="selectedPoll">
                <option disabled value>Select Poll</option>
                <option
                  v-for="poll in filteredPolls() "
                  v-bind:key="poll.id"
                  v-bind:value="poll"
                >{{ poll.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <button v-if="selected && selectedPoll" v-on:click="search()" class="btn btn-success">Search</button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <ul v-if="selectedPoll && searched" class="list-group">
          <li
            class="list-group-item"
            v-bind:key="question.id"
            v-for="question in selectedPoll.questions"
          >{{question.name}}</li>
        </ul>
      </div>
      <div class="col-md-12" v-if="answers"> 
        <div class="container-fluid">
          <ul class="list-group">
            <li class="list-group-item active">{{findSeletedCampaign().name}}</li>
            <li
              class="list-group-item list-group-item-info"
              :key="poll.id"
              v-for="poll in findSeletedCampaign().polls"
            >
              <div href="#" class="list-group-item list-group-item-info">
                <strong>{{poll.name}}</strong>
                <span class="pull-right">
                  <strong>User prediction: {{hasPredictionInPoll(poll.id)?hasPredictionInPoll(poll.id):'No prediction'}}</strong>
                </span>
              </div>
              <ul class="list-group">
                <li
                  class="list-group-item list-group-item-success"
                  v-for="question in poll.questions"
                  :key="question.id"
                >
                  <a href="#" class="list-group-item list-group-item-success">{{question.name}}</a>
                  <ul class="list-group">
                    <li
                      class="list-group-item list-group-item-warning"
                      v-for="answer in question.answers"
                      :key="answer.id"
                    >{{answer.name}}</li>
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
      polls: [],
      errored: false,
      answers: false,
      selected: "",
      selectedPoll: "",
      seletedCampaign: false,
      searched: false,
      campaigns: []
    };
  },
  methods: {
    filterPolls: function($event) {
      this.selectedPoll = "";
      this.searched = false;
    },
    filteredPolls: function($event) {
      console.log(this.selected);
      return this.polls.filter(poll => poll.campaign_id == this.selected);

      console.log($event);
    },

    search: function() {
      console.log("search");
      axios
        .get("/admin/get_user_answers/" + this.selected )
        .then(response => {
          if (response.data.success && response.data.success.length) {
            this.answers = response.data.success;
            this.findSeletedCampaign();
                  this.searched = true; 

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
    hasPredictionInPoll(pollId) {
      console.log(pollId);

      return this.answers.reduce(function(a, b) {
        console.log(a);
        console.log(b);

        return pollId == b.poll_id ? a + 1 : a;
      }, 0);
    }
  },
  mounted() {
    axios
      .post("/admin/get_campaigns")
      .then(response => {
        console.log(response.data.success);
        if (response.data.success) {
          this.campaigns = response.data.success.campaigns;
          this.polls = response.data.success.polls;
        }
      })
      .catch(error => {
        console.log(error);
        this.errored = true;
      })
      .finally(() => (this.loading = false));
  }
};
</script>
