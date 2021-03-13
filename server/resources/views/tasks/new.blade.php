@include('include.header')
  <section class="section">
	<div class="container" id="app">
	@if($errors->any())
	<div class="notification is-danger">
		{{$errors->first()}}
	</div>
	@endif
	


      <div class="field">
        <label class="label">Hashlist</label>
        <div class="control">
          <div class="select is-fullwidth">
            <select name="hashlist" @change="calculateTime" v-model="selectedHashlist">
              <option v-for="hashlist in hashlists"  v-if="hashlist.status == 'valid'" ref="hashlist" v-bind:value="hashlist.id" >
				@{{ hashlist.name }}
			   </option>
            
             </select>
          </div>
        </div>
      </div>
	  
	  
	  
	  
	     <div class="field">
        <label class="label">Templates</label>
        <div class="control">
          <div class="select is-fullwidth">
            <select name="templates" @change="calculateTime"  v-model="selectedTemplate">
              <option v-for="template in templates"  v-if="template.status == 'valid'"  ref="template" v-bind:value="template.id" >
				@{{ template.name }}
			   </option>
            
             </select>
          </div>
        </div>
      </div>

	     <div class="field" v-if="time !== false" >
        <label class="label">Time needed: <span  >@{{time}}</span></label>

      </div>

		<div class="field">
		  <div class="control">
			<button class="button is-primary" v-if="time !== false" v-on:click="submit">Submit</button>
		  </div>
		</div>


</div>


  </section>

<script>

new Vue({
  el: '#app',
  data() {
    return {
      hashlists: null,
	  templates: null,
	  time: false,
	  selectedHashlist:null,
	  selectedTemplate:null,
	  
    };
  },
  mounted() {
    axios.get('/api/hashlists').then(response => (this.hashlists = response.data));
	axios.get('/api/templates').then(response => (this.templates = response.data));
  },
   methods: {
        calculateTime(event) {
			axios.post('/api/tasks/calculate',{hashlist_id:this.selectedHashlist,template_id:this.selectedTemplate}).then(response => (this.time = response.data));
        },
		
		 submit : function (event){
			axios.post('/api/tasks',{hashlist_id:this.selectedHashlist,template_id:this.selectedTemplate}).then((response) => {
			if(response.data!==false)
			{
				window.location.href = "/";
			}
  
    });
		
    }
  
  
}});
</script>



@include('include.footer')