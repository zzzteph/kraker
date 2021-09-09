@include('include.header')
  <section class="section">
  
  
		
  
 
		<div class="container" id="app">
		<div class="table-container">
			<table class="table is-fullwidth">
			<thead>
				<th>#</th>
				<th><abbr title="Agents online on this task">AO</abbr></th>
				<th>Template</th>
				<th>Hashlist</th>
				<th><abbr title="Cracked passwords count">Cracked</abbr></th>
				<th>Progress</th>
				<th><abbr title="Estimated timeout for current task part">ETA</abbr></th>
				<th>Priority</th>
				<th></th>
			</thead>
			<tbody>
				<tr v-for="task in tasks" v-bind:class=" task.agents.length>0 ? 'has-background-info-light' : ''">
				<td><a v-bind:href="'/tasks/'+ task.id">@{{task.id}}</a></td>
				<td>@{{task.agents.length}}</td>
				<td><a v-bind:href="'/templates/'+ task.template_id">@{{task.template_name}}</a></td>
				<td><a v-bind:href="'/hashlists/'+ task.hashlist_id">@{{task.hashlist_name}}</a></td>
				<td>@{{task.cracked}}</td>
				<td>
					<progress class="progress is-danger" v-if="task.progress<25" v-bind:value="task.progress" max="100"></progress>
					<progress class="progress is-warning" v-else-if="task.progress>=25 && task.progress<50" v-bind:value="task.progress" max="100"></progress>
					<progress class="progress is-success" v-else v-bind:value="task.progress" max="100"></progress>
				</td>
								<td>
				<span v-if="task.eta!=false">@{{task.eta}} <i class="fas fa-hourglass-start"></i></span>
				<span v-if="task.eta==false"><i class="fas fa-question-circle"></i></span>




				</td>
				<td>
				
				<div class="buttons">
					<button class="button is-primary is-small" v-on:click="increase($event,task.id)">+</button>
					<button class="button is-danger is-small" v-on:click="decrease($event,task.id)">-</button>
				</div>
				
				
				</td>
				<td>
				
				<button class="button is-danger is-small"  v-on:click="stop($event,task.id)">Stop</button>
				
				</td>
				
				
				</tr>
			</tbody>
		</table>
		</div>
		
		</div>
  </section>
<script>



new Vue({
  el: '#app',
  data() {
    return {
      tasks: null
    };
  },
  
  
    methods: {

        update: function(event) {
            axios.get('/api/tasks/live').then((response) => {
                this.tasks = response.data;
            });
        },
		
	 increase: function(event,id) {            axios.put('/api/tasks/'+id+'/priority',{    action: 'increase'  });     },
		decrease: function(event,id) { axios.put('/api/tasks/'+id+'/priority',{    action: 'decrease'  });        },
		stop: function(event,id) {   

 if(confirm("Do you really want to stop the task?")){

		axios.put('/api/tasks/'+id,		      {    status: 'stopped'  });     
 }		},
    },
  
  mounted() {
	  
	 this.update();
    var self = this;
    this.interval=setInterval(function() {
			self.update();
    }, 2000);
	  
   
	
	
	
	
	
  }});
</script>

@include('include.footer')