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
              <button
                v-if="selected && selectedPoll"
                v-on:click="search()"
                class="btn btn-success"
              >Search</button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div v-if="totalReport" class="panel panel-default">
          <div class="panel-heading">
            <strong>Total report</strong>
          </div>
          <table class="table">
            <thead>
              <tr>
                <th>Poll ID</th>
                <th>Poll Name</th>
                <th>Total users</th>
                <th>Winners</th>
                <th>Losers</th>
                <th>Correct 7</th>
                <th>Correct 6</th>
                <th>Correct 5</th>
                <th>Correct #7</th>
              </tr>
            </thead>
            <tbody>
              <tr v-bind:key="index" v-for="(rep,index) in totalReport">
                <td>{{rep.p_id}}</td>
                <td>{{rep.name}}</td>
                <td>{{rep.users}}</td>
                <td>{{rep.winners}}</td>
                <td>{{rep.users - rep.winners}}</td>
                <td>{{rep.correct_answers_7}}</td>
                <td>{{rep.correct_answers_6}}</td>
                <td>{{rep.correct_answers_5}}</td>
                <td>{{rep.correct_number_seven}}</td>
              </tr>
<tr v-if="totalReportSummary">
                <td><strong>Summary</strong></td>
                <td>-</td>
                <td><strong>{{totalReportSummary[0].users}}</strong></td>
                <td><strong>{{totalReportSummary[0].winners}}</strong></td>
                <td><strong>{{totalReportSummary[0].users - totalReportSummary[0].winners}}</strong></td>
                <td><strong>{{totalReportSummary[0].correct_answers_7}}</strong></td>
                <td><strong>{{totalReportSummary[0].correct_answers_6}}</strong></td>
                <td><strong>{{totalReportSummary[0].correct_answers_5}}</strong></td>
                <td><strong>{{totalReportSummary[0].correct_number_seven}}</strong></td>

</tr>

            </tbody>
          </table>
        </div>

        <div v-if="groupedAnswers && selectedPoll && searched" class="panel panel-default">
          <div class="panel-heading">
            <strong>Report</strong>
            <export-excel
              class="btn btn-success pull-right mb-1"
              :data="json_data"
              :fields="json_fields"
              worksheet="My Worksheet"
              name="filename.xls"
            >Download Excel</export-excel>
          </div>
          <table class="table">
            <thead>
              <tr>
                <th>User ID</th>
                <th>Total</th>
                <th>Correct</th>
              </tr>
            </thead>
            <tbody>
              <tr v-bind:key="index" v-for="(gAnswer,index) in groupedAnswers">
                <td>{{index}}</td>
                <td>{{gAnswer.length}}</td>
                <td>{{gAnswer.filter(qA=>qA.correct==1).length}}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <ul v-if="selectedPoll && searched" class="list-group">
          <li
            class="list-group-item list-group-item-success"
            v-bind:key="question.id"
            v-for="question in selectedPoll.questions"
          >
            <strong>{{question.name}}</strong>
            <span
              class="pull-right"
            >Total: ({{totalAnswers(question).length}}) , Correct: ({{correctAnswers(question).length}})</span>
            <ul class="list-group">
              <li
                class="list-group-item list-group-item-info"
                v-for="userAnswer in totalAnswers(question)"
                v-bind:key="userAnswer.id"
              >
                <strong>User ID: {{userAnswer.user_id }}</strong>
                <span
                  v-html="question.answers.find(an=>an.id==userAnswer.answer_id?an:false).correct?
                '<span class=\'btn btn-success \'><span class=\'glyphicon glyphicon glyphicon-ok\' aria-hidden=\'true\'></span></span>'
                :'<span class=\'btn btn-danger \'><span class=\'glyphicon glyphicon-remove\' aria-hidden=\'true\'></span></span>' "
                ></span>
              </li>
            </ul>
          </li>
        </ul>
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
      groupedAnswers: false,
      selected: "",
      selectedPoll: "",
      seletedCampaign: false,
      totalReport: false,
      totalReportSummary: false,
      loadingTotalReport: false,
      searched: false,
      campaigns: [],
      json_fields: {
        User: "user_id",
        Total: "total",
        Correct: "correct"
      },
      json_data: [
        {
          user_id: "Tony Peña",
          total: "New York",
          correct: "United States"
        }
      ],
      json_meta: [
        [
          {
            key: "charset",
            value: "utf-8"
          }
        ]
      ]
    };
  },
  methods: {
    getJsonData: () => this.json_data,
    filterPolls: function($event) {
      this.selectedPoll = "";
      this.searched = false;
      this.groupedAnswers = [];
      console.log(this.selected);
      if (this.selected) {
        this.getTotalReport(this.selected);
      }
    },
    getTotalReport: function(campaignId) {
      this.loadingTotalReport = true;
      axios
        .get("/admin/get_total_report/" + this.selected)
        .then(response => {
          if (response.data.success && response.data.success.length) {
            this.totalReport = response.data.success;
          } else {
            this.totalReport = false;
          }
        })
        .catch(error => {
          this.errored = true;
        })
        .finally(() => (this.loadingTotalReport = false));

      axios
        .get("/admin/get_total_report_summary/" + this.selected)
        .then(response => {
          console.log(response.data)
          if (response.data.success && response.data.success.length) {
            this.totalReportSummary = response.data.success;
          } else {
            this.totalReportSummary = false;
          }
        })
        .catch(error => {
          this.errored = true;
        })
        .finally(() => (this.loadingTotalReport = false));
    },

    filteredPolls: function($event) {
      return this.polls.filter(poll => poll.campaign_id == this.selected);
    },

    search: function() {
      this.groupedAnswers = [];
      axios
        .get("/admin/get_user_answers/" + this.selectedPoll.id)
        .then(response => {
          if (response.data.success && response.data.success.length) {
            this.answers = response.data.success;
            this.searched = true;
            this.groupedAnswers = this.answers.reduce(
              (objectsByKeyValue, obj) => ({
                ...objectsByKeyValue,
                [obj["user_id"]]: (
                  objectsByKeyValue[obj["user_id"]] || []
                ).concat(obj)
              }),
              {}
            );
            let t = JSON.parse(JSON.stringify(this.groupedAnswers));

            this.json_data = Object.keys(t).map(function(key, index) {
              return {
                user_id: key,
                total: t[key].length,
                correct: t[key].filter(a => a.correct == 1).length
              };
            });
          } else {
            this.answers = false;
          }
        })
        .catch(error => {
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
      return this.answers.reduce(function(a, b) {
        return pollId == b.poll_id ? a + 1 : a;
      }, 0);
    },

    totalAnswers: function(question) {
      return this.answers.filter(answer =>
        question.id == answer.question_id ? answer : false
      );
    },

    correctAnswers: function(question) {
      return this.answers.filter(answer =>
        question.id == answer.question_id ? answer : false
      );
    }
  },

  mounted() {
    axios
      .post("/admin/get_campaigns")
      .then(response => {
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
