@include('include.header')
  <section class="section">
	<div class="container">
	@if($errors->any())
	<div class="notification is-danger">
		{{$errors->first()}}
	</div>
	@endif
	<form method="POST" action="/login">
	@csrf
		<div class="field">
		  <label class="label">Name</label>
		  <div class="control has-icons-left has-icons-right">
			<input class="input is-success" type="text" name="name" placeholder="Login" value="admin">
			<span class="icon is-small is-left">
			  <i class="fas fa-user"></i>
			</span>
		  </div>

		</div>


		<div class="field">
		  <label class="label">Password</label>
		  <div class="control has-icons-left has-icons-right">
			<input class="input is-success" type="password" name="password" placeholder="Password" >
			<span class="icon is-small is-left">
			  <i class="fas fa-user"></i>
			</span>
		  </div>

		</div>


		<div class="field is-grouped">
		  <div class="control">
			<button class="button is-link">Sign-in</button>
		  </div>
		</div>
	</form>
	</div>
  </section>

@include('include.footer')