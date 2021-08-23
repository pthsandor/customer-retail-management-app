<template>
  <div id="right">
      <h1>Development CRM</h1>
      <div class="horizontal">
          <img :src="require('../images/horizontal.png').default" alt="">
      </div>
      <p>
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Hic, eius facere, delectus aliquid perferendis laborum enim quasi ut quisquam aliquam facilis nihil pariatur maxime necessitatibus asperiores natus repellendus reprehenderit quia.
      </p>
      <div class="tasks">
          <div class="add-tasks">
              <h2>Today's Task</h2>
              <div class="add-action">
                  <img :src="require('../images/add.png').default" alt="">
              </div>
          </div>
          <ul class="tasks-list"> 
              <li v-for="task in todaytasks" v-bind:key="task.id">
                   <div class="info">
                          <div class="left">
                              <label class="myCheckbox">
                                  <input 
                                  type="checkbox"
                                  name="test" 
                                  :checked="task.completed"
                                  @change="updateTodayTask(task.taskID)"
                                  />
                                  <span></span>
                              </label>
                              <h4>{{task.title}}</h4>
                          </div>

                          <div class="right">
                              <img :src="require('../images/edit.png').default" alt="">
                              <img :src="require('../images/del.png').default" alt="" @click="deleteTask(task.taskID)">

                              <button
                                v-bind:class="{
                                inprogress: !task.approved,
                                approved: task.approved,
                                }" >
                {{ task.approved ? "Approved" : "In progress" }}
              </button>
                          </div>
                      </div>
              </li>
          </ul>
      </div>
      <div class="upcoming">
          <div class="add-tasks">
              <h2>Upcoming</h2>
              <div class="add-action">
                  <img :src="require('../images/add.png').default" alt="">
              </div>
              </div>
              <form action="" @submit="addUpcomingTask">
                  <input type="text" v-model="newTask">
              </form>

              <ul class="tasks-list">
                  <li v-for="upcomingtask in upcoming" v-bind:key="upcomingtask.id">
                      <div class="info">
                          <div class="left">
                              
                              <label class="myCheckbox">
                                  <input 
                                  type="checkbox"
                                  name="test" 
                                  :checked="upcomingtask.completed"
                                  @change="checkUpcoming(upcomingtask.taskID)"
                                  />
                                  <span></span>
                              </label>
                           
                              
                              <h4>{{upcomingtask.title}}</h4>
                          </div>

                          <div class="right">
                              <img :src="require('../images/edit.png').default" alt="">
                              <img :src="require('../images/del.png').default" alt="" @click="delUpcoming(upcomingtask.taskID)">
                              <button
                                v-bind:class="{
                                inprogress: !upcoming.waiting,
                                }" >
                                    {{ upcoming.waiting ? "Approved" : "Waiting" }}
                            </button>
                          </div>
                      </div>
                  </li>
              </ul>
          </div>
      </div>
  
</template>

<script>
export default {
    data(){
        return {
            todaytasks: [],
            upcoming: [],
            newTask: "",
        };
    },

    created(){
        this.fetchTodayTasks();
        this.fetchUpcoming();
    },

    methods: {
        // ** Upcoming Task *//

        // Get upcoming
        fetchUpcoming() {
            fetch('/api/upcoming')
            .then((res) => res.json())
            .then(({data}) => {
                this.upcoming = data;
            })
            .catch((err) => console.log(err));
        },

        // Add upcoming task
        addUpcomingTask(e) {
            e.preventDefault();

           if(this.upcoming.length > 4) {
               alert("Please complete the upcoming task");
           }else{
               const newTask={
                   title: this.newTask,
                   waiting: true,
                   taskID: Math.random().toString(3).substring(7),
               };
               // Post Request
               fetch("/api/upcoming", {
                   method: "POST",
                   headers: {
                       "content-type": "application/json",
                   },
                   body: JSON.stringify(newTask),
               }).then(() => this.upcoming.push(newTask));

               // Clear or reset new task
               this.newTask = "";
           }
        },
        // Delete Upcoming Task
        delUpcoming (taskID){
            if(confirm("Are you sure?")){
                fetch('/api/upcoming/${taskID}', {
                    method: 'delete',
                }).then((res) => res.json())
                .then((data) => {
                    this.upcoming = this.upcoming.filter(
                        ({ taskID: id }) => id !== taskID
                        );
                })
                .catch((err) => console.log(err));
            }
        },
         // Check upcoming task
        checkUpcoming(taskID){
            if(this.todaytasks.length > 4){
                alert("Sorry complete existing task");
                window.location.href="/";
            }else{
                this.addDailyTask(taskID);
                console.log('check');

                // Deleted from this task from db
                fetch('/api/upcoming/${taskID}',{
                    method: "delete"}).then(() => (this.upcoming = this.upcoming.filter(({taskID: id}) => id !== taskID
                    ))
                    );
            }
        },

        // ** Today Task method *//

        // Get today tasks
        fetchTodayTasks() {
            fetch("/api/dailytask")
            .then((res) => res.json())
            .then(({data}) => {
                this.todaytasks = data
                })
            .catch(err => console.log(err));
        },

        addDailyTask(taskID){
            // Get task
            const task = this.upcoming.filter(({taskID: id}) => id == taskID)[0];

            // Post request
            fetch("/api/dailytask", {
                method: "POST",
                headers: {
                    'content-type': "application/json"
                },
                body: JSON.stringify(task),
            }).then(() => this.todaytasks.unshift(task))
            .catch((err) => console.log(err));
        },

            // Update today task
            updateTodayTask(taskID){
                if(confirm("Task Completed?")){
                    fetch('/api/dailytask/${taskID}',
                    { method: "delete",  })
                    .then((res) => res.json())
                .then((data) => {
                    this.todaytasks = this.todaytasks.filter(
                        ({ taskID: id }) => id !== taskID
                        );
                })
                .catch((err) => console.log(err));
                    
                }
            },

           // Delete Upcoming Task
        deleteTask (taskID){
            if(confirm("Are you sure?")){
                fetch('/api/dailytask/${taskID}', {
                    method: 'delete',
                })
                .then((res) => res.json())
                .then((data) => {
                    this.todaytasks = this.todaytasks.filter(
                        ({ taskID: id }) => id !== taskID
                        );
                })
                .catch((err) => console.log(err));
            }
        },
    },
};
</script>

<style>

</style>