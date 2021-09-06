@include('include.header')
  <section class="section">
	<div class="container">

	@if($errors->any())
	<div class="notification is-danger">
		{{$errors->first()}}
	</div>
	@endif
<form method="POST"  enctype="multipart/form-data" action="/hashlists/new">
    @csrf


      <div class="field">
        <label class="label">Name</label>
        <div class="control">
          <input class="input" type="text" placeholder="Text input" name="name">
        </div>
      </div>
      
  
      <div class="field">
        <label class="label">Hashtype</label>
        <div class="control">

            <select name="hashtype" class="selectpicker">
            
			

            @foreach ($hashtypes as $hashtype)
             <option value="{{$hashtype->id}}">{{$hashtype->name}}</option>
          @endforeach
            
             </select>

        </div>
      </div>
      
      <div class="field">
        <label class="label">File with hashes</label>
     <div id="file-js-example" class="file has-name">
      <label class="file-label">
        <input class="file-input" type="file" name="hashfile">
        <span class="file-cta">
          <span class="file-icon">
            <i class="fas fa-upload"></i>
          </span>
          <span class="file-label">
            Choose a file
          </span>
        </span>
        <span class="file-name">
          No file uploaded
        </span>
      </label>
    </div>
</div>
      
	  
	<div class="field">
        <label class="label">(Optional) Or paste hashes here</label>
		<textarea class="textarea" name="hashlist_text"></textarea>

</div>
	  
	  
	  
      <div class="field">
        <div class="control">
          <button class="button is-success">Submit</button>
        </div>

      </div>
	  </form>
</div>


  </section>
<script>
$(function() {
  $('select').selectize();
});


  const fileInput = document.querySelector('#file-js-example input[type=file]');
  fileInput.onchange = () => {
    if (fileInput.files.length > 0) {
      const fileName = document.querySelector('#file-js-example .file-name');
      fileName.textContent = fileInput.files[0].name;
    }
  }
</script>




@include('include.footer')