@include('include.header')
  <section class="section">
	<div class="container" id="app">
 	@if($errors->any())
	<div class="notification is-danger">
		{{$errors->first()}}
	</div>
	@endif
 
 
 
  <form method="POST" action="/templates">
  
    <div class="field">
  <label class="label">Name *</label>
  <div class="control">
    <input class="input" name="name" type="text" placeholder="Text input">
  </div>
</div>
  
  <div class="field">
    <label class="label">Choose template</label>
    <div class="control">
      <div class="select">
        <select name="TemplateType" @change="onChange($event)" >
        <option disabled selected value> -- select an option -- </option>
          <option value="wordlist">Wordlist (+rules)</option>
          <option value="mask">Mask</option>
        </select>
      </div>
    </div>
  </div>


  <template v-if="template === 'wordlist'">
  
  @csrf



  <div class="field">
    <label class="label">Wordlist *</label>
    <div class="control">
      <div class="select">
        <select name="wordlist">
            @foreach ($wordlists as $wordlist)
                <option value="{{$wordlist->id}}">{{$wordlist->name}} {{$wordlist->count}}</option>
            @endforeach
        </select>
      </div>
    </div>
  </div>



  <div class="field">
    <label class="label">Rule</label>
    <div class="control">
      <div class="select">
        <select name="rule">
        <option disabled selected value> -- select an option -- </option>
            @foreach ($rules as $rule)
                <option value="{{$rule->id}}">{{$rule->name}}</option>
            @endforeach
        </select>
      </div>
    </div>
  </div>






  <div class="field">
  <div class="control">
    <button class="button is-link">Submit</button>
  </div>
</div>

  
  </template>


  <template v-if="template === 'mask'">
@csrf



<div class="field">
  <label class="label">Mask *</label>
  <div class="control">
    <input class="input" name="mask" type="text" placeholder="?l?l?l?l?l">
  </div>
</div>


<div class="field">
  <label class="label">Charset 1</label>
  <div class="control">
    <input class="input" name="charset1" type="text" placeholder="Text input">
  </div>
</div>


<div class="field">
  <label class="label">Charset 2</label>
  <div class="control">
    <input class="input" name="charset2" type="text" placeholder="Text input">
  </div>
</div>


<div class="field">
  <label class="label">Charset 3 </label>
  <div class="control">
    <input class="input" name="charset3" type="text" placeholder="Text input">
  </div>
</div>


<div class="field">
  <label class="label">Charset 4 </label>
  <div class="control">
    <input class="input" name="charset4" type="text" placeholder="Text input">
  </div>
</div>

<div class="field">
  <div class="control">
    <button class="button is-link">Submit</button>
  </div>
</div>

  
  </template>

</form>
</div>
</section>

<script>
var app = new Vue({
  el: '#app',
  data: {
    template:""
  },
  methods: {
    onChange(event) {
            this.template=event.target.value;
        }
    }
  
  
})
</script>
@include('include.footer')